<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');
    
    // Controlador y configuraciones
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
    $filas = $modal->ConsultarProduccionDiaria($Fecha1, $Fecha2);
?>

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

    #reporte-cards-container {
        display: none;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .reporte-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .reporte-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    @media (max-width: 1200px) {
        #reporte-cards-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* BUSCADOR MOVIL */
    #reporte-mobile-search {
        display: none;
    }

    @media (max-width: 768px) {
        #reporte-table-wrapper {
            display: none;
        }

        #reporte-cards-container {
            display: grid;
            grid-template-columns: 1fr;
        }

        #reporte-mobile-search {
            display: block;
            margin-bottom: 14px;
        }

        .modal-title, h2 {
            font-size: 15px !important;
        }
    }

    /* Contenedor con scroll único y limpio */
#reporte-table-wrapper {
    overflow-x: auto;
    width: 100%;
}

/* Fijar únicamente la 1ª columna (Folio Pisa) */
#reporte-produccion th:first-child,
#reporte-produccion td:first-child {
    position: sticky;
    left: 0;
    background-color: #ffffff !important; /* Fondo sólido para que no se transparente el texto */
    z-index: 10;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Ligera sombra elegante al deslizar */
}

/* Asegurar que la cabecera quede arriba del contenido al scroll */
#reporte-produccion th:first-child {
    z-index: 11;
}

/* Altura fija con scroll vertical y horizontal propio */
#reporte-table-wrapper {
    max-height: 500px; /* Define la altura que prefieras (ej. 500px o 60vh) */
    overflow-y: auto;  /* Barra vertical propia de la tabla */
    overflow-x: auto;  /* Barra horizontal propia */
    position: relative;
}

/* Congelar cabecera arriba dentro del contenedor */
#reporte-produccion thead th {
    position: sticky;
    top: 0;
    background-color: #f8f9fc !important;
    z-index: 12;
}

/* Congelar celda de intersección (Folio Pisa en thead) */
#reporte-produccion thead th:first-child {
    z-index: 20 !important;
}



</style>

<div id="contenedor-tabla-reporte">
    <div id="reporte-buttons-container"></div>
    <div id="reporte-table-wrapper">
        <table class="table table-bordered display nowrap" style="width:100%" id="reporte-produccion">
            <thead>
                <tr>
                    <th>Folio Pisa</th>
                    <th>Teléfono</th>
                    <th>Tipo Tarea</th>
                    <th>Área</th>
                    <th>División</th>
                    <th>Cope</th>
                    <th>Contratista</th>
                    <th>Técnico</th>
                    <th>Nº Serie TAC</th>
                    <th>Contratista Asignado</th>
                    <th>Técnico Asignado</th>
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
                    <th>Área</th>
                    <th>División</th>
                    <th>Cope</th>
                    <th>Contratista</th>
                    <th>Técnico</th>
                    <th>Nº Serie TAC</th>
                    <th>Contratista Asignado</th>
                    <th>Técnico Asignado</th>
                    <th>Fecha Liq</th>
                    <th>Señalamiento</th>
                    <th>Navegación</th>
                    <th>Acción</th>
                </tr>
            </tfoot>
            <tbody>
                <?php if (!empty($filas)): foreach ($filas as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Folio_Pisa'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['TELEFONO'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Tipo_de_Tarea'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['NOM_AREA'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['NOM_DIVISION'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['NOM_CT'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Contratista'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Tecnico'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Num__Serie'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>

                        <!-- Contratista Asignado -->
                        <td><?= htmlspecialchars($row['Contratista_Asignado'] ?? 'Sin asignar', ENT_QUOTES, 'UTF-8') ?></td>

                        <!-- Técnico Asignado -->
                        <td><?= htmlspecialchars($row['Tecnico_Asignado'] ?? 'Sin asignar', ENT_QUOTES, 'UTF-8') ?></td>

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



    <div id="reporte-mobile-search">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="reporte-search-input" class="form-control" placeholder="Buscar en el reporte...">
        </div>
    </div>

    <div id="reporte-cards-container"></div>
</div>
<script>
    (function() {
        // Destruir la instancia previa si existe
        if ($.fn.DataTable.isDataTable('#reporte-produccion')) {
            $('#reporte-produccion').DataTable().clear().destroy();
            $('#reporte-produccion').empty(); 
        }

        // Inicializar DataTables limpio
        $('#reporte-produccion').DataTable({
            responsive: false,
            pagingType: 'full_numbers',
            dom: '<"row mb-3"<"col-md-6"B><"col-md-6 d-flex justify-content-end"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                    className: 'btn btn-success btn-sm mr-2',
                    exportOptions: { columns: ':not(:last-child)' }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: { columns: ':not(:last-child)' }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });
    })();
</script>