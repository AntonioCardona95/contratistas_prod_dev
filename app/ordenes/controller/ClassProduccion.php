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

public function getTotalAlertas($fechaInicio = null, $fechaFin = null)
{
    $where = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $where = "WHERE Fecha_Liquidacion BETWEEN '$fechaInicio 00:00:00' AND '$fechaFin 23:59:59'";
    }

    $sql = "SELECT COUNT(*) AS total_alertas
            FROM Produccion_alertas
            $where";

    //  Usa la conexión principal
    $result = $this->runQuery($sql);

    if (!empty($result)) {
        if (isset($result[0]['total_alertas'])) {
            return (int)$result[0]['total_alertas'];
        } elseif (isset($result[0]['COUNT(*)'])) {
            return (int)$result[0]['COUNT(*)'];
        }
    }

    return 0;
}



/*public function getTotalAlertas($fechaInicio = null, $fechaFin = null)
{
    $where = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $where = "WHERE Fecha_Liquidacion BETWEEN '$fechaInicio' AND '$fechaFin'";
    }

    $sql = "SELECT COUNT(*) AS total_alertas
            FROM Produccion_alertas
            $where";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total_alertas'] : 0;
} */


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
            FROM seg_ns_prod_eval 
            /* FROM seg_ns_prod_eval_tmp */
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
    // PREPARAR IDS
    // =========================
    $folios = array_unique(array_column($registros, 'folio'));
    $telefonos = array_unique(array_column($registros, 'telefono'));

    $inFolios = "'" . implode("','", $folios) . "'";
    $inTelefonos = "'" . implode("','", $telefonos) . "'";

    // Debug con var_dump en pantalla (texto plano)
//var_dump("=== Folios ===");
//var_dump($inFolios);

//var_dump("=== Telefonos ===");
//var_dump($inTelefonos);

    // =========================
    //  EXISTENTES (cabecera)
    // =========================
    $existentes = [];
    $sqlExistentes = "
        SELECT Folio_Pisa
        FROM Produccion_alertas
        WHERE Folio_Pisa IN ($inFolios)
    ";
    foreach ($conexion->query($sqlExistentes) as $row) {
        $existentes[$row['Folio_Pisa']] = true;
    }

    // =========================
    //  DETALLE EXISTENTE (histórico)
    // =========================
    $detalleExistente = [];
    $sqlDetalleExistente = "
        SELECT Folio_Pisa, Tipo_Alerta, Activa
        FROM Produccion_alertas_detalle
        WHERE Folio_Pisa IN ($inFolios)
    ";
    foreach ($conexion->query($sqlDetalleExistente) as $row) {
        $detalleExistente[$row['Folio_Pisa']][$row['Tipo_Alerta']] = [
            'activa' => (int)$row['Activa']
        ];
    }

    // =========================
    // COORDIAPP
    // =========================

    // =========================
// COORDIAPP
// =========================
// =========================
// COORDIAPP
// =========================
$coordData = [];

$sql = "SELECT 
    Folio_Pisa,
    Telefono,
    Ont,
    Tecnologia,
    Foto_Ont,
    No_Serie_ONT,
    Foto_INE,
    validacion_LEDS_ONT_fallida,
    Foto_Casa_Cliente,
    validacion_foto_fallida,
    Foto_Puerto,
    Codigo_Liquidacion,
    Tipo_Tarea,
    Fecha_Coordiapp,
    validacion_foto_fachada_intento,
    validacion_foto_terminal_intento,
validacion_leds_ont_intento,
origen_ont,
carga_manual
FROM tecnico_instalaciones_coordiapp
WHERE Folio_Pisa IN ($inFolios)
  AND Telefono IN ($inTelefonos)
  AND Estatus_Orden = 'COMPLETADA'
  AND Step_Registro = '5'
ORDER BY Fecha_Coordiapp DESC";

foreach ($conexion->query($sql) as $row) {

    $folio    = trim((string)$row['Folio_Pisa']);
    $telefono = trim((string)$row['Telefono']);

    // llave compuesta
    $key = $folio . '_' . $telefono;

    // nos quedamos con el más reciente
    if (!isset($coordData[$key])) {
        $coordData[$key] = $row;
    }
}
    
    // anterior //
    /*$coordData = [];
    $sql = "SELECT 
        Folio_Pisa,
        Telefono,
        Ont,
        Tecnologia,
        Foto_Ont,
        No_Serie_ONT,
        Foto_INE,
        validacion_LEDS_ONT_fallida,
        Foto_Casa_Cliente,
        validacion_foto_fallida,
        Foto_Puerto,
        Codigo_Liquidacion,
        Tipo_Tarea
    FROM tecnico_instalaciones_coordiapp
    WHERE Folio_Pisa IN ($inFolios)
      AND Estatus_Orden = 'COMPLETADA'
      AND Step_Registro = '5'";
    foreach ($conexion->query($sql) as $row) {
        $coordData[$row['Folio_Pisa']] = $row;
    } */
    

    // está tomando el último registro que devuelve el query para ese folio
    // =========================
    // TAC
    // =========================
    $tacData = [];
    $sql = "SELECT Folio_Pisa, TELEFONO, Num__Serie, TECNOLOGIA, Tipo_de_Tarea AS Tipo_De_Tarea
            FROM qm_tac
            WHERE Folio_Pisa IN ($inFolios)
              AND Calificador_Edo = 'COMPLETADA'";
    foreach ($conexion_analisis->query($sql) as $row) {
        $tacData[$row['Folio_Pisa']] = $row;
    }

    //  Duplicados TAC
    $ontDuplicadasTac = [];
    foreach ($tacData as $row) {
        $serie = strtoupper(trim($row['Num__Serie']));
        $tel   = trim($row['TELEFONO']);
        if (!empty($serie) && $serie !== 'DESCONOCIDO' && $serie !== 'NS NO OBTENIDO') {
            $ontDuplicadasTac[$serie][$tel] = true;
        }
    }

    // =========================
    // HISTORIAL ORDEN (30 días)
    // =========================
    $tacOrdenData = [];
    $sqlOrden = "
        WITH ultimas AS (
            SELECT TELEFONO, MAX(DATE(FECHA_LIQ)) AS ultima_fecha
            FROM qm_tac
            WHERE Calificador_Edo = 'COMPLETADA'
              AND TIPO = 'ORDEN'
            GROUP BY TELEFONO
        )
        SELECT u.TELEFONO, t.Folio_Pisa, DATE(t.FECHA_LIQ) AS Fecha_Liq, u.ultima_fecha
        FROM ultimas u
        JOIN qm_tac t ON t.TELEFONO = u.TELEFONO
        WHERE t.Calificador_Edo = 'COMPLETADA'
          AND t.TIPO = 'ORDEN'
          AND DATE(t.FECHA_LIQ) BETWEEN u.ultima_fecha - INTERVAL 30 DAY
                                   AND u.ultima_fecha + INTERVAL 30 DAY
          AND DATE(t.FECHA_LIQ) <> u.ultima_fecha
        ORDER BY u.TELEFONO, t.FECHA_LIQ
    ";
    foreach ($conexion_analisis->query($sqlOrden) as $row) {
        $tel = trim($row['TELEFONO']);
        $tacOrdenData[$tel][] = $row['Fecha_Liq'];
    }

  // =========================
// ESCI
// =========================
$esciData = [];
$esciSeriesMap = [];
$series = [];

// Primero obtenemos las series válidas de los teléfonos en la lista
$sqlSeries = "
    SELECT DISTINCT M_SERIE
    FROM seg_ns_eci
    WHERE TELEFONO IN ($inTelefonos)
";
foreach ($conexion_analisis->query($sqlSeries) as $row) {
    $serie = strtoupper(trim($row['M_SERIE']));
    if (!empty($serie) && $serie !== 'DESCONOCIDO') {
        $series[] = "'$serie'";
    }
}
$inSeries = !empty($series) ? implode(',', array_unique($series)) : "''";

// Consulta principal: ordenamos por F_CONSULTA y FECHA_ULTIMO_EVENTO descendente
$sql = "
    SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, F_CONSULTA, DID_ACTIVO
    FROM seg_ns_eci
    WHERE M_SERIE IN ($inSeries)
    ORDER BY TELEFONO, F_CONSULTA DESC, FECHA_ULTIMO_EVENTO DESC
";

// En el foreach, nos quedamos con el primer registro de cada teléfono
// Nota: esto equivale a hacer LIMIT 1 por teléfono
foreach ($conexion_analisis->query($sql) as $row) {
    $tel = preg_replace('/\D/', '', $row['TELEFONO']);
    $serie = strtoupper(trim($row['M_SERIE']));

    if (!isset($esciData[$tel])) {
        $esciData[$tel] = $row;
        $esciData[$tel]['M_SERIE'] = $serie;
    }

    if (!empty($serie) && $serie !== 'DESCONOCIDO' && !empty($tel)) {
        $esciSeriesMap[$serie][$tel] = true;
    }
}


   /* foreach ($conexion_analisis->query($sql) as $row) {
    $tel = preg_replace('/\D/', '', $row['TELEFONO']);
    $serie = strtoupper(trim($row['M_SERIE']));

    // Siempre guardar el último registro (ya viene ordenado por fecha DESC)
    $esciData[$tel] = $row;
    $esciData[$tel]['M_SERIE'] = $serie;

    if (!empty($serie) && $serie !== 'DESCONOCIDO' && !empty($tel)) {
        $esciSeriesMap[$serie][$tel] = true;
    }
} */


    // =========================
    // PREPARES
    // =========================
    $stmtAlerta = $conexion->prepare("
INSERT INTO Produccion_alertas
(Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga,
 FK_Contratista, Semana, anio, metraje, Alerta, Detalle_Esci, Bandera_Revision, Detalle_Tipo_Tarea, Detalle_Tecnologia, Detalle_Series)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");
 
$stmtUpdate = $conexion->prepare("
UPDATE Produccion_alertas SET
    Telefono = ?, Tipo_Tarea = ?, Cope = ?, Fecha_Liquidacion = ?,
    Fecha_Carga = ?, FK_Contratista = ?, Semana = ?, anio = ?, metraje = ?,
    Alerta = ?, Detalle_Esci = ?, Bandera_Revision = 0,
    Detalle_Tipo_Tarea = ?, Detalle_Tecnologia = ?, Detalle_Series = ?
WHERE Folio_Pisa = ?
");

    $stmtDetalle = $conexion->prepare("
        INSERT INTO Produccion_alertas_detalle
        (Folio_Pisa, Tipo_Alerta, Activa, Fecha_Creacion, Detalle_Esci, Detalle_Tac)
        VALUES (?,?,?,?,?,?)
    ");

    $stmtDesactivar = $conexion->prepare("
        UPDATE Produccion_alertas_detalle
        SET Activa = 0
        WHERE Folio_Pisa = ? AND Tipo_Alerta = ?
    ");

    $stmtActivar = $conexion->prepare("
        UPDATE Produccion_alertas_detalle
        SET Activa = 1, Fecha_Creacion = ?, Detalle_Esci = ?, Detalle_Tac = ?
        WHERE Folio_Pisa = ? AND Tipo_Alerta = ?
    ");

    // =========================
    // LOOP PRINCIPAL
    // =========================
    foreach ($registros as $r) {

        $folio = $r['folio'];
        $telefono = trim($r['telefono']);

        //$coord = $coordData[$folio] ?? null;
        // NUEVO
    $keyCoord = $folio . '_' . $telefono;

    $coord = $coordData[$keyCoord] ?? null;

        $tac   = $tacData[$folio] ?? null;
        $esci  = $esciData[$telefono] ?? null;

        $tacOrdenHist = $tacOrdenData[$telefono] ?? null;

        $serieTac   = isset($tac['Num__Serie']) ? strtoupper(trim($tac['Num__Serie'])) : null;
        $serieCoord = isset($coord['Ont']) ? strtoupper(trim($coord['Ont'])) : null;
        $serieEsci  = $esci['M_SERIE'] ?? null;

        $resultadoAlertas = $this->evaluarAlertas([
            'folio' => $folio,
            'telefono' => $telefono,
            'coord' => $coord,
            'tac' => $tac,
            'esci' => $esci,
            'serieTac' => $serieTac,
            'serieCoord' => $serieCoord,
            'serieEsci' => $serieEsci,
            'tacOrdenHist' => $tacOrdenHist,
            'idContratista' => $r['idContratista'] ?? 0,
            'tipoTarea' => $coord['Tipo_Tarea'] ?? ($r['tipoTarea'] ?? ''),
            'ontDuplicadasTac' => $ontDuplicadasTac,
            'esciSeriesMap' => $esciSeriesMap
        ]);

        $alertasActuales = $resultadoAlertas['alertas'] ?? [];
        $alertasTexto = !empty($alertasActuales)
            ? implode(' | ', array_unique($alertasActuales))
            : null;

        // =========================
        // INSERT o UPDATE (cabecera)
        // =========================
        if (!isset($existentes[$folio])) {

            if (!empty($alertasActuales)) {
                $stmtAlerta->execute([
                    $folio,
                    $telefono,
                    $r['tipoTarea'] ?? null,
                    $r['cope'] ?? null,
                    $r['fechaLiquidacion'] ?? null,
                    date('Y-m-d H:i:s'),
                    $r['idContratista'] ?? 0,
                    $r['semana'] ?? 0,
                    $r['anio'] ?? 0,
                    $r['metraje'] ?? 0,
                    $alertasTexto,
                    $resultadoAlertas['detalleEsci'] ?? null,
                    0,
                    $resultadoAlertas['detalleTipoTarea'] ?? null,
                    $resultadoAlertas['detalleTecnologia'] ?? null,
                    $resultadoAlertas['detalleSeries'] ?? null
                ]);
            }

        } else {

            // UPDATE SIEMPRE (reevaluación)
            $stmtUpdate->execute([
                $telefono,
                $r['tipoTarea'] ?? null,
                $r['cope'] ?? null,
                $r['fechaLiquidacion'] ?? null,
                date('Y-m-d H:i:s'),
                $r['idContratista'] ?? 0,
                $r['semana'] ?? 0,
                $r['anio'] ?? 0,
                $r['metraje'] ?? 0,
                $alertasTexto,
                $resultadoAlertas['detalleEsci'] ?? null,
                $resultadoAlertas['detalleTipoTarea'] ?? null, 
                $resultadoAlertas['detalleTecnologia'] ?? null,
                $resultadoAlertas['detalleSeries'] ?? null,
                $folio
            ]);
        }

        // =========================
        //  MANEJO DE DETALLE (SIN DELETE)
        // =========================

        // 1) Desactivar las que ya no aplican
        if (isset($detalleExistente[$folio])) {
            foreach ($detalleExistente[$folio] as $tipoAnterior => $info) {
                if (!in_array($tipoAnterior, $alertasActuales, true) && $info['activa'] === 1) {
                    $stmtDesactivar->execute([$folio, $tipoAnterior]);
                    $detalleExistente[$folio][$tipoAnterior]['activa'] = 0;
                }
            }
        }

        // 2) Insertar nuevas o reactivar existentes
        foreach ($alertasActuales as $tipoAlerta) {

            // No existe → INSERT
            if (!isset($detalleExistente[$folio][$tipoAlerta])) {

                $stmtDetalle->execute([
                    $folio,
                    $tipoAlerta,
                    1,
                    date('Y-m-d H:i:s'),
                    $resultadoAlertas['detalleEsci'] ?? null,
                    $resultadoAlertas['detalleTac'] ?? null
                ]);

                $detalleExistente[$folio][$tipoAlerta] = ['activa' => 1];

            } else {

                // Existe pero inactiva → REACTIVAR
                if ($detalleExistente[$folio][$tipoAlerta]['activa'] === 0) {

                    $stmtActivar->execute([
                        date('Y-m-d H:i:s'),
                        $resultadoAlertas['detalleEsci'] ?? null,
                        $resultadoAlertas['detalleTac'] ?? null,
                        $folio,
                        $tipoAlerta
                    ]);

                    $detalleExistente[$folio][$tipoAlerta]['activa'] = 1;
                }
            }
        }
    }
}




private function evaluarAlertas($data)
{
    $alertas = [];
    $detalleEsci = null;
    $detalleTac = null;
    $detalleTipoTarea = null;
    $detalleTecnologia = null;
    $detalleSeries = null;

    // =========================
    // ALERTA ORDEN 30 DÍAS
    // =========================
    $this->alertaOrdenRevision30Dias($data, $alertas);

    // =========================
    // CASO: NO EXISTE COORDIAPP
    // =========================
    if ($data['coord'] === null) {

        $this->alertaOntDuplicadaTac($data, $alertas, $detalleTac);
        $this->alertaOntDuplicadaEsci($data, $alertas, $detalleEsci);
        $this->alertaSerieDesconocida($data, $alertas);
        $this->alertaEstadoEsci($data, $alertas, $detalleEsci);

        //  SOLO UNA VEZ (con detalle)
        $this->alertaSeriesDiferente($data, $alertas, $detalleSeries);

        $alertas[] = 'NO EN COORDIAPP';

        return [
            'alertas' => $alertas,
            'detalleEsci' => $detalleEsci,
            'detalleTac' => $detalleTac,
            'detalleTipoTarea' => $detalleTipoTarea,
            'detalleTecnologia' => $detalleTecnologia,
            'detalleSeries' => $detalleSeries
        ];
    }

    // =========================
    // CASO: SÍ EXISTE COORDIAPP
    // =========================
    $this->alertaTipoTareaDiferente($data, $alertas, $detalleTipoTarea);

    $this->alertaOntDuplicadaTac($data, $alertas, $detalleTac);
    $this->alertaOntDuplicadaEsci($data, $alertas, $detalleEsci);
    $this->alertaSerieDesconocida($data, $alertas);

    //  SOLO UNA VEZ (con detalle)
    $this->alertaSeriesDiferente($data, $alertas, $detalleSeries);

    $this->alertaTecnologiaDiferente($data, $alertas, $detalleTecnologia);
    $this->alertaEstadoEsci($data, $alertas, $detalleEsci);

    // =========================
    // DETECCIÓN DE QUEJA
    // =========================
    $esQueja = $this->esTipoQueja($data['tipoTarea'] ?? '');

    // =========================
    // VALIDACIONES NORMALES
    // =========================
    if (!$esQueja) {
        $this->alertaFotoOntFallida($data, $alertas);
        $this->alertaOntTrasera($data, $alertas);
        $this->alertaFotoCasaCliente($data, $alertas);
        $this->alertaFotoR20($data, $alertas);
        $this->alertaLedsOntFallida($data, $alertas);
        $this->alertaTerminalFallida($data, $alertas);
        $this->alertaTerminalFallidaIA($data, $alertas);
        $this->alertaFotoDomicilioFallida($data, $alertas);
        $this->alertaOntAlmacen($data, $alertas);
    }

    // =========================
    // VALIDACIÓN ESPECIAL (QUEJAS)
    // =========================
    if ($esQueja) {
        $this->alertaFotosPorTipoQueja($data, $alertas);
    }

    // =========================
    // RETURN FINAL
    // =========================
    return [
        'alertas' => $alertas,
        'detalleEsci' => $detalleEsci,
        'detalleTac' => $detalleTac,
        'detalleTipoTarea' => $detalleTipoTarea,
        'detalleTecnologia' => $detalleTecnologia,
        'detalleSeries' => $detalleSeries
    ];
}

private function alertaTipoTareaDiferente($data, &$alertas, &$detalleTipoTarea)
{
    $tipoTareaTac   = isset($data['tac']['Tipo_De_Tarea']) ? trim($data['tac']['Tipo_De_Tarea']) : '';
    $tipoTareaCoord = isset($data['coord']['Tipo_Tarea'])   ? trim($data['coord']['Tipo_Tarea'])   : '';

    $tipoTacNorm   = $tipoTareaTac   ? $this->normalizarTexto($tipoTareaTac)   : '';
    $tipoCoordNorm = $tipoTareaCoord ? $this->normalizarTexto($tipoTareaCoord) : '';

    // Detectar sufijo en TAC y Coordiapp
    $tacTieneSufijo    = preg_match('/(PE|PI)$/i', $tipoTacNorm);
    $coordTieneSufijo  = preg_match('/(PE|PI)$/i', $tipoCoordNorm);

    // Si TAC tiene sufijo y Coordiapp no → recortamos TAC
    if ($tacTieneSufijo && !$coordTieneSufijo) {
        $tipoTacNorm = substr($tipoTacNorm, 0, -2);
    }

    // Comparación final
    if ($tipoTacNorm && $tipoCoordNorm && $tipoTacNorm !== $tipoCoordNorm) {
        $alertas[] = 'TIPO_TAREA_DIFERENTE';

        // Base del detalle
        $detalleTipoTarea =
            "TIPO DE TAREA COORDIAPP: {$tipoTareaCoord} | TIPO DE TAREA TAC: {$tipoTareaTac}";

        // Si fue carga manual (campo = 1), lo indicamos
        if (!empty($data['coord']['carga_manual']) && (int)$data['coord']['carga_manual'] === 1) {
            $detalleTipoTarea .= " | CARGA MANUAL";
        }
    }
}


/*private function alertaTipoTareaDiferente($data, &$alertas, &$detalleTipoTarea)
{
    $tipoTareaTac   = isset($data['tac']['Tipo_De_Tarea']) ? trim($data['tac']['Tipo_De_Tarea']) : '';
    $tipoTareaCoord = isset($data['coord']['Tipo_Tarea'])   ? trim($data['coord']['Tipo_Tarea'])   : '';

    $tipoTacNorm   = $tipoTareaTac   ? $this->normalizarTexto($tipoTareaTac)   : '';
    $tipoCoordNorm = $tipoTareaCoord ? $this->normalizarTexto($tipoTareaCoord) : '';

    // Detectar sufijo en TAC y Coordiapp
    $tacTieneSufijo    = preg_match('/(PE|PI)$/i', $tipoTacNorm);
    $coordTieneSufijo  = preg_match('/(PE|PI)$/i', $tipoCoordNorm);

    // Si TAC tiene sufijo y Coordiapp no → recortamos TAC
    if ($tacTieneSufijo && !$coordTieneSufijo) {
        $tipoTacNorm = substr($tipoTacNorm, 0, -2);
    }

    // Comparación final
    if ($tipoTacNorm && $tipoCoordNorm && $tipoTacNorm !== $tipoCoordNorm) {
        $alertas[] = 'TIPO_TAREA_DIFERENTE';
        $detalleTipoTarea =
            "TIPO DE TAREA COORDIAPP: {$tipoTareaCoord} | TIPO DE TAREA TAC: {$tipoTareaTac}";
    }
} */


private function esTipoQueja(string $tipoTarea): bool
{
    $tipoTarea = strtoupper(trim($tipoTarea));
    $tiposQueja = ['Q', 'R', 'C', 'EB', 'EI', 'EU', 'GD', 'MG', 'F597', 'F841'];

    foreach ($tiposQueja as $prefijo) {
        if (strpos($tipoTarea, $prefijo) === 0) {
            return true;
        }
    }
    return false;
}

// QUEJAS //

private function alertaFotosPorTipoQueja($data, &$alertas)
{
    $tipoTarea = strtoupper(trim($data['tipoTarea'] ?? ''));
    $codigoLiquidacion = strtoupper(trim($data['coord']['Codigo_Liquidacion'] ?? ''));

    // Si no es queja, no hacemos nada
    if (!$this->esTipoQueja($tipoTarea)) {
        return;
    }

    $validar = [];

    // Definir qué fotos sí se deben validar según el código de liquidación
    if (in_array($codigoLiquidacion, [
        'JUMPER/PARALELO EXTERIOR',
        'CONECTOR MECANICO DAÑADO',
        'CAMBIO DE PUERTO',
        'CAMBIO DE TERMINAL'
    ])) {
        $validar = ['Foto_Casa_Cliente','Foto_Puerto'];
    } elseif ($codigoLiquidacion === 'MODEM DAÑADO') {
        $validar = ['Foto_Casa_Cliente','Foto_Ont'];
    } elseif (in_array($codigoLiquidacion, [
        'CAMBIO PAR PRINCIPAL',
        'CAMBIO PAR SECUNDARIO',
        'ROSETA OPTICA',
        'CAMBIO DE INTERIOR'
    ])) {
        $validar = ['Foto_Casa_Cliente'];
    } elseif (in_array($codigoLiquidacion, [
        'CAMBIO PARALELO Y MODEM',
        'OTRO'
    ])) {
        $validar = ['Foto_Casa_Cliente','Foto_Puerto','Foto_Ont'];
    }

    // Validar solo esas evidencias
    foreach ($validar as $campo) {
        $valor = $data['coord'][$campo] ?? '';

        if (empty($valor) || !preg_match('/\.(jpg|jpeg|png)$/i', $valor)) {
            $alertas[] = "FALTA_EVIDENCIA_" . strtoupper($campo);
        }
    }

    // Validaciones binarias (solo si es queja y hay evidencias a validar)
    if (!empty($validar)) {
	
	$valor = $data['coord']['validacion_foto_fallida'] ?? 0;
        $foto = $data['coord']['validacion_foto_fachada_intento'];

        if ((int)$valor === 1 && str_contains($foto, 'NO RECONOCIO')) {
            $primeros3 = substr($foto, 0, 3);
            $alertas[] = 'FOTO DOMICILIO FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
        } elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 422')) {
            $primeros3 = substr($foto, 0, 3);
            $alertas[] = 'FOTO DOMICILIO FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
        } elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 500')) {
       	    $primeros3 = substr($foto, 0, 3);
            $alertas[] = 'FOTO DOMICILIO FALLIDA ERROR IA (' . $primeros3 . ' NO RESPONDIO)';
        }

	$valor = $data['coord']['validacion_foto_fallida'] ?? 0;
    	$foto = $data['coord']['validacion_foto_terminal_intento'];

    	if ((int)$valor === 1 && str_contains($foto, 'NO RECONOCIO')) {
             $primeros3 = substr($foto, 0, 3);
             $alertas[] = 'FOTO TERMINAL FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    	} elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 422')) {
             $primeros3 = substr($foto, 0, 3);
             $alertas[] = 'FOTO TERMINAL FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    	} elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 500')) {
             $primeros3 = substr($foto, 0, 3);
             $alertas[] = 'FOTO TERMINAL FALLIDA ERROR IA (' . $primeros3 . ' NO RESPONDIO)';
    	}

	$valor = $data['coord']['validacion_LEDS_ONT_fallida'] ?? 0;
    	$foto = $data['coord']['validacion_leds_ont_intento'];

    	if ((int)$valor === 1 && str_contains($foto, 'NO RECONOCIO')) {
            $primeros3 = substr($foto, 0, 3);
            $alertas[] = 'FOTO LEDS FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    	} elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 422')) {
            $primeros3 = substr($foto, 0, 3);
            $alertas[] = 'FOTO LEDS FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    	} elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 500')) {
            $primeros3 = substr($foto, 0, 3);
            $alertas[] = 'FOTO LEDS FALLIDA ERROR IA (' . $primeros3 . ' NO RESPONDIO)';
    	}

    }
}


/*private function alertaFotosPorTipoQueja($data, &$alertas)
{
    $tipoTarea = strtoupper(trim($data['tipoTarea'] ?? ''));
    $codigoLiquidacion = strtoupper(trim($data['coord']['Codigo_Liquidacion'] ?? ''));

    // Si no es queja, no hacemos nada
    if (!$this->esTipoQueja($tipoTarea)) {
        return;
    }

    $validar = [];

    // Definir qué fotos sí se deben validar según el código de liquidación
    if (in_array($codigoLiquidacion, [
        'JUMPER/PARALELO EXTERIOR',
        'CONECTOR MECANICO DAÑADO',
        'CAMBIO DE PUERTO',
        'CAMBIO DE TERMINAL'
    ])) {
        $validar = ['Foto_Casa_Cliente','Foto_Puerto'];
    } elseif ($codigoLiquidacion === 'MODEM DAÑADO') {
        $validar = ['Foto_Casa_Cliente','Foto_Ont'];
    } elseif (in_array($codigoLiquidacion, [
        'CAMBIO PAR PRINCIPAL',
        'CAMBIO PAR SECUNDARIO',
        'ROSETA OPTICA',
        'CAMBIO DE INTERIOR'
    ])) {
        $validar = ['Foto_Casa_Cliente'];
    } elseif (in_array($codigoLiquidacion, [
        'CAMBIO PARALELO Y MODEM',
        'OTRO'
    ])) {
        $validar = ['Foto_Casa_Cliente','Foto_Puerto','Foto_Ont'];
    }

    // Validar solo esas evidencias
    foreach ($validar as $campo) {
        $valor = $data['coord'][$campo] ?? '';

        if (empty($valor) || !preg_match('/\.(jpg|jpeg|png)$/i', $valor)) {
            $alertas[] = "FALTA_EVIDENCIA_" . strtoupper($campo);
        }
    }

    // Validaciones binarias (solo si es queja y hay evidencias a validar)
    if (!empty($validar)) {
        if ((int)($data['coord']['validacion_foto_fallida'] ?? 0) === 1) {
            $alertas[] = 'VALIDACION_FOTO_FALLIDA';
        }

        if ((int)($data['coord']['validacion_LEDS_ONT_fallida'] ?? 0) === 1) {
            $alertas[] = 'VALIDACION_LEDS_FALLIDA';
        }
    }
} */



/*private function alertaOrdenRevision30Dias($data, &$alertas)
{
    $fechas = $data['tacOrdenHist'] ?? null;
    $fechaActual = $data['fechaLiquidacion'] ?? null;

    if (empty($fechas) || empty($fechaActual)) return;

    $fechaBase = strtotime($fechaActual);

    foreach ($fechas as $fechaComparar) {
        $diff = abs(($fechaBase - strtotime($fechaComparar)) / 86400);
        if ($diff <= 30 && $fechaComparar != $fechaActual) {
            $alertas[] = "ORDEN A REVISION POR 30 DIAS";
            return;
        }
    }
} */

private function alertaOrdenRevision30Dias($data, &$alertas)
{
    $fechas = $data['tacOrdenHist'] ?? null;

    // Si el SQL ya filtró, basta con verificar si hay registros
    if (!empty($fechas)) {
        $alertas[] = "ORDEN A REVISION POR 30 DIAS";
    }
}



/*private function alertaOrdenRevision30Dias($data, &$alertas)
{
    $fechas = $data['tacOrdenHist'] ?? null;

    if (empty($fechas) || count($fechas) < 2) return;

    sort($fechas);

    $total = count($fechas);

    for ($i = 0; $i < $total; $i++) {

        $f1 = strtotime($fechas[$i]);

        for ($j = $i + 1; $j < $total; $j++) {

            $f2 = strtotime($fechas[$j]);

            $diff = abs(($f2 - $f1) / 86400);

            if ($diff <= 30) {
                $alertas[] = "ORDEN A REVISION POR 30 DIAS";
                return;
            }
        }
    }
} */

    private function alertaSeriesDiferente($data, &$alertas, &$detalleSeries)
{
    // Normalizar valores
    $serieTac   = strtoupper(trim($data['serieTac'] ?? ''));
    $serieCoord = strtoupper(trim($data['serieCoord'] ?? ''));
    $serieEsci  = strtoupper(trim($data['serieEsci'] ?? ''));

    // Filtrar valores inválidos
    $invalidos = ['DESCONOCIDO', 'NS NO OBTENIDO', ''];
    $serieTac   = in_array($serieTac, $invalidos) ? null : $serieTac;
    $serieCoord = in_array($serieCoord, $invalidos) ? null : $serieCoord;
    $serieEsci  = in_array($serieEsci, $invalidos) ? null : $serieEsci;

    $hayDiferencia = false;

    if ($data['coord'] !== null) {
        // Caso: coord tiene serie válida
        if ($serieCoord) {
            $series = array_filter([$serieTac, $serieCoord, $serieEsci]);
            if (count($series) > 1 && count(array_unique($series)) > 1) {
                $hayDiferencia = true;
            }
        } else {
            // Caso: coord vacío/null → igual debe evaluarse
            if (($serieTac && $serieEsci) || ($serieTac || $serieEsci)) {
                // Si TAC y ESCI existen y son distintos → diferencia
                if ($serieTac && $serieEsci && $serieTac !== $serieEsci) {
                    $hayDiferencia = true;
                }
                // Si coord está vacío/null pero TAC o ESCI sí tienen valores → también consideramos diferencia
                if (!$serieCoord) {
                    $hayDiferencia = true;
                }
            }
        }
    } else {
        // No existe coord → comparar TAC vs ESCI
        if ($serieTac && $serieEsci && $serieTac !== $serieEsci) {
            $hayDiferencia = true;
        }
    }

    if ($hayDiferencia) {
        $alertas[] = 'NUMERO_SERIE_DIFERENTE';

        // Base del detalle
        $detalleSeries = "Coordiapp: " . ($serieCoord ?? 'N/A') .
                         " | TAC: " . ($serieTac ?? 'N/A') .
                         " | ESCI: " . ($serieEsci ?? 'N/A');

        // Agregar origen_ont si existe en coordiapp
        if (!empty($data['coord']['origen_ont'])) {
            $detalleSeries .= " | ORIGEN: " . $data['coord']['origen_ont'];
        }
    }
}


   /* private function alertaSeriesDiferente($data, &$alertas, &$detalleSeries)
{
    // Normalizar valores
    $serieTac   = strtoupper(trim($data['serieTac'] ?? ''));
    $serieCoord = strtoupper(trim($data['serieCoord'] ?? ''));
    $serieEsci  = strtoupper(trim($data['serieEsci'] ?? ''));

    // Filtrar valores inválidos
    $invalidos = ['DESCONOCIDO', 'NS NO OBTENIDO', ''];
    $serieTac   = in_array($serieTac, $invalidos) ? null : $serieTac;
    $serieCoord = in_array($serieCoord, $invalidos) ? null : $serieCoord;
    $serieEsci  = in_array($serieEsci, $invalidos) ? null : $serieEsci;

    $hayDiferencia = false;

    if ($data['coord'] !== null) {
        if ($serieCoord) {
            $series = array_filter([$serieTac, $serieCoord, $serieEsci]);
            if (count($series) > 1 && count(array_unique($series)) > 1) {
                $hayDiferencia = true;
            }
        } else {
            if ($serieTac && $serieEsci && $serieTac !== $serieEsci) {
                $hayDiferencia = true;
            }
        }
    } else {
        if ($serieTac && $serieEsci && $serieTac !== $serieEsci) {
            $hayDiferencia = true;
        }
    }

    if ($hayDiferencia) {
        $alertas[] = 'NUMERO_SERIE_DIFERENTE';

        //  AQUÍ ARMAS EL DETALLE
        $detalleSeries = "Coordiapp: " . ($serieCoord ?? 'N/A') .
                         " | TAC: " . ($serieTac ?? 'N/A') .
                         " | ESCI: " . ($serieEsci ?? 'N/A');
    }
} */

/*private function alertaSeriesDiferente($data, &$alertas)
{
    // Normalizar valores
    $serieTac   = strtoupper(trim($data['serieTac'] ?? ''));
    $serieCoord = strtoupper(trim($data['serieCoord'] ?? ''));
    $serieEsci  = strtoupper(trim($data['serieEsci'] ?? ''));

    // Filtrar valores inválidos
    $invalidos = ['DESCONOCIDO', 'NS NO OBTENIDO', ''];
    $serieTac   = in_array($serieTac, $invalidos) ? null : $serieTac;
    $serieCoord = in_array($serieCoord, $invalidos) ? null : $serieCoord;
    $serieEsci  = in_array($serieEsci, $invalidos) ? null : $serieEsci;

    // Caso: existe Coordiapp
    if ($data['coord'] !== null) {
        if ($serieCoord) {
            // Comparar los tres
            $series = array_filter([$serieTac, $serieCoord, $serieEsci]);
            if (count($series) > 1 && count(array_unique($series)) > 1) {
                $alertas[] = 'NUMERO_SERIE_DIFERENTE';
            }
        } else {
            // Coordiapp vacío → comparar TAC vs Esci
            if ($serieTac && $serieEsci && $serieTac !== $serieEsci) {
                $alertas[] = 'NUMERO_SERIE_DIFERENTE';
            }
        }
    } else {
        // Caso: no existe Coordiapp → comparar TAC vs Esci
        if ($serieTac && $serieEsci && $serieTac !== $serieEsci) {
            $alertas[] = 'NUMERO_SERIE_DIFERENTE';
        }
    }
} */

    /*private function alertaEstadoEsci($data, &$alertas, &$detalleEsci)
{
    $esci = $data['esci'] ?? null;
    var_dump($esci);
    
    if (!$esci) {
        return; // no hay datos de esci para este teléfono
    }

    //tomar el campo M_SERIE del array $esci.
    $mSerie = strtoupper(trim($esci['M_SERIE'] ?? ''));
    
    // tomar el campo DID_ACTIVO del array $esci.
    $didActivo = isset($esci['DID_ACTIVO']) ? intval($esci['DID_ACTIVO']) : null;
    
    //tomas el campo F_CONSULTA del array $esci.
    $fConsulta = $esci['F_CONSULTA'] ?? null; // ahora usamos F_CONSULTA

    // Caso: número de serie desconocido
    if ($mSerie === 'DESCONOCIDO') {
        $alertas[] = 'SIN NAVEGACION';
        $detalleEsci = "Número de serie desconocido en Esci";
        return;
    }

    // Caso: servicio sin navegación (DID_ACTIVO = 0)
    if ($didActivo === 0) {
        $alertas[] = 'SIN NAVEGACION';
        if ($fConsulta) {
            $detalleEsci = "ultima revision: $fConsulta";
        }
        return;
    }

    // Caso: servicio inactivo (DID_ACTIVO = -1)
    if ($didActivo === -1) {
        $alertas[] = 'SERVICIO INACTIVO';
        if ($fConsulta) {
            $detalleEsci = "ultima vez que navego: $fConsulta";
        }
        return;
    }
} */

    private function alertaEstadoEsci($data, &$alertas, &$detalleEsci)
{
    $esci = $data['esci'] ?? null;
    if (!$esci) {
        return; // no hay datos de esci para este teléfono
    }

    $mSerie     = strtoupper(trim($esci['M_SERIE'] ?? ''));
    $didActivo  = isset($esci['DID_ACTIVO']) ? intval($esci['DID_ACTIVO']) : null;
    $fConsulta  = $esci['F_CONSULTA'] ?? null;

    // Caso: servicio inactivo (DID_ACTIVO = -1) → prioridad absoluta
    if ($didActivo === -1) {
        $alertas[] = 'SERVICIO INACTIVO';
        if ($fConsulta) {
            $detalleEsci = "última vez que navegó: $fConsulta";
        } else {
            $detalleEsci = "Servicio inactivo en Esci";
        }
        return;
    }

    // Caso: número de serie desconocido (solo si no es -1)
    if ($mSerie === 'DESCONOCIDO') {
        $alertas[] = 'SIN NAVEGACION';
        $detalleEsci = "Número de serie desconocido en Esci";
        return;
    }

    // Caso: servicio sin navegación (DID_ACTIVO = 0)
    if ($didActivo === 0) {
        $alertas[] = 'SIN NAVEGACION';
        if ($fConsulta) {
            $detalleEsci = "última revisión: $fConsulta";
        }
        return;
    }
}



   /*private function alertaEstadoEsci($data, &$alertas, &$detalleEsci)
{
    $esci = $data['esci'] ?? null;
    if (!$esci) {
        return; // no hay datos de esci para este teléfono
    }

    $mSerie     = strtoupper(trim($esci['M_SERIE'] ?? ''));
    $didActivo  = isset($esci['DID_ACTIVO']) ? intval($esci['DID_ACTIVO']) : null;
    $fConsulta  = $esci['F_CONSULTA'] ?? null;

    // Caso: servicio inactivo (DID_ACTIVO = -1) → siempre SIN SERVICIO
    if ($didActivo === -1) {
        $alertas[] = 'SIN SERVICIO';
        if ($fConsulta) {
            $detalleEsci = "ultima vez que navego: $fConsulta";
        } else {
            $detalleEsci = "Servicio inactivo";
        }
        return;
    }

    // Caso: número de serie desconocido (solo aplica si no es -1)
    if ($mSerie === 'DESCONOCIDO') {
        $alertas[] = 'SIN NAVEGACION';
        $detalleEsci = "Numero de serie desconocido en Esci";
        return;
    }

    // Caso: servicio sin navegación (DID_ACTIVO = 0)
    if ($didActivo === 0) {
        $alertas[] = 'SIN NAVEGACION';
        if ($fConsulta) {
            $detalleEsci = "ultima revision: $fConsulta";
        }
        return;
    }
} */



/*private function alertaEstadoEsci($data, &$alertas, &$detalleEsci)
{
    $esci = $data['esci'] ?? null;
    var_dump($esci);
    
    if (!$esci) {
        return; // no hay datos de esci para este teléfono
    }

    //tomar el campo M_SERIE del array $esci.
    $mSerie = strtoupper(trim($esci['M_SERIE'] ?? ''));
    
    // tomar el campo DID_ACTIVO del array $esci.
    $didActivo = isset($esci['DID_ACTIVO']) ? intval($esci['DID_ACTIVO']) : null;
    
    //tomas el campo F_CONSULTA del array $esci.
    $fConsulta = $esci['F_CONSULTA'] ?? null; // ahora usamos F_CONSULTA

    // Caso: número de serie desconocido
    if ($mSerie === 'DESCONOCIDO') {
        $alertas[] = 'SIN NAVEGACION';
        $detalleEsci = "Número de serie desconocido en Esci";
        return;
    }

    // Caso: servicio sin navegación (DID_ACTIVO = 0)
    if ($didActivo === 0) {
        $alertas[] = 'SIN NAVEGACION';
        if ($fConsulta) {
            $detalleEsci = "ultima revision: $fConsulta";
        }
        return;
    }

    // Caso: servicio inactivo (DID_ACTIVO = -1)
    if ($didActivo === -1) {
        $alertas[] = 'SERVICIO INACTIVO';
        if ($fConsulta) {
            $detalleEsci = "ultima vez que navego: $fConsulta";
        }
        return;
    }
} */





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

/*private function alertaFolioCapturadoCoordiapp($data, &$alertas)
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
} */

/*private function alertaFolioCapturadoCoordiapp($data, &$alertas)
{
    $folio = $data['folio'] ?? null;

    // Verificar si el folio está presente en los datos de CoordIAPP
    if ($folio && isset($data['coord'][$folio])) {
        $alertas[] = 'FOLIO_CAPTURADO_EN_COORDIAPP';
    }
} */

    private function alertaNoCoordiapp($data, &$alertas) {
    if ($data['coord'] === null) {
        $alertas[] = 'NO EN COORDIAPP';
    }
}



private function normalizarTexto($texto)
{
    if (!$texto) return $texto;

    $texto = strtoupper(trim($texto));

    // Quitar acentos
    $buscar = ['Á','É','Í','Ó','Ú','Ñ'];
    $reemplazar = ['A','E','I','O','U','N'];

    return str_replace($buscar, $reemplazar, $texto);
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



// FOTO LEDS FALLIDA IA

private function alertaLedsOntFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_LEDS_ONT_fallida'] ?? 0;
    $foto = $data['coord']['validacion_leds_ont_intento'];

    if ((int)$valor === 1 && str_contains($foto, 'NO RECONOCIO')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'FOTO LEDS FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 422')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'FOTO LEDS FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 500')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'FOTO LEDS FALLIDA ERROR IA (' . $primeros3 . ' NO RESPONDIO)';
    }
}

/*private function alertaLedsOntFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_LEDS_ONT_fallida'] ?? 0;

    if ((int)$valor === 1) {
        $alertas[] = 'EVIDENCIA_LEDS_ONT_FALLIDA';
    }
} */
 
/*private function alertaFotoDomicilioFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;
    $foto = $data['coord']['validacion_foto_fachada_intento'];

    if ((int)$valor === 1 && str_contains($foto, 'NO RECONOCIO')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'FOTO DOMICILIO FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 422')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'FOTO DOMICILIO FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 500')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'FOTO DOMICILIO FALLIDA ERROR IA (' . $primeros3 . ' NO RESPONDIO)';
    }
} */

    private function alertaFotoDomicilioFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;
    $foto  = $data['coord']['validacion_foto_fachada_intento'] ?? '';

    // Convertir siempre a string para evitar warnings
    $fotoStr = (string)$foto;

    if ((int)$valor === 1 && str_contains($fotoStr, 'NO RECONOCIO')) {
        $primeros3 = substr($fotoStr, 0, 3);
        $alertas[] = 'FOTO DOMICILIO FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($fotoStr, 'NO RESPONDIO 422')) {
        $primeros3 = substr($fotoStr, 0, 3);
        $alertas[] = 'FOTO DOMICILIO FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($fotoStr, 'NO RESPONDIO 500')) {
        $primeros3 = substr($fotoStr, 0, 3);
        $alertas[] = 'FOTO DOMICILIO FALLIDA ERROR IA (' . $primeros3 . ' NO RESPONDIO)';
    }
}



//  FOTO DOMICILIO IA 
/*private function alertaFotoDomicilioFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;

    if ((int)$valor === 1) {
        $alertas[] = 'FOTO_DOMICILIO_FALLIDA';
    }
} */

/*private function alertaFotoDomicilioFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;
    $foto = $data['coord']['validacion_foto_fachada_intento'];

    if ((int)$valor === 1 && str_contains($foto, 'NO RECONOCIO')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'Foto fallida mal tomada (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'Foto fallida error IA (' . $primeros3 . ' NO RESPONDIO)';
    }
} */


// FOTO TERMINAL IA

// FOTO TERMINAL IA

private function alertaTerminalFallidaIA($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;
    $foto  = $data['coord']['validacion_foto_terminal_intento'] ?? '';

    // Convertir siempre a string para evitar warnings
    $fotoStr = (string)$foto;

    if ((int)$valor === 1 && str_contains($fotoStr, 'NO RECONOCIO')) {
        $primeros3 = substr($fotoStr, 0, 3);
        $alertas[] = 'FOTO TERMINAL FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($fotoStr, 'NO RESPONDIO 422')) {
        $primeros3 = substr($fotoStr, 0, 3);
        $alertas[] = 'FOTO TERMINAL FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($fotoStr, 'NO RESPONDIO 500')) {
        $primeros3 = substr($fotoStr, 0, 3);
        $alertas[] = 'FOTO TERMINAL FALLIDA ERROR IA (' . $primeros3 . ' NO RESPONDIO)';
    }
}



/*private function alertaTerminalFallidaIA($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;
    $foto = $data['coord']['validacion_foto_terminal_intento'];

    if ((int)$valor === 1 && str_contains($foto, 'NO RECONOCIO')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'FOTO TERMINAL FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 422')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'FOTO TERMINAL FALLIDA MAL TOMADA (' . $primeros3 . ' NO RECONOCIO)';
    } elseif ((int)$valor === 1 && str_contains($foto, 'NO RESPONDIO 500')) {
        $primeros3 = substr($foto, 0, 3);
        $alertas[] = 'FOTO TERMINAL FALLIDA ERROR IA (' . $primeros3 . ' NO RESPONDIO)';
    }
} */


/*private function alertaTerminalFallidaIA($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;

    if ((int)$valor === 1) {
        $alertas[] = 'FOTO TERMINAL FALLIDA';
    }
} */



// =========================
//  ALERTAS
// =========================

private function alertaOntDuplicadaTac($data, &$alertas, &$detalleTac)
{
    $tac = $data['tac'] ?? null;
    if (!$tac) {
        return; // no hay datos TAC para este teléfono
    }

    $telefono = $data['telefono'] ?? null;
    $serieTac = strtoupper(trim($tac['Num__Serie'] ?? ''));

    //  Validar que el número de serie sea válido
    if (empty($serieTac) || $serieTac === 'DESCONOCIDO' || $serieTac === 'NS NO OBTENIDO') {
        return; // no se considera duplicado si no hay serie válido
    }

    // Buscar en el mapa de duplicados por serie
    $ontDuplicadasTac = $data['ontDuplicadasTac'] ?? [];

    if (isset($ontDuplicadasTac[$serieTac])) {
        // Verificar que el teléfono actual esté dentro de los duplicados
        // y que haya más de un teléfono asociado a esa serie
        $telefonosDuplicados = array_keys($ontDuplicadasTac[$serieTac] ?? []);
        if (count($telefonosDuplicados) > 1 && in_array($telefono, $telefonosDuplicados)) {
            $alertas[] = 'ONT_DUPLICADA_EN_TAC';
            $detalleTac = "Serie duplicada en TAC: $serieTac en teléfonos " . implode(', ', $telefonosDuplicados);
        }
    }
}



private function alertaOntDuplicadaEsci($data, &$alertas, &$detalleEsci)
{
    $serie = $data['serieEsci'];

    if ($serie && isset($data['esciSeriesMap'][$serie])) {

        $telefonos = array_keys($data['esciSeriesMap'][$serie]);

        // 🔥 asegurar únicos reales
        $telefonos = array_unique($telefonos);

        if (count($telefonos) > 1) {
            $alertas[] = 'ONT_DUPLICADA_EN_ESCI';
            $detalleEsci = "Serie $serie con teléfonos: " . implode(',', $telefonos);
        }
    }
}

// ESCI POSIBLE NUEVA

/*private function alertaOntDuplicadaEsci($data, &$alertas, &$detalleEsci)
{
    $serie = strtoupper(trim($data['serieEsci'] ?? ''));

    // 🚨 Validar que el número de serie sea válido
    if (empty($serie) || $serie === 'DESCONOCIDO' || $serie === 'NS NO OBTENIDO') {
        return; // no se considera duplicado si no hay serie válida
    }

    if (isset($data['esciSeriesMap'][$serie])) {
        if (count($data['esciSeriesMap'][$serie]) > 1) {
            $alertas[] = 'ONT_DUPLICADA_EN_ESCI';
            $detalleEsci = "Serie $serie duplicada en ESCI";
        }
    }
} */


private function alertaSerieDesconocida($data, &$alertas)
{
    if ($data['serieEsci'] === 'DESCONOCIDO') {
        $alertas[] = 'SERIE_DESCONOCIDA';
    }
}

/*private function alertaEventoEsci($data, &$alertas, &$detalleEsci)
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
} */

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

private function alertaTecnologiaDiferente($data, &$alertas, &$detalleTecnologia)
{
    $tecTacRaw   = $data['tac']['TECNOLOGIA']   ?? '';
    $tecCoordRaw = $data['coord']['Tecnologia'] ?? '';

    $tecTac   = $this->normalizarTecnologiaAlertas($tecTacRaw);
    $tecCoord = $this->normalizarTecnologiaAlertas($tecCoordRaw);

    // Caso 1: TAC tiene valor y Coordiapp no
    if ($tecTac && !$tecCoord) {
        $alertas[] = 'TECNOLOGIA_DIFERENTE';
        $detalleTecnologia = "TECNOLOGIA COORDIAPP: " . ($tecCoordRaw ?: 'SIN DATO') .
                             " | TECNOLOGIA TAC: " . ($tecTacRaw ?: 'SIN DATO');
    }
    // Caso 2: Ambos tienen valor y son diferentes
    elseif ($tecTac && $tecCoord && $tecTac !== $tecCoord) {
        $alertas[] = 'TECNOLOGIA_DIFERENTE';
        $detalleTecnologia = "TECNOLOGIA COORDIAPP: " . ($tecCoordRaw ?: 'SIN DATO') .
                             " | TECNOLOGIA TAC: " . ($tecTacRaw ?: 'SIN DATO');
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

    // Si viene vacío, N/A o NULL → alerta directa
    if ($ont === '' || $ont === 'N/A' || $ont === 'NULL') {
        $alertas[] = 'ONT_NO_ENCONTRADA_EN_ALMACEN';
        return;
    }

    //  NORMALIZAR
    $ont = $this->normalizarSerie($ont);

    $conexion = $this->get_conexion();

    // Buscar primero en material_det_salida (cuando ya salió al contratista)
    $stmt = $conexion->prepare("
        SELECT FK_Contratista_Salida
        FROM material_det_salida
        WHERE Num_Serie_S = ?
        LIMIT 1
    ");
    $stmt->execute([$ont]);
    $rowSalida = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rowSalida) {
        // =========================
        //  ALERTAS DE SALIDA
        // =========================
        if (empty($rowSalida['FK_Contratista_Salida'])) {
            $alertas[] = 'ONT_NO_ASIGNADA_AL_CONTRATISTA';
            return;
        }

        if ((int)$rowSalida['FK_Contratista_Salida'] !== (int)$data['idContratista']) {
            $alertas[] = 'ONT_ASIGNADA_A_OTRO_CONTRATISTA';
            return;
        }

        return; // todo correcto, no hay alerta
    }

    // Si no está en salida, verificar si existe en almacén
    $stmt = $conexion->prepare("
        SELECT idMaterial
        FROM materiales_det
        WHERE Num_Serie = ?
        LIMIT 1
    ");
    $stmt->execute([$ont]);
    $rowAlmacen = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rowAlmacen) {
        // Existe en almacén pero no ha salido
        $alertas[] = 'ONT_NO_ASIGNADA_AL_CONTRATISTA';
    } else {
        // No existe en ninguna tabla
        $alertas[] = 'ONT_NO_ENCONTRADA_EN_ALMACEN';
    }
}








/*private function alertaOntAlmacen($data, &$alertas)
{
    if (isset($data['coord']['Ont'])) {
        $ontCrudo = $data['coord']['Ont'];

        echo "<pre>";
        echo "=== DEBUG ONT ENTRANTE ===\n";
        echo "Valor crudo:\n";
        var_dump($ontCrudo);
        echo "Valor en HEX:\n";
        var_dump(bin2hex($ontCrudo));
        echo "</pre>";
    } else {
        echo "<pre>ONT no está definida en data['coord']</pre>";
    }

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
} */


/*private function alertaOntAlmacen($data, &$alertas)
{
    // DEBUG SOLO PARA ESA ONT ESPECÍFICA
    if (isset($data['coord']['Ont'])) {
        $ontCrudo = $data['coord']['Ont'];

        // Si coincide con la ONT que quieres revisar
        if (strtoupper(trim($ontCrudo)) === 'HWTCDF9DF4B3') {
            echo "<pre>";
            echo "=== DEBUG SOLO PARA HWTCDF9DF4B3 ===\n";
            echo "Valor crudo recibido:\n";
            var_dump($ontCrudo);
            echo "Valor en HEX:\n";
            var_dump(bin2hex($ontCrudo));
            echo "</pre>";
        }
    }

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
} */


/*private function alertaOntAlmacen($data, &$alertas)
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
} */


/*private function alertaOntAlmacen($data, &$alertas)
{


    $ont = isset($data['coord']['Ont']) 
        ? strtoupper(trim($data['coord']['Ont'])) 
        : '';

    if ($ont === '') return;

    $ont = $this->normalizarSerie($ont);
    $conexion = $this->get_conexion();

    //  Verificar si existe en materiales_det (almacén)
    $stmt = $conexion->prepare("
        SELECT idMaterial, FK_Almacen_Entrada_Det
        FROM materiales_det
        WHERE REPLACE(UPPER(Num_Serie), ' ', '') = ?
        LIMIT 1
    ");
    $stmt->execute([$ont]);
    $rowEntrada = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rowEntrada) {
        $alertas[] = 'ONT_NO_ENCONTRADA_EN_ALMACEN';
        return;
    }

    //Verificar si está asignada en material_det_salida
    $stmt = $conexion->prepare("
        SELECT FK_Contratista_Salida
        FROM material_det_salida
        WHERE REPLACE(UPPER(Num_Serie_S), ' ', '') = ?
        LIMIT 1
    ");
    $stmt->execute([$ont]);
    $rowSalida = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rowSalida) {
        $alertas[] = 'ONT_NO_ASIGNADA_AL_CONTRATISTA';
        return;
    }

    if ((int)$rowSalida['FK_Contratista_Salida'] !== (int)$data['idContratista']) {
        $alertas[] = 'ONT_ASIGNADA_A_OTRO_CONTRATISTA';
        return;
    }
} */


/*private function alertaOntAlmacen($data, &$alertas)
{
    //  VIENE DE COORDIAPP
    $ontOriginal = isset($data['coord']['Ont']) 
        ? $data['coord']['Ont'] 
        : '';

    echo "<pre>";
    echo "=== DEBUG ALERTA ONT ALMACEN ===\n";
    echo "ONT original (de Coordiapp): [$ontOriginal]\n";

    $ont = strtoupper(trim($ontOriginal));
    echo "ONT después de strtoupper/trim: [$ont]\n";

    if ($ont === '') {
        echo "ONT vacío, se sale de la función\n";
        echo "</pre>";
        return;
    }

    //  NORMALIZAR
    $ont = $this->normalizarSerie($ont);
    echo "ONT después de normalizarSerie: [$ont]\n";

    $conexion = $this->get_conexion();

    $sql = "
        SELECT FK_Contratista_Salida, Num_Serie_S
        FROM material_det_salida
        WHERE REPLACE(UPPER(Num_Serie_S), ' ', '') = ?
        LIMIT 1
    ";
    echo "SQL ejecutado:\n$sql\n";
    echo "Parámetro enviado: [$ont]\n";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$ont]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Resultado de la consulta:\n";
    var_dump($row);
    echo "</pre>";

    // =========================
    //  ALERTAS
    // =========================

    if (!$row) {
        echo "No se encontró coincidencia en material_det_salida\n";
        $alertas[] = 'ONT_NO_ENCONTRADA_EN_ALMACEN';
        return;
    }

    if (empty($row['FK_Contratista_Salida'])) {
        echo "ONT encontrada pero sin contratista asignado\n";
        $alertas[] = 'ONT_NO_ASIGNADA_AL_CONTRATISTA';
        return;
    }

    if ((int)$row['FK_Contratista_Salida'] !== (int)$data['idContratista']) {
        echo "ONT encontrada pero asignada a otro contratista\n";
        echo "FK_Contratista_Salida en BD: " . $row['FK_Contratista_Salida'] . "\n";
        echo "idContratista en data: " . $data['idContratista'] . "\n";
        $alertas[] = 'ONT_ASIGNADA_A_OTRO_CONTRATISTA';
        return;
    }

    echo "ONT encontrada y asignada correctamente al contratista\n";
} */


/*private function alertaOntAlmacen($data, &$alertas)
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
} */

private function normalizarSerie($serie)
{
    // Quitar espacios al inicio y fin
    $serie = trim($serie);

    // Quitar TODOS los espacios internos
    $serie = preg_replace('/\s+/', '', $serie);

    // Convertir a mayúsculas
    $serie = strtoupper($serie);

    // Opcional: quitar caracteres raros invisibles
    $serie = preg_replace('/[^A-Z0-9]/', '', $serie);

    return $serie;
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