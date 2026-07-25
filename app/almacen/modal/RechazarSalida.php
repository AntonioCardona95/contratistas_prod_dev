<?php
// modal/RechazarSalida.php
session_start();
require_once("../controller/ClassOps.php");
require_once("../controller/ClassNotificaciones.php");

header('Content-Type: application/json');

$idContratistas = $_SESSION['idContratistas'];
$folio = (int)($_POST['folio'] ?? 0);

if (!$folio) {
    echo json_encode(['success' => false, 'message' => 'Folio inválido.']);
    exit;
}

$modal = new Ops();
$resultado = $modal->RechazarSalida($folio, $idContratistas);

if ($resultado['success']) {
    $notificaciones = new Notificaciones();
    $mensaje = "El contratista rechazó la salida de material folio #{$folio}.";
    $notificaciones->Insertar(
        $idContratistas,
        $resultado['idUsuarioNass'],
        'salida_rechazada',
        $mensaje,
        'Salidas_Rechazadas.php?folio=' . $folio,
        $folio
    );
}

echo json_encode($resultado);