<?php

require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassResultados.php");

$fecha1 = isset($_GET['Fecha1']) ? $_GET['Fecha1'] . " 00:00:00" : null;
$fecha2 = isset($_GET['Fecha2']) ? $_GET['Fecha2'] . " 23:59:59" : null;

$resultados = new Resultados();
$conteos = $resultados->getConteoAlertasPorTipo($fecha1, $fecha2);

header('Content-Type: application/json; charset=utf-8');
echo json_encode(array_values($conteos), JSON_UNESCAPED_UNICODE);



/*require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassResultados.php");

$resultados = new Resultados();
$conteos = $resultados->getConteoAlertasPorTipo();

header('Content-Type: application/json; charset=utf-8');
echo json_encode(array_values($conteos), JSON_UNESCAPED_UNICODE); */
