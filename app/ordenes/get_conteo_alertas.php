<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassOps.php");
session_start();

$modal = new Ops();
$idContratistas = $_SESSION['idContratistas'] ?? null;
$semana = $_POST['semana'] ?? null;

if (!$idContratistas || !$semana) {
    echo 0;
    exit;
}

// Obtener expedientes del contratista
$expedientesData = $modal->ObtenerExpedientesPorContratista($idContratistas);
$expedientes = [];
foreach ($expedientesData as $exp) {
    $expedientes[] = $exp['NExpediente'];
}

// Si no hay expedientes, devolver 0
if (empty($expedientes)) {
    echo 0;
    exit;
}

// Obtener las órdenes con incidencias
$filas = $modal->OrdenesConIncidencias($idContratistas, $expedientes, $semana);
$filas = is_array($filas) ? $filas : [];

// Contar cuántas tienen alerta
$totalAlertas = 0;
foreach ($filas as $row) {
    if (!empty($row['Alerta'])) {
        $totalAlertas++;
    }
}

echo $totalAlertas;