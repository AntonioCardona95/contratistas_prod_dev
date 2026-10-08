<?php
require_once("../controller/ClassConsultasProduccion.php");
$obj = new ClassConsultasProduccion();

$action = $_POST['action'] ?? '';

// 1. Obtener División por Área
if ($action === 'getDivisionByArea') {
    $idArea = (int)($_POST['idArea'] ?? 0);
    $division = $obj->obtenerDivisionPorArea($idArea);

    if ($division !== null) {
        echo json_encode($division);
    } else {
        echo json_encode([
            'error' => true,
            'mensaje' => 'No se encontró división asociada para esta área.',
            'idArea' => $idArea
        ]);
    }
    exit;
}

// 2. Obtener COPEs por División
if ($action === 'getCopesByDivision') {
    $idDivision = (int)($_POST['idDivision'] ?? 0);
    $copes = $obj->obtenerCopesPorDivision($idDivision);

    echo json_encode($copes ? $copes : []);
    exit;
}