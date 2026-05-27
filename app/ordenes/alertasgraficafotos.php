<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassResultados.php");

$resultados = new Resultados();

$fecha1 = $_GET['Fecha1'] ?? null;
$fecha2 = $_GET['Fecha2'] ?? null;


$conteos = $resultados->getConteoAlertasFotos($fecha1, $fecha2);

header('Content-Type: application/json; charset=utf-8');
echo json_encode(array_values($conteos), JSON_UNESCAPED_UNICODE);
