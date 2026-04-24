<?php

/**
 * Autor: Ing. Antonio Cardona
 * Estructura adaptada para la consulta de producción de contratistas
 */

require_once($_SERVER['DOCUMENT_ROOT'] . "/cnx/cnx.php");

class Produccion extends Conexion
{
    /**
     * Ejecuta una consulta SQL con parámetros y retorna los resultados.
     * 
     * @param string $sql Consulta SQL con placeholders (ej. :Fecha1)
     * @param array $params Asociativo con valores para los placeholders
     * @return array Filas devueltas (array de arrays asociativos). En error devuelve []
     */
    protected function runQuery($sql, $params = array())
    {
        $rows = array();
        $conexion = $this->get_conexion(); // Conexión a la base de datos

        try {
            $stmt = $conexion->prepare($sql); // Prepara la consulta

            foreach ($params as $key => $val) {
                $param = ':' . ltrim($key, ':');
                // Vincula los parámetros
                if (is_int($val)) {
                    $stmt->bindValue($param, $val, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($param, $val, PDO::PARAM_STR);
                }
            }

            $stmt->execute(); // Ejecuta la consulta
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtiene los resultados

            if (method_exists($stmt, 'closeCursor')) {
                $stmt->closeCursor();
            }
        } catch (PDOException $e) {
            // Loguea el error en caso de excepción
            error_log("[Produccion::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
            return array(); // Retorna un array vacío en caso de error
        }

        return is_array($rows) ? $rows : array(); // Retorna las filas si es un array, si no retorna array vacío
    }

    protected function runQueryAnalisis($sql, $params = array())
{
    $rows = array();
    $conexion = $this->get_conexion_analisis(); // 👈 IMPORTANTE

    try {
        $stmt = $conexion->prepare($sql);

        foreach ($params as $key => $val) {
            $param = ':' . ltrim($key, ':');

            if (is_int($val)) {
                $stmt->bindValue($param, $val, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($param, $val, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (method_exists($stmt, 'closeCursor')) {
            $stmt->closeCursor();
        }

    } catch (PDOException $e) {
        error_log("[Produccion::runQueryAnalisis] Error: " . $e->getMessage());
        return array();
    }

    return is_array($rows) ? $rows : array();
}

/*public function getTotalTac($fecha_inicio, $fecha_fin)
{
    $sql = "SELECT COUNT(1) AS total
            FROM seg_ns_prod_eval
            WHERE FECHA_LIQD BETWEEN :inicio AND :fin";

    $result = $this->runQueryAnalisis($sql, [
        'inicio' => $fecha_inicio,
        'fin' => $fecha_fin
    ]);

    return !empty($result) ? (int)$result[0]['total'] : 0;
} */

 /*public function getTotalTac()
{
    $sql = "SELECT COUNT(1) AS total
            FROM seg_ns_prod_eval";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total'] : 0;
} */

    public function getTotalTac($fechaInicio = null, $fechaFin = null)
{
    $where = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $where = "WHERE FECHA_LIQD BETWEEN '$fechaInicio' AND '$fechaFin'";
    }

    $sql = "SELECT COUNT(1) AS total
            FROM seg_ns_prod_eval
            $where";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total'] : 0;
}

/*public function getTotalAlertas($fechaInicio = null, $fechaFin = null)
{
    $whereFechas = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $whereFechas = "WHERE FECHA_LIQD BETWEEN '$fechaInicio' AND '$fechaFin'";
    }

    $sql = "SELECT COUNT(*) AS total_alertas
            FROM (
                SELECT t.Num__Serie
                FROM qm_tac t
                INNER JOIN (
                    SELECT DISTINCT TELEFONO
                    FROM seg_ns_prod_eval
                    $whereFechas
                ) s ON s.TELEFONO = t.TELEFONO
                WHERE t.Calificador_Edo = 'COMPLETADA'
                GROUP BY t.Num__Serie
                HAVING COUNT(DISTINCT t.TELEFONO) > 1
            ) AS duplicados";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total_alertas'] : 0;
} */


public function obtenerRegistrosParaAlertas($fechaInicio = null, $fechaFin = null)
{
    $where = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $where = "WHERE FECHA_LIQD BETWEEN '$fechaInicio' AND '$fechaFin'";
    }

    $sql = "SELECT 
                Folio_Pisa AS folio,
                TELEFONO AS telefono,
                Tipo_de_Tarea AS tipoTarea,
                NOM_CT AS cope,
                FECHA_LIQ AS fechaLiquidacion,
                idContratista,
                Semana,
                Anio,
                0 AS metraje
            FROM seg_ns_prod_eval_tmp
            $where";

    return $this->runQueryAnalisis($sql);
}

/*public function limpiarAlertas($fechaInicio = null, $fechaFin = null)
{
    $where = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $where = "WHERE Fecha_Liquidacion BETWEEN '$fechaInicio' AND '$fechaFin'";
    }

    $sql = "DELETE FROM Produccion_alertas $where";
    $this->runQuery($sql);
} */

public function InsertarAlertasMasivo($registros)
{
    if (empty($registros)) return;

    $conexion = $this->get_conexion();
    $conexion_analisis = $this->get_conexiondos();

    $conexion->exec("SET NAMES 'utf8mb4'");
    $conexion_analisis->exec("SET NAMES 'utf8mb4'");

    // =========================
    //  PREPARAR IDS
    // =========================
    $folios = array_unique(array_column($registros, 'folio'));
    $telefonos = array_unique(array_column($registros, 'telefono'));

    $inFolios = "'" . implode("','", $folios) . "'";
    $inTelefonos = "'" . implode("','", $telefonos) . "'";

    // =========================
    //  COORDIAPP
    // =========================
    $coordData = [];
    $sql = "SELECT Folio_Pisa,Telefono, Ont, Tecnologia , Foto_Ont ,No_Serie_ONT,Foto_INE, validacion_LEDS_ONT_fallida, Foto_Casa_Cliente, validacion_foto_fallida,Foto_Puerto
            FROM tecnico_instalaciones_coordiapp
            WHERE Folio_Pisa IN ($inFolios)";
    foreach ($conexion->query($sql) as $row) {
        $coordData[$row['Folio_Pisa']] = $row;
    }

    // =========================
    //  TAC
    // =========================
    $tacData = [];
    $sql = "SELECT Folio_Pisa, TELEFONO, Num__Serie, TECNOLOGIA
            FROM qm_tac
            WHERE Folio_Pisa IN ($inFolios)
              AND Calificador_Edo = 'COMPLETADA'";
    foreach ($conexion_analisis->query($sql) as $row) {
        $tacData[$row['Folio_Pisa']] = $row;
    }

    // =========================
    //  ONT DUPLICADA TAC
    // =========================
    $sqlOntDuplicada = "
        SELECT Num__Serie
        FROM qm_tac
        WHERE Calificador_Edo = 'COMPLETADA'
          AND Num__Serie IS NOT NULL
          AND Num__Serie != ''
        GROUP BY Num__Serie
        HAVING COUNT(DISTINCT TELEFONO) > 1
    ";
    $ontDuplicadasTac = [];
    foreach ($conexion_analisis->query($sqlOntDuplicada) as $row) {
        $serie = strtoupper(trim($row['Num__Serie']));
        $ontDuplicadasTac[$serie] = true;
    }

    // =========================
    //  ESCI
    // =========================
    $esciData = [];
    $esciSeriesMap = [];
    $sql = "
        SELECT TELEFONO, M_SERIE, ULTIMO_EVENTO
        FROM seg_ns_eci
        WHERE TELEFONO IN ($inTelefonos)
    ";
    foreach ($conexion_analisis->query($sql) as $row) {
        $tel = trim($row['TELEFONO']);
        $serie = strtoupper(trim($row['M_SERIE']));
        if (!isset($esciData[$tel])) {
            $esciData[$tel] = $row;
            $esciData[$tel]['M_SERIE'] = $serie;
        }
        if (!empty($serie) && $serie !== 'DESCONOCIDO') {
            $esciSeriesMap[$serie][$tel] = true;
        }
    }

    // =========================
    //  INSERTS
    // =========================
    $stmtAlerta = $conexion->prepare("
        INSERT INTO Produccion_alertas
        (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, anio, metraje, Alerta, Detalle_Esci, Bandera_Revision)
        VALUES (?,?,?,?,?,?,?,?,?,?,?, ?, ?)
    ");

    $stmtDetalle = $conexion->prepare("
        INSERT INTO Produccion_alertas_detalle
        (Folio_Pisa, Tipo_Alerta, Activa, Fecha_Creacion, Detalle_Esci, Detalle_Tac)
        VALUES (?,?,?,?,?,?)
    ");

    // =========================
    //  LOOP PRINCIPAL
    // =========================
    foreach ($registros as $r) {

        $folio = $r['folio'];
        $telefono = trim($r['telefono']);

        $coord = $coordData[$folio] ?? null;
        $tac   = $tacData[$folio] ?? null;
        $esci  = $esciData[$telefono] ?? null;

        // SERIES
        $serieTac   = isset($tac['Num__Serie']) ? strtoupper(trim($tac['Num__Serie'])) : null;
        $serieCoord = isset($coord['Ont']) ? strtoupper(trim($coord['Ont'])) : null;
        $serieEsci  = $esci['M_SERIE'] ?? null;

        // MOTOR DE ALERTAS
        $resultadoAlertas = $this->evaluarAlertas([
            'folio' => $folio,
            'telefono' => $telefono,
            'coord' => $coord,
            'tac' => $tac,
            'esci' => $esci,
            'serieTac' => $serieTac,
            'serieCoord' => $serieCoord,
            'serieEsci' => $serieEsci,
            'ontDuplicadasTac' => $ontDuplicadasTac,
            'esciSeriesMap' => $esciSeriesMap,
            'idContratista' => $r['idContratista']
        ]);

        if (!empty($resultadoAlertas['alertas'])) {

            // INSERT en Produccion_alertas (concatenado)
            $stmtAlerta->execute([
                $folio,
                $telefono,
                $r['tipoTarea'] ?? null,
                $r['cope'] ?? null,
                $r['fechaLiquidacion'] ?? null,
                $r['fechaCarga'] ?? date('Y-m-d H:i:s'),
                $r['idContratista'] ?? null,
                $r['semana'] ?? null,
                $r['anio'] ?? null,
                $r['metraje'] ?? null,
                implode(' | ', array_unique($resultadoAlertas['alertas'])),
                $resultadoAlertas['detalleEsci'] ?? null,
                0
            ]);

            // INSERT en Produccion_alertas_detalle (una fila por cada alerta)
            foreach ($resultadoAlertas['alertas'] as $tipoAlerta) {
                $stmtDetalle->execute([
                    $folio,
                    $tipoAlerta,
                    1,
                    date('Y-m-d H:i:s'),
                    $resultadoAlertas['detalleEsci'] ?? null,
                    $resultadoAlertas['detalleTac'] ?? null
                ]);
            }
        }
    }
}


private function evaluarAlertas($data)
{
    $alertas = [];
    $detalleEsci = null;
    $detalleTac = null;
        $this->alertaFolioCapturadoCoordiapp($data, $alertas);

    $this->alertaTipoTareaDiferente($data, $alertas);
    $this->alertaOntDuplicadaTac($data, $alertas, $detalleTac);
    $this->alertaOntDuplicadaEsci($data, $alertas, $detalleEsci);
    $this->alertaSerieDesconocida($data, $alertas);
    $this->alertaEventoEsci($data, $alertas, $detalleEsci);
    $this->alertaSeriesDiferentes($data, $alertas);
    $this->alertaTecnologiaDiferente($data, $alertas);

    //  CORREGIDAS
    $this->alertaFotoOntFallida($data, $alertas); //  SIN detalleTac
    $this->alertaLedsOntFallida($data, $alertas);
    $this->alertaTerminalFallida($data, $alertas);
    $this->alertaTerminalFallidaIA($data, $alertas);
    $this->alertaFotoDomicilioFallida($data, $alertas);
    $this->alertaFotoR20($data, $alertas);
    $this->alertaOntTrasera($data, $alertas);
    $this->alertaOntAlmacen($data, $alertas);
    $this->alertaFotoCasaCliente($data, $alertas);
    

    return [
        'alertas' => $alertas,
        'detalleEsci' => $detalleEsci,
        'detalleTac' => $detalleTac
    ];
}


/*private function evaluarAlertas($data)
{
    $alertas = [];
    $detalleEsci = null;
    $detalleTac = null;

    // =========================
    //  BLOQUEO PRINCIPAL
    // =========================
    if (empty($data['coord'])) {
        $alertas[] = 'NO_CARGADA_EN_COORDIAPP';

        return [
            'alertas' => $alertas,
            'detalleEsci' => null,
            'detalleTac' => null
        ];
    }

    // =========================
    //  SOLO SI EXISTE COORDIAPP
    // =========================
    $this->alertaFolioCapturadoCoordiapp($data, $alertas);
    $this->alertaTipoTareaDiferente($data, $alertas);
    $this->alertaOntDuplicadaTac($data, $alertas, $detalleTac);
    $this->alertaOntDuplicadaEsci($data, $alertas, $detalleEsci);
    $this->alertaSerieDesconocida($data, $alertas);
    $this->alertaEventoEsci($data, $alertas, $detalleEsci);
    $this->alertaSeriesDiferentes($data, $alertas);
    $this->alertaTecnologiaDiferente($data, $alertas);

    $this->alertaFotoOntFallida($data, $alertas);
    $this->alertaLedsOntFallida($data, $alertas);
    $this->alertaTerminalFallida($data, $alertas);
    $this->alertaTerminalFallidaIA($data, $alertas);
    $this->alertaFotoDomicilioFallida($data, $alertas);
    $this->alertaFotoR20($data, $alertas);
    $this->alertaOntTrasera($data, $alertas);
    $this->alertaOntAlmacen($data, $alertas);
    $this->alertaFotoCasaCliente($data, $alertas);

    return [
        'alertas' => $alertas,
        'detalleEsci' => $detalleEsci,
        'detalleTac' => $detalleTac
    ];
} */

private function alertaFolioCapturadoCoordiapp($data, &$alertas)
{
    $folio = $data['folio'] ?? null;

    //  SI NO EXISTE EN COORDIAPP → ALERTA
    if (empty($data['coord'])) {
        $alertas[] = 'FOLIO_NO_CARGADO_EN_COORDIAPP';
        return;
    }

    //  validación extra de seguridad (opcional pero recomendable)
    if (isset($data['coord']['Folio_Pisa']) && $data['coord']['Folio_Pisa'] !== $folio) {
        $alertas[] = 'FOLIO_NO_CARGADO_EN_COORDIAPP';
    }
}

/*private function alertaFolioCapturadoCoordiapp($data, &$alertas)
{
    $folio = $data['folio'] ?? null;

    // Verificar si el folio está presente en los datos de CoordIAPP
    if ($folio && isset($data['coord'][$folio])) {
        $alertas[] = 'FOLIO_CAPTURADO_EN_COORDIAPP';
    }
} */

private function alertaTipoTareaDiferente($data, &$alertas)
{
    // Obtener los tipos de tarea de TAC y CoordIAPP
    $tipoTareaTac = $data['tac']['Tipo_De_Tarea'] ?? null;
    $tipoTareaCoord = $data['coord']['Tipo_Tarea'] ?? null;

    // Comparar los valores de Tipo_De_Tarea y Tipo_Tarea
    if ($tipoTareaTac && $tipoTareaCoord && $tipoTareaTac !== $tipoTareaCoord) {
        $alertas[] = 'TIPO_TAREA_DIFERENTE';
    }
}

private function alertaTerminalFallida($data, &$alertas)
{
    $foto = $data['coord']['Foto_Puerto'] ?? '';

    if (empty($foto) || !preg_match('/\.(jpg|jpeg|png)$/i', $foto)) {
        $alertas[] = 'FOTO TERMINAL FALLIDA';
        return;
    }

    if (strpos($foto, '../') === false) {
        $alertas[] = 'FOTO TERMINAL FALLIDA';
        return;
    }
}

private function alertaOntTrasera($data, &$alertas)
{
    $foto = $data['coord']['No_Serie_ONT'] ?? '';

    if (empty($foto) || !preg_match('/\.(jpg|jpeg|png)$/i', $foto)) {
        $alertas[] = 'FOTO ONT TRASERA FALLIDA';
        return;
    }

    if (strpos($foto, '../') === false) {
        $alertas[] = 'FOTO ONT TRASERA FALLIDA';
        return;
    }
}

private function alertaFotoR20($data, &$alertas)
{
    $foto = $data['coord']['Foto_INE'] ?? '';

    if (empty($foto) || !preg_match('/\.(jpg|jpeg|png)$/i', $foto)) {
        $alertas[] = 'FOTO OS FALLIDA';
        return;
    }

    if (strpos($foto, '../') === false) {
        $alertas[] = 'FOTO OS FALLIDA';
        return;
    }
}



//  FOTO GENERAL (NO TAC)
private function alertaFotoOntFallida($data, &$alertas)
{
    $foto = $data['coord']['Foto_Ont'] ?? '';

    if (empty($foto) || !preg_match('/\.(jpg|jpeg|png)$/i', $foto)) {
        $alertas[] = 'FOTO_ONT_FRONTAL_FALLIDA';
        return;
    }

    if (strpos($foto, '../') === false) {
        $alertas[] = 'FOTO_ONT_FRONTAL_FALLIDA';
        return;
    }
}

//  FOTO CASA CLIENTE
private function alertaFotoCasaCliente($data, &$alertas)
{
    $foto = $data['coord']['Foto_Casa_Cliente'] ?? '';

    if (empty($foto) || !preg_match('/\.(jpg|jpeg|png)$/i', $foto)) {
        $alertas[] = 'FOTO_CASA_CLIENTE_FALLIDA';
        return;
    }

    if (strpos($foto, '../') === false) {
        $alertas[] = 'FOTO_CASA_CLIENTE_FALLIDA';
        return;
    }
}



//  LEDS
private function alertaLedsOntFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_LEDS_ONT_fallida'] ?? 0;

    if ((int)$valor === 1) {
        $alertas[] = 'EVIDENCIA_LEDS_ONT_FALLIDA';
    }
}

//  FOTO DOMICILIO
private function alertaFotoDomicilioFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;

    if ((int)$valor === 1) {
        $alertas[] = 'FOTO_DOMICILIO_FALLIDA';
    }
}

private function alertaTerminalFallidaIA($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;

    if ((int)$valor === 1) {
        $alertas[] = 'FOTO TERMINAL FALLIDA';
    }
}



// =========================
//  ALERTAS
// =========================

private function alertaOntDuplicadaTac($data, &$alertas, &$detalleTac)
{
    if ($data['serieTac'] && isset($data['ontDuplicadasTac'][$data['serieTac']])) {
        $alertas[] = 'ONT_DUPLICADA_EN_TAC';
        $detalleTac = "Serie {$data['serieTac']} duplicada en TAC";
    }
}

private function alertaOntDuplicadaEsci($data, &$alertas, &$detalleEsci)
{
    $serie = $data['serieEsci'];

    if ($serie && isset($data['esciSeriesMap'][$serie])) {
        if (count($data['esciSeriesMap'][$serie]) > 1) {
            $alertas[] = 'ONT_DUPLICADA_EN_ESCI';
            $detalleEsci = "Serie $serie duplicada en ESCI";
        }
    }
}

private function alertaSerieDesconocida($data, &$alertas)
{
    if ($data['serieEsci'] === 'DESCONOCIDO') {
        $alertas[] = 'SERIE_DESCONOCIDA';
    }
}

private function alertaEventoEsci($data, &$alertas, &$detalleEsci)
{
    $evento = $data['esci']['ULTIMO_EVENTO'] ?? null;
    $serie = $data['serieEsci'];

    if ($evento !== null) {

        if ($evento === 0) {
            $alertas[] = 'SIN_NAVEGACION';
            $detalleEsci = "Serie $serie sin navegación";
        }

        if ($evento === -1) {
            $alertas[] = 'SERVICIO_INACTIVO';
            $detalleEsci = "Serie $serie con servicio inactivo";
        }
    }
}

private function alertaSeriesDiferentes($data, &$alertas)
{
    // Imprimir los valores de los números de serie para depuración
    //echo 'Serie TAC: ' . $data['serieTac'] . '<br>';
    //echo 'Serie COORDIAPP: ' . $data['serieCoord'] . '<br>';
    //secho 'Serie ESCI: ' . $data['serieEsci'] . '<br>';
    
    // Obtener los números de serie
    $serieTac = $data['serieTac'];
    $serieCoord = $data['serieCoord'];
    $serieEsci = $data['serieEsci'];

    // Verificar que ninguno de los números de serie sea "DESCONOCIDO"
    if ($serieTac !== 'DESCONOCIDO' && $serieCoord !== 'DESCONOCIDO' && $serieEsci !== 'DESCONOCIDO') {
        // Comparar los números de serie si no son "DESCONOCIDO"
        $series = array_filter([$serieTac, $serieCoord, $serieEsci]);

        // Si hay al menos dos valores diferentes en los números de serie
        if (count($series) > 1 && count(array_unique($series)) !== 1) {
            $alertas[] = 'NUMERO_SERIE_DIFERENTE';
        }
    }

    // **No compararemos los teléfonos** aquí, ya que no quieres alertas sobre teléfonos diferentes.
    // Solo estamos buscando alertas sobre los números de serie.
}

/*private function alertaSeriesDiferentes($data, &$alertas)
{
    $series = array_filter([
        $data['serieTac'],
        $data['serieCoord'],
        $data['serieEsci']
    ]);

    if (count($series) > 1) {
        if (!(count(array_unique($series)) === 1)) {
            $alertas[] = 'NUMERO_SERIE_DIFERENTE';
        }
    }
} */

private function alertaTecnologiaDiferente($data, &$alertas)
{
    // Normalizar los valores de TAC y CoordIAPP
    $tecTacRaw   = $data['tac']['TECNOLOGIA'] ?? null;
    $tecCoordRaw = $data['coord']['Tecnologia'] ?? null;

    //  Normalizamos los valores
    $tecTac   = $this->normalizarTecnologiaAlertas($tecTacRaw);
    $tecCoord = $this->normalizarTecnologiaAlertas($tecCoordRaw);

    // Si la tecnología de TAC está definida y la de CoordIAPP está vacía
    if ($tecTac && !$tecCoord) {
        $alertas[] = 'TECNOLOGIA_DIFERENTE';
    }

    // Comparamos las tecnologías normalizadas
    elseif ($tecTac && $tecCoord && $tecTac !== $tecCoord) {
        $alertas[] = 'TECNOLOGIA_DIFERENTE';
    }
}

private function normalizarTecnologiaAlertas($valor)
{
    if (!$valor) return null;

    $valor = strtoupper(trim($valor));
    $valor = preg_replace('/\s+/', ' ', $valor);

    $valorSinEspacios = str_replace(' ', '', $valor);

    // =========================
    //  REGLAS INTELIGENTES
    // =========================

    //  CUALQUIER COSA QUE CONTENGA "FIBRA"
    if (strpos($valor, 'FIBRA') !== false) {
        return 'FIBRA';
    }

    //  COBRE
    if (strpos($valor, 'COBRE') !== false) {
        return 'COBRE';
    }

    // =========================
    //  MAPEO ESPECÍFICO
    // =========================
    $map = [
        'CAMBACEO' => 'FIBRA',
        'CAMBIOFIBRA' => 'FIBRA',
        'CAMBIO FIBRA' => 'FIBRA'
    ];

    return $map[$valor] ?? $map[$valorSinEspacios] ?? $valor;
}

private function alertaOntAlmacen($data, &$alertas)
{
    //  VIENE DE COORDIAPP
    $ont = isset($data['coord']['Ont']) 
        ? strtoupper(trim($data['coord']['Ont'])) 
        : '';

    if ($ont === '') return;

    //  NORMALIZAR
    $ont = $this->normalizarSerie($ont);

    $conexion = $this->get_conexion();

    $stmt = $conexion->prepare("
        SELECT FK_Contratista_Salida
        FROM material_det_salida
        WHERE REPLACE(UPPER(Num_Serie_S), ' ', '') = ?
        LIMIT 1
    ");

    $stmt->execute([$ont]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // =========================
    //  ALERTAS
    // =========================

    if (!$row) {
        $alertas[] = 'ONT_NO_ENCONTRADA_EN_ALMACEN';
        return;
    }

    if (empty($row['FK_Contratista_Salida'])) {
        $alertas[] = 'ONT_NO_ASIGNADA_AL_CONTRATISTA';
        return;
    }

    if ((int)$row['FK_Contratista_Salida'] !== (int)$data['idContratista']) {
        $alertas[] = 'ONT_ASIGNADA_A_OTRO_CONTRATISTA';
        return;
    }
}


public function obtenerProduccion($Fecha1, $Fecha2, $idContratistas)
    {
        $sql = "SELECT
                    q.Folio_Pisa,
                    q.TELEFONO,
                    q.Tipo_de_Tarea,
                    q.FECHA_LIQ,
                    q.NOM_CT
                FROM
                    qm_tac q
                JOIN
                    tecnicos te ON q.Expediente = te.NExpediente
                JOIN
                    contratistas t ON te.FK_Contratista_Tecnico = t.idContratistas
                WHERE
                    te.FK_Contratista_Tecnico = :idContratistas
                    AND q.FECHA_LIQ BETWEEN :Fecha1 AND :Fecha2
                    AND q.Calificador_Edo = 'COMPLETADA'
                ORDER BY
                    q.FECHA_LIQ";

        // Ejecuta la consulta con los parámetros necesarios
        return $this->runQuery($sql, [
            'Fecha1' => $Fecha1,
            'Fecha2' => $Fecha2,
            'idContratistas' => $idContratistas
        ]);
    }
}
?>