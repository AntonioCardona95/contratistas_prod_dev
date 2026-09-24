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

    /**
     * Consulta las series registradas en ECI para una lista de teléfonos
     */
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
     * Inserción masiva procesando origen de serie y señalamientos acumulados
     */
    public function InsertBatchProduccionYSenalamiento(array $registros, array $mapExistentes)
    {
        if (empty($registros)) return true;

        $conexion = $this->conexion(); // Conexión 'erpprint'

        try {
            $conexion->beginTransaction();

            $batchSize = 200;
            $chunks = array_chunk($registros, $batchSize);

            // 1. Inserción masiva en seg_produccion_diaria agregando Origen_Serie
            $sqlDiariaBase = "INSERT INTO seg_produccion_diaria (
                                Expediente, Folio_Pisa, TELEFONO, Tipo_de_Tarea, NOM_CT,
                                FECHA_LIQ, FECHA_LIQD, Semana, Sem, Anio,
                                idContratista, Contratista, Tecnico, NOM_DIVISION, NOM_AREA,
                                Num__Serie, Origen_Serie, DISTRITO, TERMINAL, Fecha_Insercion, Tiene_Senalamiento
                            ) VALUES ";

            foreach ($chunks as $chunk) {
                $values = [];
                $params = [];

                foreach ($chunk as $reg) {
                    $serie = $reg['Num__Serie'] ?? '';
                    $origen = $reg['Origen_Serie'] ?? 'PRODUCCION';
                    
                    // Tiene señalamiento si ya fue marcado por conflicto ECI o si no existe en almacén
                    $faltaAlmacen = $this->esSerieInvalida($serie) || !isset($mapExistentes[$serie]);
                    $tieneSenalamiento = ($reg['Tiene_Senalamiento_Conflicto'] ?? false) || $faltaAlmacen;

                    $values[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

                    array_push(
                        $params,
                        $reg['Expediente'], $reg['Folio_Pisa'], $reg['TELEFONO'], $reg['Tipo_de_Tarea'], $reg['NOM_CT'],
                        $reg['FECHA_LIQ'], $reg['FECHA_LIQD'], $reg['Semana'], $reg['Sem'], $reg['Anio'],
                        $reg['idContratista'], $reg['Contratista'], $reg['Tecnico'], $reg['NOM_DIVISION'], $reg['NOM_AREA'],
                        $serie, $origen, $reg['DISTRITO'], $reg['TERMINAL'], $tieneSenalamiento ? 1 : 0
                    );
                }

                $stmtDiaria = $conexion->prepare($sqlDiariaBase . implode(',', $values));
                $stmtDiaria->execute($params);
            }

            // 2. Recolectar registros que generan señalamientos
            $senalamientos = [];

            foreach ($registros as $reg) {
                $serie = $reg['Num__Serie'] ?? '';
                
                // Señalamiento 1: Discrepancia entre la serie de Producción y ECI
                if (!empty($reg['Tiene_Senalamiento_Conflicto'])) {
                    $senalamientos[] = [
                        'Folio_Pisa'         => $reg['Folio_Pisa'],
                        'TELEFONO'           => $reg['TELEFONO'],
                        'FECHA_LIQ'          => $reg['FECHA_LIQ'],
                        'Tipo_Senalamiento'  => 'DISCREPANCIA SERIE ECI',
                        'Detalle'            => "Serie Producción (" . $reg['Serie_Original_Prod'] . ") difiere de Serie ECI (" . $serie . ")"
                    ];
                }

                // Señalamiento 2: No encontrado en Almacén o serie vacía
                if ($this->esSerieInvalida($serie)) {
                    $senalamientos[] = [
                        'Folio_Pisa'         => $reg['Folio_Pisa'],
                        'TELEFONO'           => $reg['TELEFONO'],
                        'FECHA_LIQ'          => $reg['FECHA_LIQ'],
                        'Tipo_Senalamiento'  => 'NO ENCONTRADO EN ALMACEN',
                        'Detalle'            => "Num__Serie sin asignar o no obtenido"
                    ];
                } elseif (!isset($mapExistentes[$serie])) {
                    $senalamientos[] = [
                        'Folio_Pisa'         => $reg['Folio_Pisa'],
                        'TELEFONO'           => $reg['TELEFONO'],
                        'FECHA_LIQ'          => $reg['FECHA_LIQ'],
                        'Tipo_Senalamiento'  => 'NO ENCONTRADO EN ALMACEN',
                        'Detalle'            => "Num__Serie=" . $serie . " (Origen: " . ($reg['Origen_Serie'] ?? 'PRODUCCION') . ") no encontrado en almacén"
                    ];
                }
            }

            // 3. Inserción masiva en seg_produccion_senalamiento
            if (!empty($senalamientos)) {
                $chunksSenalamiento = array_chunk($senalamientos, $batchSize);
                $sqlSenalBase = "INSERT INTO seg_produccion_senalamiento (
                                    Folio_Pisa, TELEFONO, FECHA_LIQ, Tipo_Senalamiento, Detalle, Fecha_Registro
                                ) VALUES ";

                foreach ($chunksSenalamiento as $chunk) {
                    $values = [];
                    $params = [];

                    foreach ($chunk as $sen) {
                        $values[] = "(?, ?, ?, ?, ?, NOW())";
                        array_push(
                            $params,
                            $sen['Folio_Pisa'],
                            $sen['TELEFONO'],
                            $sen['FECHA_LIQ'],
                            $sen['Tipo_Senalamiento'],
                            $sen['Detalle']
                        );
                    }

                    $stmtSenal = $conexion->prepare($sqlSenalBase . implode(',', $values));
                    $stmtSenal->execute($params);
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