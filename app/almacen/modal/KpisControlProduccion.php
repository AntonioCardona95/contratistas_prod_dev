<?php
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../controller/ClassConsultasProduccion.php");
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

// Instancias de las clases
$index = new Index();
$consultas = new ClassConsultasProduccion();

// TOTAL PRODUCCION HISTORICO
$TotalProduccion = 0;
// ... (tu lógica existente) ...

// NUEVO: PRODUCCION POR FECHAS SELECCIONADAS
$TotalProduccionFechas = 0;
$qrProduccionFechas = $consultas->CountProduccionPorFechas($Fecha1, $Fecha2);
if (is_array($qrProduccionFechas)) {
    if (!empty($qrProduccionFechas)) {
        foreach ($qrProduccionFechas as $row) {
            if (isset($row['TOTAL'])) {
                $TotalProduccionFechas = (int)$row['TOTAL'];
            }
        }
    }
} else {
    $TotalProduccionFechas = (int)$qrProduccionFechas;
}


// TOTAL ONT / EQUIPOS SIN ENTRADA A ALMACÉN (GENERAL)
$TotalOntSinAlmacen = 0;
$qrOntSinAlmacen = $consultas->CountOntSinEntradaAlmacen($Fecha1, $Fecha2, $idContratistas);

if (is_array($qrOntSinAlmacen)) {
    if (!empty($qrOntSinAlmacen)) {
        foreach ($qrOntSinAlmacen as $row) {
            if (isset($row['TOTAL'])) {
                $TotalOntSinAlmacen = (int)$row['TOTAL'];
            }
        }
    }
} else {
    $TotalOntSinAlmacen = (int)$qrOntSinAlmacen;
}


// TOTAL ONT / EQUIPOS QUE SÍ ENTRARON A ALMACÉN
$TotalOntConAlmacen = 0;
$qrOntConAlmacen = $consultas->CountOntConEntradaAlmacen($Fecha1, $Fecha2, $idContratistas);

if (is_array($qrOntConAlmacen)) {
    if (!empty($qrOntConAlmacen)) {
        foreach ($qrOntConAlmacen as $row) {
            if (isset($row['TOTAL'])) {
                $TotalOntConAlmacen = (int)$row['TOTAL'];
            }
        }
    }
} else {
    $TotalOntConAlmacen = (int)$qrOntConAlmacen;
}

// TOTAL PRODUCCION HISTORICO (usa ClassConsultasProduccion)
$TotalProduccion = 0;
$qrProduccionTotal = $consultas->CountProduccionTotal();
if (is_array($qrProduccionTotal)) {
    if (!empty($qrProduccionTotal)) {
        foreach ($qrProduccionTotal as $row) {
            if (isset($row['TOTAL'])) {
                $TotalProduccion = (int)$row['TOTAL'];
            }
        }
    }
} else {
    $TotalProduccion = (int)$qrProduccionTotal;
}

// NUMEROS DE SERIE ASIGNADOS A TECNICOS (usa Index)
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
$TotalInstaladosTAC = (int)$qrInstaladosTAC;

// NUMEROS DE SERIE DISPONIBLES ERP
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

// NUMEROS DE SERIE DISPONIBLES ERP (T)
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

// CHARTS
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

<link rel="stylesheet" href="css/kpisdashboard.css">
<!-- Content Row -->
<div class="row kpi-cards-row">

    <!-- Card 0: Total Producción -->
<div class="col-xl col-md-6 mb-4">
    <a href="#" class="kpi-card-link">
        <div class="kpi-card kpi-card-dark" style="border-left: 4px solid #343a40;">
            <div class="kpi-card-body">
                <div class="kpi-card-icon"><i class="fas fa-industry"></i></div>
                <div class="kpi-card-label">Total Producción</div>
                <!-- Aquí ya se imprime el valor correcto -->
                <div class="kpi-card-number"><?php echo number_format($TotalProduccion); ?></div>
            </div>
        </div>
    </a>
</div>

<!-- Nueva Card: Producción Por Fechas -->
    <div class="col-xl col-md-6 mb-4">
        <a href="#" class="kpi-card-link">
            <div class="kpi-card kpi-card-dark" style="border-left: 4px solid #17a2b8;">
                <div class="kpi-card-body">
                    <div class="kpi-card-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="kpi-card-label">Producción por Fechas</div>
                    <div class="kpi-card-number"><?php echo number_format($TotalProduccionFechas); ?></div>
                </div>
            </div>
        </a>
    </div>


    <!-- Card: Total ONT Sin Entrada a Almacén (General) -->
<div class="col-xl col-md-6 mb-4">
    <a href="#" class="kpi-card-link">
        <div class="kpi-card kpi-card-dark" style="border-left: 4px solid #dc3545;">
            <div class="kpi-card-body">
                <div class="kpi-card-icon"><i class="fas fa-boxes"></i></div>
                <div class="kpi-card-label">ONT sin Entrada a Almacén</div>
                <div class="kpi-card-number"><?php echo number_format($TotalOntSinAlmacen); ?></div>
            </div>
        </div>
    </a>
</div>


<!-- NUEVA CARD: ONT con Entrada a Almacén -->
<div class="col-xl col-md-6 mb-4">
    <a href="#" class="kpi-card-link">
        <div class="kpi-card kpi-card-dark" style="border-left: 4px solid #28a745;">
            <div class="kpi-card-body">
                <div class="kpi-card-icon"><i class="fas fa-warehouse"></i></div>
                <div class="kpi-card-label">ONT con Entrada a Almacén</div>
                <div class="kpi-card-number"><?php echo number_format($TotalOntConAlmacen); ?></div>
            </div>
        </div>
    </a>
</div>


    

</div>

<!-- Seis gráficas derivadas de las tarjetas -->
<div class="row">
  





<!-- Embudo -->
  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header"><strong>Embudo operativo de números de serie</strong></div>
      <div class="card-body chart-area"><canvas id="chartEmbudo"></canvas></div>
    </div>
  </div>

  <!-- Tendencia -->
  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header"><strong>Asignaciones, pendientes y liquidaciones por día</strong></div>
      <div class="card-body chart-area"><canvas id="chartTendencia"></canvas></div>
    </div>
  </div>

  

  <!-- Rendimiento por técnico -->
  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header"><strong>Rendimiento por técnico</strong></div>
      <div class="card-body chart-area">
        <div class="technician-chart-scroll" aria-label="Desplazamiento horizontal de técnicos">
          <div class="technician-chart-inner" style="width: <?php echo max(100, count($chart_tecnicos_labels) * 115); ?>px;">
            <canvas id="chartRendimiento"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Efectividad -->
  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header"><strong>Efectividad de liquidación por técnico</strong></div>
      <div class="card-body chart-area">
        <div class="technician-chart-scroll" aria-label="Desplazamiento horizontal de técnicos">
          <div class="technician-chart-inner" style="width: <?php echo max(100, count($chart_tecnicos_labels) * 115); ?>px;">
            <canvas id="chartEfectividad"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Inventario -->
  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header"><strong>Inventario total contra inventario reciente</strong></div>
      <div class="card-body chart-area"><canvas id="chartInventario"></canvas></div>
    </div>
  </div>

  <!-- Conciliación -->
  <div class="col-xl-6 mb-4">
    <div class="card shadow h-100">
      <div class="card-header"><strong>Conciliación COORDIAPP contra TAC</strong></div>
      <div class="card-body chart-area"><canvas id="chartConciliacion"></canvas></div>
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