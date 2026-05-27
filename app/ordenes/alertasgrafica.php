<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassResultados.php");

$resultados = new Resultados();
$conteos = $resultados->getConteoAlertasPorTipo();

header('Content-Type: application/json; charset=utf-8');
echo json_encode(array_values($conteos), JSON_UNESCAPED_UNICODE);
