<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Mostrar todos los errores excepto Deprecated y Notices
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 1);

// No mostrar nada en pantalla (solo log)
//ini_set('display_errors', 0);


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
        "total" => count($registros),
        "debug" => $debugRows
         //"debug" => $registros
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