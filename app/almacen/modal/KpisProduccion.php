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

// RECIBIMOS PARAMETROS
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

// --- Producción por fechas ---
$TotalProduccion = 0;
if (!empty($_POST['Fecha1']) && !empty($_POST['Fecha2'])) {
    $qrProduccion = $consultas->CountProduccionPorFechas($Fecha1, $Fecha2);
    if (is_array($qrProduccion) && !empty($qrProduccion)) {
        foreach ($qrProduccion as $row) {
            if (isset($row['TOTAL'])) {
                $TotalProduccion = (int)$row['TOTAL'];
            }
        }
    }

    // Traer registros de producción
    $registros = $consultas->GetProduccionPorFechas($Fecha1, $Fecha2);

    if (!empty($registros)) {
        // Step 1: Detectar registros que no tienen número de serie válido
        $telefonosParaEci = [];
        foreach ($registros as $key => $reg) {
            if ($consultas->esSerieInvalida($reg['Num__Serie'] ?? '')) {
                if (!empty($reg['TELEFONO'])) {
                    $telefonosParaEci[] = $reg['TELEFONO'];
                }
            }
        }

        // Step 2: Si hay teléfonos faltantes, buscar sus series en seg_ns_eci
        if (!empty($telefonosParaEci)) {
            $mapTelefonosEci = $consultas->GetSeriesPorTelefonosEci($telefonosParaEci);

            // Reasignar el número de serie recuperado a los registros correspondientes
            foreach ($registros as $key => &$reg) {
                if ($consultas->esSerieInvalida($reg['Num__Serie'] ?? '')) {
                    $tel = $reg['TELEFONO'] ?? '';
                    if (isset($mapTelefonosEci[$tel])) {
                        $reg['Num__Serie'] = $mapTelefonosEci[$tel];
                    }
                }
            }
            unset($reg); // Romper referencia
        }

        // Step 3: Recolectar las series (ya corregidas) y buscar en Almacén
        $numSeries = array_column($registros, 'Num__Serie');
        $existentes = $consultas->GetNumSeriesEnAlmacen($numSeries);
        $mapExistentes = array_flip($existentes);

        // Step 4: Inserción masiva única
        $consultas->InsertBatchProduccionYSenalamiento($registros, $mapExistentes);
    }
}

// --- Producción acumulada ---
$ProduccionAcumulada = 0;
$qrProduccionTotal = $consultas->CountProduccionTotal();
if (is_array($qrProduccionTotal) && !empty($qrProduccionTotal)) {
    foreach ($qrProduccionTotal as $row) {
        if (isset($row['TOTAL'])) {
            $ProduccionAcumulada = (int)$row['TOTAL'];
        }
    }
}

// NUMEROS DE SERIE DISPONIBLES ERP (EN FECHA)
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

// NUMEROS DE SERIE DISPONIBLES ERP (TOTALES)
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

<!-- Content Row -->
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

    <!-- Producción por fechas -->
    <div class="col-kpi-5 mb-4">
        <div class="kpi-card kpi-card-info">
            <div class="kpi-card-body">
                <div class="kpi-card-icon"><i class="fas fa-star"></i></div>
                <div class="kpi-card-label">Total Producción (por fechas)</div>
                <div class="kpi-card-number"><?php echo number_format($TotalProduccion); ?></div>
            </div>
        </div>
    </div>

    <!-- Números de serie totales sin asignar -->
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

    <!-- Números de serie sin asignar en fecha -->
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