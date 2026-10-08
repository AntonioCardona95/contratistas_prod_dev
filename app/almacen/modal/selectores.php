<?php
// Ocultar mensajes de Deprecated/Warnings para que no corrompan la salida JSON
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

require_once("../controller/ClassConsultasProduccion.php");
$obj = new ClassConsultasProduccion();

$action = $_POST['action'] ?? '';

// Definir el encabezado HTTP como JSON
header('Content-Type: application/json; charset=utf-8');

if ($action === 'getDivisionByArea') {
    $idArea = $_POST['idArea'] ?? 0;
    $division = $obj->obtenerDivisionPorArea($idArea);
    echo json_encode($division);
    exit;
}

if ($action === 'getCopesByArea') {
    $idArea = $_POST['idArea'] ?? 0;
    $copes = $obj->obtenerCopesPorArea($idArea);
    echo json_encode($copes);
    exit;
}