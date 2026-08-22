<?php
// IMPORTAR LA CLASE DE NAVEGACIÓN
require_once(__DIR__ . '/controller/ClassNavegacion.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CONFIGURAR LA RESPUESTA JSON
header('Content-Type: application/json');

// OBTENER EL ID DEL USUARIO DESDE LA SESIÓN
try {
    if (empty($_SESSION['idContratistas'])) {
        echo json_encode(['error' => 'Usuario no autenticado.']);
        exit;
    }

    $idContratistas = $_SESSION['idContratistas'];
    $fecha_inicio = $_GET['fecha_inicio'] ?? null;
    $fecha_fin = $_GET['fecha_fin'] ?? null;

    $navegacion = new Navegacion();
    $resultado = $navegacion->FoliosContratista($idContratistas, $fecha_inicio, $fecha_fin);

    echo json_encode($resultado);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}