<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassOps.php");

    $modal = new Ops();
    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];

    $Fecha1 = $_POST['Fecha1'];
    $Fecha2 = $_POST['Fecha2'];

    $mes_anterior = date('Y-m-d', strtotime('-1 month'));

    if(empty($Fecha1)){
        $Fecha1 = "2025-04-14 00:00:00";
        $Fecha2 = date('Y-m-d')." 23:59:59";
        $filas = $modal->Salidas($idContratistas,$Fecha1,$Fecha2);
    }elseif(!empty($Fecha1) && !empty($Fecha2)){
        $Fecha1 = $_POST['Fecha1']." 00:00:00"; 
        $Fecha2 = $_POST['Fecha2']." 23:59:59";
        $filas = $modal->Salidas($idContratistas,$Fecha1,$Fecha2);
    }
?>

<style>
    #salidas-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    #salidas-cards-container {
        display: none;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .salida-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .salida-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    .salida-card-header {
        padding: 18px 18px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .salida-card-body {
        padding: 0 18px 18px;
    }

    .salida-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f3f5;
        gap: 10px;
    }

    .salida-field:last-child {
        border-bottom: none;
    }

    .salida-field span {
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        flex: 1;
    }

    .salida-field strong {
        color: #1f2937;
        font-size: 0.95rem;
        font-weight: 600;
        text-align: right;
        min-width: 120px;
    }

    @media (max-width: 1200px) {
        #salidas-cards-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* ── BUSCADOR MOVIL ── */
    #salidas-mobile-search {
        display: none;
    }

    @media (max-width: 768px) {
        #salidas-table-wrapper {
            display: none;
        }

        #salidas-cards-container {
            display: grid;
            grid-template-columns: 1fr;
        }

        #salidas-mobile-search {
            display: block;
            margin-bottom: 14px;
        }

        .modal-title {
            font-size: 15px !important;
        }

        h2 {
            font-size: 15px !important;
        }
    }
</style>

<div id="salidas-loading" style="text-align: center; padding: 20px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Cargando datos, por favor espere...</p>
</div>

<div id="contenedor-tabla-salidas" style="opacity: 0; transition: opacity 0.3s ease;">
    <div id="salidas-table-wrapper">
        <table class="table table-bordered display nowrap" style="width:100%" id="ventas">
            <thead>
                <tr>
                    <th>Folio Salida</th>
                    <th>Contratista</th>
                    <th>Tecnico</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Folio Salida</th>
                    <th>Contratista</th>
                    <th>Tecnico</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                    <th>Accion</th>
                </tr>
            </tfoot>
            <tbody>
                <?php if (!empty($filas)): foreach ($filas as $row): ?>
                    <tr data-folio="<?= htmlspecialchars($row['Folio_Salida_T'], ENT_QUOTES, 'UTF-8') ?>"
                        data-contratista="<?= htmlspecialchars(strtoupper($row['Contratista'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-tecnico="<?= htmlspecialchars(strtoupper($row['Tecnico'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-cantidad="<?= htmlspecialchars($row['Cantidad_T'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        data-fecha="<?= htmlspecialchars(strtoupper($row['Fecha_T'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <td><?= $row['Folio_Salida_T'] ?></td>
                        <td><?= $row['Contratista'] ?></td>
                        <td><?= $row['Tecnico'] ?></td>
                        <td><?= $row['Cantidad_T'] ?></td>
                        <td><?= $row['Fecha_T'] ?></td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm btn-detalle-salida"
                                data-folio="<?= htmlspecialchars($row['Folio_Salida_T'], ENT_QUOTES, 'UTF-8') ?>"
                                title="Ver detalle">
                                <i class="fa-solid fa-file-lines"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div id="salidas-mobile-search">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="salidas-search-input" class="form-control" placeholder="Buscar salidas...">
        </div>
    </div>

    <div id="salidas-cards-container"></div>
</div>

<script>
	$(document).ready(function () {
        let table = new DataTable('#ventas', {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: false,
            searching: true,
            scrollY: true,
            scrollX: true,
            initComplete: function(settings, json) {
                $('#salidas-loading').hide();
                $('#contenedor-tabla-salidas').css('opacity', '1');
            }
        });

        new DataTable.Buttons(table, {
                buttons: [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fas fa-file-excel"></i> ',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend:    'pdfHtml5',
                        text:      '<i class="fas fa-file-pdf"></i> ',
                        titleAttr: 'Exportar a PDF',
                        className: 'btn btn-danger'
                    }
                ],
        });
        
        table
            .buttons(0, null)
            .container()
            .prependTo(table.table().container());

        function renderSalidaCards() {
            const container = $('#salidas-cards-container');
            container.empty();

            const rows = table.rows({ search: 'applied' }).nodes();

            if (rows.length === 0) {
                container.html('<div class="text-center text-muted">No se encontraron salidas.</div>');
                return;
            }

            rows.each(function(node) {
                const row = $(node);
                const folio       = row.data('folio');
                const contratista = row.data('contratista');
                const tecnico     = row.data('tecnico');
                const cantidad    = row.data('cantidad');
                const fecha       = row.data('fecha');

                const card = `
                    <div class="salida-card">
                        <div class="salida-card-header">
                            <div>
                                <div class="font-weight-bold">${folio}</div>
                                <div class="text-muted small">${contratista}</div>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm btn-detalle-salida"
                                data-folio="${folio}" title="Ver detalle">
                                <i class="fa-solid fa-file-lines"></i>
                            </button>
                        </div>
                        <div class="salida-card-body">
                            <div class="salida-field">
                                <span>Técnico</span>
                                <strong>${tecnico}</strong>
                            </div>
                            <div class="salida-field">
                                <span>Cantidad</span>
                                <strong>${cantidad}</strong>
                            </div>
                            <div class="salida-field">
                                <span>Fecha</span>
                                <strong>${fecha}</strong>
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });
        }

        renderSalidaCards();

        table.on('search.dt page.dt draw.dt', function() {
            renderSalidaCards();
        });

        // Buscador móvil
        $('#salidas-search-input').on('input', function() {
            table.search($(this).val()).draw();
        });
	});
</script>