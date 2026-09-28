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
        $conexionAnalisis = $this->get_conexion_analytics();

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

    protected function normalizarFolioPisa($folio)
    {
        return trim((string)$folio);
    }

    protected function seriesDifierenPorUnCaracter($serieA, $serieB)
    {
        $serieA = $this->normalizarSerie($serieA);
        $serieB = $this->normalizarSerie($serieB);

        if ($serieA === '' || strlen($serieA) !== strlen($serieB)) {
            return false;
        }

        $diferencias = 0;
        for ($indice = 0; $indice < strlen($serieA); $indice++) {
            if ($serieA[$indice] !== $serieB[$indice]) {
                $diferencias++;
                if ($diferencias > 1) {
                    return false;
                }
            }
        }

        return $diferencias === 1;
    }

    protected function detectarSerieMalCapturada($serieAlmacen, $registrosCoordiapp, $registrosTacPorFolioTelefono, $registrosEciPorTelefono)
    {
        $mejorCoincidencia = null;
        $mejorFechaEvento = 0;

        foreach ($registrosCoordiapp as $registroCoordiapp) {
            $folioPisa = $this->normalizarFolioPisa($registroCoordiapp['Folio_Pisa'] ?? '');
            $telefono = $this->normalizarTelefono($registroCoordiapp['Telefono'] ?? '');
            if ($folioPisa === '' || $telefono === '') {
                continue;
            }

            $claveFolioTelefono = $folioPisa . '|' . $telefono;
            foreach ($registrosTacPorFolioTelefono[$claveFolioTelefono] ?? array() as $registroTac) {
                $serieTac = $this->normalizarSerie($registroTac['Num__Serie'] ?? '');
                foreach ($registrosEciPorTelefono[$telefono] ?? array() as $registroEci) {
                    $serieEci = $this->normalizarSerie($registroEci['M_SERIE'] ?? '');
                    if ((int)($registroEci['DID_ACTIVO'] ?? 0) !== 1
                        || $serieTac === ''
                        || $serieTac !== $serieEci
                        || !$this->seriesDifierenPorUnCaracter($serieAlmacen, $serieEci)) {
                        continue;
                    }

                    $fechaBase = null;
                    foreach (array(
                        $registroCoordiapp['Fecha_Coordiapp'] ?? null,
                        $registroTac['FECHA_LIQ'] ?? null
                    ) as $fechaFuente) {
                        $marcaTiempo = !empty($fechaFuente) ? strtotime($fechaFuente) : false;
                        if ($marcaTiempo !== false && ($fechaBase === null || $marcaTiempo > $fechaBase)) {
                            $fechaBase = $marcaTiempo;
                        }
                    }

                    $fechaEvento = strtotime($registroEci['FECHA_ULTIMO_EVENTO'] ?? '');
                    if ($fechaEvento === false || ($fechaBase !== null && $fechaEvento < strtotime(date('Y-m-d H:i:s', $fechaBase) . ' +3 days'))) {
                        continue;
                    }

                    if ($fechaEvento > $mejorFechaEvento) {
                        $mejorFechaEvento = $fechaEvento;
                        $mejorCoincidencia = array(
                            'coordiapp' => $registroCoordiapp,
                            'tac' => $registroTac,
                            'eci' => $registroEci,
                            'folio_pisa' => $folioPisa
                        );
                    }
                }
            }
        }

        return $mejorCoincidencia;
    }

    protected function clasificarTipoTarea($tipoTarea)
    {
        $tipo = strtoupper(trim((string)$tipoTarea));

        if ($tipo === '') {
            return 'Otros';
        }

        if (preg_match('/^A4/', $tipo)) {
            return 'A4';
        }

        if (preg_match('/^(FC|TI|TS|TV)/', $tipo)) {
            return 'Migraciones';
        }

        if (preg_match('/^(CD|D)/', $tipo)) {
            return 'Cambios';
        }

        if (preg_match('/^(Q|R|C|EB|EI|EU|GD|MG|F597|F841)/', $tipo)) {
            return 'Quejas';
        }

        if (preg_match('/^A(?!4)/', $tipo)) {
            return 'Altas';
        }

        return 'Otros';
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
                    DATE(m.Fecha_Salida_S) BETWEEN :fechaInicio AND :fechaFin
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
        return $this->ObtenerDetalleFolios(array($folio), $contratistaId);
    }

    // OBTENER LAS SERIES Y SU ESTADO PARA VARIOS FOLIOS EN UNA SOLA EJECUCION
    public function ObtenerDetalleFolios($folios, $contratistaId)
    {
        $folios = array_values(array_filter(array_map('intval', (array)$folios)));
        if (empty($folios)) {
            return array();
        }

        $marcadoresFolios = array();
        $parametrosFolios = array();
        foreach ($folios as $indice => $folioId) {
            $marcador = ':folio_' . $indice;
            $marcadoresFolios[] = $marcador;
            $parametrosFolios[$marcador] = $folioId;
        }
        $parametrosFolios[':contratistaId'] = $contratistaId;

        // 1. OBTENER LAS SERIES DEL FOLIO DESDE LAS TABLAS DE ALMACÉN.
        // SOLO SE CONSIDERAN MATERIALES DEL TIPO ONT Y MODEM.
        $sql = "SELECT
                    m.Num_Serie_S,
                    m.Folio_Salida_Material_Det_S,
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
                WHERE transferencia.Folio_Salida_ST IN (" . implode(',', $marcadoresFolios) . ")
                    AND transferencia.FK_Contra_ST = COALESCE(NULLIF(:contratistaId, 0), transferencia.FK_Contra_ST)
                    AND p.Tipo_Proyecto IN ('ONT', 'Modem')
                ORDER BY transferencia.Folio_Salida_ST DESC, p.Producto, m.Num_Serie_S";

        $series = $this->runQuery($sql, $parametrosFolios);

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
                "SELECT Folio_Pisa, Ont, Telefono, Fecha_Coordiapp, Tipo_Tarea
                 FROM tecnico_instalaciones_coordiapp
                 WHERE Ont IN (" . implode(',', $marcadoresCoordiapp) . ")
                    AND Estatus_Orden = 'COMPLETADA'
                    AND Step_Registro = '5'
                 ORDER BY Fecha_Coordiapp DESC",
                $parametrosCoordiapp
            );

            // AGRUPAR TODOS LOS REGISTROS DE COORDIAPP POR NÚMERO DE SERIE.
            foreach ($registrosCoordiapp as $registroCoordiapp) {
                $serieCoordiapp = $this->normalizarSerie($registroCoordiapp['Ont'] ?? '');
                if ($serieCoordiapp !== '') {
                    $registroCoordiapp['Tipo_Tarea_Clasificado'] = $this->clasificarTipoTarea($registroCoordiapp['Tipo_Tarea'] ?? '');
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

        foreach ($series as $serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');

            if ($serieNormalizada !== '') {
                $seriesUnicas[$serieNormalizada] = true;
            }
        }

        // 3. BUSCAR EN TAC POR NÚMERO DE SERIE, TELÉFONO Y FECHA DE LIQUIDACIÓN.
        // SOLO SE TOMAN REGISTROS CON ESTADO COMPLETADA.
        $tacPorSerie = array();
        $tacRegistrosPorSerie = array();
        $registrosTacTodos = array();
        $duplicadosTacPorSerie = array();
        if (!empty($seriesUnicas)) {
            $seriesParaTac = array_keys($seriesUnicas);
            $marcadoresTac = implode(',', array_fill(0, count($seriesParaTac), '?'));
            $registrosTacPorSerie = $this->runAnalysisQuery(
                "SELECT Folio_Pisa, Num__Serie, TELEFONO, FECHA_LIQ, Calificador_Edo, Tipo_de_Tarea
                 FROM qm_tac
                 WHERE Num__Serie IN ($marcadoresTac)
                    AND Calificador_Edo = 'COMPLETADA'
                 ORDER BY FECHA_LIQ DESC",
                $seriesParaTac
            );
            $registrosTacTodos = $registrosTacPorSerie;
        }

        $registrosTacUnicos = array();
        $registrosTacPorFolioTelefono = array();
        foreach ($registrosTacTodos as $registroTac) {
            $claveTac = implode('|', array(
                $this->normalizarFolioPisa($registroTac['Folio_Pisa'] ?? ''),
                $this->normalizarSerie($registroTac['Num__Serie'] ?? ''),
                $this->normalizarTelefono($registroTac['TELEFONO'] ?? ''),
                (string)($registroTac['FECHA_LIQ'] ?? '')
            ));
            $registrosTacUnicos[$claveTac] = $registroTac;
        }
        $clavesTacVistas = $registrosTacUnicos;
        $registrosTacTodos = array_values($registrosTacUnicos);

        // AGRUPAR TODOS LOS REGISTROS TAC ENCONTRADOS POR SERIE.
        foreach ($registrosTacTodos as $registroTac) {
            $folioPisa = $this->normalizarFolioPisa($registroTac['Folio_Pisa'] ?? '');
            $telefonoTac = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');
            if ($folioPisa !== '' && $telefonoTac !== '') {
                $registrosTacPorFolioTelefono[$folioPisa . '|' . $telefonoTac][] = $registroTac;
            }

            $serieTac = $this->normalizarSerie($registroTac['Num__Serie'] ?? '');
            if ($serieTac !== '') {
                $registroTac['Tipo_de_Tarea_Clasificado'] = $this->clasificarTipoTarea($registroTac['Tipo_de_Tarea'] ?? '');
                $tacRegistrosPorSerie[$serieTac][] = $registroTac;
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

        // TODAS LAS SERIES PARTICIPAN EN ECI. EN DUPLICADOS, EL TELÉFONO DE ECI
        // PERMITE IDENTIFICAR CUAL DE LOS REGISTROS ES EL VIGENTE.
        $seriesUnicasParaEci = array();
        foreach ($series as $serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');

            if ($serieNormalizada !== '') {
                $seriesUnicasParaEci[$serieNormalizada] = true;
            }
        }
        $seriesUnicas = $seriesUnicasParaEci;

        // 4. BUSCAR EN ECI POR SERIE O TELÉFONO, CONSIDERANDO ÚNICAMENTE SERVICIOS ACTIVOS.
        // SEPARAR POR COLUMNAS EVITA EL OR Y PERMITE USAR EL ÍNDICE CORRESPONDIENTE.
        $eciPorSerie = array();
        $eciRegistrosPorSerie = array();

        if (!empty($seriesUnicas)) {
            $seriesParaEci = array_keys($seriesUnicas);
            $marcadoresSerieEci = implode(',', array_fill(0, count($seriesParaEci), '?'));
            $registrosEciPorSerie = $this->runAnalysisQuery(
                "SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, DID_ACTIVO, TECNOLOGIA
                 FROM seg_ns_eci
                 WHERE DID_ACTIVO = 1
                     AND M_SERIE IN ($marcadoresSerieEci)
                 ORDER BY FECHA_ULTIMO_EVENTO DESC",
                $seriesParaEci
            );

            foreach ($registrosEciPorSerie as $registroEci) {
                $serieEci = $this->normalizarSerie($registroEci['M_SERIE'] ?? '');
                if ($serieEci !== '') {
                    $eciRegistrosPorSerie[$serieEci][] = $registroEci;
                }
            }
        }

        // ELEGIR SIEMPRE EL REGISTRO ECI MAS RECIENTE POR FECHA_ULTIMO_EVENTO.
        foreach ($eciRegistrosPorSerie as $serieEci => $registrosEciSerie) {
            $registroEciMasReciente = array();

            foreach ($registrosEciSerie as $registroEci) {
                $fechaUltimoEventoEci = strtotime($registroEci['FECHA_ULTIMO_EVENTO'] ?? '');
                $fechaUltimoEventoEci = $fechaUltimoEventoEci === false ? 0 : $fechaUltimoEventoEci;

                if (empty($registroEciMasReciente) || $fechaUltimoEventoEci > strtotime($registroEciMasReciente['FECHA_ULTIMO_EVENTO'] ?? '')) {
                    $registroEciMasReciente = $registroEci;
                }

            }

            $eciPorSerie[$serieEci] = $registroEciMasReciente;
        }

        $seriesCandidatasMalCapturadas = array();
        $foliosPisaCandidatos = array();
        $telefonosCandidatos = array();

        foreach ($series as $serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            if ($serieNormalizada === '' || !empty($eciPorSerie[$serieNormalizada])) {
                continue;
            }

            $fechaLiquidacionMasAntigua = null;
            foreach ($coordiappRegistrosPorSerie[$serieNormalizada] ?? array() as $registroCoordiapp) {
                $fecha = $registroCoordiapp['Fecha_Coordiapp'] ?? null;
                $marcaTiempo = !empty($fecha) ? strtotime($fecha) : false;
                if ($marcaTiempo !== false && ($fechaLiquidacionMasAntigua === null || $marcaTiempo < $fechaLiquidacionMasAntigua)) {
                    $fechaLiquidacionMasAntigua = $marcaTiempo;
                }
            }
            foreach ($tacRegistrosPorSerie[$serieNormalizada] ?? array() as $registroTac) {
                $fecha = $registroTac['FECHA_LIQ'] ?? null;
                $marcaTiempo = !empty($fecha) ? strtotime($fecha) : false;
                if ($marcaTiempo !== false && ($fechaLiquidacionMasAntigua === null || $marcaTiempo < $fechaLiquidacionMasAntigua)) {
                    $fechaLiquidacionMasAntigua = $marcaTiempo;
                }
            }

            if ($fechaLiquidacionMasAntigua === null || $fechaLiquidacionMasAntigua > strtotime('-14 days')) {
                continue;
            }

            $seriesCandidatasMalCapturadas[$serieNormalizada] = true;
            foreach ($coordiappRegistrosPorSerie[$serieNormalizada] ?? array() as $registroCoordiapp) {
                $folioPisa = $this->normalizarFolioPisa($registroCoordiapp['Folio_Pisa'] ?? '');
                $telefono = $this->normalizarTelefono($registroCoordiapp['Telefono'] ?? '');
                if ($folioPisa !== '' && $telefono !== '') {
                    $foliosPisaCandidatos[$folioPisa] = true;
                    $telefonosCandidatos[$telefono] = true;
                }
            }
        }

        $registrosTacPorFolio = array();
        if (!empty($foliosPisaCandidatos)) {
            $foliosParaTac = array_keys($foliosPisaCandidatos);
            $marcadoresFolioTac = implode(',', array_fill(0, count($foliosParaTac), '?'));
            $registrosTacPorFolio = $this->runAnalysisQuery(
                "SELECT Folio_Pisa, Num__Serie, TELEFONO, FECHA_LIQ, Calificador_Edo, Tipo_de_Tarea
                 FROM qm_tac
                 WHERE Folio_Pisa IN ($marcadoresFolioTac)
                    AND Calificador_Edo = 'COMPLETADA'
                 ORDER BY FECHA_LIQ DESC",
                $foliosParaTac
            );
            $registrosTacTodos = array_merge($registrosTacTodos, $registrosTacPorFolio);
        }

        $seriesTacActualizadas = array();
        foreach ($registrosTacPorFolio as $registroTac) {
            $claveTac = implode('|', array(
                $this->normalizarFolioPisa($registroTac['Folio_Pisa'] ?? ''),
                $this->normalizarSerie($registroTac['Num__Serie'] ?? ''),
                $this->normalizarTelefono($registroTac['TELEFONO'] ?? ''),
                (string)($registroTac['FECHA_LIQ'] ?? '')
            ));
            if (isset($clavesTacVistas[$claveTac])) {
                continue;
            }

            $clavesTacVistas[$claveTac] = $registroTac;
            $folioPisa = $this->normalizarFolioPisa($registroTac['Folio_Pisa'] ?? '');
            $telefonoTac = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');
            $serieTac = $this->normalizarSerie($registroTac['Num__Serie'] ?? '');

            if ($folioPisa !== '' && $telefonoTac !== '') {
                $registrosTacPorFolioTelefono[$folioPisa . '|' . $telefonoTac][] = $registroTac;
            }
            if ($serieTac !== '') {
                $registroTac['Tipo_de_Tarea_Clasificado'] = $this->clasificarTipoTarea($registroTac['Tipo_de_Tarea'] ?? '');
                $tacRegistrosPorSerie[$serieTac][] = $registroTac;
                $seriesTacActualizadas[$serieTac] = true;

                if (!isset($tacPorSerie[$serieTac])) {
                    $tacPorSerie[$serieTac] = $registroTac;
                }
            }
        }

        foreach ($seriesTacActualizadas as $serieTac => $valor) {
            $registrosTacSerie = $tacRegistrosPorSerie[$serieTac] ?? array();
            $telefonosTacSerie = array();

            foreach ($registrosTacSerie as $registroTac) {
                $telefonosTacSerie[] = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');
            }

            if (count(array_unique($telefonosTacSerie)) > 1) {
                $duplicadosTacPorSerie[$serieTac] = $registrosTacSerie;
            } else {
                unset($duplicadosTacPorSerie[$serieTac]);
            }
        }

        foreach ($series as &$serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            $serie['Duplicado_Tac'] = isset($duplicadosTacPorSerie[$serieNormalizada]) ? 1 : 0;
            $serie['Detalles_Duplicado_Tac'] = $duplicadosTacPorSerie[$serieNormalizada] ?? array();
        }
        unset($serie);

        foreach ($seriesCandidatasMalCapturadas as $serieCandidata => $valor) {
            foreach ($tacRegistrosPorSerie[$serieCandidata] ?? array() as $registroTac) {
                $telefono = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');
                if ($telefono !== '') {
                    $telefonosCandidatos[$telefono] = true;
                }
            }
        }

        $eciRegistrosPorTelefono = array();
        if (!empty($telefonosCandidatos)) {
            $telefonosParaEci = array_keys($telefonosCandidatos);
            $marcadoresTelefonoEci = implode(',', array_fill(0, count($telefonosParaEci), '?'));
            $registrosEciPorTelefono = $this->runAnalysisQuery(
                "SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, DID_ACTIVO, TECNOLOGIA
                 FROM seg_ns_eci
                 WHERE DID_ACTIVO = 1
                     AND TELEFONO IN ($marcadoresTelefonoEci)
                 ORDER BY FECHA_ULTIMO_EVENTO DESC",
                $telefonosParaEci
            );

            foreach ($registrosEciPorTelefono as $registroEci) {
                $telefono = $this->normalizarTelefono($registroEci['TELEFONO'] ?? '');
                if ($telefono !== '') {
                    $eciRegistrosPorTelefono[$telefono][] = $registroEci;
                }
            }
        }

        // 5. RESOLVER EL ESTADO DE NAVEGACIÓN DE CADA SERIE.
        foreach ($series as &$serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            $registroTac = $tacPorSerie[$serieNormalizada] ?? array();
            $registroEci = $eciPorSerie[$serieNormalizada] ?? array();

            $telefonoEci = $this->normalizarTelefono($registroEci['TELEFONO'] ?? '');

            // CUANDO ECI RESUELVE UN DUPLICADO, MOSTRAR EN LA TABLA EL REGISTRO
            // DE TAC Y COORDIAPP QUE CORRESPONDE AL TELEFONO ELEGIDO.
            $registroTacSeleccionado = $registroTac;
            foreach ($tacRegistrosPorSerie[$serieNormalizada] ?? array() as $registroTacCandidato) {
                if ($telefonoEci !== '' && $this->normalizarTelefono($registroTacCandidato['TELEFONO'] ?? '') === $telefonoEci) {
                    $registroTacSeleccionado = $registroTacCandidato;
                    break;
                }
            }

            $registroCoordiappSeleccionado = $coordiappPorSerie[$serieNormalizada] ?? array();
            foreach ($coordiappRegistrosPorSerie[$serieNormalizada] ?? array() as $registroCoordiappCandidato) {
                if ($telefonoEci !== '' && $this->normalizarTelefono($registroCoordiappCandidato['Telefono'] ?? '') === $telefonoEci) {
                    $registroCoordiappSeleccionado = $registroCoordiappCandidato;
                    break;
                }
            }

            $esCandidatoRojo = isset($seriesCandidatasMalCapturadas[$serieNormalizada]);
            $coincidenciaSerieMalCapturada = $esCandidatoRojo
                ? $this->detectarSerieMalCapturada(
                    $serie['Num_Serie_S'] ?? '',
                    $coordiappRegistrosPorSerie[$serieNormalizada] ?? array(),
                    $registrosTacPorFolioTelefono,
                    $eciRegistrosPorTelefono
                )
                : null;
            $serieMalCapturada = $coincidenciaSerieMalCapturada !== null;

            if ($serieMalCapturada) {
                $registroCoordiappSeleccionado = $coincidenciaSerieMalCapturada['coordiapp'];
                $registroTacSeleccionado = $coincidenciaSerieMalCapturada['tac'];
                $registroEci = $coincidenciaSerieMalCapturada['eci'];
                $telefonoEci = $this->normalizarTelefono($registroEci['TELEFONO'] ?? '');
            }

            $telefonoCoordiappSeleccionado = $this->normalizarTelefono($registroCoordiappSeleccionado['Telefono'] ?? '');
            $telefonoTacSeleccionado = $this->normalizarTelefono($registroTacSeleccionado['TELEFONO'] ?? '');

            $fechaCoordiapp = $registroCoordiappSeleccionado['Fecha_Coordiapp'] ?? null;
            $fechaTac = $registroTacSeleccionado['FECHA_LIQ'] ?? null;
            $fechaBase = null;

            foreach (array($fechaCoordiapp, $fechaTac) as $fechaPlataforma) {
                if (!empty($fechaPlataforma) && ($fechaBase === null || strtotime($fechaPlataforma) > strtotime($fechaBase))) {
                    $fechaBase = $fechaPlataforma;
                }
            }

            // LA CONSULTA ECI DEBE SER AL MENOS TRES DÍAS POSTERIOR A TAC/COORDIAPP.
            $fechaUltimoEventoEci = $registroEci['FECHA_ULTIMO_EVENTO'] ?? null;
            $fechaEciValida = false;
            if (!empty($fechaUltimoEventoEci)) {
                $fechaEciValida = $fechaBase === null || strtotime($fechaUltimoEventoEci) >= strtotime($fechaBase . ' +3 days');
            }

            $serie['Telefono_Coordiapp'] = $registroCoordiappSeleccionado['Telefono'] ?? null;
            $serie['Fecha_Coordiapp'] = $fechaCoordiapp;
            $serie['Telefono_Tac'] = $registroTacSeleccionado['TELEFONO'] ?? null;
            $serie['Fecha_Liquidacion_Tac'] = $fechaTac;
            $serie['Telefono_Eci'] = $registroEci['TELEFONO'] ?? null;
            $serie['Fecha_Ultimo_Evento_Eci'] = $fechaUltimoEventoEci;
            $serie['Estado_Eci'] = $registroEci['DID_ACTIVO'] ?? null;
            $serie['Serie_Eci'] = $registroEci['M_SERIE'] ?? null;
            $serie['Folio_Pisa_Validacion'] = $serieMalCapturada ? $coincidenciaSerieMalCapturada['folio_pisa'] : null;

            $fechasLiquidacion = array($fechaCoordiapp, $fechaTac);
            foreach ($coordiappRegistrosPorSerie[$serieNormalizada] ?? array() as $registroCoordiappCandidato) {
                $fechasLiquidacion[] = $registroCoordiappCandidato['Fecha_Coordiapp'] ?? null;
            }
            foreach ($tacRegistrosPorSerie[$serieNormalizada] ?? array() as $registroTacCandidato) {
                $fechasLiquidacion[] = $registroTacCandidato['FECHA_LIQ'] ?? null;
            }

            $fechaLiquidacionMasAntigua = null;
            foreach ($fechasLiquidacion as $fechaLiquidacion) {
                if (!empty($fechaLiquidacion) && ($fechaLiquidacionMasAntigua === null || strtotime($fechaLiquidacion) < strtotime($fechaLiquidacionMasAntigua))) {
                    $fechaLiquidacionMasAntigua = $fechaLiquidacion;
                }
            }

            $serie['Alerta_Eci_Pendiente'] = 0;
            if ($fechaLiquidacionMasAntigua !== null && empty($registroEci)) {
                $serie['Alerta_Eci_Pendiente'] = strtotime($fechaLiquidacionMasAntigua) <= strtotime('-14 days') ? 1 : 0;
            }

            $motivosNoNavegacion = array();
            $hayCoordiapp = $telefonoCoordiappSeleccionado !== '';
            $hayTac = $telefonoTacSeleccionado !== '';
            $hayEci = !empty($registroEci);
            $fuentesConTelefono = array_values(array_filter(array(
                $telefonoCoordiappSeleccionado,
                $telefonoTacSeleccionado,
                $telefonoEci
            )));
            $telefonosCoinciden = count(array_unique($fuentesConTelefono)) <= 1;

            if ($hayEci && $hayCoordiapp && $telefonoCoordiappSeleccionado !== $telefonoEci) {
                $motivosNoNavegacion[] = 'El telefono de Coordiapp no coincide el de la Navegacion';
            }

            if ($hayEci && $hayTac && $telefonoTacSeleccionado !== $telefonoEci) {
                $motivosNoNavegacion[] = 'El telefono de TAC no coincide con el Navegando';
            }

            if (!empty($serie['Duplicado_Coordiapp']) && !$serieMalCapturada) {
                $motivosNoNavegacion[] = 'Serie duplicada en Coordiapp';
            }

            if (!empty($serie['Duplicado_Tac']) && !$serieMalCapturada) {
                $motivosNoNavegacion[] = 'Serie duplicada en TAC';
            }

            if ($hayEci && ($hayCoordiapp || $hayTac) && $telefonosCoinciden && !$fechaEciValida) {
                $motivosNoNavegacion[] = 'La Navegacion aun no cumple 3 dias desde la liquidacion';
            }

            if (!$hayCoordiapp && !$hayTac && !$hayEci) {
                $motivosNoNavegacion[] = 'Sin registros en Coordiapp, TAC o Navegacion';
            }

            if ((int)$serie['Alerta_Eci_Pendiente'] === 1) {
                $motivosNoNavegacion[] = 'Liquidacion con mas de 14 dias sin Navegacion';
            }

            if (!$hayEci && ($hayCoordiapp || $hayTac) && (int)$serie['Alerta_Eci_Pendiente'] !== 1) {
                $motivosNoNavegacion[] = 'Liquidacion reciente sin Navegacion';
            }

            // NAVEGA SI ECI ESTA ACTIVO, EL TELEFONO ES CONSISTENTE Y LA FECHA ES VALIDA.
            $eciActivo = !empty($registroEci) && (int)($registroEci['DID_ACTIVO'] ?? 0) === 1;
            $navegando = $eciActivo && $telefonosCoinciden && $fechaEciValida;
            $serie['Navegando'] = $navegando ? 1 : 0;
            $serie['Motivos_No_Navegacion'] = $navegando ? array() : $motivosNoNavegacion;
            $serie['Observacion_Navegacion'] = $navegando && $serieMalCapturada
                ? 'Navegando con observacion: posible serie mal capturada en almacen; validada con Folio Pisa ' . $coincidenciaSerieMalCapturada['folio_pisa']
                : null;

            if ($navegando) {
                $serie['Estado_Navegacion'] = $serieMalCapturada ? 'navegando-observacion' : 'navegando';
            } elseif ((int)$serie['Alerta_Eci_Pendiente'] === 1) {
                $serie['Estado_Navegacion'] = 'rojo';
            } elseif ($hayEci && ($hayCoordiapp || $hayTac) && !$telefonosCoinciden) {
                $serie['Estado_Navegacion'] = 'gris';
            } elseif ($hayEci && ($hayCoordiapp || $hayTac) && $telefonosCoinciden && !$fechaEciValida) {
                $serie['Estado_Navegacion'] = 'amarillo';
            } elseif (!$hayEci && ($hayCoordiapp || $hayTac)) {
                $serie['Estado_Navegacion'] = 'verde';
            } else {
                $serie['Estado_Navegacion'] = 'blanco';
            }
        }
        unset($serie);

        return $series;
    }
}