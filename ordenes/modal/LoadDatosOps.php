
<?php
/**
 * Tabla de Órdenes Operaciones - Contratistas
 * Muestra las órdenes con cálculo de distancia entre cliente y terminal.
 * Incluye exportación a Excel y PDF, y enlaces a mapas y galería de imágenes.
 * 
 * @author  Ing. Antonio Cardona
 * @since   1.0
 * @version 2.1
 */

// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusión de clases necesarias
require_once("../controller/ClassContratista.php");

// Inicio de sesión y obtención de variables de sesión
session_start();
$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

// Recepción y validación de parámetros de fechas
$Fecha1 = $_POST['Fecha1'] ?? '';
$Fecha2 = $_POST['Fecha2'] ?? '';
$mes_anterior = date('Y-m-d', strtotime('-1 month'));

if (empty($Fecha1)) {
    $Fecha1 = $mes_anterior . " 00:00:00";
    $Fecha2 = date('Y-m-d') . " 23:59:59";
} else {
    $Fecha1 = $Fecha1 . " 00:00:00";
    $Fecha2 = $Fecha2 . " 23:59:59";
}

// Consulta de órdenes
$modal = new Coordiapp();
$filas = $modal->OrdenesCoordiApp($idContratistas, $Fecha1, $Fecha2);
$filas = is_array($filas) ? $filas : [];

$modaltac = new Tac();
// OrdenesTac expects date-only parameters (it does tac.FECHA_LIQ >= :Fecha1 AND tac.FECHA_LIQ < DATE_ADD(:Fecha2, INTERVAL 1 DAY))
// If we pass a datetime ending with 23:59:59, DATE_ADD will extend into the next day end and include extra rows.
// Convert to date-only (YYYY-MM-DD) to match the intended behavior when the user selects days.
$Fecha1_date = substr($Fecha1, 0, 10);
$Fecha2_date = substr($Fecha2, 0, 10);
$filas_tac = $modaltac->OrdenesTac($idContratistas, $Fecha1_date, $Fecha2_date);
$filas_tac = is_array($filas_tac) ? $filas_tac : [];

// Construir conjuntos de claves para detectar duplicados (por Folio_Pisa o Expediente)
$keys_coordi = [];
foreach ($filas as $r) {
    $k = trim((string)($r['Folio_Pisa'] ?? $r['NExpediente'] ?? ''));
    if ($k !== '') $keys_coordi[$k] = true;
}
$keys_tac = [];
foreach ($filas_tac as $r) {
    $k = trim((string)($r['Folio_Pisa'] ?? $r['Expediente'] ?? ''));
    if ($k !== '') $keys_tac[$k] = true;
}

// Normalizar y combinar los resultados de ambas fuentes en un solo arreglo
$combined = [];

// Normalizar Coordiapp
foreach ($filas as $r) {
    $key = trim((string)($r['Folio_Pisa'] ?? $r['NExpediente'] ?? ''));
    $status = ($key !== '' && isset($keys_tac[$key])) ? 'both' : 'single';
    $combined[] = [
        'Folio_Pisa' => $r['Folio_Pisa'] ?? '',
        'Telefono' => $r['Telefono'] ?? '',
        'Division' => $r['Division'] ?? '',
        'Area' => $r['area'] ?? '',
        'Cope' => $r['COPE'] ?? '',
        'Contratista' => trim((($r['Contratista'] ?? '') . ' ' . strtoupper($r['apellido_paterno'] ?? '') . ' ' . strtoupper($r['apellido_materno'] ?? ''))),
        'Expediente' => $r['NExpediente'] ?? '',
        'Tecnico' => trim((($r['Nombre_T'] ?? '') . ' ' . ($r['Apellidos_T'] ?? ''))),
        'Fecha_Liquidacion' => $r['Fecha_Coordiapp'] ?? '',
        'Origen' => 'COORDIAPP',
        'status' => $status
    ];
}

// Normalizar TAC
foreach ($filas_tac as $r) {
    $key = trim((string)($r['Folio_Pisa'] ?? $r['Expediente'] ?? ''));
    $status = ($key !== '' && isset($keys_coordi[$key])) ? 'both' : 'single';
    $combined[] = [
        'Folio_Pisa' => $r['Folio_Pisa'] ?? '',
        'Telefono' => $r['TELEFONO'] ?? $r['Telefono'] ?? '',
        'Division' => $r['NOM_DIVISION'] ?? '',
        'Area' => $r['NOM_AREA'] ?? '',
        'Cope' => $r['NOM_CT'] ?? '',
        'Contratista' => $r['NOM_CT'] ?? '',
        'Expediente' => $r['Expediente'] ?? '',
        'Tecnico' => $r['Tecnico'] ?? '',
        'Fecha_Liquidacion' => $r['FECHA_LIQ'] ?? '',
        'Origen' => 'TAC',
        'status' => $status
    ];
}

// Ordenar combinado por fecha descendente (asumiendo formato YYYY-MM-DD HH:MM:SS)
usort($combined, function($a, $b){
    return strcmp($b['Fecha_Liquidacion'] ?? '', $a['Fecha_Liquidacion'] ?? '');
});

// --- Agregación para gráfica: conteo por Division y por Origen (COORDIAPP / TAC)
$division_counts = [];
// Contar a partir de las filas originales COORDIAPP
foreach ($filas as $r) {
    $div = trim((string)($r['Division'] ?? $r['NOM_DIVISION'] ?? 'SIN DIVISION')) ?: 'SIN DIVISION';
    if (!isset($division_counts[$div])) $division_counts[$div] = ['COORDIAPP' => 0, 'TAC' => 0];
    $division_counts[$div]['COORDIAPP']++;
}
// Contar a partir de las filas originales TAC
foreach ($filas_tac as $r) {
    $div = trim((string)($r['NOM_DIVISION'] ?? $r['Division'] ?? 'SIN DIVISION')) ?: 'SIN DIVISION';
    if (!isset($division_counts[$div])) $division_counts[$div] = ['COORDIAPP' => 0, 'TAC' => 0];
    $division_counts[$div]['TAC']++;
}

// Asegurar orden consistente
ksort($division_counts);
$division_labels = array_keys($division_counts);
$division_coordi = array_map(fn($v) => $v['COORDIAPP'] ?? 0, $division_counts);
$division_tac = array_map(fn($v) => $v['TAC'] ?? 0, $division_counts);

// --- Agregación para gráfica: conteo por Area y por Origen (COORDIAPP / TAC)
$area_counts = [];
// Contar a partir de las filas originales COORDIAPP
foreach ($filas as $r) {
    $area = trim((string)($r['area'] ?? $r['Area'] ?? 'SIN AREA')) ?: 'SIN AREA';
    if (!isset($area_counts[$area])) $area_counts[$area] = ['COORDIAPP' => 0, 'TAC' => 0];
    $area_counts[$area]['COORDIAPP']++;
}
// Contar a partir de las filas originales TAC
foreach ($filas_tac as $r) {
    $area = trim((string)($r['NOM_AREA'] ?? $r['area'] ?? $r['Area'] ?? 'SIN AREA')) ?: 'SIN AREA';
    if (!isset($area_counts[$area])) $area_counts[$area] = ['COORDIAPP' => 0, 'TAC' => 0];
    $area_counts[$area]['TAC']++;
}

ksort($area_counts);
$area_labels = array_keys($area_counts);
$area_coordi = array_map(fn($v) => $v['COORDIAPP'] ?? 0, $area_counts);
$area_tac = array_map(fn($v) => $v['TAC'] ?? 0, $area_counts);

// --- Agregación para gráfica: conteo por Cope (NOM_CT / COPE) y por Origen
$cope_counts = [];
// Contar a partir de las filas originales COORDIAPP
foreach ($filas as $r) {
    $cope = trim((string)($r['COPE'] ?? $r['Cope'] ?? $r['NOM_CT'] ?? 'SIN COPE')) ?: 'SIN COPE';
    if (!isset($cope_counts[$cope])) $cope_counts[$cope] = ['COORDIAPP' => 0, 'TAC' => 0];
    $cope_counts[$cope]['COORDIAPP']++;
}
// Contar a partir de las filas originales TAC
foreach ($filas_tac as $r) {
    $cope = trim((string)($r['NOM_CT'] ?? $r['COPE'] ?? 'SIN COPE')) ?: 'SIN COPE';
    if (!isset($cope_counts[$cope])) $cope_counts[$cope] = ['COORDIAPP' => 0, 'TAC' => 0];
    $cope_counts[$cope]['TAC']++;
}

ksort($cope_counts);
$cope_labels = array_keys($cope_counts);
$cope_coordi = array_map(fn($v) => $v['COORDIAPP'] ?? 0, $cope_counts);
$cope_tac = array_map(fn($v) => $v['TAC'] ?? 0, $cope_counts);

?>
<!-- Título centrado para la tabla combinada -->
<div class="text-center mb-3">
    <h4 class="font-weight-bold">DATOS COORDIAPP/TAC</h4>
</div>

<!-- Gráfica: Producción por División -->
<div class="card mb-3">
    <div class="card-header">
        <strong>PRODUCCION DIVISION</strong>
    </div>
    <div class="card-body">
        <canvas id="produccionDivisionChart" style="width:100%;height:260px;"></canvas>
    </div>
    <div class="card-footer small text-muted">Comparativa COORDIAPP vs TAC por división</div>
</div>

<!-- Gráfica: Producción por Area -->
<div class="card mb-3">
    <div class="card-header">
        <strong>PRODUCCION AREA</strong>
    </div>
    <div class="card-body">
        <canvas id="produccionAreaChart" style="width:100%;height:260px;"></canvas>
    </div>
    <div class="card-footer small text-muted">Comparativa COORDIAPP vs TAC por área</div>
</div>

<!-- Gráfica: Producción por Cope -->
<div class="card mb-3">
    <div class="card-header">
        <strong>PRODUCCION COPE</strong>
    </div>
    <div class="card-body">
        <canvas id="produccionCopeChart" style="width:100%;height:260px;"></canvas>
    </div>
    <div class="card-footer small text-muted">Comparativa COORDIAPP vs TAC por COPE</div>
</div>

<table class="table table-bordered display nowrap datatable-init" style="width:100%" id="combined">
    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Division</th>
            <th>Area</th>
            <th>Cope</th>
            <th>Contratista</th>
            <th>Expediente</th>
            <th>Tecnico</th>
            <th>Fecha Liquidacion</th>
            <th>Origen</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Division</th>
            <th>Area</th>
            <th>Cope</th>
            <th>Contratista</th>
            <th>Expediente</th>
            <th>Tecnico</th>
            <th>Fecha Liquidacion</th>
            <th>Origen</th>
        </tr>
    </tfoot>
    <tbody>
    <?php foreach ($combined as $row):?>
        <?php $rowClass = ($row['status'] ?? 'single') === 'both' ? 'table-success' : 'table-danger'; ?>
        <tr class="<?= $rowClass ?>">
            <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Telefono'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Division'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Area'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Cope'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Contratista'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Expediente'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Tecnico'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Fecha_Liquidacion'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Origen'] ?? '') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
    $(document).ready(function(){
        // Opciones comunes (ajusta según necesites)
        const dtOptions = {
            fixedColumns: { start: 1, end: 1 },
            responsive: true,
            language: { URL: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: false,
            searching: true,
            scrollY: true,
            scrollX: true
        };

        // Botones comunes
        const dtButtons = [
            {
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel"></i> ',
            titleAttr: 'Exportar a Excel',
            className: 'btn btn-success'
            },
            {
            extend: 'pdfHtml5',
            text: '<i class="fas fa-file-pdf"></i> ',
            titleAttr: 'Exportar a PDF',
            className: 'btn btn-danger'
            }
        ];

        // Inicializar cada tabla con la clase datatable-init
        $('.datatable-init').each(function(){
            const $table = $(this);
            // Inicializa DataTable sobre el DOM element (this)
            const table = new DataTable(this, dtOptions);

            // Crear botones para esta instancia
            new DataTable.Buttons(table, { buttons: dtButtons });

            // Inserta los botones en el contenedor de la tabla (mismo comportamiento que tenías)
            table
            .buttons(0, null)
            .container()
            .prependTo(table.table().container());
        });
    });
</script>

<!-- Script para generar gráfica utilizando Chart.js -->
<script>
    (function(){
        // Datos Division
        const labels = <?= json_encode(array_values($division_labels)) ?>;
        const dataCoordi = <?= json_encode(array_values($division_coordi)) ?>;
        const dataTac = <?= json_encode(array_values($division_tac)) ?>;

        const ctx = document.getElementById('produccionDivisionChart').getContext('2d');
        // Asegurar que las barras no se solapen y establecer grosor consistente
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'COORDIAPP',
                        backgroundColor: 'rgba(75, 192, 192, 0.85)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        data: dataCoordi,
                        order: 1,
                        barPercentage: 0.6,
                        categoryPercentage: 0.6,
                        maxBarThickness: 60
                    },
                    {
                        label: 'TAC',
                        backgroundColor: 'rgba(255, 99, 132, 0.85)',
                        borderColor: 'rgba(255,99,132,1)',
                        borderWidth: 1,
                        data: dataTac,
                        order: 2,
                        barPercentage: 0.6,
                        categoryPercentage: 0.6,
                        maxBarThickness: 60
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, min: 0 }
                },
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                }
            }
        });

    // Datos Area
    const labelsArea = <?= json_encode(array_values($area_labels)) ?>;
    const dataCoordiArea = <?= json_encode(array_values($area_coordi)) ?>;
    const dataTacArea = <?= json_encode(array_values($area_tac)) ?>;

    const ctxArea = document.getElementById('produccionAreaChart').getContext('2d');
        new Chart(ctxArea, {
            type: 'bar',
            data: {
                labels: labelsArea,
                datasets: [
                    {
                        label: 'COORDIAPP',
                        backgroundColor: 'rgba(75, 192, 192, 0.85)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        data: dataCoordiArea,
                        order: 1,
                        barPercentage: 0.6,
                        categoryPercentage: 0.6,
                        maxBarThickness: 60
                    },
                    {
                        label: 'TAC',
                        backgroundColor: 'rgba(255, 99, 132, 0.85)',
                        borderColor: 'rgba(255,99,132,1)',
                        borderWidth: 1,
                        data: dataTacArea,
                        order: 2,
                        barPercentage: 0.6,
                        categoryPercentage: 0.6,
                        maxBarThickness: 60
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, min: 0 }
                },
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                }
            }
        });
        // Datos Cope
        const labelsCope = <?= json_encode(array_values($cope_labels)) ?>;
        const dataCoordiCope = <?= json_encode(array_values($cope_coordi)) ?>;
        const dataTacCope = <?= json_encode(array_values($cope_tac)) ?>;

        const ctxCope = document.getElementById('produccionCopeChart').getContext('2d');
        new Chart(ctxCope, {
            type: 'bar',
            data: {
                labels: labelsCope,
                datasets: [
                    {
                        label: 'COORDIAPP',
                        backgroundColor: 'rgba(75, 192, 192, 0.85)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        data: dataCoordiCope,
                        order: 1,
                        barPercentage: 0.6,
                        categoryPercentage: 0.6,
                        maxBarThickness: 60
                    },
                    {
                        label: 'TAC',
                        backgroundColor: 'rgba(255, 99, 132, 0.85)',
                        borderColor: 'rgba(255,99,132,1)',
                        borderWidth: 1,
                        data: dataTacCope,
                        order: 2,
                        barPercentage: 0.6,
                        categoryPercentage: 0.6,
                        maxBarThickness: 60
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, min: 0 }
                },
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                }
            }
        });
    })();
</script>