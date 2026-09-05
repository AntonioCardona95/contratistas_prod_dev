<?php

require_once __DIR__ . '/../controller/ClassTraspasos.php';
require_once __DIR__ . '/../../config/ContratistaContexto.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

try {
    requerirSesionAlmacen(true);
    $idContratista = obtenerIdContratistaContexto();
    $folio = filter_input(INPUT_POST, 'folio', FILTER_VALIDATE_INT);
    $idTecnicoOrigen = filter_input(INPUT_POST, 'FK_Tecnico_Salida_Det', FILTER_VALIDATE_INT);
    $idTecnicoDestino = filter_input(INPUT_POST, 'FK_Tecnico_Salida_Destino', FILTER_VALIDATE_INT);
    $comentarios = trim((string)($_POST['Descripcion_Salida_Det'] ?? ''));
    $series = $_POST['numeros_series'] ?? [];

    $series = array_values(array_unique(array_filter(array_map('trim', $series))));

    if ($idContratista <= 0 || !$folio || !$idTecnicoOrigen || !$idTecnicoDestino || empty($series)) {
        throw new RuntimeException('Faltan datos obligatorios para registrar el traspaso.');
    }

    if ($idTecnicoOrigen === $idTecnicoDestino) {
        throw new RuntimeException('El técnico origen y destino deben ser diferentes.');
    }

    $traspasos = new Traspasos();
    $seriesDisponibles = $traspasos->LoadSeriesTraspasos($idTecnicoOrigen, $idContratista);
    $permitidas = [];
    foreach ($seriesDisponibles as $serieDisponible) {
        $permitidas[(string)$serieDisponible['Num_Serie_Salida_Det']] = true;
    }

    foreach ($series as $serie) {
        if (!isset($permitidas[$serie])) {
            throw new RuntimeException('Una o más series ya no están disponibles para el técnico origen.');
        }
    }

    $fecha = date('Y-m-d H:i:s');
    foreach ($series as $serie) {
        if (!$traspasos->InsertTraspasoTecnico($folio, $idTecnicoOrigen, $idTecnicoDestino, $idContratista, $serie, $fecha)) {
            throw new RuntimeException('No fue posible registrar una de las series.');
        }

        if (!$traspasos->UpdateTraspasoTecnico($idTecnicoOrigen, $idTecnicoDestino, $serie)) {
            throw new RuntimeException('No fue posible actualizar la serie transferida.');
        }
    }

    if (!$traspasos->InsertTraspasoTecnicoT(
        $folio,
        $idTecnicoOrigen,
        $idTecnicoDestino,
        $idContratista,
        count($series),
        $fecha,
        $comentarios
    )) {
        throw new RuntimeException('No fue posible registrar el resumen del traspaso.');
    }

    echo json_encode(['ok' => true]);
} catch (Throwable $exception) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'mensaje' => $exception->getMessage()
    ]);
}
