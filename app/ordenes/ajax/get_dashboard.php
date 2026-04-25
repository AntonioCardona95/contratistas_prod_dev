<?php
require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

// Usa los mismos nombres que manda el JS
$fechaInicio = $_GET['fecha_inicio'] ?? null;
$fechaFin    = $_GET['fecha_fin'] ?? null;

$totalTac     = $prod->getTotalTac($fechaInicio, $fechaFin);
$totalAlertas = $prod->getTotalAlertas($fechaInicio, $fechaFin);

// Asegúrate de que SOLO se imprima JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "total_tac" => $totalTac,
    "alertas"   => $totalAlertas
]);




/*require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

$inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fin    = $_GET['fecha_fin'] ?? date('Y-m-d');

$totalTac = $prod->getTotalTac($inicio, $fin);

echo json_encode([
    "total_tac" => $totalTac
]); */