<?php

require_once(__DIR__ . '/../../cnx/cnx.php');

class Navegacion extends Conexion
{
    protected function runQuery($sql, $params = array())
    {
        $rows = array();
        // Las tablas de almacén y Coordiapp están en la base principal.
        $conexion = $this->conexion();

        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                $param = ':' . ltrim($key, ':');
                $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (method_exists($stmt, 'closeCursor')) {
                $stmt->closeCursor();
            }
        } catch (PDOException $e) {
            error_log("[Navegacion::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
            return array();
        }

        return is_array($rows) ? $rows : array();
    }

    protected function runExecute($sql, $params = array())
    {
        $conexion = $this->conexion();
        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                $param = ':' . ltrim($key, ':');
                $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("[Navegacion::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }

    protected function runAnalysisQuery($sql, $params = array())
    {
        $rows = array();
        // TAC y ECI están en analisis_bd, por eso usan otra conexión.
        $conexionAnalisis = $this->get_conexiondos();

        try {
            $statement = $conexionAnalisis->prepare($sql);
            $statement->execute($params);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            $statement->closeCursor();
        } catch (PDOException $e) {
            error_log('[Navegacion::runAnalysisQuery] Error: ' . $e->getMessage());
        }

        return is_array($rows) ? $rows : array();
    }

    protected function normalizarSerie($serie)
    {
        // Permite comparar series aunque tengan espacios o diferencias de mayúsculas.
        return strtoupper(preg_replace('/\s+/', '', trim((string)$serie)));
    }

    protected function normalizarTelefono($telefono)
    {
        // Permite comparar teléfonos ignorando guiones, espacios y otros símbolos.
        return preg_replace('/\D/', '', (string)$telefono);
    }

    //================ CONSULTAS DE NAVAGACION ===================

    // OBTENER FOLIOS PARA UN CONTRATISTA
    public function FoliosContratista($contratistaId, $fechaInicio, $fechaFin)
    {
        $sql = "SELECT
                t.Folio_Salida_ST,
                COUNT(DISTINCT m.Num_Serie_S) AS CantidadSeries
            FROM transferencias_t t
                INNER JOIN material_det_salida m ON t.Folio_Salida_ST = m.Folio_Salida_Material_Det_S
                INNER JOIN producto p ON m.FK_Material_S = p.idProducto
                WHERE
                    p.Tipo_Proyecto IN ('ONT', 'Modem') AND
                    t.FK_Contra_ST = :contratistaId AND
                    DATE(Fecha_Salida_ST) BETWEEN :fechaInicio AND :fechaFin
                GROUP BY t.Folio_Salida_ST
                ORDER BY t.Folio_Salida_ST DESC";

        $params = array(
            ':fechaInicio' => $fechaInicio,
            ':fechaFin'    => $fechaFin,
            ':contratistaId' => $contratistaId
        );

        return $this->runQuery($sql, $params);
    }

    // OBTENER LAS SERIES Y SU ESTADO PARA UN FOLIO
    public function ObtenerDetalleFolio($folio, $contratistaId)
    {
        // 1. Obtener las series del folio desde las tablas de almacén.
        $sql = "SELECT
                    m.Num_Serie_S,
                    p.Producto,
                    s.Folio_Salida_Det,
                    s.FK_Tecnico_Salida_Det,
                    CONCAT(t.Nombre_T, ' ', t.Apellidos_T) AS Tecnico,
                    c.Ont AS Serie_Coordiapp,
                    c.Telefono AS Telefono_Coordiapp,
                    c.Fecha_Coordiapp
                FROM transferencias_t AS transferencia
                INNER JOIN material_det_salida AS m
                    ON transferencia.Folio_Salida_ST = m.Folio_Salida_Material_Det_S
                INNER JOIN producto AS p
                    ON m.FK_Material_S = p.idProducto
                LEFT JOIN salidas_contratistas AS s
                    ON m.Num_Serie_S = s.Num_Serie_Salida_Det
                LEFT JOIN tecnicos AS t
                    ON s.FK_Tecnico_Salida_Det = t.idTecnico
                LEFT JOIN View_Detalle_Coordiapp_Completadas AS c
                    ON m.Num_Serie_S = c.Ont
                WHERE transferencia.Folio_Salida_ST = :folio
                    AND transferencia.FK_Contra_ST = :contratistaId
                    AND p.Tipo_Proyecto IN ('ONT', 'Modem')
                ORDER BY p.Producto, m.Num_Serie_S";

        $series = $this->runQuery($sql, array(
            ':folio' => $folio,
            ':contratistaId' => $contratistaId
        ));

        if (empty($series)) {
            return array();
        }

        // 2. Crear listas únicas para consultar TAC y ECI en bloque.
        $seriesUnicas = array();
        $telefonosUnicos = array();

        foreach ($series as $serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            $telefonoCoordiapp = $this->normalizarTelefono($serie['Telefono_Coordiapp'] ?? '');

            if ($serieNormalizada !== '') {
                $seriesUnicas[$serieNormalizada] = true;
            }

            if ($telefonoCoordiapp !== '') {
                $telefonosUnicos[$telefonoCoordiapp] = true;
            }
        }

        // 3. Buscar en TAC por número de serie y obtener teléfono y fecha de liquidación.
        $tacPorSerie = array();
        if (!empty($seriesUnicas)) {
            $seriesParaTac = array_keys($seriesUnicas);
            $marcadoresTac = implode(',', array_fill(0, count($seriesParaTac), '?'));
            $registrosTac = $this->runAnalysisQuery(
                "SELECT Num__Serie, TELEFONO, FECHA_LIQ
                 FROM qm_tac
                 WHERE REPLACE(UPPER(Num__Serie), ' ', '') IN ($marcadoresTac)
                 ORDER BY FECHA_LIQ DESC",
                $seriesParaTac
            );

            foreach ($registrosTac as $registroTac) {
                $serieTac = $this->normalizarSerie($registroTac['Num__Serie'] ?? '');
                if ($serieTac !== '' && !isset($tacPorSerie[$serieTac])) {
                    $tacPorSerie[$serieTac] = $registroTac;
                    $telefonoTac = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');
                    if ($telefonoTac !== '') {
                        $telefonosUnicos[$telefonoTac] = true;
                    }
                }
            }
        }

        // 4. Buscar en ECI por serie o teléfono, considerando únicamente servicios activos.
        $eciPorSerie = array();
        if (!empty($seriesUnicas) || !empty($telefonosUnicos)) {
            $condicionesEci = array();
            $parametrosEci = array();

            if (!empty($seriesUnicas)) {
                $seriesParaEci = array_keys($seriesUnicas);
                $marcadoresSerieEci = implode(',', array_fill(0, count($seriesParaEci), '?'));
                $condicionesEci[] = "REPLACE(UPPER(M_SERIE), ' ', '') IN ($marcadoresSerieEci)";
                $parametrosEci = array_merge($parametrosEci, $seriesParaEci);
            }

            if (!empty($telefonosUnicos)) {
                $telefonosParaEci = array_keys($telefonosUnicos);
                $marcadoresTelefonoEci = implode(',', array_fill(0, count($telefonosParaEci), '?'));
                $condicionesEci[] = "REPLACE(REPLACE(REPLACE(TELEFONO, '-', ''), ' ', ''), '+', '') IN ($marcadoresTelefonoEci)";
                $parametrosEci = array_merge($parametrosEci, $telefonosParaEci);
            }

            $registrosEci = $this->runAnalysisQuery(
                "SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, F_CONSULTA, DID_ACTIVO, TECNOLOGIA
                 FROM seg_ns_eci
                                 WHERE DID_ACTIVO = 1
                                     AND (" . implode(' OR ', $condicionesEci) . ")
                 ORDER BY F_CONSULTA DESC, FECHA_ULTIMO_EVENTO DESC",
                $parametrosEci
            );

            foreach ($registrosEci as $registroEci) {
                $serieEci = $this->normalizarSerie($registroEci['M_SERIE'] ?? '');
                if ($serieEci !== '' && !isset($eciPorSerie[$serieEci])) {
                    $eciPorSerie[$serieEci] = $registroEci;
                }
            }
        }

        // 5. Resolver el estado de navegación de cada serie.
        foreach ($series as &$serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            $registroTac = $tacPorSerie[$serieNormalizada] ?? array();
            $registroEci = $eciPorSerie[$serieNormalizada] ?? array();

            $telefonoCoordiapp = $this->normalizarTelefono($serie['Telefono_Coordiapp'] ?? '');
            $telefonoTac = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');
            $telefonoEci = $this->normalizarTelefono($registroEci['TELEFONO'] ?? '');
            $telefonosRegistrados = array_values(array_filter(array($telefonoCoordiapp, $telefonoTac, $telefonoEci)));

            // Si existen teléfonos en varias plataformas, todos deben coincidir.
            $telefonosCoinciden = count(array_unique($telefonosRegistrados)) <= 1;

            $fechaCoordiapp = $serie['Fecha_Coordiapp'] ?? null;
            $fechaTac = $registroTac['FECHA_LIQ'] ?? null;
            $fechaBase = null;

            foreach (array($fechaCoordiapp, $fechaTac) as $fechaPlataforma) {
                if (!empty($fechaPlataforma) && ($fechaBase === null || strtotime($fechaPlataforma) > strtotime($fechaBase))) {
                    $fechaBase = $fechaPlataforma;
                }
            }

            // La consulta ECI debe ser al menos tres días posterior a TAC/Coordiapp.
            $fechaConsultaEci = $registroEci['F_CONSULTA'] ?? null;
            $fechaEciValida = false;
            if (!empty($fechaConsultaEci)) {
                $fechaEciValida = $fechaBase === null || strtotime($fechaConsultaEci) >= strtotime($fechaBase . ' +3 days');
            }

            $serie['Telefono_Tac'] = $registroTac['TELEFONO'] ?? null;
            $serie['Fecha_Liquidacion_Tac'] = $fechaTac;
            $serie['Telefono_Eci'] = $registroEci['TELEFONO'] ?? null;
            $serie['Fecha_Consulta_Eci'] = $fechaConsultaEci;
            $serie['Estado_Eci'] = $registroEci['DID_ACTIVO'] ?? null;

            // Navega solo si ECI está activo, existe, los teléfonos coinciden y la fecha es válida.
            $eciActivo = !empty($registroEci) && (int)($registroEci['DID_ACTIVO'] ?? 0) === 1;
            $serie['Navegando'] = $eciActivo && $telefonosCoinciden && $fechaEciValida ? 1 : 0;
        }
        unset($serie);

        return $series;
    }
}