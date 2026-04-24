<?php

require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

$totalTac = $prod->getTotalTac();

echo json_encode([
    "total_tac" => $totalTac
]);


/*require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

$inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fin    = $_GET['fecha_fin'] ?? date('Y-m-d');

$totalTac = $prod->getTotalTac($inicio, $fin);

echo json_encode([
    "total_tac" => $totalTac
]); */