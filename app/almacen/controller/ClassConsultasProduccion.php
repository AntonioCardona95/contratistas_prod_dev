<?php

/**
 * Clase encargada de la gestión de Producción, Consultas y Estado de Navegación
 */

require_once($_SERVER['DOCUMENT_ROOT'] . "/cnx/cnx.php");

class ClassConsultasProduccion extends Conexion
{
    protected function runQuery($sql, $params = array(), $conexionTipo = 'default')
    {
        switch ($conexionTipo) {
            case 'analisis':
            case 'analytics':
                $conexion = $this->get_conexion_analisis();
                break;
            case 'nass':
                $conexion = $this->get_conexion_nass();
                break;
            case 'dos':
                $conexion = $this->get_conexiondos();
                break;
            case 'erpprint':
                $conexion = $this->conexion();
                break;
            default:
                $conexion = $this->get_conexion();
                break;
        }

        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                if (is_int($key)) {
                    $stmt->bindValue($key + 1, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                } else {
                    $param = ':' . ltrim($key, ':');
                    $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (method_exists($stmt, 'closeCursor')) {
                $stmt->closeCursor();
            }
            return $rows;
        } catch (PDOException $e) {
            return array();
        }
    }

    protected function runExecute($sql, $params = array(), $conexionTipo = 'default')
    {
        switch ($conexionTipo) {
            case 'analisis':
            case 'analytics':
                $conexion = $this->get_conexion_analisis();
                break;
            case 'nass':
                $conexion = $this->get_conexion_nass();
                break;
            case 'dos':
                $conexion = $this->get_conexiondos();
                break;
            case 'erpprint':
                $conexion = $this->conexion();
                break;
            default:
                $conexion = $this->get_conexion();
                break;
        }

        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                if (is_int($key)) {
                    $stmt->bindValue($key + 1, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                } else {
                    $param = ':' . ltrim($key, ':');
                    $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }
            $stmt->execute();
            return (int)$conexion->lastInsertId();
        } catch (PDOException $e) {
            return 0;
        }
    }
    /**
     * Cuenta el total de producción en la vista/tabla seg_ns_prod_eval para un rango de fechas
     */
    public function CountProduccionPorFechas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT COUNT(1) AS TOTAL
                FROM seg_ns_prod_eval
                WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

        $params = array(
            ':fechaInicio' => $fechaInicio,
            ':fechaFin'    => $fechaFin
        );

        return $this->runQuery($sql, $params, 'analisis');
    }

    /**
     * Cuenta el total acumulado histórico de producción
     */
    public function CountProduccionTotal()
    {
        $sql = "SELECT COUNT(1) AS TOTAL
                FROM seg_ns_prod_eval";

        return $this->runQuery($sql, array(), 'analisis');
    }

 public function CountSinNavegacionPorFechas($fechaInicio, $fechaFin)
    {
        // 1. Limpieza de fechas (YYYY-MM-DD)
        $f1 = substr($fechaInicio, 0, 10);
        $f2 = substr($fechaFin, 0, 10);

        // 2. Consulta SQL parametrizada con :fechaInicio y :fechaFin
        $sql = "SELECT COUNT(1) AS TOTAL
                FROM seg_produccion_navegacion
                WHERE (
                    Flag_Navega = '0' 
                    OR Flag_Navega = 0 
                    OR Flag_Navega IS NULL 
                    OR Estatus_Navegacion = 'SIN NAVEGACION'
                    OR Estatus_Navegacion LIKE '%NO REGISTRADO%'
                )
                AND DATE(FECHA_LIQ) BETWEEN :fechaInicio AND :fechaFin";

        $params = array(
            ':fechaInicio' => $f1,
            ':fechaFin'    => $f2
        );

        // 3. Importante: Se especifica 'erpprint' como tercer parámetro de conexión
        $res = $this->runQuery($sql, $params, 'erpprint');

        return isset($res[0]['TOTAL']) ? (int)$res[0]['TOTAL'] : 0;
    }




    /*public function CountSinNavegacionPorFechas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT COUNT(1) AS TOTAL
                FROM seg_produccion_navegacion
                WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin
                  AND Flag_Navega = 0";

        $params = [
            ':fechaInicio' => $fechaInicio,
            ':fechaFin'    => $fechaFin
        ];

        $res = $this->runQuery($sql, $params, 'erpprint');
        return isset($res[0]['TOTAL']) ? (int)$res[0]['TOTAL'] : 0;
    }  */

    /**
     * Obtiene los registros de producción para el rango de fechas especificado
     */
    public function GetProduccionPorFechas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT 
                    Expediente, Folio_Pisa, TELEFONO, Tipo_de_Tarea, NOM_CT,
                    FECHA_LIQ, FECHA_LIQD, Semana, Sem, Anio,
                    idContratista, Contratista, Tecnico, NOM_DIVISION, NOM_AREA,
                    Num__Serie, DISTRITO, TERMINAL
                FROM seg_ns_prod_eval
                WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

        $params = [
            ':fechaInicio' => $fechaInicio,
            ':fechaFin'    => $fechaFin
        ];

        return $this->runQuery($sql, $params, 'analisis');
    }

    /**
     * Determina si un número de serie se considera inválido o nulo
     */
    public function esSerieInvalida(?string $serie): bool
    {
        if (empty($serie)) return true;
        
        $serieLimpia = strtoupper(trim($serie));
        $invalidas = ['NS_NOOBTENIDO', 'NS_NO OBTENIDO', 'NS NO OBTENIDO', 'NS NOOBTENIDO', 'N/A', 'NULL', 'UNDEFINED', '0', 'DESCONOCIDO'];
        
        return in_array($serieLimpia, $invalidas, true);
    }

    /**
     * Obtiene el detalle ordenado de ESCI por cada teléfono proporcionado
     */
    public function GetDetalleEciPorTelefonos(array $telefonos): array
    {
        if (empty($telefonos)) return [];

        $telefonos = array_unique(array_filter($telefonos));
        if (empty($telefonos)) return [];

        $placeholders = implode(',', array_fill(0, count($telefonos), '?'));
        
        $sql = "SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, F_CONSULTA, DID_ACTIVO, TECNOLOGIA
                FROM seg_ns_eci
                WHERE TELEFONO IN ($placeholders)
                ORDER BY TELEFONO, F_CONSULTA DESC, FECHA_ULTIMO_EVENTO DESC";

        $rows = $this->runQuery($sql, array_values($telefonos), 'analisis');

        $mapaEci = [];
        foreach ($rows as $row) {
            $tel = trim($row['TELEFONO']);
            if (!isset($mapaEci[$tel])) {
                $mapaEci[$tel] = [
                    'TELEFONO'            => $tel,
                    'M_SERIE'             => strtoupper(trim($row['M_SERIE'] ?? '')),
                    'FECHA_ULTIMO_EVENTO' => $row['FECHA_ULTIMO_EVENTO'] ?? null,
                    'F_CONSULTA'          => $row['F_CONSULTA'] ?? null,
                    'DID_ACTIVO'          => isset($row['DID_ACTIVO']) ? (int)$row['DID_ACTIVO'] : null,
                    'TECNOLOGIA'          => $row['TECNOLOGIA'] ?? null
                ];
            }
        }

        return $mapaEci;
    }

    

    /**
     * Valida qué números de serie existen actualmente en la tabla de materiales (Almacén)
     */
    public function GetNumSeriesEnAlmacen(array $numSeries)
    {
        $numSeries = array_unique(array_filter($numSeries));
        if (empty($numSeries)) return [];

        $placeholders = implode(',', array_fill(0, count($numSeries), '?'));
        $sql = "SELECT Num_Serie FROM materiales_det WHERE Num_Serie IN ($placeholders)";

        $rows = $this->runQuery($sql, array_values($numSeries), 'erpprint');
        return array_column($rows, 'Num_Serie');
    }

    /**
     * Procesa e inserta/actualiza producción, señalamientos y estados de navegación.
     * Mantiene la producción sin duplicar registros y actualiza dinámicamente el estatus de navegación.
     */
    public function InsertBatchProduccionYSenalamiento(array $registros, array $mapExistentes, array $mapEciDetalle)
    {
        if (empty($registros)) return true;

        $conexion = $this->conexion(); // Conexión 'erpprint'

        try {
            $conexion->beginTransaction();

            $batchSize = 200;
            $senalamientos = [];
            $navegaciones  = [];

            // -------------------------------------------------------------
            // 1. INSERCIÓN / ACTUALIZACIÓN EN PRODUCCIÓN DIARIA
            // -------------------------------------------------------------
            $sqlDiariaBase = "INSERT INTO seg_produccion_diaria (
                                Expediente, Folio_Pisa, TELEFONO, Tipo_de_Tarea, NOM_CT,
                                FECHA_LIQ, FECHA_LIQD, Semana, Sem, Anio,
                                idContratista, Contratista, Tecnico, NOM_DIVISION, NOM_AREA,
                                Num__Serie, Serie_Eci, Existe_Serie_Prod_Almacen, Existe_Serie_Eci_Almacen,
                                DISTRITO, TERMINAL, Fecha_Insercion, Tiene_Senalamiento
                            ) VALUES ";

            // Si la producción ya existía, actualizamos datos evaluados sin duplicar filas
            $sqlDiariaDuplicate = " ON DUPLICATE KEY UPDATE
                                    Serie_Eci = VALUES(Serie_Eci),
                                    Existe_Serie_Prod_Almacen = VALUES(Existe_Serie_Prod_Almacen),
                                    Existe_Serie_Eci_Almacen = VALUES(Existe_Serie_Eci_Almacen),
                                    Tiene_Senalamiento = VALUES(Tiene_Senalamiento)";

            $chunks = array_chunk($registros, $batchSize);

            foreach ($chunks as $chunk) {
                $values = [];
                $params = [];

                foreach ($chunk as $reg) {
                    $tel       = trim($reg['TELEFONO'] ?? '');
                    $folioPisa = trim($reg['Folio_Pisa'] ?? '');
                    $fechaLiq  = $reg['FECHA_LIQ'] ?? '';

                    $serieProd = trim($reg['Num__Serie'] ?? '');
                    
                    // Datos de ESCI para el teléfono
                    $eciInfo   = $mapEciDetalle[$tel] ?? null;
                    $serieEci  = $eciInfo['M_SERIE'] ?? null;

                    $prodEsValida = !$this->esSerieInvalida($serieProd);
                    $eciEsValida  = $serieEci !== null && !$this->esSerieInvalida($serieEci);

                    // Evaluaciones de almacén
                    $existeProdEnAlmacen = $prodEsValida && isset($mapExistentes[$serieProd]);
                    $existeEciEnAlmacen  = $eciEsValida && isset($mapExistentes[$serieEci]);

                    // Estatus de Navegación Actualizado
                    $didActivo = $eciInfo['DID_ACTIVO'] ?? null;
                    $fEvento   = $eciInfo['FECHA_ULTIMO_EVENTO'] ?? null;
                    $fConsulta = $eciInfo['F_CONSULTA'] ?? null;

                    $estatusNav = 'NO REGISTRADO EN ESCI';
                    $flagNavega = 0;

                    if ($eciInfo !== null) {
                        if ($didActivo === 1) {
                            $estatusNav = 'NAVEGANDO';
                            $flagNavega = 1;
                        } elseif ($didActivo === 0) {
                            $estatusNav = 'SIN NAVEGACION';
                            $flagNavega = 0;
                        } elseif ($didActivo === -1) {
                            $estatusNav = 'SERVICIO INACTIVO';
                            $flagNavega = 0;
                        } elseif ($this->esSerieInvalida($serieEci)) {
                            $estatusNav = 'SIN NAVEGACION';
                            $flagNavega = 0;
                        }
                    }

                    // Acumulamos datos para seg_produccion_navegacion
                    $navegaciones[] = [
                        'Folio_Pisa'          => $folioPisa,
                        'TELEFONO'            => $tel,
                        'FECHA_LIQ'           => $fechaLiq,
                        'DID_ACTIVO'          => $didActivo,
                        'Estatus_Navegacion'  => $estatusNav,
                        'Flag_Navega'         => $flagNavega,
                        'Num__Serie_Eci'      => $serieEci,
                        'FECHA_ULTIMO_EVENTO' => $fEvento,
                        'F_CONSULTA'          => $fConsulta
                    ];

                    // Evaluación de Señalamientos
                    $hayDiscrepanciaSeries = false;
                    if ($prodEsValida && $eciEsValida && strtoupper($serieProd) !== strtoupper($serieEci)) {
                        $hayDiscrepanciaSeries = true;

                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'NUMEROS DE SERIE DIFERENTES PARA EL TELEFONO',
                            'Detalle'           => "Producción traía ($serieProd) y ECI traía ($serieEci)"
                        ];
                    }

                    if (!$prodEsValida) {
                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'SERIE PRODUCCION INVALIDA O VACIA',
                            'Detalle'           => "Producción registró serie sin obtener o vacía: '$serieProd'"
                        ];
                    } elseif (!$existeProdEnAlmacen) {
                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'NO ENCONTRADO EN ALMACEN',
                            'Detalle'           => "Serie " . $serieProd . " (Origen: PRODUCCION) NO fue encontrada en almacén"
                        ];
                    }

                    if ($eciEsValida && !$existeEciEnAlmacen) {
                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'NO ENCONTRADO EN ALMACEN',
                            'Detalle'           => "Serie " . $serieEci . " (Origen: ECI) NO fue encontrada en almacén"
                        ];
                    }

                    if ($flagNavega === 0) {
                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'SIN NAVEGACION EN ESCI',
                            'Detalle'           => "Estatus: $estatusNav" . ($fEvento ? " | Última vez que navegó: $fEvento" : "")
                        ];
                    }

                    $tieneFallos = $hayDiscrepanciaSeries || !$prodEsValida || !$existeProdEnAlmacen || ($eciEsValida && !$existeEciEnAlmacen) || ($flagNavega === 0);
                    $tieneSenalamiento = $tieneFallos ? 1 : 0;

                    $values[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

                    array_push(
                        $params,
                        $reg['Expediente'], $folioPisa, $tel, $reg['Tipo_de_Tarea'], $reg['NOM_CT'],
                        $fechaLiq, $reg['FECHA_LIQD'], $reg['Semana'], $reg['Sem'], $reg['Anio'],
                        $reg['idContratista'], $reg['Contratista'], $reg['Tecnico'], $reg['NOM_DIVISION'], $reg['NOM_AREA'],
                        $serieProd, $serieEci, $existeProdEnAlmacen ? 1 : 0, $existeEciEnAlmacen ? 1 : 0,
                        $reg['DISTRITO'], $reg['TERMINAL'], $tieneSenalamiento
                    );
                }

                $stmtDiaria = $conexion->prepare($sqlDiariaBase . implode(',', $values) . $sqlDiariaDuplicate);
                $stmtDiaria->execute($params);
            }

            // -------------------------------------------------------------
            // 2. ACTUALIZACIÓN DINÁMICA EN SEGUIMIENTO DE NAVEGACIÓN
            // -------------------------------------------------------------
            if (!empty($navegaciones)) {
                $chunksNav = array_chunk($navegaciones, $batchSize);
                $sqlNavBase = "INSERT INTO seg_produccion_navegacion (
                                    Folio_Pisa, TELEFONO, FECHA_LIQ, DID_ACTIVO, Estatus_Navegacion,
                                    Flag_Navega, Num__Serie_Eci, FECHA_ULTIMO_EVENTO, F_CONSULTA, Fecha_Evaluacion
                                ) VALUES ";

                $sqlNavDuplicate = " ON DUPLICATE KEY UPDATE
                                    DID_ACTIVO = VALUES(DID_ACTIVO),
                                    Estatus_Navegacion = VALUES(Estatus_Navegacion),
                                    Flag_Navega = VALUES(Flag_Navega),
                                    Num__Serie_Eci = VALUES(Num__Serie_Eci),
                                    FECHA_ULTIMO_EVENTO = VALUES(FECHA_ULTIMO_EVENTO),
                                    F_CONSULTA = VALUES(F_CONSULTA),
                                    Fecha_Evaluacion = NOW()";

                foreach ($chunksNav as $chunk) {
                    $valuesNav = [];
                    $paramsNav = [];

                    foreach ($chunk as $nav) {
                        $valuesNav[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                        array_push(
                            $paramsNav,
                            $nav['Folio_Pisa'],
                            $nav['TELEFONO'],
                            $nav['FECHA_LIQ'],
                            $nav['DID_ACTIVO'],
                            $nav['Estatus_Navegacion'],
                            $nav['Flag_Navega'],
                            $nav['Num__Serie_Eci'],
                            $nav['FECHA_ULTIMO_EVENTO'],
                            $nav['F_CONSULTA']
                        );
                    }

                    $stmtNav = $conexion->prepare($sqlNavBase . implode(',', $valuesNav) . $sqlNavDuplicate);
                    $stmtNav->execute($paramsNav);
                }
            }

            // -------------------------------------------------------------
            // 3. LIMPIEZA Y REFRESH DE SEÑALAMIENTOS DE NAVEGACIÓN
            // -------------------------------------------------------------
            // Borramos los señalamientos de 'SIN NAVEGACION EN ESCI' de los folios a procesar 
            // para que si ya navegan, el señalamiento desaparezca automáticamente.
            $foliosLote = array_unique(array_column($registros, 'Folio_Pisa'));
            if (!empty($foliosLote)) {
                $phFolios = implode(',', array_fill(0, count($foliosLote), '?'));
                $stmtClean = $conexion->prepare("DELETE FROM seg_produccion_senalamiento 
                                                 WHERE Folio_Pisa IN ($phFolios) 
                                                   AND Tipo_Senalamiento = 'SIN NAVEGACION EN ESCI'");
                $stmtClean->execute(array_values($foliosLote));
            }

            // Inserción de los nuevos señalamientos calculados
            if (!empty($senalamientos)) {
                $chunksSenalamiento = array_chunk($senalamientos, $batchSize);
                $sqlSenalBase = "INSERT INTO seg_produccion_senalamiento (
                                    Folio_Pisa, TELEFONO, FECHA_LIQ, Tipo_Senalamiento, Detalle, Fecha_Registro
                                ) VALUES ";

                foreach ($chunksSenalamiento as $chunk) {
                    $valuesSen = [];
                    $paramsSen = [];

                    foreach ($chunk as $sen) {
                        $valuesSen[] = "(?, ?, ?, ?, ?, NOW())";
                        array_push(
                            $paramsSen,
                            $sen['Folio_Pisa'],
                            $sen['TELEFONO'],
                            $sen['FECHA_LIQ'],
                            $sen['Tipo_Senalamiento'],
                            $sen['Detalle']
                        );
                    }

                    $stmtSenal = $conexion->prepare($sqlSenalBase . implode(',', $valuesSen));
                    $stmtSenal->execute($paramsSen);
                }
            }

            $conexion->commit();
            return true;

        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("Error en InsertBatchProduccionYSenalamiento: " . $e->getMessage());
            return false;
        }
    }


    /**
 * Obtiene registros completos de producción (diaria + navegación + señalamientos)
 * en un rango de fechas
 */
public function VisualizarTablaFechas($fechaInicio, $fechaFin)
{
    $sql = "SELECT 
                d.Folio_Pisa,
                d.TELEFONO,
                d.Tipo_de_Tarea,
                d.Contratista,
                d.Tecnico,
                d.NOM_DIVISION,
                d.NOM_AREA,
                d.Num__Serie,
                d.Serie_Eci,
                d.Existe_Serie_Prod_Almacen,
                d.Existe_Serie_Eci_Almacen,
                d.FECHA_LIQ,
                d.Semana,
                d.Anio,
                d.Tiene_Senalamiento,
                n.Estatus_Navegacion,
                n.Flag_Navega,
                n.FECHA_ULTIMO_EVENTO,
                s.Tipo_Senalamiento,
                s.Detalle AS Detalle_Senalamiento
            FROM seg_produccion_diaria d
            LEFT JOIN seg_produccion_navegacion n 
                ON d.Folio_Pisa = n.Folio_Pisa 
               AND d.TELEFONO = n.TELEFONO 
               AND d.FECHA_LIQ = n.FECHA_LIQ
            LEFT JOIN seg_produccion_senalamiento s 
                ON d.Folio_Pisa = s.Folio_Pisa 
               AND d.TELEFONO = s.TELEFONO 
               AND d.FECHA_LIQ = s.FECHA_LIQ
            WHERE d.FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    $params = array(
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin
    );

    return $this->runQuery($sql, $params, 'analisis');
}


    
}