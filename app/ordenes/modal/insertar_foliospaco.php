<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassNomina.php");
session_start();

$idContratista = $_SESSION['idContratistas'] ?? null;
$emulador      = $_POST['emulador'] ?? null;
$idEmulador    = $_POST['idEmulador'] ?? null;
$semana        = $_POST['semana'] ?? null;
$tecnologia    = $_POST['tecnologia'] ?? null;
$filas         = $_POST['filas'] ?? [];

if (!$idContratista || !$emulador || !$semana || !$tecnologia || empty($filas)) {
    echo json_encode([
        'ok' => false,
        'error' => 'Parámetros incompletos'
    ]);
    exit;
}

// Zona horaria
date_default_timezone_set('America/Mexico_City');

try {
    $nomina = new Nomina();

    // ===============================
    // OBTENER NOMBRE DEL CONTRATISTA
    // ===============================
    $nombreContratista = $nomina->obtenerNombreContratistaPorId((int)$idContratista);

    if (!$nombreContratista) {
        throw new Exception("No se encontró el nombre del contratista");
    }

    // ===============================
    // LIMPIAR NOMBRE (SIN ESPACIOS NI ACENTOS)
    // ===============================
    $nombreLimpio = strtoupper($nombreContratista);

    $nombreLimpio = strtr($nombreLimpio, [
        'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U',
        'Ñ'=>'N'
    ]);

    $nombreLimpio = preg_replace('/\s+/', '', $nombreLimpio);

    // ===============================
    // FECHA FORMATO ddmmyy
    // ===============================
    $fecha = date('dmy');

    // ===============================
    // RANDOM DE 3 DIGITOS
    // ===============================
    $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

    // ===============================
    // FOLIO PACO FINAL
    // ===============================
    $folioPaco = strtoupper($emulador) . '_' . $semana . '_' . $nombreLimpio . '_' . $fecha . '_' . $random;

    // ===============================
    // INSERTAR
    // ===============================
    if ($idEmulador == 2 && strtoupper($tecnologia) === 'COBRE') {

        $nomina->insertarFoliosPacoCobre(
            $folioPaco,
            $idContratista,
            $idEmulador,
            $semana,
            $tecnologia,
            $filas
        );

    } else {

        $nomina->insertarFoliosPaco(
            $folioPaco,
            $idContratista,
            $idEmulador,
            $semana,
            $tecnologia,
            $filas
        );
    }

    echo json_encode([
        'ok' => true
    ]);

} catch (Exception $e) {

    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage()
    ]);
}