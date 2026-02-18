<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Monterrey');
include_once("../controller/ClassOps.php");

// Sanitization function
function sanitizeInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Alert function
function showAlert($message, $redirect = null)
{
    echo "<script>
        alert('$message');
        window.onload = function() {
            " . ($redirect ? "window.location.href = '$redirect';" : "") . "
        };
    </script>";
}

// Input validation
$folio = $_POST['folio'];
$FK_Contratista_Salida_Det = $_POST['FK_Contratista_Salida'];
$FK_Tecnico_Salida_Det = $_POST['FK_Tecnico_Salida_Det'];
$Descripcion_Salida_Det = sanitizeInput($_POST['Descripcion_Salida_Det'] ?? '');
$numeros_series = $_POST['numeros_series'] ?? [];
$FK_Material_Salidas_Contratistas = sanitizeInput($_POST['FK_Material_Salidas_Contratistas'] ?? '');
$Fecha_Salida_Det = date('Y-m-d H:i:s');
$Ubicacion = 'SIN INSTALAR';

// Validate required fields
if (!$folio || !$FK_Contratista_Salida_Det || !$FK_Tecnico_Salida_Det || empty($numeros_series)) {
    showAlert("Faltan datos obligatorios. Verifique e intente nuevamente.");
    exit;
}

try {
    $modal = new Ops();
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
            showAlert("Transferencia Exitosa!", "../salidas.php");
        } else {
            throw new Exception("Error al registrar la transferencia.");
        }
    } else {
        throw new Exception("Error al procesar los números de serie.");
    }
} catch (Exception $e) {
    showAlert("Error: " . $e->getMessage());
}
