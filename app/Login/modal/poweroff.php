<?php
date_default_timezone_set('America/Monterrey');

// Si la sesión ya está activa, destrúyela
if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION = [];
    session_destroy();
}

// Redirige al login
header("Location: ../../index.html");
exit;
?>
