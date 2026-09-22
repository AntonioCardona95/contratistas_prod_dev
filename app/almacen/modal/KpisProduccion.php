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

<style>
  .kpi-cards-row {
    margin-bottom: 1.25rem;
  }

  /* Fuerza el ancho exacto equivalente a 1/5 del contenedor (el tamaño original de cuando eran 5 tarjetas) */
  .col-kpi-5 {
    flex: 0 0 20%;
    max-width: 20%;
    padding-right: 0.75rem;
    padding-left: 0.75rem;
  }

  .kpi-card-link {
    display: block;
    height: 100%;
    color: inherit !important;
    text-decoration: none;
  }

  .kpi-card-link:visited,
  .kpi-card-link:hover,
  .kpi-card-link:focus,
  .kpi-card-link:active {
    color: inherit !important;
    text-decoration: none;
  }

  .kpi-card {
    --kpi-color: #185abc;
    --kpi-soft-color: rgba(24, 90, 188, 0.11);
    position: relative;
    height: 100%;
    min-height: 172px;
    overflow: hidden;
    border: 1px solid #e6eaf0;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 3px 12px rgba(15, 23, 42, 0.07);
    transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
  }

  .kpi-card::after {
    position: absolute;
    right: -28px;
    bottom: -40px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--kpi-soft-color);
    content: '';
  }

  .kpi-card:hover {
    border-color: var(--kpi-color);
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.13);
    transform: translateY(-4px);
  }

  .kpi-card-body {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    height: 100%;
    padding: 1.25rem 1.3rem;
  }

  .kpi-card-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    margin-bottom: 1rem;
    border-radius: 10px;
    background: var(--kpi-soft-color);
    color: var(--kpi-color);
    font-size: 1.15rem;
  }

  .kpi-card-label {
    margin-bottom: 0.35rem;
    color: #596579;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    line-height: 1.35;
    text-transform: uppercase;
  }

  .kpi-card-number {
    color: #182235;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
  }

  .kpi-card-danger {
    --kpi-color: #c0392b;
    --kpi-soft-color: rgba(192, 57, 43, 0.11);
  }

  .kpi-card-warning {
    --kpi-color: #b7791f;
    --kpi-soft-color: rgba(183, 121, 31, 0.13);
  }

  @media (max-width: 992px) {
    .col-kpi-5 {
      flex: 0 0 50%;
      max-width: 50%;
    }
  }

  @media (max-width: 768px) {
    .col-kpi-5 {
      flex: 0 0 100%;
      max-width: 100%;
    }

    .kpi-card {
      min-height: 148px;
    }

    .kpi-card-body {
      padding: 1rem;
    }

    .kpi-card-icon {
      width: 38px;
      height: 38px;
      margin-bottom: 0.75rem;
    }

    .kpi-card-number {
      font-size: 1.75rem;
    }
  }
</style>

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