<?php
/**
 * Archivo de configuración y optimizaciones del módulo
 * AUDITORÍAS COORDIAPP - VERSION 2.0 OPTIMIZADA
 * Mejoras: comprobaciones para session, headers y directorios de logs.
 */

// Evitar ejecuciones parciales si ya se enviaron cabeceras
if (php_sapi_name() !== 'cli') {
    // Iniciar sesión si aún no está iniciada
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
}

// Helper para enviar header sólo si no se han enviado headers
function safe_header($header) {
    if (!headers_sent()) {
        header($header);
    }
}

// Security headers
safe_header('X-Content-Type-Options: nosniff');
safe_header('X-Frame-Options: DENY');
safe_header('X-XSS-Protection: 1; mode=block');

// Configuración de cache para archivos estáticos (usa path sin query string)
if (isset($_SERVER['REQUEST_URI'])) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $extension = pathinfo($path, PATHINFO_EXTENSION);

    $longCache = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'woff', 'woff2', 'ttf', 'eot'];
    if (in_array(strtolower($extension), $longCache, true)) {
        safe_header('Cache-Control: public, max-age=2592000'); // 30 días
        safe_header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT');
    } else {
        safe_header('Cache-Control: no-cache, must-revalidate');
        safe_header('Expires: 0');
    }
}

// Configuración de compresión GZIP (si está disponible y no hay compresión ya activa)
if (php_sapi_name() !== 'cli' && extension_loaded('zlib') && !ini_get('zlib.output_compression') && !headers_sent() && !ob_get_level()) {
    @ob_start('ob_gzhandler');
}

// Configuración de timezone (si no está definida en php.ini)
@date_default_timezone_set('America/Monterrey');

// Preparar directorio de logs y fichero por defecto
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$errorLogFile = $logDir . '/error.log';

// Configuración de errores para producción/desarrollo
if (defined('ENVIRONMENT')) {
    if (ENVIRONMENT === 'development') {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
    } else {
        error_reporting(0);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        ini_set('error_log', $errorLogFile);
    }
} else {
    // Por defecto, modo desarrollo
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    ini_set('log_errors', '1');
    ini_set('error_log', $errorLogFile);
}

// Funciones auxiliares optimizadas
class AuditoriaHelpers
{
    /**
     * Sanitiza entrada de datos
     */
    public static function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida formato de fecha
     */
    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Genera token CSRF
     */
    public static function generateCSRFToken()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                // Fallback
                $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
            }
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida token CSRF
     */
    public static function validateCSRFToken($token)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
    }

    /**
     * Log de actividades
     */
    public static function logActivity($action, $details = '')
    {
        $logFile = __DIR__ . '/logs/activity.log';
        $timestamp = date('Y-m-d H:i:s');
        $user = isset($_SESSION) && isset($_SESSION['Usuario']) ? $_SESSION['Usuario'] : 'Anonymous';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

        $logEntry = "[{$timestamp}] User: {$user} | IP: {$ip} | Action: {$action} | Details: {$details}" . PHP_EOL;

        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Respuesta JSON optimizada
     */
    public static function jsonResponse($data, $statusCode = 200)
    {
        if (!headers_sent()) {
            http_response_code((int)$statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            $json = json_encode(['error' => 'Failed to encode response']);
        }
        echo $json;
        exit;
    }
}

// Configuración de limpieza de sesión (timeout)
if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}
if (!empty($_SESSION['last_activity'])) {
    // Timeout de sesión: 2 horas
    if (time() - (int)$_SESSION['last_activity'] > 7200) {
        // limpiar sesión de forma segura
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }
        @session_destroy();
        // redireccionar si no es CLI y no se ha enviado output
        if (php_sapi_name() !== 'cli' && !headers_sent()) {
            header('Location: ../Login/modal/poweroff.php');
            exit;
        }
    }
}
$_SESSION['last_activity'] = time();

// Constantes de configuración (definir sólo si no existen)
if (!defined('DB_POOL_SIZE')) {
    define('DB_POOL_SIZE', 5); // Tamaño del pool de conexiones
}
if (!defined('DB_TIMEOUT')) {
    define('DB_TIMEOUT', 30);  // Timeout de consultas en segundos
}
if (!defined('CACHE_DURATION')) {
    define('CACHE_DURATION', 300); // Cache de 5 minutos para consultas frecuentes
}

if (!defined('MODULE_VERSION')) {
    define('MODULE_VERSION', '2.0.0');
}
if (!defined('MODULE_NAME')) {
    define('MODULE_NAME', 'Auditorías CoordiApp');
}
if (!defined('MAX_RECORDS_PER_PAGE')) {
    define('MAX_RECORDS_PER_PAGE', 100);
}
if (!defined('DEFAULT_RECORDS_PER_PAGE')) {
    define('DEFAULT_RECORDS_PER_PAGE', 25);
}

// Fin del archivo
?>