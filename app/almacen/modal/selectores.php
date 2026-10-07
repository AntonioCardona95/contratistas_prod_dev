<?php
require_once("../controller/ClassConsultasProduccion.php");
$obj = new ClassConsultasProduccion();

$action = $_POST['action'] ?? '';

if ($action === 'getDivisionByArea') {
    $idArea = $_POST['idArea'] ?? 0;
    $division = $obj->obtenerDivisionPorArea($idArea);
    echo json_encode($division);
    exit;
}

if ($action === 'getCopesByDivision') {
    $idDivision = $_POST['idDivision'] ?? 0;
    $copes = $obj->obtenerCopesPorDivision($idDivision);
    echo json_encode($copes);
    exit;
}
