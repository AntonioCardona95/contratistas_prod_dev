<?php
// Validar que la sesión no esté activa Y que los encabezados no se hayan enviado aún
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@set_time_limit(0);
@ini_set('memory_limit', '512M');

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/..');
}

require_once("../controller/ClassConsultasProduccion.php");

$fechaInicio = '';
$fechaFin    = '';

$consultas = new ClassConsultasProduccion();
$resultado = $consultas->SincronizarProduccionDiariaPorRango($fechaInicio, $fechaFin);

echo "Encontrados: {$resultado['total']} | Procesados: {$resultado['procesados']}\n";