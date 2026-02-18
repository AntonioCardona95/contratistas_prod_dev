<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassOps.php");
session_start();

$idContratistas = $_SESSION['idContratistas'] ?? null;

$emulador   = $_POST['emulador'] ?? null;
$semana     = $_POST['semana'] ?? null;
$tecnologia = $_POST['tecnologia'] ?? null;

if (!$idContratistas || !$emulador || !$semana || !$tecnologia) {
    echo '❌ Datos incompletos';
    exit;
}

$modal = new Ops();

/* 🔥 MISMA CONSULTA QUE LA TABLA */
$filas = $modal->ObtenerProduccionContratistaSinIncidencias(
    $idContratistas,
    strtoupper($emulador),
    $semana,
    $tecnologia
);

if (!is_array($filas) || empty($filas)) {
    echo '⚠️ No hay órdenes para liberar';
    exit;
}

/* ===========================
   INSERCIÓN DE NÓMINA
=========================== */

$insertadas = 0;

foreach ($filas as $row) {

    $folio     = $row['Folio_Pisa'];
    $telefono  = $row['Telefono'];
    $total     = $row['Total'] ?? 0; // AJUSTA ESTE CAMPO
    $fecha     = date('Y-m-d H:i:s');

    /* 🔒 EVITAR DUPLICADOS */
    if ($modal->ExistePagoNomina($folio)) {
        continue;
    }

    $modal->InsertarNomina([
        'id_contratista' => $idContratistas,
        'folio'          => $folio,
        'telefono'       => $telefono,
        'emulador'       => $emulador,
        'tecnologia'     => $tecnologia,
        'semana'         => $semana,
        'total'          => $total,
        'fecha'          => $fecha
    ]);

    /* OPCIONAL: marcar orden como liberada */
    $modal->MarcarOrdenLiberada($folio);

    $insertadas++;
}

echo "✅ Nómina liberada correctamente. Registros: $insertadas";
