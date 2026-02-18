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
$Folio_Traspaso_T = $_POST['folio'];
$fk_Contratista_Traspaso = $_POST['FK_Contratista_Salida'];
$fk_Tecnico_Origen_Traspaso_T = $_POST['FK_Tecnico_Salida_Det'];
$fk_Tecnico_Destino_Traspaso_T = $_POST['FK_Tecnico_Salida_Destino'];
$Descripcion_Salida_Det = sanitizeInput($_POST['Descripcion_Salida_Det'] ?? '');
$numeros_series = $_POST['numeros_series'] ?? [];
$Fecha_Traspaso = date('Y-m-d H:i:s');

// Validate required fields
if (!$Folio_Traspaso_T || !$fk_Contratista_Traspaso || !$fk_Tecnico_Origen_Traspaso_T || empty($numeros_series)) {
    showAlert("Faltan datos obligatorios. Verifique e intente nuevamente.");
    exit;
}

try {
    $modal = new Ops();
    $success = true;

    // Process each serial number
    foreach ($numeros_series as $numero_serie) {
        $numero_serie = sanitizeInput($numero_serie);
        $insert = $modal->InsertTraspasoTecnico(
            $Folio_Traspaso_T,
            $fk_Tecnico_Origen_Traspaso_T,
            $fk_Tecnico_Destino_Traspaso_T,
            $fk_Contratista_Traspaso,
            $numero_serie,
            $Fecha_Traspaso
        );

        $update = $modal->UpdateTraspasoTecnico($fk_Tecnico_Origen_Traspaso_T,$fk_Tecnico_Destino_Traspaso_T,$numero_serie);

        if (!$insert || !$update) {
            $success = false;
            break;
        }
    }

    // Insert summary record
    if ($success) {
        $insert2 = $modal->InsertTraspasoTecnicoT(
            $Folio_Traspaso_T,
            $fk_Tecnico_Origen_Traspaso_T,
            $fk_Tecnico_Destino_Traspaso_T,
            $fk_Contratista_Traspaso,
            count($numeros_series),
            $Fecha_Traspaso,
            $Descripcion_Salida_Det
        );

        if ($insert2) {
            showAlert("Transferencia Exitosa!", "../traspasos.php");
        } else {
            throw new Exception("Error al registrar la transferencia.");
        }
    } else {
        throw new Exception("Error al procesar los números de serie.");
    }
} catch (Exception $e) {
    showAlert("Error: " . $e->getMessage());
}
