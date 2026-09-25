<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassIndex.php");
require_once(__DIR__ . '/../../config/ContratistaContexto.php');
require_once("../controller/ClassConsultasProduccion.php");

$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = obtenerIdContratistaContexto();

$Fecha1 = $_POST['Fecha1'] ?? '';
$Fecha2 = $_POST['Fecha2'] ?? '';
@$Division = $_POST['Division'];
@$Area = $_POST['Area'];
@$Cope = $_POST['Cope'];

$mes_anterior = date('Y-m-d', strtotime('-3 month'));

if (!empty($Fecha1) && !empty($Fecha2)) {
    $Fecha1 = $_POST['Fecha1'] . " 00:00:00";
    $Fecha2 = $_POST['Fecha2'] . " 23:59:59";
} else {
    $Fecha1 = $mes_anterior . " 00:00:00";
    $Fecha2 = date('Y-m-d') . " 23:59:59";
}

$index = new Index();
$consultas = new ClassConsultasProduccion();

$TotalProduccion = 0;
$TotalSinNavegacion = 0; // Se inicializa en 0

// SOLO SE EJECUTA SI EL USUARIO SELECCIONÓ Y ENVIÓ FECHAS
if (!empty($_POST['Fecha1']) && !empty($_POST['Fecha2'])) {
    // 1. Obtener conteo de producción evaluada en el rango de fechas
    $qrProduccion = $consultas->CountProduccionPorFechas($Fecha1, $Fecha2);
    if (is_array($qrProduccion) && !empty($qrProduccion)) {
        foreach ($qrProduccion as $row) {
            if (isset($row['TOTAL'])) {
                $TotalProduccion = (int)$row['TOTAL'];
            }
        }
    }

    // 2. Conteo de 'Sin Navegación' en el rango de fechas seleccionadas
    $qrSinNavegacion = $consultas->CountSinNavegacionPorFechas($Fecha1, $Fecha2);
    if (is_array($qrSinNavegacion) && !empty($qrSinNavegacion)) {
        foreach ($qrSinNavegacion as $row) {
            if (isset($row['TOTAL'])) {
                $TotalSinNavegacion = (int)$row['TOTAL'];
            }
        }
    } else {
        $TotalSinNavegacion = (int)$qrSinNavegacion;
    }

    // 3. Obtener los registros de producción del periodo y procesar
    $registros = $consultas->GetProduccionPorFechas($Fecha1, $Fecha2);

    if (!empty($registros)) {
        // Paso A: Mapear datos detallados de ESCI por teléfono
        $telefonos = array_column($registros, 'TELEFONO');
        $mapEciDetalle = $consultas->GetDetalleEciPorTelefonos($telefonos);

        // Paso B: Recolectar números de serie válidos (Producción + ESCI) para verificar existencias en Almacén
        $seriesABuscar = [];
        foreach ($registros as $reg) {
            $tel = trim($reg['TELEFONO'] ?? '');
            $serieProd = trim($reg['Num__Serie'] ?? '');
            
            $eciInfo  = $mapEciDetalle[$tel] ?? null;
            $serieEci = $eciInfo['M_SERIE'] ?? null;

            if (!$consultas->esSerieInvalida($serieProd)) {
                $seriesABuscar[] = $serieProd;
            }
            if ($serieEci !== null && !$consultas->esSerieInvalida($serieEci)) {
                $seriesABuscar[] = $serieEci;
            }
        }

        // Paso C: Consultar existencia en almacén
        $existentesEnAlmacen = $consultas->GetNumSeriesEnAlmacen($seriesABuscar);
        $mapExistentes = array_flip($existentesEnAlmacen);

        // Paso D: Procesar inserción/actualización de producción, navegación y señalamientos
        $consultas->InsertBatchProduccionYSenalamiento($registros, $mapExistentes, $mapEciDetalle);
    }
}

// --- KPI Consultas Globales ---
$ProduccionAcumulada = 0;
$qrProduccionTotal = $consultas->CountProduccionTotal();
if (is_array($qrProduccionTotal) && !empty($qrProduccionTotal)) {
    foreach ($qrProduccionTotal as $row) {
        if (isset($row['TOTAL'])) {
            $ProduccionAcumulada = (int)$row['TOTAL'];
        }
    }
}
?>

<link rel="stylesheet" href="css/kpis-produccion.css">

<div class="row kpi-cards-row">
    <div class="col-kpi-5 mb-4">
        <div class="kpi-card kpi-card-success">
            <div class="kpi-card-body">
                <div class="kpi-card-icon"><i class="fas fa-database"></i></div>
                <div class="kpi-card-label">Producción acumulada</div>
                <div class="kpi-card-number"><?php echo number_format($ProduccionAcumulada); ?></div>
            </div>
        </div>
    </div>

    <div class="col-kpi-5 mb-4">
        <div class="kpi-card kpi-card-info">
            <div class="kpi-card-body">
                <div class="kpi-card-icon"><i class="fas fa-star"></i></div>
                <div class="kpi-card-label">Total Producción (por fechas)</div>
                <div class="kpi-card-number"><?php echo number_format($TotalProduccion); ?></div>
            </div>
        </div>
    </div>


<div class="col-kpi-5 mb-4">
    <a href="TablaProduccion.php?Fecha1=<?php echo urlencode($_POST['Fecha1'] ?? ''); ?>&Fecha2=<?php echo urlencode($_POST['Fecha2'] ?? ''); ?>" 
       class="kpi-card-link">
        <div class="kpi-card kpi-card-info">
            <div class="kpi-card-body">
                <div class="kpi-card-icon"><i class="fas fa-star"></i></div>
                <div class="kpi-card-label">Total Producción (por fechas)</div>
                <div class="kpi-card-number"><?php echo number_format($TotalProduccion); ?></div>
            </div>
        </div>
    </a>
</div>


    
</div>