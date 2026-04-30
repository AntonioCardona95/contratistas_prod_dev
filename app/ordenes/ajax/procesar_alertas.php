<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

// =========================
//  RECIBIR FECHAS
// =========================
$fechaInicio = $_GET['fecha_inicio'] ?? null;
$fechaFin    = $_GET['fecha_fin'] ?? null;

// =========================
//  VALIDACIÓN BÁSICA
// =========================
if (empty($fechaInicio) || empty($fechaFin)) {
    echo json_encode([
        "ok" => false,
        "msg" => "Debes seleccionar un rango de fechas"
    ]);
    exit;
}

// =========================
//  TIEMPO DE EJECUCIÓN
// =========================
set_time_limit(0);

$inicioTotal = microtime(true);

try {

    // =========================
    //  1. OBTENER REGISTROS BASE
    // =========================
    $registros = $prod->obtenerRegistrosParaAlertas($fechaInicio, $fechaFin);

    if (empty($registros)) {
        echo json_encode([
            "ok" => false,
            "msg" => "No hay registros para procesar en ese rango"
        ]);
        exit;
    }

    // =========================
    //  2. PROCESAR POR LOTES
    // =========================
    $tamanoLote = 100; // 🔥 más estable
    $lotes = array_chunk($registros, $tamanoLote);

    $totalProcesados = 0;
    $totalLotes = count($lotes);

    foreach ($lotes as $index => $lote) {

        $inicioLote = microtime(true);

        // 🔴 KILL SWITCH
        if (file_exists("/tmp/stop_alertas")) {
            throw new Exception("Proceso detenido manualmente");
        }

        // 🔹 Procesar lote
        $prod->InsertarAlertasMasivo($lote);

        $totalProcesados += count($lote);

        $tiempoLote = round(microtime(true) - $inicioLote, 2);

        // 🔹 Log detallado
        file_put_contents(
            "/tmp/proceso_alertas.log",
            "Lote " . ($index + 1) . "/$totalLotes | Registros: " . count($lote) . " | Tiempo: {$tiempoLote}s | Total: $totalProcesados\n",
            FILE_APPEND
        );

        // ❌ QUITAMOS usleep (solo úsalo si saturas BD)
        // usleep(200000);
    }

    $tiempoTotal = round(microtime(true) - $inicioTotal, 2);

    // =========================
    //  RESPUESTA
    // =========================
    echo json_encode([
        "ok" => true,
        "msg" => "Procesamiento por lotes completado",
        "total" => $totalProcesados,
        "lotes" => $totalLotes,
        "tiempo_total_segundos" => $tiempoTotal
    ]);

} catch (Exception $e) {

    echo json_encode([
        "ok" => false,
        "msg" => $e->getMessage()
    ]);
}

/*error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

// =========================
//  RECIBIR FECHAS
// =========================
$fechaInicio = $_GET['fecha_inicio'] ?? null;
$fechaFin    = $_GET['fecha_fin'] ?? null;

// =========================
//  VALIDACIÓN BÁSICA
// =========================
if (empty($fechaInicio) || empty($fechaFin)) {
    echo json_encode([
        "ok" => false,
        "msg" => "Debes seleccionar un rango de fechas"
    ]);
    exit;
}

// =========================
//  TIEMPO DE EJECUCIÓN
// =========================
set_time_limit(300);

try {

    // =========================
    //  1. OBTENER REGISTROS BASE
    // =========================
    $registros = $prod->obtenerRegistrosParaAlertas($fechaInicio, $fechaFin);

    if (empty($registros)) {
        echo json_encode([
            "ok" => false,
            "msg" => "No hay registros para procesar en ese rango"
        ]);
        exit;
    }

    // =========================
    //  2. PROCESAR ALERTAS + CORRECTAS (SIN DUPLICADOS)
    // =========================
    $prod->InsertarAlertasMasivo($registros);

    // =========================
    //  RESPUESTA
    // =========================
    echo json_encode([
        "ok" => true,
        "msg" => "Procesamiento completado (alertas y correctas, sin duplicados)",
        "total" => count($registros)
    ]);

} catch (Exception $e) {

    echo json_encode([
        "ok" => false,
        "msg" => $e->getMessage()
    ]);
}  */





/*error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

// =========================
//  RECIBIR FECHAS
// =========================
$fechaInicio = $_GET['fecha_inicio'] ?? null;
$fechaFin    = $_GET['fecha_fin'] ?? null;

// =========================
//  VALIDACIÓN BÁSICA
// =========================
if (empty($fechaInicio) || empty($fechaFin)) {
    echo " Debes seleccionar un rango de fechas";
    exit;
}

// =========================
//  TIEMPO DE EJECUCIÓN
// =========================
set_time_limit(300);

try {

    // =========================
    //  1. OBTENER REGISTROS BASE
    // =========================
    $registros = $prod->obtenerRegistrosParaAlertas($fechaInicio, $fechaFin);

    if (empty($registros)) {
        echo " No hay registros para procesar en ese rango";
        exit;
    }

    // =========================
    //  2. PROCESAR ALERTAS (SIN LIMPIAR)
    // =========================
    $prod->InsertarAlertasMasivo($registros);

    // =========================
    //  RESPUESTA
    // =========================
    echo " Alertas procesadas correctamente (" . count($registros) . " registros)";

} catch (Exception $e) {

    echo " Error: " . $e->getMessage();
} */