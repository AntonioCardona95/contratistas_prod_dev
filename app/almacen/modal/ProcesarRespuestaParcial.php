<?php
session_start();
require_once("../controller/ClassOps.php");
require_once("../controller/ClassNotificaciones.php");

header('Content-Type: application/json');

$idContratistas = $_SESSION['idContratistas'];
$folio = (int)($_POST['folio'] ?? 0);
$seriesAceptadas = $_POST['seriesAceptadas'] ?? [];
if (is_string($seriesAceptadas)) {
    $seriesAceptadas = json_decode($seriesAceptadas, true) ?: [];
}
$seriesRechazadas = json_decode($_POST['seriesRechazadas'] ?? '[]', true) ?: [];
$ajustesCantidad = json_decode($_POST['ajustesCantidad'] ?? '[]', true) ?: [];

if (!$folio) {
    echo json_encode(['success' => false, 'message' => 'Folio inválido.']);
    exit;
}

$modal = new Ops();
$resultado = $modal->ProcesarRespuestaParcial($folio, $idContratistas, $seriesAceptadas, $seriesRechazadas, $ajustesCantidad);

if ($resultado['success']) {
    $huboRechazo = !empty($seriesRechazadas) || !empty(array_filter($ajustesCantidad, fn($a) => $a['cantidad_aceptada'] < $a['cantidad_solicitada']));
    $huboAceptado = !empty($seriesAceptadas) || !empty(array_filter($ajustesCantidad, fn($a) => $a['cantidad_aceptada'] > 0));

    if ($huboRechazo && $huboAceptado) {
        $tipo = 'salida_parcial';
        $mensaje = "El contratista acepto parcialmente la salida folio #{$folio}. Requiere correccion.";
        $url = 'Salidas_Parciales.php?folio=' . $folio;
    } elseif ($huboRechazo) {
        $tipo = 'salida_rechazada';
        $mensaje = "El contratista rechazo la salida de material folio #{$folio}.";
        $url = 'Salidas_Rechazadas.php?folio=' . $folio;
    } else {
        $tipo = 'salida_aceptada';
        $mensaje = "El contratista acepto la salida de material folio #{$folio}.";
        $url = 'Salidas.php?folio=' . $folio;
    }

    $notificaciones = new Notificaciones();
    $notificaciones->Insertar($idContratistas, $resultado['idUsuarioNass'], $tipo, $mensaje, $url, $folio);
}

echo json_encode($resultado);