<?php
// pruebas nada mas //
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
    echo "<pre>";
    echo "ERROR: Debes seleccionar un rango de fechas\n";
    echo "</pre>";
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
        echo "<pre>";
        echo "No hay registros para procesar en ese rango\n";
        echo "</pre>";
        exit;
    }

    // =========================
    //  2. PROCESAR ALERTAS + CORRECTAS (SIN DUPLICADOS)
    // =========================
    $prod->InsertarAlertasMasivo($registros);

    // =========================
    //  RESPUESTA (SOLO PARA DEBUG)
    // =========================
    echo "<pre>";
    echo "Procesamiento completado (alertas y correctas, sin duplicados)\n";
    echo "Total registros: " . count($registros) . "\n";
    echo "=== DEBUG REGISTROS ===\n";
    var_dump($registros);
    echo "</pre>";

} catch (Exception $e) {

    echo "<pre>";
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "</pre>";
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
} */





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