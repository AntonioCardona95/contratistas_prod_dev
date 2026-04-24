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
    echo "❌ Debes seleccionar un rango de fechas";
    exit;
}

// =========================
//  TIEMPO DE EJECUCIÓN (IMPORTANTE)
// =========================
set_time_limit(300); // hasta 5 minutos si es necesario

try {

    // =========================
    //  1. OBTENER REGISTROS BASE
    // =========================
    $registros = $prod->obtenerRegistrosParaAlertas($fechaInicio, $fechaFin);

    if (empty($registros)) {
        echo "⚠️ No hay registros para procesar en ese rango";
        exit;
    }

    // =========================
    //  2. LIMPIAR ALERTAS PREVIAS (EVITA DUPLICADOS)
    // =========================
    $prod->limpiarAlertas($fechaInicio, $fechaFin);

    // =========================
    //  3. PROCESAR ALERTAS
    // =========================
    $prod->InsertarAlertasMasivo($registros);

    // =========================
    //  RESPUESTA
    // =========================
    echo "✅ Alertas procesadas correctamente (" . count($registros) . " registros)";

} catch (Exception $e) {

    echo "❌ Error: " . $e->getMessage();
}