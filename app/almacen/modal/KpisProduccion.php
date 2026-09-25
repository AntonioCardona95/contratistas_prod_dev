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

    // 2. Obtener los registros de producción del periodo
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

// --- KPI Consultas ---
$ProduccionAcumulada = 0;
$qrProduccionTotal = $consultas->CountProduccionTotal();
if (is_array($qrProduccionTotal) && !empty($qrProduccionTotal)) {
    foreach ($qrProduccionTotal as $row) {
        if (isset($row['TOTAL'])) {
            $ProduccionAcumulada = (int)$row['TOTAL'];
        }
    }
}

// Conteo corregido: consulta el total de 'SIN NAVEGACION' en la tabla de navegación según el periodo
$TotalSinNavegacion = $consultas->CountSinNavegacionPorFechas($Fecha1, $Fecha2);

$TotalDisponible = 0;
$qrDisponibles = $index->CountONTDisponibles($idContratistas, $Fecha1, $Fecha2);
if (is_array($qrDisponibles)) {
    if (!empty($qrDisponibles)) {
        foreach ($qrDisponibles as $row) {
            if (isset($row['TOTAL'])) {
                $TotalDisponible = (int)$row['TOTAL'];
            }
        }
    }
} else {
    $TotalDisponible = (int)$qrDisponibles;
}

$TTotalDisponible = 0;
$qrDisponiblesT = $index->CountONTDisponiblesT($idContratistas);
if (is_array($qrDisponiblesT)) {
    if (!empty($qrDisponiblesT)) {
        foreach ($qrDisponiblesT as $row) {
            if (isset($row['TOTAL'])) {
                $TTotalDisponible = (int)$row['TOTAL'];
            }
        }
    }
} else {
    $TTotalDisponible = (int)$qrDisponiblesT;
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
        <a href="detalle_ont_sin_navegacion.php" class="kpi-card-link">
            <div class="kpi-card kpi-card-dark" style="background-color: #6c757d; color: #fff;">
                <div class="kpi-card-body">
                    <div class="kpi-card-icon"><i class="fas fa-wifi-slash"></i></div>
                    <div class="kpi-card-label">Sin navegación (en fecha)</div>
                    <div class="kpi-card-number"><?php echo number_format($TotalSinNavegacion); ?></div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-kpi-5 mb-4">
        <a href="detalle_ont_disponibles.php" data-detalle="total" class="kpi-card-link">
            <div class="kpi-card kpi-card-danger">
                <div class="kpi-card-body">
                    <div class="kpi-card-icon"><i class="fas fa-box-open"></i></div>
                    <div class="kpi-card-label">Números de serie totales sin asignar</div>
                    <div class="kpi-card-number"><?php echo number_format($TTotalDisponible); ?></div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-kpi-5 mb-4">
        <a href="detalle_ont_disponibles.php" data-detalle="fecha" class="kpi-card-link">
            <div class="kpi-card kpi-card-warning">
                <div class="kpi-card-body">
                    <div class="kpi-card-icon"><i class="fas fa-clock"></i></div>
                    <div class="kpi-card-label">Números de serie sin asignar (en fecha)</div>
                    <div class="kpi-card-number"><?php echo number_format($TotalDisponible); ?></div>
                </div>
            </div>
        </a>
    </div>
</div>