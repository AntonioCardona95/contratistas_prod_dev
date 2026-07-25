<?php
session_start();
require_once(__DIR__ . '/controller/ClassNotificaciones.php');

header('Content-Type: application/json');

$notificaciones = new Notificaciones();
$idUsuario = $_SESSION['idContratistas'];

// MARCAR COMO LEIDO (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'marcar_leido') {
    $idNotificacion = (int)$_POST['id'];
    $notificaciones->MarcarComoLeida($idNotificacion);
    echo json_encode(['success' => true]);
    exit;
}

// CARGA INICIAL DEL DROPDOWN, SIN TOAST (GET)
if (isset($_GET['solo_dropdown'])) {
    $recientes = $notificaciones->ObtenerRecientes($idUsuario, 10);
    $noLeidas = $notificaciones->ContarNoLeidas($idUsuario);

    echo json_encode([
        'recientes' => $recientes,
        'count_total' => $noLeidas
    ]);
    exit;
}

// POLLING NORMAL, CON TOAST PARA LO NUEVO (GET)
$desdeId = isset($_GET['desde_id']) ? (int)$_GET['desde_id'] : 0;

$noLeidas = $notificaciones->ContarNoLeidas($idUsuario);
$nuevas = $notificaciones->ObtenerNuevas($idUsuario, $desdeId);

echo json_encode([
    'count_total' => $noLeidas,
    'nuevas' => $nuevas
]);