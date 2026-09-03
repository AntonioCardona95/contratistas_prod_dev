<?php
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassIndex.php");
session_start();
$Contratista = $_SESSION['Contratista'];
$idContratistas = $_SESSION['idContratistas'];

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

// NUMEROS DE SERIE ASIGNADOS A TECNICOS
$TotalAsignados = 0;
$sqlCompletas = $index->CountMaterialesAsignados($idContratistas, $Fecha1, $Fecha2);
if (is_array($sqlCompletas)) {
    if (!empty($sqlCompletas)) {
        foreach ($sqlCompletas as $row) {
            if (isset($row['TOTAL'])) {
                $TotalAsignados = (int)$row['TOTAL'];
            }
        }
    }
} else {
    $TotalAsignados = (int)$sqlCompletas;
}

// NUMEROS DE SERIE SIN INSTALAR
$TotalSinInstalar = 0;
$qrIncompletas = $index->CountMaterialesSinInstalar($idContratistas, $Fecha1, $Fecha2);
if (is_array($qrIncompletas)) {
    if (!empty($qrIncompletas)) {
        foreach ($qrIncompletas as $row) {
            if (isset($row['TOTAL'])) {
                $TotalSinInstalar = (int)$row['TOTAL'];
            }
        }
    }
} else {
    $TotalSinInstalar = (int)$qrIncompletas;
}

  // NUMEROS DE SERIE INSTALADOS COORDIAPP
  $TotalInstalados = 0;
  $qrInstalados = $index->CountMaterialesInstalados($idContratistas, $Fecha1, $Fecha2);
  if (is_array($qrInstalados)) {
    if (!empty($qrInstalados)) {
      foreach ($qrInstalados as $row) {
        if (isset($row['TOTAL'])) {
          $TotalInstalados = (int)$row['TOTAL'];
        }
      }
    }
  } else {
    $TotalInstalados = (int)$qrInstalados;
  }

  // NUMEROS DE SERIE INSTALADOS TAC
  $TotalInstaladosTAC = 0;
  $qrInstaladosTAC = $index->CountOntInstaladosTAC($idContratistas, $Fecha1, $Fecha2);
  // El metodo devuelve un int (total), asi que asignamos directamente.
  $TotalInstaladosTAC = (int)$qrInstaladosTAC;

  // NUMEROS DE SERIE DISPONIBLES ERP
  $TotalDisponible = 0;
  // CountONTDisponibles acepta solo el id del contratista.
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

  // NUMEROS DE SERIE DISPONIBLES ERP
  $TTotalDisponible = 0;
  // CountONTDisponibles acepta solo el id del contratista.
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

  $chart_dias = $index->CountMaterialesPorDia($idContratistas, $Fecha1, $Fecha2);
  $chart_tecnicos = $index->CountMaterialesPorTecnico($idContratistas, $Fecha1, $Fecha2);

  $chart_dias_labels = [];
  $chart_dias_asignadas = [];
  $chart_dias_sin_liquidar = [];
  $chart_dias_liquidadas = [];
  foreach ((array)$chart_dias as $row) {
    $chart_dias_labels[] = (string)$row['FECHA'];
    $chart_dias_asignadas[] = (int)$row['ASIGNADAS'];
    $chart_dias_sin_liquidar[] = (int)$row['SIN_LIQUIDAR'];
    $chart_dias_liquidadas[] = (int)$row['LIQUIDADAS'];
  }

  $chart_tecnicos_labels = [];
  $chart_tecnicos_asignadas = [];
  $chart_tecnicos_sin_liquidar = [];
  $chart_tecnicos_liquidadas = [];
  $chart_tecnicos_efectividad = [];
  foreach ((array)$chart_tecnicos as $row) {
    $chart_tecnicos_labels[] = trim((string)$row['NOMBRE_TECNICO']);
    $chart_tecnicos_asignadas[] = (int)$row['ASIGNADAS'];
    $chart_tecnicos_sin_liquidar[] = (int)$row['SIN_LIQUIDAR'];
    $chart_tecnicos_liquidadas[] = (int)$row['LIQUIDADAS'];
    $chart_tecnicos_efectividad[] = (int)$row['ASIGNADAS'] > 0
      ? round(((int)$row['LIQUIDADAS'] / (int)$row['ASIGNADAS']) * 100, 1) : 0;
  }
?>

<style>
  .kpi-cards-row {
    margin-bottom: 1.25rem;
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

  .kpi-card-info {
    --kpi-color: #147d92;
    --kpi-soft-color: rgba(20, 125, 146, 0.11);
  }

  .kpi-card-primary {
    --kpi-color: #185abc;
    --kpi-soft-color: rgba(24, 90, 188, 0.11);
  }

  .kpi-card-success {
    --kpi-color: #25834b;
    --kpi-soft-color: rgba(37, 131, 75, 0.11);
  }

  .chart-area {
    position: relative;
    min-height: 320px;
    padding: 1.25rem;
    border: 1px solid #edf0f4;
    border-radius: 0 0 12px 12px;
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
  }

  .chart-area canvas {
    max-height: 330px;
  }

  .technician-chart-scroll {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
  }

  .technician-chart-inner {
    position: relative;
    height: 330px;
    min-width: 100%;
  }

  .technician-chart-inner canvas {
    max-height: none;
  }

  .chart-area + * {
    border-radius: 12px;
  }

  #LoadKPIS .card {
    overflow: hidden;
    border: 1px solid #e6eaf0;
    border-radius: 12px;
    box-shadow: 0 5px 18px rgba(15, 23, 42, 0.07);
  }

  #LoadKPIS .card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #edf0f4;
    background: #ffffff;
    color: #182235;
  }

  #LoadKPIS .card-header strong {
    color: #596579;
    font-size: 0.82rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }

  @media (max-width: 768px) {
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

    .chart-area {
      min-height: 270px;
      padding: 0.75rem;
    }
  }
</style>

<!-- Content Row -->
<div class="row kpi-cards-row">

    <div class="col-xl col-md-6 mb-4">
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

    <div class="col-xl col-md-6 mb-4">
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

    <div class="col-xl col-md-6 mb-4">
      <a href="detalle_ont_asignados.php" class="kpi-card-link">
        <div class="kpi-card kpi-card-info">
          <div class="kpi-card-body">
            <div class="kpi-card-icon"><i class="fas fa-user-check"></i></div>
            <div class="kpi-card-label">Números de serie asignados</div>
            <div class="kpi-card-number"><?php echo number_format($TotalAsignados); ?></div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl col-md-6 mb-4">
      <a href="ont_sin_instalar_det.php" class="kpi-card-link">
        <div class="kpi-card kpi-card-primary">
          <div class="kpi-card-body">
            <div class="kpi-card-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="kpi-card-label">Números de serie sin liquidar</div>
            <div class="kpi-card-number"><?php echo number_format($TotalSinInstalar); ?></div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl col-md-6 mb-4">
      <a href="ont_instaladas_det.php" class="kpi-card-link">
        <div class="kpi-card kpi-card-success">
          <div class="kpi-card-body">
            <div class="kpi-card-icon"><i class="fas fa-check-double"></i></div>
            <div class="kpi-card-label">Números de serie liquidados</div>
            <div class="kpi-card-number"><?php echo number_format($TotalInstalados); ?></div>
                </div>
            </div>
        </a>
    </div>

</div>
<!-- Seis graficas derivadas de las cinco tarjetas -->
<div class="row">
  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header">
                <strong>Embudo operativo de números de serie</strong>
      </div>
      <div class="card-body chart-area">
        <canvas id="chartEmbudo"></canvas>
      </div>
    </div>
  </div>

  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header">
        <strong>Asignaciones, pendientes y liquidaciones por dia</strong>
      </div>
      <div class="card-body chart-area">
        <canvas id="chartTendencia"></canvas>
      </div>
    </div>
  </div>

  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header">
        <strong>Rendimiento por tecnico</strong>
      </div>
      <div class="card-body chart-area">
        <div class="technician-chart-scroll" aria-label="Desplazamiento horizontal de técnicos">
          <div class="technician-chart-inner" style="width: <?php echo max(100, count($chart_tecnicos_labels) * 115); ?>px;">
            <canvas id="chartRendimiento"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header">
        <strong>Efectividad de liquidacion por tecnico</strong>
      </div>
      <div class="card-body chart-area">
        <div class="technician-chart-scroll" aria-label="Desplazamiento horizontal de técnicos">
          <div class="technician-chart-inner" style="width: <?php echo max(100, count($chart_tecnicos_labels) * 115); ?>px;">
            <canvas id="chartEfectividad"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header">
        <strong>Inventario total contra inventario reciente</strong>
      </div>
      <div class="card-body chart-area">
        <canvas id="chartInventario"></canvas>
      </div>
    </div>
  </div>

  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header">
        <strong>Conciliacion COORDIAPP contra TAC</strong>
      </div>
      <div class="card-body chart-area">
        <canvas id="chartConciliacion"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  var labelsDias = <?php echo json_encode($chart_dias_labels, JSON_UNESCAPED_UNICODE); ?>;
  var asignadasDias = <?php echo json_encode($chart_dias_asignadas, JSON_NUMERIC_CHECK); ?>;
  var pendientesDias = <?php echo json_encode($chart_dias_sin_liquidar, JSON_NUMERIC_CHECK); ?>;
  var liquidadasDias = <?php echo json_encode($chart_dias_liquidadas, JSON_NUMERIC_CHECK); ?>;
  var labelsTecnicos = <?php echo json_encode($chart_tecnicos_labels, JSON_UNESCAPED_UNICODE); ?>;
  var asignadasTecnicos = <?php echo json_encode($chart_tecnicos_asignadas, JSON_NUMERIC_CHECK); ?>;
  var pendientesTecnicos = <?php echo json_encode($chart_tecnicos_sin_liquidar, JSON_NUMERIC_CHECK); ?>;
  var liquidadasTecnicos = <?php echo json_encode($chart_tecnicos_liquidadas, JSON_NUMERIC_CHECK); ?>;
  var efectividadTecnicos = <?php echo json_encode($chart_tecnicos_efectividad, JSON_NUMERIC_CHECK); ?>;
  var charts = [];
  var colors = {
    danger: '#c0392b',
    warning: '#b7791f',
    info: '#147d92',
    primary: '#185abc',
    success: '#25834b'
  };

  function makeChart(id, type, labels, datasets, options) {
    var canvas = document.getElementById(id);

    if (!canvas) {
      return;
    }

    charts.push(new Chart(canvas, {
      type: type,
      data: {
        labels: labels,
        datasets: datasets
      },
      options: options || {}
    }));
  }

  var common = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          usePointStyle: true,
          padding: 18
        }
      },
      tooltip: {
        backgroundColor: '#182235',
        titleColor: '#ffffff',
        bodyColor: '#e6eaf0',
        padding: 12,
        cornerRadius: 8,
        displayColors: true
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          color: '#edf0f4'
        },
        ticks: {
          color: '#596579'
        }
      },
      x: {
        grid: {
          display: false
        },
        ticks: {
          color: '#596579',
          maxRotation: 0,
          minRotation: 0,
          autoSkip: false,
          callback: function (value) {
            var label = this.getLabelForValue(value);
            var words = label.split(' ');
            var lines = [];
            var currentLine = '';

            words.forEach(function (word) {
              if ((currentLine + ' ' + word).trim().length > 16 && currentLine) {
                lines.push(currentLine);
                currentLine = word;
              } else {
                currentLine = (currentLine + ' ' + word).trim();
              }
            });

            if (currentLine) {
              lines.push(currentLine);
            }

            return lines.slice(0, 3);
          }
        }
      }
    }
  };

  makeChart(
    'chartEmbudo',
    'bar',
    ['Asignadas', 'Sin liquidar', 'Liquidadas'],
    [{
      label: 'Números de serie',
      data: [
        <?php echo (int)$TotalAsignados; ?>,
        <?php echo (int)$TotalSinInstalar; ?>,
        <?php echo (int)$TotalInstalados; ?>
      ],
            backgroundColor: [colors.info, colors.primary, colors.success],
            borderRadius: 8,
            borderSkipped: false,
            hoverBackgroundColor: [colors.info, colors.primary, colors.success]
    }],
    common
  );

  makeChart('chartTendencia', 'line', labelsDias, [
    {
      label: 'Asignadas',
      data: asignadasDias,
            borderColor: colors.info,
            backgroundColor: 'rgba(20, 125, 146, .12)',
            pointBackgroundColor: colors.info,
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            tension: 0.35,
            fill: true
    },
    {
      label: 'Sin liquidar',
      data: pendientesDias,
            borderColor: colors.danger,
            backgroundColor: 'transparent',
            pointBackgroundColor: colors.danger,
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            tension: 0.35
    },
    {
      label: 'Liquidadas',
      data: liquidadasDias,
            borderColor: colors.warning,
            backgroundColor: 'transparent',
            pointBackgroundColor: colors.warning,
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 4,
            tension: 0.35
    }
  ], common);

  makeChart('chartRendimiento', 'bar', labelsTecnicos, [
    {
      label: 'Asignadas',
      data: asignadasTecnicos,
            backgroundColor: colors.info,
            borderRadius: 6,
            borderSkipped: false
    },
    {
      label: 'Sin liquidar',
      data: pendientesTecnicos,
            backgroundColor: colors.primary,
            borderRadius: 6,
            borderSkipped: false
    },
    {
      label: 'Liquidadas',
      data: liquidadasTecnicos,
            backgroundColor: colors.success,
            borderRadius: 6,
            borderSkipped: false
    }
  ], common);

  makeChart(
    'chartEfectividad',
    'bar',
    labelsTecnicos,
    [{
      label: 'Efectividad (%)',
      data: efectividadTecnicos,
            backgroundColor: colors.success,
            borderRadius: 6,
            borderSkipped: false
    }],
    {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#596579',
            maxRotation: 0,
            minRotation: 0,
            autoSkip: false,
            callback: function (value) {
              var label = this.getLabelForValue(value);
              var words = label.split(' ');
              var lines = [];
              var currentLine = '';

              words.forEach(function (word) {
                if ((currentLine + ' ' + word).trim().length > 16 && currentLine) {
                  lines.push(currentLine);
                  currentLine = word;
                } else {
                  currentLine = (currentLine + ' ' + word).trim();
                }
              });

              if (currentLine) {
                lines.push(currentLine);
              }

              return lines.slice(0, 3);
            }
          }
        },
        y: {
          beginAtZero: true,
          max: 100,
          ticks: {
            callback: function (value) {
              return value + '%';
            }
          }
        }
      }
    }
  );

  makeChart(
    'chartInventario',
    'bar',
    ['Total sin asignar', 'Sin asignar en fecha'],
    [{
            label: 'Números de serie',
      data: [
        <?php echo (int)$TTotalDisponible; ?>,
        <?php echo (int)$TotalDisponible; ?>
      ],
            backgroundColor: [colors.danger, colors.warning],
            borderRadius: 8,
            borderSkipped: false
    }],
    common
  );

  makeChart(
    'chartConciliacion',
    'bar',
    ['COORDIAPP', 'TAC'],
    [{
            label: 'Números de serie liquidados',
      data: [
        <?php echo (int)$TotalInstalados; ?>,
        <?php echo (int)$TotalInstaladosTAC; ?>
      ],
            backgroundColor: [colors.info, colors.primary],
            borderRadius: 8,
            borderSkipped: false
    }],
    common
  );
})();
</script>