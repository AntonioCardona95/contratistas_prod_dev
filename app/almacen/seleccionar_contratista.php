<?php
require_once __DIR__ . '/../config/ContratistaContexto.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['idContratistas'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'mensaje' => 'Sesión no válida.']);
    exit;
}

$idContratista = filter_input(INPUT_POST, 'idContratista', FILTER_VALIDATE_INT);
if ($idContratista === false || $idContratista === null || !cambiarContratistaContexto($idContratista)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'mensaje' => 'No está autorizado para seleccionar ese contratista.']);
    exit;
}

echo json_encode(['ok' => true]);