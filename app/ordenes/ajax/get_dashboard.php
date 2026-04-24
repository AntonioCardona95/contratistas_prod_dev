<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

// (por si después quieres usarlas)
$fechaInicio = $_GET['fecha_inicio'] ?? null;
$fechaFin    = $_GET['fecha_fin'] ?? null;

$totalTac = $prod->getTotalTac();
$totalAlertas = $prod->getTotalAlertas(); // ✅ NUEVO

/*echo json_encode([
    "total_tac" => $totalTac,
    "alertas"   => $totalAlertas
]); */

echo json_encode([
    "total_tac" => 100,
    "alertas" => 50
]);

/*require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

$inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fin    = $_GET['fecha_fin'] ?? date('Y-m-d');

$totalTac = $prod->getTotalTac($inicio, $fin);

echo json_encode([
    "total_tac" => $totalTac
]); */