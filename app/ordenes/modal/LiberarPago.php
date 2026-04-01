<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassOps.php");
session_start();

$idContratista = $_SESSION['idContratistas'] ?? null;

$emulador   = $_POST['emulador'] ?? null;
$tecnologia = $_POST['tecnologia'] ?? null;
$semana     = $_POST['semana'] ?? null;

if (!$idContratista || !$emulador || !$tecnologia || !$semana) {
    exit('Datos incompletos');
}

$folioPaco = strtoupper($emulador) . '_' . $semana;

$ops = new Ops();

$resultado = $ops->InsertarFoliosProduccion(
    $idContratista,
    strtoupper($emulador),
    $tecnologia,
    $semana,
    $folioPaco
);

echo $resultado
    ? "Pago liberado correctamente. Folio: $folioPaco"
    : "Error al liberar el pago";
