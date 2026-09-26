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

// Capturamos los valores puros del POST
$postFecha1 = $_POST['Fecha1'] ?? '';
$postFecha2 = $_POST['Fecha2'] ?? '';

@$Division = $_POST['Division'];
@$Area = $_POST['Area'];
@$Cope = $_POST['Cope'];

$mes_anterior = date('Y-m-d', strtotime('-3 month'));

if (!empty($postFecha1) && !empty($postFecha2)) {
    $Fecha1 = $postFecha1 . " 00:00:00";
    $Fecha2 = $postFecha2 . " 23:59:59";
} else {
    $Fecha1 = $mes_anterior . " 00:00:00";
    $Fecha2 = date('Y-m-d') . " 23:59:59";
}

$index = new Index();
$consultas = new ClassConsultasProduccion();

$TotalProduccion = 0;
$TotalSinNavegacion = 0;

// SOLO SE EJECUTA SI EL USUARIO SELECCIONÓ Y ENVIÓ FECHAS
if (!empty($postFecha1) && !empty($postFecha2)) {

    // 1. Conteo de producción (utiliza el rango con horas)
    $qrProduccion = $consultas->CountProduccionPorFechas($Fecha1, $Fecha2);
    if (is_array($qrProduccion) && !empty($qrProduccion)) {
        foreach ($qrProduccion as $row) {
            if (isset($row['TOTAL'])) {
                $TotalProduccion = (int)$row['TOTAL'];
            }
        }
    }

    // 2. Conteo de 'Sin Navegación' (la función retorna un entero directamente)
    $TotalSinNavegacion = (int)$consultas->CountSinNavegacionPorFechas($postFecha1, $postFecha2);

    // 3. Procesar registros
    $registros = $consultas->GetProduccionPorFechas($Fecha1, $Fecha2);
    if (!empty($registros)) {
        $telefonos = array_column($registros, 'TELEFONO');
        $mapEciDetalle = $consultas->GetDetalleEciPorTelefonos($telefonos);

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

        $existentesEnAlmacen = $consultas->GetNumSeriesEnAlmacen($seriesABuscar);
        $mapExistentes = array_flip($existentesEnAlmacen);

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
    <!-- Producción acumulada -->
    <div class="col-kpi-5 mb-4">
        <div class="kpi-card kpi-card-success">
            <div class="kpi-card-body">
                <div class="kpi-card-icon"><i class="fas fa-database"></i></div>
                <div class="kpi-card-label">Producción acumulada</div>
                <div class="kpi-card-number"><?php echo number_format($ProduccionAcumulada); ?></div>
            </div>
        </div>
    </div>

    <!-- Total Producción (por fechas) -->
    <div class="col-kpi-5 mb-4">
        <a href="TablaProduccionFechas.php?Fecha1=<?php echo urlencode($postFecha1); ?>&Fecha2=<?php echo urlencode($postFecha2); ?>" class="kpi-card-link">
            <div class="kpi-card kpi-card-info">
                <div class="kpi-card-body">
                    <div class="kpi-card-icon"><i class="fas fa-star"></i></div>
                    <div class="kpi-card-label">Total Producción (por fechas)</div>
                    <div class="kpi-card-number"><?php echo number_format($TotalProduccion); ?></div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total No Navegación -->
    <div class="col-kpi-5 mb-4">
        <a href="TablaProduccionFechas.php?Fecha1=<?php echo urlencode($postFecha1); ?>&Fecha2=<?php echo urlencode($postFecha2); ?>&tipo=no_navegacion" class="kpi-card-link">
            <div class="kpi-card kpi-card-info">
                <div class="kpi-card-body">
                    <div class="kpi-card-icon"><i class="fas fa-star"></i></div>
                    <div class="kpi-card-label">Total No Navegacion</div>
                    <div class="kpi-card-number"><?php echo number_format($TotalSinNavegacion); ?></div>
                </div>
            </div>
        </a>
    </div>
</div>