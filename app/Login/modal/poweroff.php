<?php
// Asegúrate de que no haya espacios ni saltos de línea antes de esta etiqueta

// Configuración de zona horaria
date_default_timezone_set('America/Monterrey');

// Inicia la sesión
session_start();

// Limpia variables de sesión
$_SESSION = [];

// Destruye la sesión si está activa
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Redirige antes de enviar cualquier salida
header("Location: ../../index.html");
exit; // Importante para detener la ejecución después de redirigir
?>
