<?php
require_once("ClassResultados.php");

$resultados = new Resultados();
$conteos = $resultados->getConteoAlertasPorTipo();

// Devolver solo los valores en el orden fijo
header('Content-Type: application/json');
echo json_encode(array_values($conteos));
