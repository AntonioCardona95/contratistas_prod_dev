<link rel="stylesheet" href="../css/kpis-produccion.css">

<?php
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassIndex.php");
require_once(__DIR__ . '/../../config/ContratistaContexto.php');
session_start();
$Contratista = $_SESSION['Contratista'];
$idContratistas = obtenerIdContratistaContexto();

// RECIBIMOS PARAMETROS
$Fecha1 = $_POST['Fecha1'];
$Fecha2 = $_POST['Fecha2'];
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

<!-- Content Row -->
<div class="row kpi-cards-row">

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