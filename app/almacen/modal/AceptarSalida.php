<?php
// modal/AceptarSalida.php
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
$resultado = $modal->AceptarSalida($folio, $idContratistas);

if ($resultado['success']) {
    // NOTIFICAR DE REGRESO A NASS
    $notificaciones = new Notificaciones();
    $mensaje = "El contratista aceptó la salida de material folio #{$folio}.";
    $notificaciones->Insertar(
        $idContratistas,               // id_emisor (el contratista)
        $resultado['idUsuarioNass'],   // id_receptor (el usuario de NASS que la creó)
        'salida_aceptada',
        $mensaje,
        'Salidas.php?folio=' . $folio, // url_destino DENTRO DE NASS
        $folio
    );
}

echo json_encode($resultado);