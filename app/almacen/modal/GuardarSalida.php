<?php
// Este archivo responde JSON al formulario AJAX; los errores PHP no deben
// mezclarse con la respuesta que interpreta SweetAlert.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

date_default_timezone_set('America/Monterrey');
include_once("../controller/ClassSalidas.php");

// Sanitization function
function sanitizeInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// El folio enviado por el navegador es solo visual. El definitivo se obtiene
// bajo bloqueo justo antes de insertar, para impedir folios duplicados.
$folioProvisional = isset($_POST['folio']) ? (int)$_POST['folio'] : 0;
$FK_Contratista_Salida_Det = $_POST['FK_Contratista_Salida'] ?? null;
$FK_Tecnico_Salida_Det = $_POST['FK_Tecnico_Salida_Det'] ?? null;
$Descripcion_Salida_Det = sanitizeInput($_POST['Descripcion_Salida_Det'] ?? '');
$numeros_series = $_POST['numeros_series'] ?? [];
$FK_Material_Salidas_Contratistas = sanitizeInput($_POST['FK_Material_Salidas_Contratistas'] ?? '');
$Fecha_Salida_Det = date('Y-m-d H:i:s');
$Ubicacion = 'SIN INSTALAR';

// Validate required fields
if (!$FK_Contratista_Salida_Det || !$FK_Tecnico_Salida_Det || empty($numeros_series)) {
    jsonResponse([
        'success' => false,
        'message' => 'Faltan datos obligatorios. Verifique e intente nuevamente.'
    ], 422);
}

try {
    $modal = new Salidas();
    $folio = $modal->FolioSeguroSalida();
    if ($folio === false) {
        throw new Exception('No fue posible reservar un folio de salida.');
    }
    $success = true;

    // Process each serial number
    foreach ($numeros_series as $numero_serie) {
        $numero_serie = sanitizeInput($numero_serie);
        $insert = $modal->InsertSalidaTecnico(
            $folio,
            $FK_Contratista_Salida_Det,
            $FK_Tecnico_Salida_Det,
            $Fecha_Salida_Det,
            $numero_serie,
            $Descripcion_Salida_Det,
            $FK_Material_Salidas_Contratistas
        );

        $update = $modal->EstatusSalidaTecnico($numero_serie, $Ubicacion);

        if (!$insert || !$update) {
            $success = false;
            break;
        }
    }

    // Insert summary record
    if ($success) {
        $insert2 = $modal->InsertSalidaTecnicoT(
            $FK_Contratista_Salida_Det,
            $FK_Tecnico_Salida_Det,
            count($numeros_series),
            $Fecha_Salida_Det,
            $folio
        );

        if ($insert2) {
            jsonResponse([
                'success' => true,
                'folio_real' => $folio,
                'cambio_folio' => $folioProvisional !== $folio,
                'redirect' => 'salidas.php'
            ]);
        } else {
            throw new Exception("Error al registrar la transferencia.");
        }
    } else {
        throw new Exception("Error al procesar los números de serie.");
    }
} catch (Exception $e) {
    error_log('[newsalida2] Error: ' . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => $e->getMessage()
    ], 500);
}
