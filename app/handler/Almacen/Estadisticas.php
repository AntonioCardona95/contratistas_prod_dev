<?php

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  require_once __DIR__ . '/../../controller/ClassOps.php';
  session_start();
  $Contratista = $_SESSION['Contratista'];
  $idContratistas = $_SESSION['idContratistas'];
  
  //RECIBIMOS PARAMETROS
  $Fecha1 = $_POST['fecha'];
  $Fecha2 = $_POST['fechaFinal']; 
  @$Division = $_POST['Division'];
  @$Area = $_POST['Area'];
  @$Cope = $_POST['Cope'];

  $mes_anterior = date('Y-m-d', strtotime('-1 month'));

  if(!empty($Fecha1) && !empty($Fecha2)){
    $Fecha1 = $_POST['fecha']." 00:00:00";
    $Fecha2 = $_POST['fechaFinal']." 23:59:59";
  }else{
    $Fecha1 = $mes_anterior." 00:00:00";
    $Fecha2 = date('Y-m-d')." 23:59:59";
  }
  
  $modal = new Ops();
  $tac = new Tac();
  
  //ONT ASIGNADAS A TECNICOS
  $TotalAsignados = 0;
  $sqlCompletas = $modal->CountMaterialesAsignados($idContratistas,$Fecha1,$Fecha2);
  if (is_array($sqlCompletas)) {
    if(!empty($sqlCompletas)){
      foreach($sqlCompletas as $row){
        if (isset($row['TOTAL'])) { $TotalAsignados = (int)$row['TOTAL']; }
      }
    }
  } else {
    $TotalAsignados = (int)$sqlCompletas;
  }
  
  //ONT SIN INSTALAR
  $TotalSinInstalar = 0;
  $qrIncompletas = $modal->CountMaterialesSinInstalar($idContratistas,$Fecha1,$Fecha2);
  if (is_array($qrIncompletas)) {
    if(!empty($qrIncompletas)){
        foreach($qrIncompletas as $row){
            if (isset($row['TOTAL'])) { $TotalSinInstalar = (int)$row['TOTAL']; }
        }
    }
  } else {
    $TotalSinInstalar = (int)$qrIncompletas;
  }

  //ONT INSTALADOS COORDIAPP
  $TotalInstalados = 0;
  $qrInstalados = $modal->CountMaterialesInstalados($idContratistas,$Fecha1,$Fecha2);
  if (is_array($qrInstalados)) {
    if(!empty($qrInstalados)){
        foreach($qrInstalados as $row){
            if (isset($row['TOTAL'])) { $TotalInstalados = (int)$row['TOTAL']; }
        }
    }
  } else {
    $TotalInstalados = (int)$qrInstalados;
  }

  //ONT INSTALADOS TAC
  $TotalInstaladosTAC = 0;
  $qrInstaladosTAC = $tac->CountOntInstaladosTAC($idContratistas,$Fecha1,$Fecha2);
  // el método devuelve un int (total), así que asignamos directamente
  $TotalInstaladosTAC = (int)$qrInstaladosTAC;

  //ONT DISPONIBLES ERP
  $TotalDisponible = 0;
  // CountONTDisponibles acepta solo el id del contratista
  $qrDisponibles = $modal->CountONTDisponibles($idContratistas, $Fecha1, $Fecha2);
  if (is_array($qrDisponibles)) {
    if(!empty($qrDisponibles)){
        foreach($qrDisponibles as $row){
            if (isset($row['TOTAL'])) { $TotalDisponible = (int)$row['TOTAL']; }
        }
    }
  } else {
    $TotalDisponible = (int)$qrDisponibles;
  }

  //ONT DISPONIBLES ERP
  $TTotalDisponible = 0;
  // CountONTDisponibles acepta solo el id del contratista
  $qrDisponiblesT = $modal->CountONTDisponiblesT($idContratistas);
  if (is_array($qrDisponiblesT)) {
    if(!empty($qrDisponiblesT)){
        foreach($qrDisponiblesT as $row){
            if (isset($row['TOTAL'])) { $TTotalDisponible = (int)$row['TOTAL']; }
        }
    }
  } else {
    $TTotalDisponible = (int)$qrDisponiblesT;
  }

  //GRAFICA TECNICO MAS PRODUCTIVO
  $tecnicos_mas_prod = $modal->TecnicosConMasONTDisponibles($idContratistas,$Fecha1,$Fecha2); 
  $tecnicos_mas_ont_instalados = $modal->TecnicosConMasONTInstaladas($idContratistas,$Fecha1,$Fecha2);
  //TECNOLOGIA MAS USADA
  $tecnologia_mas_prod = $modal->InstaladasVSNoInstaladas($idContratistas,$Fecha1,$Fecha2);  

  // Prepare data structures for charts (clean, single responsibility)
  $hc_data1 = [];
  if (!empty($tecnologia_mas_prod) && is_array($tecnologia_mas_prod)) {
    foreach ($tecnologia_mas_prod as $row) {
      $hc_data1[] = [
        'name' => (string)$row['Ont_Ubicacion'],
        'y' => (float)$row['TOTAL']
      ];
    }
  }

  // COORDIAPP vs TAC pie (container2)
  $hc_data2 = [
    ['name' => 'COORDIAPP', 'y' => (float)$TotalDisponible],
    ['name' => 'TAC', 'y' => (float)$TotalInstaladosTAC]
  ];

  // Datos preparados para Chart.js (etiquetas y valores)
  // Usamos estructuras simples y normalizamos tipos para que json_encode genere arrays limpios
  $chart_tecnicos_labels = [];
  $chart_tecnicos_data = [];
  if (!empty($tecnicos_mas_prod) && is_array($tecnicos_mas_prod)) {
    foreach ($tecnicos_mas_prod as $row) {
      $chart_tecnicos_labels[] = 'EXP: ' . (string)$row['NExpediente'];
      $chart_tecnicos_data[] = (int)$row['TOTAL'];
    }
  }

  $chart_instalados_labels = [];
  $chart_instalados_data = [];
  if (!empty($tecnicos_mas_ont_instalados) && is_array($tecnicos_mas_ont_instalados)) {
    foreach ($tecnicos_mas_ont_instalados as $row) {
      $chart_instalados_labels[] = 'EXP: ' . (string)$row['NExpediente'];
      $chart_instalados_data[] = (int)$row['TOTAL'];
    }
  }
?>

<!-- CONTENEDOR DE TARJETAS DE ESTADÍSTICAS -->
<div class="row mb-4">

    <!-- 1. TOTAL DE ONT DISPONIBLES -->
    <div class="col-xl col-md-6">
        <a href="#" style="text-decoration: none; color: inherit;" onclick="abrirModalDetalle('disponibles', 'TOTAL DE ONTS DISPONIBLES', 'fas fa-ban'); return false;">
            <div class="card stat-card danger h-100 mb-2">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stat-label">TOTAL DE ONTS DISPONIBLES</div>
                    <div class="stat-number"><?php echo number_format($TTotalDisponible); ?></div>
                </div>
            </div>
        </a>
    </div>

    <!-- 2. ONTS RECIENTES SIN ASIGNAR-->
    <div class="col-xl col-md-6">
        <a href="#" style="text-decoration: none; color: inherit;" onclick="abrirModalDetalle('disponibles', 'ONTS RECIENTES SIN ASIGNAR', 'fas fa-clock'); return false;">
            <div class="card stat-card warning h-100 mb-2">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-label">ONTS RECIENTES SIN ASIGNAR</div>
                    <div class="stat-number"><?php echo number_format($TotalDisponible); ?></div>
                </div>
            </div>
        </a>
    </div>

    <!-- 3. ONTS ASIGNADAS -->
    <div class="col-xl col-md-6">
        <a href="#" style="text-decoration: none; color: inherit;" onclick="abrirModalDetalle('asignadas', 'ONTS ASIGNADAS', 'fas fa-check-circle'); return false;">
            <div class="card stat-card primary h-100 mb-2">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-label">ONTS ASIGNADAS</div>
                    <div class="stat-number"><?php echo number_format($TotalAsignados); ?></div>
                </div>
            </div>
        </a>
    </div>

    <!-- 4. ONTS SIN LIQUIDAR -->
    <div class="col-xl col-md-6">
        <a href="#" style="text-decoration: none; color: inherit;" onclick="abrirModalDetalle('sinliquidar', 'ONTS SIN LIQUIDAR', 'fas fa-hourglass-half'); return false;">
            <div class="card stat-card primary h-100 mb-2">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-label">ONTS SIN LIQUIDAR</div>
                    <div class="stat-number"><?php echo number_format($TotalSinInstalar); ?></div>
                </div>
            </div>
        </a>
    </div>

    <!-- 5. ONTS LIQUIDADAS -->
    <div class="col-xl col-md-6">
        <a href="#" style="text-decoration: none; color: inherit;" onclick="abrirModalDetalle('liquidadas', 'ONTS LIQUIDADAS', 'fas fa-check-double'); return false;">
            <div class="card stat-card success h-100 mb-2">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="stat-label">ONTS LIQUIDADAS</div>
                    <div class="stat-number"><?php echo number_format($TotalInstalados); ?></div>
                </div>
            </div>
        </a>
    </div>

</div>

<!-- Content Row - Gráficas -->
<div class="row">

  <!-- Gráfica 1: Control de Asignación -->
  <div class="col-xl-6 col-lg-6">
      <div class="card chart-card">
          <div class="card-header">
              <h6><i class="fas fa-chart-pie"></i> Control de Asignación de ONTs</h6>
          </div>
          <div class="card-body">
            <div id="container" style="width:100%; height:350px;"></div>
          </div>
      </div>
  </div>

  <!-- Gráfica 2: Coherencia de Reporte -->
  <div class="col-xl-6 col-lg-6">
      <div class="card chart-card">
          <div class="card-header">
              <h6><i class="fas fa-sync-alt"></i> Coherencia de Reporte (COORDIAPP vs TAC)</h6>
          </div>
          <div class="card-body">
            <div id="container2" style="width:100%; height:350px;"></div>
          </div>
      </div>
  </div>

</div>
<!-- Content Row - Gráficas por Técnico -->
<div class="row">

    <!-- Gráfica: ONTs por Técnico (Asignadas) -->
    <div class="col-xl-6 col-lg-6">
        <div class="card chart-card">
            <div class="card-header">
                <h6><i class="fas fa-users"></i> ONTs Asignadas por Técnico</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfica: ONTs Instaladas por Técnico -->
    <div class="col-xl-6 col-lg-6">
        <div class="card chart-card">
            <div class="card-header">
                <h6><i class="fas fa-check-circle"></i> ONTs Instaladas por Técnico</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="myPieChart2"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
Highcharts.chart('container', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'DISTRIBUCIÓN DE ORDENES DE SERVICIO POR USO DE ONT'
    },
    tooltip: {
        valueSuffix: '%'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.percentage:.1f}%',
                style: {
                    fontSize: '1.2em',
                    textOutline: 'none',
                    opacity: 0.7
                }
            }
        }
    },
    colors: ['#ff0000', '#3357FF', '#FFC300', '#DAF7A6'], // Colores personalizados
  series: [{
    name: 'Percentage',
    colorByPoint: true,
    data: <?php echo json_encode($hc_data1, JSON_NUMERIC_CHECK); ?>
  }]
});
</script>

<script>
Highcharts.chart('container2', {
    chart: {
        type: 'pie'
    },
    title: {
        text: 'DISTRIBUCIÓN DE ORDENES DE SERVICIO POR USO DE ONT'
    },
    tooltip: {
        valueSuffix: '%'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '{point.name}: {point.percentage:.1f}%',
                style: {
                    fontSize: '1.2em',
                    textOutline: 'none',
                    opacity: 0.7
                }
            }
        }
    },
    colors: ['#ff0000', '#3357FF', '#FFC300', '#DAF7A6'], // Colores personalizados
    series: [{
        name: 'Percentage',
        colorByPoint: true,
        data: [
          {
            name: '<?php echo addslashes("COORDIAPP"); ?>',
            y: <?php echo (float)$TotalInstalados; ?>
          },
          {
            name: '<?php echo addslashes("TAC"); ?>',
            y: <?php echo (float)$TotalInstaladosTAC; ?>
          }
        ]
    }]
});
</script>

<!-- JavaScript común para Chart.js: helpers y construcción de barras reutilizable -->
<script>
// Datos JSON inyectados de PHP (listas limpias)
var chart_tecnicos_labels = <?php echo json_encode($chart_tecnicos_labels, JSON_UNESCAPED_UNICODE); ?>;
var chart_tecnicos_data = <?php echo json_encode($chart_tecnicos_data, JSON_NUMERIC_CHECK); ?>;

var chart_instalados_labels = <?php echo json_encode($chart_instalados_labels, JSON_UNESCAPED_UNICODE); ?>;
var chart_instalados_data = <?php echo json_encode($chart_instalados_data, JSON_NUMERIC_CHECK); ?>;

// Configuración por defecto para Chart.js (se aplica a todos los gráficos)
if (window.Chart && Chart.defaults) {
  Chart.defaults.global.defaultFontFamily = 'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
  Chart.defaults.global.defaultFontColor = '#858796';
}

/**
 * Formatea números con separador de miles.
 * number: número a formatear
 * decimals: cantidad de decimales (opc.)
 */
function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) { var k = Math.pow(10, prec); return '' + Math.round(n * k) / k; };
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) { s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep); }
  if ((s[1] || '').length < prec) { s[1] = s[1] || ''; s[1] += new Array(prec - s[1].length + 1).join('0'); }
  return s.join(dec);
}

/**
 * Construye un gráfico de barras en el canvas indicado.
 * Usamos suggestedMax con 20% de headroom para escalar el eje Y.
 */
function buildBarChart(canvasId, labels, data, labelText) {
  var max = (data && data.length) ? Math.max.apply(null, data) : 0;
  var suggestedMax = Math.ceil(max * 1.2) || 5;
  var ctx = document.getElementById(canvasId);
  if (!ctx) return null;
  return new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: labelText || 'Valor',
        backgroundColor: '#4e73df',
        hoverBackgroundColor: '#2e59d9',
        borderColor: '#4e73df',
        data: data
      }]
    },
    options: {
      maintainAspectRatio: false,
      layout: { padding: { left: 5, right: 10, top: 15, bottom: 0 } },
      scales: {
        xAxes: [{ time: { unit: 'month' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 10 }, maxBarThickness: 25 }],
        yAxes: [{ ticks: { beginAtZero: true, suggestedMax: suggestedMax, maxTicksLimit: 10, padding: 5, callback: function(value) { return number_format(value); } }, gridLines: { color: 'rgb(234, 236, 244)', zeroLineColor: 'rgb(234, 236, 244)', drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } }]
      },
      legend: { display: false },
      tooltips: { titleMarginBottom: 10, titleFontColor: '#6e707e', titleFontSize: 14, backgroundColor: 'rgb(255,255,255)', bodyFontColor: '#858796', borderColor: '#dddfeb', borderWidth: 1, xPadding: 15, yPadding: 15, displayColors: false, caretPadding: 10, callbacks: { label: function(tooltipItem, chart) { var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || ''; return datasetLabel + ': ' + number_format(tooltipItem.yLabel); } } }
    }
  });
}

// Crear los dos gráficos de barras usando los datos inyectados desde PHP
buildBarChart('myPieChart', chart_tecnicos_labels, chart_tecnicos_data, 'ONT');
buildBarChart('myPieChart2', chart_instalados_labels, chart_instalados_data, 'ONT');

// FUNCIÓN PARA ABRIR EL MODAL Y CARGAR EL CONTENIDO CORRESPONDIENTE
function abrirModalDetalle(tipo, titulo, icono) {
    // Actualizar el título del modal con icono
    $('#detallesModalTitle').html('<i class="' + icono + '" style="color: white; margin-right: 8px;"></i> ' + titulo);
    
    // Mostrar el modal
    $('#detallesModal').modal('show');
    
    // Cargar los datos correspondientes
    cargarDetallesPorTipo(tipo);
}

// FUNCIÓN PARA CARGAR LOS DETALLES SEGÚN EL TIPO
function cargarDetallesPorTipo(tipo) {
    var fecha1 = $('#fecha').val();
    var fecha2 = $('#fechaFinal').val();
    
    var urlMap = {
        'disponibles': '/handler/Almacen/Estadisticas_TablaOntDisponiblesDet.php',
        'asignadas': '/handler/Almacen/Estadisticas_TablaOntAsignadosDet.php',
        'sinliquidar': '/handler/Almacen/Estadisticas_TablaOntSinInstalarDet.php',
        'liquidadas': '/handler/Almacen/Estadisticas_TablaOntInstaladosDet.php'
    };
    
    var archivo = urlMap[tipo] || '/handler/Almacen/Estadisticas_TablaOntDisponiblesDet.php';
    
    $.ajax({
        type: 'POST',
        url: archivo,
        data: {
            Fecha1: fecha1,
            Fecha2: fecha2
        },
        beforeSend: function() {
            $('#detallesContenido').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando datos...</div>');
        },
        success: function(data) {
            $('#detallesContenido').html(data);
        },
        error: function() {
            $('#detallesContenido').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error al cargar los datos</div>');
        }
    });
}

// LIMPIAR AL CERRAR EL MODAL
$('#detallesModal').on('hidden.bs.modal', function(e) {
    if ($.fn.DataTable.isDataTable('#modalTable')) {
        $('#modalTable').DataTable().destroy();
    }
    $('#detallesContenido').empty();
});
</script>

<!-- MODAL GENERAL PARA VER DETALLES -->
<div class="modal fade" id="detallesModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient text-white border-0 align-items-center">
                <h5 class="modal-title" id="detallesModalTitle"><i class="fas fa-table"></i> Detalles</h5>
                <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- TABLA CARGADA VÍA AJAX -->
                <div id="detallesContenido">
                    <!-- EL CONTENIDO DE LA TABLA SE CARGA AQUÍ -->
                </div>
            </div>
        </div>
    </div>
</div>