<?php

/**
 * Clase encargada de la gestión de Producción y Consultas
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

    public function CountProduccionTotal()
    {
        $sql = "SELECT COUNT(1) AS TOTAL
                FROM seg_ns_prod_eval";

        return $this->runQuery($sql, array(), 'analisis');
    }

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

    public function esSerieInvalida(?string $serie): bool
    {
        if (empty($serie)) return true;
        
        $serieLimpia = strtoupper(trim($serie));
        $invalidas = ['NS_NOOBTENIDO', 'NS_NO OBTENIDO', 'NS NO OBTENIDO', 'NS NOOBTENIDO', 'N/A', 'NULL', 'UNDEFINED', '0'];
        
        return in_array($serieLimpia, $invalidas, true);
    }

    public function GetSeriesPorTelefonosEci(array $telefonos): array
    {
        if (empty($telefonos)) return [];

        $telefonos = array_unique(array_filter($telefonos));
        if (empty($telefonos)) return [];

        $placeholders = implode(',', array_fill(0, count($telefonos), '?'));
        
        $sql = "SELECT TELEFONO, M_SERIE, F_CONSULTA
                FROM seg_ns_eci
                WHERE DID_ACTIVO = 1
                  AND TELEFONO IN ($placeholders)
                ORDER BY F_CONSULTA DESC";

        $rows = $this->runQuery($sql, array_values($telefonos), 'analisis');

        $mapTelefonoSerie = [];
        foreach ($rows as $row) {
            $tel = trim($row['TELEFONO']);
            $serie = trim($row['M_SERIE']);

            if (!isset($mapTelefonoSerie[$tel]) && !$this->esSerieInvalida($serie)) {
                $mapTelefonoSerie[$tel] = $serie;
            }
        }

        return $mapTelefonoSerie;
    }

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
     * Procesa de forma independiente las discrepancias de serie y las búsquedas en almacén.
     */
    public function InsertBatchProduccionYSenalamiento(array $registros, array $mapExistentes)
    {
         // Depuración: mostrar solo los primeros 5 registros
    echo "<pre>";
    print_r(array_slice($registros, 0, 5));
    echo "</pre>";
        if (empty($registros)) return true;

        $conexion = $this->conexion(); // Conexión 'erpprint'

        try {
            $conexion->beginTransaction();

            $batchSize = 200;
            $senalamientos = [];

            // 1. Preparar la Inserción masiva en seg_produccion_diaria
            $sqlDiariaBase = "INSERT INTO seg_produccion_diaria (
                                Expediente, Folio_Pisa, TELEFONO, Tipo_de_Tarea, NOM_CT,
                                FECHA_LIQ, FECHA_LIQD, Semana, Sem, Anio,
                                idContratista, Contratista, Tecnico, NOM_DIVISION, NOM_AREA,
                                Num__Serie, Serie_Produccion, Serie_Eci, Existe_Serie_Prod_Almacen, Existe_Serie_Eci_Almacen,
                                DISTRITO, TERMINAL, Fecha_Insercion, Tiene_Senalamiento
                            ) VALUES ";

            $chunks = array_chunk($registros, $batchSize);

            foreach ($chunks as $chunk) {
                $values = [];
                $params = [];

                foreach ($chunk as $reg) {
                    $tel       = trim($reg['TELEFONO'] ?? '');
                    $folioPisa = trim($reg['Folio_Pisa'] ?? '');
                    $fechaLiq  = $reg['FECHA_LIQ'] ?? '';

                    $serieProd = trim($reg['Serie_Produccion'] ?? '');
                    $serieEci  = $reg['Serie_Eci'] ?? null;

                    $prodEsValida = !$this->esSerieInvalida($serieProd);
                    $eciEsValida  = $serieEci !== null && !$this->esSerieInvalida($serieEci);

                    // Validar existencias independientes en Almacén
                    $existeProdEnAlmacen = $prodEsValida && isset($mapExistentes[$serieProd]);
                    $existeEciEnAlmacen  = $eciEsValida && isset($mapExistentes[$serieEci]);

                    // --- EVALUACIÓN INDEPENDIENTE DE SEÑALAMIENTOS ---

                    // A) Discrepancia de Series entre Producción y ECI
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

                    // B) Serie de PRODUCCIÓN en Almacén
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
                            'Tipo_Senalamiento' => 'SERIE PRODUCCION NO ENCONTRADA EN ALMACEN',
                            'Detalle'           => "La serie de Producción ($serieProd) NO existe en almacén"
                        ];
                    }

                    // C) Serie de ECI en Almacén (Si ECI tiene registro)
                    if ($eciEsValida && !$existeEciEnAlmacen) {
                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'SERIE ECI NO ENCONTRADA EN ALMACEN',
                            'Detalle'           => "La serie de ECI ($serieEci) NO existe en almacén"
                        ];
                    }

                    // Bandera global para seg_produccion_diaria
                    $tieneFallos = $hayDiscrepanciaSeries || !$prodEsValida || !$existeProdEnAlmacen || ($eciEsValida && !$existeEciEnAlmacen);
                    $tieneSenalamiento = $tieneFallos ? 1 : 0;

                    // Para Num__Serie asignamos Prod (o ECI si Prod venía vacía)
                    $serieFinalGuardar = $prodEsValida ? $serieProd : ($eciEsValida ? $serieEci : $serieProd);

                    $values[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

                    array_push(
                        $params,
                        $reg['Expediente'], $folioPisa, $tel, $reg['Tipo_de_Tarea'], $reg['NOM_CT'],
                        $fechaLiq, $reg['FECHA_LIQD'], $reg['Semana'], $reg['Sem'], $reg['Anio'],
                        $reg['idContratista'], $reg['Contratista'], $reg['Tecnico'], $reg['NOM_DIVISION'], $reg['NOM_AREA'],
                        $serieFinalGuardar, $serieProd, $serieEci, $existeProdEnAlmacen ? 1 : 0, $existeEciEnAlmacen ? 1 : 0,
                        $reg['DISTRITO'], $reg['TERMINAL'], $tieneSenalamiento
                    );
                }

                $stmtDiaria = $conexion->prepare($sqlDiariaBase . implode(',', $values));
                $stmtDiaria->execute($params);
            }

            // 2. Inserción masiva de los señalamientos
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
}