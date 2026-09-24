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
    $qrProduccion = $consultas->CountProduccionPorFechas($Fecha1, $Fecha2);
    if (is_array($qrProduccion) && !empty($qrProduccion)) {
        foreach ($qrProduccion as $row) {
            if (isset($row['TOTAL'])) {
                $TotalProduccion = (int)$row['TOTAL'];
            }
        }
    }

    $registros = $consultas->GetProduccionPorFechas($Fecha1, $Fecha2);

    if (!empty($registros)) {
        // Step 1: Extraer todos los teléfonos para verificar en ECI
        $telefonos = array_column($registros, 'TELEFONO');
        $mapEci = $consultas->GetSeriesPorTelefonosEci($telefonos);

        // Step 2: Comparar series y definir Origen y Serie Final a validar
        foreach ($registros as &$reg) {
            $serieProd = trim($reg['Num__Serie'] ?? '');
            $tel = trim($reg['TELEFONO'] ?? '');
            $serieEci = $mapEci[$tel] ?? null;

            $reg['Serie_Original_Prod'] = $serieProd;
            $reg['Tiene_Senalamiento_Conflicto'] = false;

            if ($consultas->esSerieInvalida($serieProd)) {
                // Caso A: Producción no traía serie válida
                if ($serieEci !== null) {
                    $reg['Num__Serie'] = $serieEci;
                    $reg['Origen_Serie'] = 'ECI';
                } else {
                    $reg['Origen_Serie'] = 'NINGUNO';
                }
            } else {
                // Caso B: Producción sí traía serie válida
                if ($serieEci !== null && strtoupper($serieProd) !== strtoupper($serieEci)) {
                    // Conflicto: La serie de ECI no coincide con Producción. Prevalece ECI.
                    $reg['Num__Serie'] = $serieEci;
                    $reg['Origen_Serie'] = 'ECI';
                    $reg['Tiene_Senalamiento_Conflicto'] = true;
                } else {
                    // Coincide o no existe en ECI, se conserva Producción
                    $reg['Origen_Serie'] = 'PRODUCCION';
                }
            }
        }
        unset($reg); // Destruir referencia

        // Step 3: Recolectar la lista de series resultantes y consultar Almacén
        $numSeries = array_column($registros, 'Num__Serie');
        $existentes = $consultas->GetNumSeriesEnAlmacen($numSeries);
        $mapExistentes = array_flip($existentes);

        // Step 4: Inserción masiva procesando datos y señalamientos
        $consultas->InsertBatchProduccionYSenalamiento($registros, $mapExistentes);
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