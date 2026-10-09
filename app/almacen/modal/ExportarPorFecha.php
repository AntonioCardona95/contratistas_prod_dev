<?php
// Forzar muestra de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../controller/ClassConsultasProduccion.php");

echo "<h1>🔍 DEPURACIÓN DE EXPORTACIÓN</h1>";

// 1. Mostrar Variables POST recibidas
echo "<h3>1. Parámetros POST recibidos:</h3>";
echo "<pre>";
var_dump($_POST);
echo "</pre>";

$fechaInicio   = $_POST['fecha_inicio'] ?? '';
$fechaFin      = $_POST['fecha_fin'] ?? '';
$idContratista = !empty($_POST['contratista']) ? $_POST['contratista'] : null;
$tipoTarea     = !empty($_POST['tipo_tarea']) ? $_POST['tipo_tarea'] : null;
$area          = !empty($_POST['area']) ? $_POST['area'] : null;
$division      = !empty($_POST['division']) ? $_POST['division'] : null;
$cope          = !empty($_POST['cope']) ? $_POST['cope'] : null;

// 2. Ejecutar consulta
$modal = new ClassConsultasProduccion();

try {
    $filas = $modal->ExportarFechas($fechaInicio, $fechaFin, $idContratista, $tipoTarea, $area, $division, $cope);
    
    echo "<h3>3. Resultado de la Consulta:</h3>";
    if (is_array($filas)) {
        echo "<b>Total de filas devueltas:</b> " . count($filas) . "<br><br>";
        if (!empty($filas)) {
            echo "<b>Muestra de la primera fila devuelta:</b>";
            echo "<pre>";
            print_r($filas[0]);
            echo "</pre>";
        } else {
            echo "<b style='color:red;'>⚠️ La consulta se ejecutó sin errores pero devolvió 0 registros.</b>";
        }
    } else {
        echo "<b style='color:red;'>⚠️ La consulta no devolvió un array. Respuesta:</b> ";
        var_dump($filas);
    }
} catch (Exception $e) {
    echo "<h3 style='color:red;'>❌ EXCEPCIÓN / ERROR CAPTURADO:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

// Detener la ejecución para que NO intente descargar el CSV y te deje ver todo en pantalla
die();