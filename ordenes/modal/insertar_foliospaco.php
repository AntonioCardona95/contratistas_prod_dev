<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassNomina.php");
session_start();


$idContratista = $_SESSION['idContratistas'] ?? null;
$emulador   = $_POST['emulador'] ?? null;      // FIBRA
$idEmulador = $_POST['idEmulador'] ?? null;    // 1
$semana     = $_POST['semana'] ?? null;
$tecnologia    = $_POST['tecnologia'] ?? null;
$filas         = $_POST['filas'] ?? [];


if (!$idContratista || !$emulador || !$semana || !$tecnologia || empty($filas)) {
    echo json_encode([
        'ok' => false,
        'error' => 'Parámetros incompletos'
    ]);
    exit;
}


$folioPaco = $emulador . '_' . $semana;
date_default_timezone_set('America/Mexico_City');


try {
    $nomina = new Nomina();

    $nomina->insertarFoliosPaco(
        $folioPaco,
        $idContratista,
        $idEmulador,
        $semana,
        $tecnologia,
        $filas
    );

    echo json_encode(['ok' => true]);

} catch (Exception $e) {
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage()
    ]);
}
