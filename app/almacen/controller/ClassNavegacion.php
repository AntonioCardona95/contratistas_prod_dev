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

    protected function esSerieTacSinNumero($serie)
    {
        $serieNormalizada = $this->normalizarSerie($serie);

        return $serieNormalizada === '' || $serieNormalizada === 'NSNOOBTENIDO';
    }

    protected function seriesCoincidenEnMinimoCaracteres($serieA, $serieB, $minimoCaracteres = 10)
    {
        $serieA = $this->normalizarSerie($serieA);
        $serieB = $this->normalizarSerie($serieB);

        if (strlen($serieA) < $minimoCaracteres || strlen($serieB) < $minimoCaracteres) {
            return false;
        }

        $longitudMayor = max(strlen($serieA), strlen($serieB));
        $caracteresCoincidentes = $longitudMayor - levenshtein($serieA, $serieB);

        return $caracteresCoincidentes >= $minimoCaracteres;
    }

    protected function esCandidataDiscrepanciaCoordiapp($serie, $coordiappRegistrosPorSerie)
    {
        $serieNormalizada = $this->normalizarSerie($serie);

        return $serieNormalizada !== ''
            && empty($coordiappRegistrosPorSerie[$serieNormalizada]);
    }

    protected function detectarDiscrepanciaCoordiapp($registrosCoordiapp, $registrosTacPorTelefono, $registrosEciPorTelefono)
    {
        $mejorCoincidencia = null;
        $mejorFechaEvento = 0;

        // COORDIAPP SE BUSCA POR TELÉFONO.
        // EL FOLIO PISA SOLO SE UTILIZA COMO DESAMBIGUADOR CUANDO
        // EXISTEN VARIOS REGISTROS DE COORDIAPP PARA EL MISMO TELÉFONO.
        $coordiappPorTelefono = array();

        foreach ($registrosCoordiapp as $registroCoordiapp) {
            $telefono = $this->normalizarTelefono($registroCoordiapp['Telefono'] ?? '');

            if ($telefono === '') {
                continue;
            }

            $coordiappPorTelefono[$telefono][] = $registroCoordiapp;
        }

        foreach ($coordiappPorTelefono as $telefono => $registrosCoordiappTelefono) {
            $registrosTacTelefono = $registrosTacPorTelefono[$telefono] ?? array();
            $registrosEciTelefono = $registrosEciPorTelefono[$telefono] ?? array();

            if (empty($registrosTacTelefono) || empty($registrosEciTelefono)) {
                continue;
            }

            $foliosTacTelefono = array();

            foreach ($registrosTacTelefono as $registroTac) {
                $folioTac = $this->normalizarFolioPisa($registroTac['Folio_Pisa'] ?? '');
                if ($folioTac !== '') {
                    $foliosTacTelefono[$folioTac] = true;
                }
            }

            foreach ($registrosCoordiappTelefono as $registroCoordiapp) {
                $folioPisaCoordiapp = $this->normalizarFolioPisa($registroCoordiapp['Folio_Pisa'] ?? '');
                $serieCoordiapp = $this->normalizarSerie($registroCoordiapp['Ont'] ?? '');

                if ($serieCoordiapp === '') {
                    continue;
                }

                // El teléfono encuentra los candidatos y el Folio Pisa confirma
                // que el registro de Coordiapp corresponde a la liquidación TAC.
                $registrosTacCandidatos = array();

                if ($folioPisaCoordiapp === '' || !isset($foliosTacTelefono[$folioPisaCoordiapp])) {
                    continue;
                }

                foreach ($registrosTacTelefono as $registroTac) {
                    $folioTac = $this->normalizarFolioPisa($registroTac['Folio_Pisa'] ?? '');
                    if ($folioTac === $folioPisaCoordiapp) {
                        $registrosTacCandidatos[] = $registroTac;
                    }
                }

                if (empty($registrosTacCandidatos)) {
                    continue;
                }

                foreach ($registrosTacCandidatos as $registroTac) {
                    $serieTac = $this->normalizarSerie($registroTac['Num__Serie'] ?? '');
                    $serieTacSinNumero = $this->esSerieTacSinNumero($registroTac['Num__Serie'] ?? '');
                    $telefonoTac = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');

                    if ($serieTacSinNumero && $telefonoTac !== $telefono) {
                        continue;
                    }

                    foreach ($registrosEciTelefono as $registroEci) {
                        $serieEci = $this->normalizarSerie($registroEci['M_SERIE'] ?? '');

                        if ((int)($registroEci['DID_ACTIVO'] ?? 0) !== 1
                            || $serieEci === ''
                            || (!$serieTacSinNumero && $serieTac !== $serieEci)
                            || $serieCoordiapp === $serieEci
                            || !$this->seriesCoincidenEnMinimoCaracteres($serieCoordiapp, $serieEci)) {
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
                            $registroTacValidado = $registroTac;
                            if ($serieTacSinNumero) {
                                $registroTacValidado['Num__Serie'] = $serieEci;
                                $registroTacValidado['Serie_Recuperada_Eci'] = 1;
                            }
                            $mejorCoincidencia = array(
                                'coordiapp' => $registroCoordiapp,
                                'tac' => $registroTacValidado,
                                'eci' => $registroEci,
                                'folio_pisa' => $this->normalizarFolioPisa($registroTac['Folio_Pisa'] ?? ($registroCoordiapp['Folio_Pisa'] ?? ''))
                            );
                        }
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
                      FROM View_Detalle_Coordiapp_Completadas
                 WHERE Ont IN (" . implode(',', $marcadoresCoordiapp) . ")
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
        $registrosTacPorTelefono = array();
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
            if ($telefonoTac !== '') {
                $registrosTacPorTelefono[$telefonoTac][] = $registroTac;
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

        // 4. BUSCAR TODO EL HISTORIAL ECI PARA IDENTIFICAR EL ESTADO MAS RECIENTE.
        // SEPARAR POR COLUMNAS EVITA EL OR Y PERMITE USAR EL ÍNDICE CORRESPONDIENTE.
        $eciPorSerie = array();
        $eciRegistrosPorSerie = array();
        $filasEciPorSerie = array();

        if (!empty($seriesUnicas)) {
            $seriesParaEci = array_keys($seriesUnicas);
            $marcadoresSerieEci = implode(',', array_fill(0, count($seriesParaEci), '?'));
            $filasEciPorSerie = $this->runAnalysisQuery(
                "SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, DID_ACTIVO, TECNOLOGIA
                 FROM seg_ns_eci
                 WHERE M_SERIE IN ($marcadoresSerieEci)
                 ORDER BY FECHA_ULTIMO_EVENTO DESC",
                $seriesParaEci
            );

            foreach ($filasEciPorSerie as $registroEci) {
                $serieEci = $this->normalizarSerie($registroEci['M_SERIE'] ?? '');
                if ($serieEci !== '') {
                    $eciRegistrosPorSerie[$serieEci][] = $registroEci;
                }
            }
        }

        // ELEGIR EL MAS RECIENTE Y DETECTAR SI EL ANTERIOR ESTABA ACTIVO.
        $eciPerdioNavegacion = array();
        foreach ($eciRegistrosPorSerie as $serieEci => $registrosEciSerie) {
            $eciPorSerie[$serieEci] = $registrosEciSerie[0];
            $registroPenultimoEci = $registrosEciSerie[1] ?? array();
            $eciPerdioNavegacion[$serieEci] = (int)($eciPorSerie[$serieEci]['DID_ACTIVO'] ?? 0) === 0
                && (int)($registroPenultimoEci['DID_ACTIVO'] ?? 0) === 1;
        }

        $eciSeriesActivasPorTelefono = array();
        foreach ($eciPorSerie as $serieEci => $registroEci) {
            $telefonoEci = $this->normalizarTelefono($registroEci['TELEFONO'] ?? '');
            if ($telefonoEci !== '' && (int)($registroEci['DID_ACTIVO'] ?? 0) === 1) {
                $eciSeriesActivasPorTelefono[$telefonoEci][$serieEci] = true;
            }
        }

        // 4.1. DETECTAR CANDIDATAS PARA DISCREPANCIA COORDIAPP.
        // REGLA: SI LA SERIE DE ALMACÉN NO EXISTE EN COORDIAPP, EL TELÉFONO
        // ES EL ANCLA PARA BUSCAR POSIBLES REGISTROS EN COORDIAPP.
        // EL FOLIO PISA NO SE USA PARA BUSCAR COORDIAPP; SE USA DESPUÉS
        // ÚNICAMENTE COMO CONFIRMACIÓN CONTRA TAC.
        $seriesCandidatasObservacion = array();
        $telefonosCandidatos = array();

        foreach ($series as $serie) {
            $serieNormalizada = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            $registroEci = $eciPorSerie[$serieNormalizada] ?? array();
            $telefonoEci = $this->normalizarTelefono($registroEci['TELEFONO'] ?? '');

            if ($telefonoEci !== '') {
                $telefonosCandidatos[$telefonoEci] = true;
            }

            if (!$this->esCandidataDiscrepanciaCoordiapp(
                $serieNormalizada,
                $coordiappRegistrosPorSerie
            )) {
                continue;
            }

            $registroTac = $tacPorSerie[$serieNormalizada] ?? array();

            $telefonoTac = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');

            $seriesCandidatasObservacion[$serieNormalizada] = true;

            if ($telefonoEci !== '') {
                $telefonosCandidatos[$telefonoEci] = true;
            }

            if ($telefonoTac !== '') {
                $telefonosCandidatos[$telefonoTac] = true;
            }

            // También se consideran todos los teléfonos asociados a la serie en TAC.
            // Esto permite que detectarDiscrepanciaCoordiapp() valide posteriormente
            // el teléfono exacto junto con el Folio Pisa.
            foreach ($tacRegistrosPorSerie[$serieNormalizada] ?? array() as $registroTacSerie) {
                $telefonoTacSerie = $this->normalizarTelefono($registroTacSerie['TELEFONO'] ?? '');
                if ($telefonoTacSerie !== '') {
                    $telefonosCandidatos[$telefonoTacSerie] = true;
                }
            }
        }

        if (!empty($telefonosCandidatos)) {
            $telefonosTacCandidatos = array_keys($telefonosCandidatos);
            $marcadoresTelefonoTac = implode(',', array_fill(0, count($telefonosTacCandidatos), '?'));
            $registrosTacPorTelefonoCandidato = $this->runAnalysisQuery(
                "SELECT Folio_Pisa, Num__Serie, TELEFONO, FECHA_LIQ, Calificador_Edo, Tipo_de_Tarea
                 FROM qm_tac
                 WHERE TELEFONO IN ($marcadoresTelefonoTac)
                    AND Calificador_Edo = 'COMPLETADA'
                 ORDER BY FECHA_LIQ DESC",
                $telefonosTacCandidatos
            );

            $clavesTacTelefono = array();
            foreach ($registrosTacPorTelefono as $telefono => $registrosTac) {
                foreach ($registrosTac as $registroTac) {
                    $clavesTacTelefono[$telefono . '|' . implode('|', array(
                        $this->normalizarFolioPisa($registroTac['Folio_Pisa'] ?? ''),
                        $this->normalizarSerie($registroTac['Num__Serie'] ?? ''),
                        (string)($registroTac['FECHA_LIQ'] ?? '')
                    ))] = true;
                }
            }

            foreach ($registrosTacPorTelefonoCandidato as $registroTac) {
                $telefono = $this->normalizarTelefono($registroTac['TELEFONO'] ?? '');
                if ($telefono === '') {
                    continue;
                }

                $claveTac = $telefono . '|' . implode('|', array(
                    $this->normalizarFolioPisa($registroTac['Folio_Pisa'] ?? ''),
                    $this->normalizarSerie($registroTac['Num__Serie'] ?? ''),
                    (string)($registroTac['FECHA_LIQ'] ?? '')
                ));
                if (isset($clavesTacTelefono[$claveTac])) {
                    continue;
                }

                $clavesTacTelefono[$claveTac] = true;
                $registroTac['Tipo_de_Tarea_Clasificado'] = $this->clasificarTipoTarea($registroTac['Tipo_de_Tarea'] ?? '');
                $registrosTacPorTelefono[$telefono][] = $registroTac;
            }
            unset($registrosTacPorTelefonoCandidato);
        }

        // 4.2. INDEXAR ECI POR TELÉFONO.
        // ECI se consulta por teléfono y sólo para las series candidatas devueltas por TAC.
        $eciRegistrosPorTelefono = array();
        $seriesEciPorTelefono = array();
        foreach ($series as $serie) {
            $serieEci = $this->normalizarSerie($serie['Num_Serie_S'] ?? '');
            if ($serieEci !== '') {
                $seriesEciPorTelefono[$serieEci] = true;
            }
        }
        foreach ($telefonosCandidatos as $telefono => $valor) {
            foreach ($registrosTacPorTelefono[$telefono] ?? array() as $registroTac) {
                $serieTac = $this->normalizarSerie($registroTac['Num__Serie'] ?? '');
                if ($serieTac !== '') {
                    $seriesEciPorTelefono[$serieTac] = true;
                }
            }
        }

        if (!empty($seriesEciPorTelefono) && !empty($telefonosCandidatos)) {
            $seriesEciCandidatas = array_keys($seriesEciPorTelefono);
            $marcadoresSerieEci = implode(',', array_fill(0, count($seriesEciCandidatas), '?'));
            $registrosEciPorTelefono = $this->runAnalysisQuery(
                "SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, DID_ACTIVO, TECNOLOGIA
                 FROM seg_ns_eci
                 WHERE M_SERIE IN ($marcadoresSerieEci)
                 ORDER BY FECHA_ULTIMO_EVENTO DESC",
                $seriesEciCandidatas
            );

            $eciMasRecientePorSerie = array();
            foreach ($registrosEciPorTelefono as $registroEci) {
                $serieEci = $this->normalizarSerie($registroEci['M_SERIE'] ?? '');
                if ($serieEci !== '' && !isset($eciMasRecientePorSerie[$serieEci])) {
                    $eciMasRecientePorSerie[$serieEci] = $registroEci;
                }
            }

            foreach ($eciMasRecientePorSerie as $registroEci) {
                if ((int)($registroEci['DID_ACTIVO'] ?? 0) !== 1) {
                    continue;
                }
                $telefono = $this->normalizarTelefono($registroEci['TELEFONO'] ?? '');
                if ($telefono !== '') {
                    $eciRegistrosPorTelefono[$telefono][] = $registroEci;
                }
            }
        }

        // 4.3. BUSCAR COORDIAPP POR TELÉFONO.
        // IMPORTANTE: el teléfono es el ancla de la discrepancia.
        // El Folio Pisa se valida posteriormente dentro de detectarDiscrepanciaCoordiapp().
        $coordiappRegistrosPorTelefono = array();
        if (!empty($telefonosCandidatos)) {
            $telefonosParaCoordiapp = array_keys($telefonosCandidatos);
            $marcadoresTelefonoCoordiapp = array();
            $parametrosTelefonoCoordiapp = array();
            foreach ($telefonosParaCoordiapp as $indice => $telefono) {
                $marcador = ':telefono_coord_' . $indice;
                $marcadoresTelefonoCoordiapp[] = $marcador;
                $parametrosTelefonoCoordiapp[$marcador] = $telefono;
            }

            $registrosCoordiappPorTelefono = $this->runQuery(
                "SELECT Folio_Pisa, Ont, Telefono, Fecha_Coordiapp, Tipo_Tarea
                 FROM View_Detalle_Coordiapp_Completadas
                 WHERE Telefono IN (" . implode(',', $marcadoresTelefonoCoordiapp) . ")
                 ORDER BY Fecha_Coordiapp DESC",
                $parametrosTelefonoCoordiapp
            );

            foreach ($registrosCoordiappPorTelefono as $registroCoordiapp) {
                $telefono = $this->normalizarTelefono($registroCoordiapp['Telefono'] ?? '');
                if ($telefono === '') {
                    continue;
                }

                $registroCoordiapp['Tipo_Tarea_Clasificado'] = $this->clasificarTipoTarea(
                    $registroCoordiapp['Tipo_Tarea'] ?? ''
                );

                $coordiappRegistrosPorTelefono[$telefono][] = $registroCoordiapp;
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

            $esCandidatoObservacion = isset($seriesCandidatasObservacion[$serieNormalizada]);
            $registrosCoordiappParaSerie = $coordiappRegistrosPorSerie[$serieNormalizada] ?? array();
            $registroEciBase = $eciPorSerie[$serieNormalizada] ?? array();
            $registroTacBase = $tacPorSerie[$serieNormalizada] ?? array();
            foreach (array(
                $this->normalizarTelefono($registroEciBase['TELEFONO'] ?? ''),
                $this->normalizarTelefono($registroTacBase['TELEFONO'] ?? '')
            ) as $telefono) {
                if ($telefono === '') {
                    continue;
                }
                foreach ($coordiappRegistrosPorTelefono[$telefono] ?? array() as $registroCoordiapp) {
                    $registrosCoordiappParaSerie[] = $registroCoordiapp;
                }
            }
            $coincidenciaDiscrepanciaCoordiapp = $esCandidatoObservacion
                ? $this->detectarDiscrepanciaCoordiapp(
                    $registrosCoordiappParaSerie,
                    $registrosTacPorTelefono,
                    $eciRegistrosPorTelefono
                )
                : null;
            if ($coincidenciaDiscrepanciaCoordiapp !== null
                && $this->normalizarSerie($coincidenciaDiscrepanciaCoordiapp['eci']['M_SERIE'] ?? '') !== $serieNormalizada) {
                $coincidenciaDiscrepanciaCoordiapp = null;
            }
            $discrepanciaCoordiapp = $coincidenciaDiscrepanciaCoordiapp !== null;

            if ($discrepanciaCoordiapp) {
                $registroCoordiappSeleccionado = $coincidenciaDiscrepanciaCoordiapp['coordiapp'];
                $registroTacSeleccionado = $coincidenciaDiscrepanciaCoordiapp['tac'];
                $registroEci = $coincidenciaDiscrepanciaCoordiapp['eci'];
                $telefonoEci = $this->normalizarTelefono($registroEci['TELEFONO'] ?? '');
                // El registro se encontró por teléfono y Folio Pisa, no por
                // coincidencia exacta de serie; reflejar la serie recuperada.
                $serie['Serie_Coordiapp'] = $registroCoordiappSeleccionado['Ont'] ?? null;
            }

            $serieTacRecuperada = !empty($registroTacSeleccionado['Serie_Recuperada_Eci']);
            if ($telefonoEci !== '' && $this->esSerieTacSinNumero($registroTacSeleccionado['Num__Serie'] ?? '')) {
                $candidatosTacSinSerie = array();
                foreach ($registrosTacPorTelefono[$telefonoEci] ?? array() as $registroTacCandidato) {
                    if ($this->esSerieTacSinNumero($registroTacCandidato['Num__Serie'] ?? '')
                        && $this->normalizarTelefono($registroTacCandidato['TELEFONO'] ?? '') === $telefonoEci) {
                        $candidatosTacSinSerie[] = $registroTacCandidato;
                    }
                }

                $registrosCoordiappTelefonoEci = $coordiappRegistrosPorTelefono[$telefonoEci] ?? array();
                $foliosCoordiappValidacion = array();
                if ($this->normalizarTelefono($registroCoordiappSeleccionado['Telefono'] ?? '') === $telefonoEci) {
                    $folioSeleccionado = $this->normalizarFolioPisa($registroCoordiappSeleccionado['Folio_Pisa'] ?? '');
                    if ($folioSeleccionado !== '') {
                        $foliosCoordiappValidacion[$folioSeleccionado] = true;
                    }
                }
                foreach ($registrosCoordiappTelefonoEci as $registroCoordiappCandidato) {
                    $folioCoordiapp = $this->normalizarFolioPisa($registroCoordiappCandidato['Folio_Pisa'] ?? '');
                    if ($folioCoordiapp !== '') {
                        $foliosCoordiappValidacion[$folioCoordiapp] = true;
                    }
                }

                $coordiappConfirmaSerie = false;
                foreach ($registrosCoordiappTelefonoEci as $registroCoordiappCandidato) {
                    if ($this->normalizarSerie($registroCoordiappCandidato['Ont'] ?? '') === $serieNormalizada) {
                        $coordiappConfirmaSerie = true;
                        break;
                    }
                }
                if (count($eciSeriesActivasPorTelefono[$telefonoEci] ?? array()) > 1
                    && !$coordiappConfirmaSerie
                    && !$discrepanciaCoordiapp) {
                    $candidatosTacSinSerie = array();
                }

                if (!empty($foliosCoordiappValidacion)) {
                    $candidatosTacSinSerie = array_values(array_filter($candidatosTacSinSerie, function ($registroTacCandidato) use ($foliosCoordiappValidacion) {
                        return isset($foliosCoordiappValidacion[$this->normalizarFolioPisa($registroTacCandidato['Folio_Pisa'] ?? '')]);
                    }));
                }

                if (count($candidatosTacSinSerie) === 1) {
                    $registroTacSeleccionado = $candidatosTacSinSerie[0];
                    $registroTacSeleccionado['Num__Serie'] = $serieNormalizada;
                    $registroTacSeleccionado['Serie_Recuperada_Eci'] = 1;
                    $serieTacRecuperada = true;

                    if ($this->normalizarTelefono($registroCoordiappSeleccionado['Telefono'] ?? '') === '') {
                        $folioTacRecuperado = $this->normalizarFolioPisa($registroTacSeleccionado['Folio_Pisa'] ?? '');
                        foreach ($registrosCoordiappTelefonoEci as $registroCoordiappCandidato) {
                            if ($folioTacRecuperado !== ''
                                && $this->normalizarFolioPisa($registroCoordiappCandidato['Folio_Pisa'] ?? '') === $folioTacRecuperado) {
                                $registroCoordiappSeleccionado = $registroCoordiappCandidato;
                                break;
                            }
                        }
                    }
                }
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
            $serie['Folio_Pisa_Validacion'] = $discrepanciaCoordiapp ? $coincidenciaDiscrepanciaCoordiapp['folio_pisa'] : null;

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

            if (!empty($serie['Duplicado_Coordiapp']) && !$discrepanciaCoordiapp) {
                $motivosNoNavegacion[] = 'Serie duplicada en Coordiapp';
            }

            if (!empty($serie['Duplicado_Tac']) && !$discrepanciaCoordiapp) {
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
            $observacionesNavegacion = array();
            if ($navegando && $serieTacRecuperada) {
                $observacionesNavegacion[] = 'Numero de serie recuperado de la navegacion: ' . $serieNormalizada
                    . '; validado con el telefono ECI ' . $telefonoEci;
            }
            if ($navegando && $discrepanciaCoordiapp) {
                $observacionesNavegacion[] = 'Posible serie mal capturada en Coordiapp: validada con Folio Pisa '
                    . $coincidenciaDiscrepanciaCoordiapp['folio_pisa'];
            }
            $serie['Observacion_Navegacion'] = !empty($observacionesNavegacion)
                ? 'Navegando con observacion: ' . implode(' | ', $observacionesNavegacion)
                : null;

            if ($navegando) {
                $serie['Estado_Navegacion'] = ($discrepanciaCoordiapp || $serieTacRecuperada)
                    ? 'navegando-observacion'
                    : 'navegando';
            } elseif (!empty($eciPerdioNavegacion[$serieNormalizada])) {
                $serie['Estado_Navegacion'] = 'negro';
                $serie['Motivos_No_Navegacion'][] = 'Perdida de navegacion: el ultimo registro ECI tiene DID_ACTIVO=0 y el penultimo DID_ACTIVO=1';
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
