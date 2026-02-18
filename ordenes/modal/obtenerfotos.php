<?php
// Iniciar buffer para capturar cualquier salida accidental
ob_start();

// Errores solo en log
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassEmuladores.php");
header('Content-Type: application/json; charset=utf-8');

// Comprobar folio
if (empty($_POST['folio'])) {
    echo json_encode(['error' => 'Folio no proporcionado']);
    exit;
}

$Folio_Pisa = $_POST['folio'];
$response = [];
$debug = [];

try {
    $modal = new Contratistas();
    $Fotos = $modal->getFotos($Folio_Pisa);

    $debug['Fotos_resultado'] = $Fotos;

    // Inicializar variables
    $Foto_Ont = $Foto_Casa_Cliente = $No_Serie_ONT = $Foto_Puerto = $Foto_INE = '';

    // Base URL para todas las fotos
    $baseURL = "https://api.ed-intra.com/";

    // Asignar valores directamente sin foreach
    $Foto_Ont = isset($Fotos['Foto_Ont']) && $Fotos['Foto_Ont'] !== null ? ltrim($Fotos['Foto_Ont'], './') : '';
    $Foto_Casa_Cliente = isset($Fotos['Foto_Casa_Cliente']) && $Fotos['Foto_Casa_Cliente'] !== null ? ltrim($Fotos['Foto_Casa_Cliente'], './') : '';
    $No_Serie_ONT = isset($Fotos['No_Serie_ONT']) && $Fotos['No_Serie_ONT'] !== null ? ltrim($Fotos['No_Serie_ONT'], './') : '';
    $Foto_Puerto = isset($Fotos['Foto_Puerto']) && $Fotos['Foto_Puerto'] !== null ? ltrim($Fotos['Foto_Puerto'], './') : '';
    $Foto_INE = isset($Fotos['Foto_INE']) && $Fotos['Foto_INE'] !== null ? ltrim($Fotos['Foto_INE'], './') : '';

    // Construir URLs finales
    $response['Foto_Ont'] = $Foto_Ont ? $baseURL . $Foto_Ont : '';
    $response['Foto_Casa_Cliente'] = $Foto_Casa_Cliente ? $baseURL . $Foto_Casa_Cliente : '';
    $response['No_Serie_ONT'] = $No_Serie_ONT ? $baseURL . $No_Serie_ONT : '';
    $response['Foto_Puerto'] = $Foto_Puerto ? $baseURL . $Foto_Puerto : '';
    $response['Foto_INE'] = $Foto_INE ? $baseURL . $Foto_INE : '';

    // Agregar debug para inspección en JS
    $debug['folio_recibido'] = $Folio_Pisa;
    $debug['Foto_Ont_original'] = $Fotos['Foto_Ont'] ?? '';
    $debug['Foto_Casa_Cliente_original'] = $Fotos['Foto_Casa_Cliente'] ?? '';
    $debug['No_Serie_ONT_original'] = $Fotos['No_Serie_ONT'] ?? '';
    $debug['Foto_Puerto_original'] = $Fotos['Foto_Puerto'] ?? '';
    $debug['Foto_INE_original'] = $Fotos['Foto_INE'] ?? '';
    $response['debug'] = $debug;

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

// Limpiar cualquier salida accidental
ob_end_clean();
echo json_encode($response);
exit;