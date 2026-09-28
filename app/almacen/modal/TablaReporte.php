<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');
    
    require_once(__DIR__ . "/../controller/ClassConsultasProduccion.php");
    require_once(__DIR__ . '/../../config/ContratistaContexto.php');
    require_once(__DIR__ . "/cargar.php");

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $Contratista = $_SESSION['Contratista'] ?? null;
    requerirSesionAlmacen();
    $idContratistas = obtenerIdContratistaContexto();

    $modal = new ClassConsultasProduccion();

    $Fecha1 = $_POST['Fecha1'] ?? '';
    $Fecha2 = $_POST['Fecha2'] ?? '';

    $mes_anterior = date('Y-m-d', strtotime('-1 month'));

    if (empty($Fecha1)) {
        $Fecha1 = $mes_anterior . " 00:00:00";
        $Fecha2 = date('Y-m-d') . " 23:59:59";
    } else {
        $Fecha1 = $Fecha1 . " 00:00:00"; 
        $Fecha2 = (!empty($Fecha2) ? $Fecha2 : date('Y-m-d')) . " 23:59:59";
    }

    // Consulta de producción
    $filas = $modal->ConsultarProduccionDiaria($idContratistas, $Fecha1, $Fecha2);

    // DEPURACIÓN: Generar logs para consola
    $totalRegistros = !empty($filas) ? count($filas) : 0;
    $debugInfo = [
        'idContratista' => $idContratistas,
        'Fecha1_Enviada' => $Fecha1,
        'Fecha2_Enviada' => $Fecha2,
        'RegistrosEncontrados' => $totalRegistros
    ];
?>

<!-- DEPURACIÓN EN CONSOLA (Presiona F12 en tu navegador) -->
<script>
    console.group("=== DEPURACIÓN REPORTE AJAX ===");
    console.log("Parámetros recibidos en PHP:", <?= json_encode($debugInfo); ?>);
    <?php if ($totalRegistros === 0): ?>
        console.warn("ADVERTENCIA: La consulta no devolvió ningún registro con estas fechas e ID de Contratista.");
    <?php else: ?>
        console.log("ÉXITO: Se encontraron <?= $totalRegistros ?> filas.");
    <?php endif; ?>
    console.groupEnd();
</script>

<style>
    @media (max-width: 768px) {
        .dt-buttons {
            margin-bottom: 15px;
        }
    }

    #reporte-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    #reporte-produccion_wrapper .dt-scroll-head th,
    #reporte-produccion_wrapper .dt-scroll-foot th,
    #reporte-produccion_wrapper .dt-scroll-body td,
    #reporte-produccion thead th,
    #reporte-produccion tfoot th,
    #reporte-produccion tbody td {
        text-align: center !important;
        vertical-align: middle !important;
    }
</style>

<!-- MENSAJE EN PANTALLA SI NO HAY RESULTADOS -->
<?php if ($totalRegistros === 0): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>No se encontraron registros.</strong> 
        Rango buscado: <code><?= htmlspecialchars($Fecha1) ?></code> a <code><?= htmlspecialchars($Fecha2) ?></code> (ID Contratista: <strong><?= htmlspecialchars($idContratistas ?? 'Sin ID') ?></strong>).
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div id="contenedor-tabla-reporte">
    <div id="reporte-buttons-container"></div>
    <div id="reporte-table-wrapper">
        <table class="table table-bordered display nowrap" style="width:100%" id="reporte-produccion">
            <thead>
                <tr>
                    <th>Folio Pisa</th>
                    <th>Teléfono</th>
                    <th>Tipo Tarea</th>
                    <th>Contratista</th>
                    <th>Técnico</th>
                    <th>Fecha Liq</th>
                    <th>Señalamiento</th>
                    <th>Navegación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Folio Pisa</th>
                    <th>Teléfono</th>
                    <th>Tipo Tarea</th>
                    <th>Contratista</th>
                    <th>Técnico</th>
                    <th>Fecha Liq</th>
                    <th>Señalamiento</th>
                    <th>Navegación</th>
                    <th>Acción</th>
                </tr>
            </tfoot>
            <tbody>
                <?php if (!empty($filas)): foreach ($filas as $row): ?>
                    <tr data-folio="<?= htmlspecialchars($row['Folio_Pisa'], ENT_QUOTES, 'UTF-8') ?>"
                        data-telefono="<?= htmlspecialchars($row['TELEFONO'], ENT_QUOTES, 'UTF-8') ?>"
                        data-contratista="<?= htmlspecialchars(strtoupper($row['Contratista'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-tecnico="<?= htmlspecialchars(strtoupper($row['Tecnico'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-fecha="<?= htmlspecialchars($row['FECHA_LIQ'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        
                        <td><?= htmlspecialchars($row['Folio_Pisa'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['TELEFONO'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Tipo_de_Tarea'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Contratista'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Tecnico'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['FECHA_LIQ'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if (!empty($row['Tiene_Senalamiento'])): ?>
                                <span class="badge badge-warning text-dark">
                                    <?= htmlspecialchars($row['Tipo_Senalamiento'] ?? 'Sí', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Sin señalamiento</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= ($row['Flag_Navega'] ?? 0) == 1 ? 'badge-success' : 'badge-danger' ?>">
                                <?= htmlspecialchars($row['Estatus_Navegacion'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm btn-detalle-reporte" 
                                    data-folio="<?= htmlspecialchars($row['Folio_Pisa'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-telefono="<?= htmlspecialchars($row['TELEFONO'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-fecha="<?= htmlspecialchars($row['FECHA_LIQ'], ENT_QUOTES, 'UTF-8') ?>" 
                                    title="Ver detalle del reporte">
                                <i class="fa-solid fa-file-lines"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#reporte-produccion')) {
            $('#reporte-produccion').DataTable().destroy();
        }

        $('#reporte-produccion').DataTable({
            responsive: true,
            pagingType: 'full_numbers',
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6 d-flex justify-content-end"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                    className: 'btn btn-success btn-sm shadow-sm mr-2',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                    className: 'btn btn-danger btn-sm shadow-sm',
                    exportOptions: { columns: ':not(:last-child)' }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                }
            }
        });
    });
</script>