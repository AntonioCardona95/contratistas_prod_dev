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
        $conexionAnalisis = $this->conexion_navegacion();

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
                t.Fecha_Salida_ST,
                COUNT(DISTINCT m.Num_Serie_S) AS CantidadSeries
            FROM transferencias_t t
                INNER JOIN material_det_salida m ON t.Folio_Salida_ST = m.Folio_Salida_Material_Det_S
                INNER JOIN producto p ON m.FK_Material_S = p.idProducto
                WHERE
                    p.Tipo_Proyecto IN ('ONT', 'Modem') AND
                    t.FK_Contra_ST = COALESCE(NULLIF(:contratistaId, 0), t.FK_Contra_ST) AND
                    DATE(Fecha_Salida_ST) BETWEEN :fechaInicio AND :fechaFin
                GROUP BY t.Folio_Salida_ST, t.Fecha_Salida_ST
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
        // 1. OBTENER LAS SERIES DEL FOLIO DESDE LAS TABLAS DE ALMACÉN.
        // SOLO SE CONSIDERAN MATERIALES DEL TIPO ONT Y MODEM.
        $sql = "SELECT
                    m.Num_Serie_S,
                    p.Producto,
                    s.Folio_Salida_Det,
                    s.Fecha_Salida_Det,
                    s.FK_Tecnico_Salida_Det,
                    t.Nombre_T,
                    t.Apellidos_T,
                    CONCAT(t.Nombre_T, ' ', t.Apellidos_T) AS Tecnico
                FROM transferencias_t AS transferencia
                INNER JOIN material_det_salida AS m
                    ON transferencia.Folio_Salida_ST = m.Folio_Salida_Material_Det_S
                INNER JOIN producto AS p
                    ON m.FK_Material_S = p.idProducto
                LEFT JOIN salidas_contratistas AS s
                    ON m.Num_Serie_S = s.Num_Serie_Salida_Det
                LEFT JOIN tecnicos AS t
                    ON s.FK_Tecnico_Salida_Det = t.idTecnico
                WHERE transferencia.Folio_Salida_ST = :folio
                    AND transferencia.FK_Contra_ST = COALESCE(NULLIF(:contratistaId, 0), transferencia.FK_Contra_ST)
                    AND p.Tipo_Proyecto IN ('ONT', 'Modem')
                ORDER BY p.Producto, m.Num_Serie_S";

        $series = $this->runQuery($sql, array(
            ':folio' => $folio,
            ':contratistaId' => $contratistaId
        ));

        if (empty($series)) {
            return array();
        }

        // 2. CARGAR LOS DATOS DE COORDIAPP POR LOTE PARA EVITAR UN JOIN COSTOSO.
        // SE CONSERVAN TODOS LOS REGISTROS ENCONTRADOS PARA PODER DETECTAR DUPLICADOS.
        $coordiappPorSerie = array();
        $coordiappRegistrosPorSerie = array();
        $duplicadosCoordiappPorSerie = array();
        $seriesUnicas = array();
        $telefonosUnicos = array();

        // CREAR LA LISTA DE SERIES NORMALIZADAS QUE SE CONSULTARÁ EN COORDIAPP.
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

        if (!empty($seriesUnicas)) {
            $parametrosCoordiapp = array();
            $marcadoresCoordiapp = array();
            $index = 0;

            foreach (array_keys($seriesUnicas) as $serieNormalizada) {
                $placeholder = ':coord_' . $index;
                $marcadoresCoordiapp[] = $placeholder;
                $parametrosCoordiapp[$placeholder] = $serieNormalizada;
                $index++;
            }

            $registrosCoordiapp = $this->runQuery(
                "SELECT Ont, Telefono, Fecha_Coordiapp
                 FROM View_Detalle_Coordiapp_Completadas
                 WHERE Ont IN (" . implode(',', $marcadoresCoordiapp) . ")",
                $parametrosCoordiapp
            );

            // AGRUPAR TODOS LOS REGISTROS DE COORDIAPP POR NÚMERO DE SERIE.
            foreach ($registrosCoordiapp as $registroCoordiapp) {
                $serieCoordiapp = $this->normalizarSerie($registroCoordiapp['Ont'] ?? '');
                if ($serieCoordiapp !== '') {
                    $coordiappRegistrosPorSerie[$serieCoordiapp][] = $registroCoordiapp;
                }
            }
        }

        // UNA SERIE ES DUPLICADA EN COORDIAPP SI TIENE TELÉFONOS DIFERENTES.
        foreach ($coordiappRegistrosPorSerie as $serieCoordiapp => $registrosCoordiappSerie) {
            $coordiappPorSerie[$serieCoordiapp] = $registrosCoordiappSerie[0];
            $telefonosCoordiappSerie = array();

            foreach ($registrosCoordiappSerie as $registroCoordiapp) {
                $telefonosCoordiappSerie[] = $this->normalizarTelefono($registroCoordiapp['Telefono'] ?? '');
            }

            if (count(array_unique($telefonosCoordiappSerie)) > 1) {
                $duplicadosCoordiappPorSerie[$serieCoordiapp] = $registrosCoordiappSerie;
            }
        }

        // ASIGNAR EL REGISTRO PRINCIPAL Y LOS DETALLES DE DUPLICADO A CADA SERIE.
        foreach ($series as &$serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            $registroCoordiapp = $coordiappPorSerie[$serieNormalizada] ?? array();
            $serie['Serie_Coordiapp'] = $registroCoordiapp['Ont'] ?? null;
            $serie['Telefono_Coordiapp'] = $registroCoordiapp['Telefono'] ?? null;
            $serie['Fecha_Coordiapp'] = $registroCoordiapp['Fecha_Coordiapp'] ?? null;
            $serie['Duplicado_Coordiapp'] = isset($duplicadosCoordiappPorSerie[$serieNormalizada]) ? 1 : 0;
            $serie['Detalles_Duplicado_Coordiapp'] = $duplicadosCoordiappPorSerie[$serieNormalizada] ?? array();
            $serie['Tecnico'] = trim(($serie['Nombre_T'] ?? '') . ' ' . ($serie['Apellidos_T'] ?? ''));
        }
        unset($serie);

        // 3. CREAR LISTAS ÚNICAS PARA CONSULTAR TAC Y ECI EN BLOQUE.
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

        // 3. BUSCAR EN TAC POR NÚMERO DE SERIE, TELÉFONO Y FECHA DE LIQUIDACIÓN.
        // SOLO SE TOMAN REGISTROS CON ESTADO COMPLETADA.
        $tacPorSerie = array();
        $tacRegistrosPorSerie = array();
        $duplicadosTacPorSerie = array();
        if (!empty($seriesUnicas)) {
            $seriesParaTac = array_keys($seriesUnicas);
            $marcadoresTac = implode(',', array_fill(0, count($seriesParaTac), '?'));
            $registrosTac = $this->runAnalysisQuery(
                "SELECT Num__Serie, TELEFONO, FECHA_LIQ, Calificador_Edo
                 FROM qm_tac
                 WHERE Num__Serie IN ($marcadoresTac)
                    AND Calificador_Edo = 'COMPLETADA'
                 ORDER BY FECHA_LIQ DESC",
                $seriesParaTac
            );

            // AGRUPAR TODOS LOS REGISTROS TAC ENCONTRADOS PARA CADA SERIE.
            foreach ($registrosTac as $registroTac) {
                $serieTac = $this->normalizarSerie($registroTac['Num__Serie'] ?? '');
                if ($serieTac !== '') {
                    $tacRegistrosPorSerie[$serieTac][] = $registroTac;
                }
            }
        }

        // UNA SERIE ES DUPLICADA EN TAC SI PRESENTA TELÉFONOS DIFERENTES.
        foreach ($tacRegistrosPorSerie as $serieTac => $registrosTacSerie) {
            $tacPorSerie[$serieTac] = $registrosTacSerie[0];
            $telefonosTacSerie = array();

            foreach ($registrosTacSerie as $registroTac) {
                $telefonosTacSerie[] = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');
            }

            if (count(array_unique($telefonosTacSerie)) > 1) {
                $duplicadosTacPorSerie[$serieTac] = $registrosTacSerie;
            }
        }

        // GUARDAR LA BANDERA Y TODOS LOS DETALLES DE LOS DUPLICADOS TAC.
        foreach ($series as &$serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            $serie['Duplicado_Tac'] = isset($duplicadosTacPorSerie[$serieNormalizada]) ? 1 : 0;
            $serie['Detalles_Duplicado_Tac'] = $duplicadosTacPorSerie[$serieNormalizada] ?? array();
        }
        unset($serie);

        // LAS SERIES DUPLICADAS NO DEBEN PARTICIPAR EN LA CONSULTA DE ECI.
        // ESTO EVITA DETERMINAR SI NAVEGAN USANDO TELÉFONOS AMBIGUOS.
        $seriesUnicasParaEci = array();
        $telefonosUnicos = array();
        foreach ($series as $serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            $esDuplicada = !empty($serie['Duplicado_Tac']) || !empty($serie['Duplicado_Coordiapp']);

            if ($serieNormalizada !== '' && !$esDuplicada) {
                $seriesUnicasParaEci[$serieNormalizada] = true;
                foreach (array($serie['Telefono_Coordiapp'] ?? '', $tacPorSerie[$serieNormalizada]['TELEFONO'] ?? '') as $telefono) {
                    $telefonoNormalizado = $this->normalizarTelefono($telefono);
                    if ($telefonoNormalizado !== '') {
                        $telefonosUnicos[$telefonoNormalizado] = true;
                    }
                }
            }
        }
        $seriesUnicas = $seriesUnicasParaEci;

        // 4. BUSCAR EN ECI POR SERIE O TELÉFONO, CONSIDERANDO ÚNICAMENTE SERVICIOS ACTIVOS.
        // SEPARAR POR COLUMNAS EVITA EL OR Y PERMITE USAR EL ÍNDICE CORRESPONDIENTE.
        $eciPorSerie = array();

        if (!empty($seriesUnicas)) {
            $seriesParaEci = array_keys($seriesUnicas);
            $marcadoresSerieEci = implode(',', array_fill(0, count($seriesParaEci), '?'));
            $registrosEciPorSerie = $this->runAnalysisQuery(
                "SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, F_CONSULTA, DID_ACTIVO, TECNOLOGIA
                 FROM seg_ns_eci
                 WHERE DID_ACTIVO = 1
                     AND M_SERIE IN ($marcadoresSerieEci)
                 ORDER BY F_CONSULTA DESC, FECHA_ULTIMO_EVENTO DESC",
                $seriesParaEci
            );

            foreach ($registrosEciPorSerie as $registroEci) {
                $serieEci = $this->normalizarSerie($registroEci['M_SERIE'] ?? '');
                if ($serieEci !== '' && !isset($eciPorSerie[$serieEci])) {
                    $eciPorSerie[$serieEci] = $registroEci;
                }
            }
        }

        if (!empty($telefonosUnicos)) {
            $telefonosParaEci = array_keys($telefonosUnicos);
            $marcadoresTelefonoEci = implode(',', array_fill(0, count($telefonosParaEci), '?'));
            $registrosEciPorTelefono = $this->runAnalysisQuery(
                "SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, F_CONSULTA, DID_ACTIVO, TECNOLOGIA
                 FROM seg_ns_eci
                 WHERE DID_ACTIVO = 1
                     AND TELEFONO IN ($marcadoresTelefonoEci)
                 ORDER BY F_CONSULTA DESC, FECHA_ULTIMO_EVENTO DESC",
                $telefonosParaEci
            );

            foreach ($registrosEciPorTelefono as $registroEci) {
                $serieEci = $this->normalizarSerie($registroEci['M_SERIE'] ?? '');
                if ($serieEci !== '' && !isset($eciPorSerie[$serieEci])) {
                    $eciPorSerie[$serieEci] = $registroEci;
                }
            }
        }

        // 5. RESOLVER EL ESTADO DE NAVEGACIÓN DE CADA SERIE.
        foreach ($series as &$serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            $registroTac = $tacPorSerie[$serieNormalizada] ?? array();
            $registroEci = $eciPorSerie[$serieNormalizada] ?? array();

            $telefonoCoordiapp = $this->normalizarTelefono($serie['Telefono_Coordiapp'] ?? '');
            $telefonoTac = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');
            $telefonoEci = $this->normalizarTelefono($registroEci['TELEFONO'] ?? '');
            $telefonosRegistrados = array_values(array_filter(array($telefonoCoordiapp, $telefonoTac, $telefonoEci)));

            // SI EXISTEN TELÉFONOS EN VARIAS PLATAFORMAS, TODOS DEBEN COINCIDIR.
            $telefonosCoinciden = count(array_unique($telefonosRegistrados)) <= 1;

            $fechaCoordiapp = $serie['Fecha_Coordiapp'] ?? null;
            $fechaTac = $registroTac['FECHA_LIQ'] ?? null;
            $fechaBase = null;

            foreach (array($fechaCoordiapp, $fechaTac) as $fechaPlataforma) {
                if (!empty($fechaPlataforma) && ($fechaBase === null || strtotime($fechaPlataforma) > strtotime($fechaBase))) {
                    $fechaBase = $fechaPlataforma;
                }
            }

            // LA CONSULTA ECI DEBE SER AL MENOS TRES DÍAS POSTERIOR A TAC/COORDIAPP.
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

            // NAVEGA SOLO SI NO HAY DUPLICADOS, ECI ESTÁ ACTIVO, LOS TELÉFONOS COINCIDEN Y LA FECHA ES VÁLIDA.
            $eciActivo = !empty($registroEci) && (int)($registroEci['DID_ACTIVO'] ?? 0) === 1;
            $esDuplicada = !empty($serie['Duplicado_Tac']) || !empty($serie['Duplicado_Coordiapp']);
            $serie['Navegando'] = !$esDuplicada && $eciActivo && $telefonosCoinciden && $fechaEciValida ? 1 : 0;
        }
        unset($serie);

        return $series;
    }
}