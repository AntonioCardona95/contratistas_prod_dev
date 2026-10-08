<?php
// Configurar encabezado explícito para JSON y UTF-8
header('Content-Type: application/json; charset=utf-8');

// Desactivar despliegue de errores HTML para no romper el JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    require_once("../controller/ClassConsultasProduccion.php");
    $obj = new ClassConsultasProduccion();

    $action = $_POST['action'] ?? '';

    if ($action === 'getDivisionByArea') {
        $idArea = $_POST['idArea'] ?? 0;

        // Validar que idArea no venga en 0 o vacío
        if (empty($idArea)) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'El idArea recibido está vacío o es 0',
                'post_recibido' => $_POST
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Ejecutar método del controlador
        $division = $obj->obtenerDivisionPorArea($idArea);

        // Diagnóstico si la BD responde null o false
        if ($division === null || $division === false) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'La función obtenerDivisionPorArea devolvió null o false. Revisa la consulta SQL o el idArea en BD.',
                'idArea' => $idArea
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Diagnóstico si la respuesta viene vacía
        if (empty($division)) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'No se encontraron divisiones asociadas al idArea proporcionado.',
                'idArea' => $idArea,
                'resultado_raw' => $division
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Intentar codificar JSON y validar si hay error de UTF-8
        $json = json_encode($division, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'Error de codificación JSON (posible problema de caracteres UTF-8 en la BD): ' . json_last_error_msg(),
                'idArea' => $idArea
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo $json;
        exit;
    }

    if ($action === 'getCopesByArea') {
        $idArea = $_POST['idArea'] ?? 0;
        
        if (empty($idArea)) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'El idArea recibido está vacío o es 0 para COPEs',
                'post_recibido' => $_POST
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $copes = $obj->obtenerCopesPorArea($idArea);
        
        if (empty($copes)) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'No se encontraron COPEs para el idArea proporcionado.',
                'idArea' => $idArea
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode($copes, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Si la acción no coincide con ninguna
    echo json_encode([
        'error' => true,
        'mensaje' => 'Acción no válida o no especificada.',
        'action_recibida' => $action
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    // Capturar excepciones o fatal errors
    echo json_encode([
        'error' => true,
        'mensaje' => 'Excepción en el servidor PHP: ' . $e->getMessage(),
        'archivo' => $e->getFile(),
        'linea' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}