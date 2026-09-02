<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassOps.php");
    $modal = new Ops();

    session_start();
    $idContratistas = $_SESSION['idContratistas'];

    $Fecha1 = $_POST['Fecha1'] ?? null;
    $Fecha2 = $_POST['Fecha2'] ?? null;
    $mes_anterior = date('Y-m-d', strtotime('-2 month'));

    if (empty($Fecha1)) {
        $Fecha1 = $mes_anterior . " 00:00:00";
        $Fecha2 = date('Y-m-d') . " 23:59:59";
    } else {
        $Fecha1 = $Fecha1 . " 00:00:00";
        $Fecha2 = $Fecha2 . " 23:59:59";
    }

    $filas = $modal->ObtenerRechazados($idContratistas, $Fecha1, $Fecha2);
?>

<style>
    @media (max-width: 768px) {
        .dt-buttons {
            margin-bottom: 15px;
        }
    }

    #rechazados-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    #rechazados-cards-container {
        display: none;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .rechazado-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .rechazado-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    .rechazado-card-header {
        padding: 18px 18px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .rechazado-card-body {
        padding: 0 18px 18px;
    }

    .rechazado-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f3f5;
        gap: 10px;
    }

    .rechazado-field:last-child {
        border-bottom: none;
    }

    .rechazado-field span {
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        flex: 1;
    }

    .rechazado-field strong {
        color: #1f2937;
        font-size: 0.95rem;
        font-weight: 600;
        text-align: right;
        min-width: 120px;
    }

    @media (max-width: 1200px) {
        #rechazados-cards-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* ── BUSCADOR MOVIL ── */
    #rechazados-mobile-search {
        display: none;
    }

    @media (max-width: 768px) {
        #rechazados-table-wrapper {
            display: none;
        }

        #rechazados-cards-container {
            display: grid;
            grid-template-columns: 1fr;
        }

        #rechazados-mobile-search {
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

<!-- CONTENEDOR PRINCIPAL DE LA TABLA, CON OPACIDAD 0 DE INICIO PARA EL EFECTO DE CARGA -->
<div id="rechazados-loading" style="text-align: center; padding: 20px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Cargando datos, por favor espere...</p>
</div>

<div id="contenedor-tabla-rechazados" style="opacity: 0; transition: opacity 0.3s ease;">
    <!-- BOTON DE EXCEL -->
    <div id="rechazados-buttons-container"></div>

    <div id="rechazados-table-wrapper">
        <table class="table table-bordered display nowrap datatable-init" style="width:100%" id="rechazados">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Almacén</th>
                    <th>Unidad de Negocio</th>
                    <th>Solicitante</th>
                    <th>Cantidad</th>
                    <th>Fecha Salida</th>
                    <th>Fecha Rechazo</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Folio</th>
                    <th>Almacén</th>
                    <th>Unidad de Negocio</th>
                    <th>Solicitante</th>
                    <th>Cantidad</th>
                    <th>Fecha Salida</th>
                    <th>Fecha Rechazo</th>
                    <th>Acción</th>
                </tr>
            </tfoot>
            <tbody>
                <?php if (!empty($filas)): foreach ($filas as $row): ?>
                    <tr data-folio="<?= $row['Folio_Salida_ST'] ?>"
                        data-almacen="<?= htmlspecialchars(strtoupper($row['Almacen'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-unidad="<?= htmlspecialchars(strtoupper($row['Unidad_Negocio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-solicitante="<?= htmlspecialchars(strtoupper($row['Solicitante_Salida_T'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-cantidad="<?= number_format($row['Cantidad_ST']) ?>"
                        data-fecha-salida="<?= htmlspecialchars(strtoupper($row['Fecha_Salida_ST'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-fecha-rechazo="<?= htmlspecialchars(strtoupper($row['Fecha_Respuesta'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <td><span class="badge badge-danger">SD-A-<?= $row['Folio_Salida_ST'] ?></span></td>
                        <td><?= strtoupper($row['Almacen'] ?? '') ?></td>
                        <td><?= strtoupper($row['Unidad_Negocio'] ?? '') ?></td>
                        <td><?= strtoupper($row['Solicitante_Salida_T'] ?? '') ?></td>
                        <td><?= number_format($row['Cantidad_ST']) ?></td>
                        <td><?= strtoupper($row['Fecha_Salida_ST'] ?? '') ?></td>
                        <td><?= strtoupper($row['Fecha_Respuesta'] ?? '') ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-dark btn-sm ver-detalle-rechazado"
                                data-folio="<?= $row['Folio_Salida_ST'] ?>"
                                title="VER DETALLES">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div id="rechazados-mobile-search">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="rechazados-search-input" class="form-control" placeholder="Buscar rechazados...">
        </div>
    </div>

    <div id="rechazados-cards-container"></div>
</div>


<!-- MODAL DE DETALLES - SOLO LECTURA -->
<div class="modal fade" id="detalleRechazadoModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient text-white border-0 align-items-center" style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%) !important;">
                <h5 class="modal-title"><i class="fas fa-circle-xmark"></i> Detalle de Salida Rechazada <span id="detalle-rechazado-folio"></span></h5>
                <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalle-rechazado-contenido">
                <div class="text-center"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var table = new DataTable('#rechazados', {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: false,
            searching: true,
            scrollY: true,
            scrollX: true,
            initComplete: function(settings, json) {
                $('#rechazados-loading').hide();
                $('#contenedor-tabla-rechazados').css('opacity', '1');
            }
        });

        new DataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success'
                }
            ]
        });

        table.buttons(0, null).container().appendTo('#rechazados-buttons-container');

        const urlParams = new URLSearchParams(window.location.search);
        const folioBuscar = urlParams.get('folio');
        if (folioBuscar) {
            table.search(folioBuscar).draw();
        }

        function renderRechazadoCards() {
            const container = $('#rechazados-cards-container');
            container.empty();

            const rows = table.rows({ search: 'applied' }).nodes();

            if (rows.length === 0) {
                container.html('<div class="text-center text-muted">No se encontraron rechazados.</div>');
                return;
            }

            rows.each(function(node) {
                const row = $(node);
                const folio        = row.data('folio');
                const almacen      = row.data('almacen');
                const unidad       = row.data('unidad');
                const solicitante  = row.data('solicitante');
                const cantidad     = row.data('cantidad');
                const fechaSalida  = row.data('fecha-salida');
                const fechaRechazo = row.data('fecha-rechazo');

                const card = `
                    <div class="rechazado-card">
                        <div class="rechazado-card-header">
                            <div>
                                <div class="font-weight-bold">SD-A-${folio}</div>
                                <div class="text-muted small">${almacen}</div>
                            </div>
                            <button type="button" class="btn btn-dark btn-sm ver-detalle-rechazado"
                                data-folio="${folio}"
                                title="VER DETALLES">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="rechazado-card-body">
                            <div class="rechazado-field">
                                <span>Unidad de Negocio</span>
                                <strong>${unidad}</strong>
                            </div>
                            <div class="rechazado-field">
                                <span>Solicitante</span>
                                <strong>${solicitante}</strong>
                            </div>
                            <div class="rechazado-field">
                                <span>Cantidad</span>
                                <strong>${cantidad}</strong>
                            </div>
                            <div class="rechazado-field">
                                <span>Fecha Salida</span>
                                <strong>${fechaSalida}</strong>
                            </div>
                            <div class="rechazado-field">
                                <span>Fecha Rechazo</span>
                                <strong>${fechaRechazo}</strong>
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });
        }

        renderRechazadoCards();

        table.on('search.dt page.dt draw.dt', function() {
            renderRechazadoCards();
        });

        // Buscador móvil
        $('#rechazados-search-input').on('input', function() {
            table.search($(this).val()).draw();
        });
    });

    $(document).off('click', '.ver-detalle-rechazado').on('click', '.ver-detalle-rechazado', function(){
        var folio = $(this).data('folio');

        $('#detalle-rechazado-folio').text('SD-A-' + folio);
        $('#detalle-rechazado-contenido').html('<div class="text-center"><div class="spinner-border text-primary"></div></div>');
        $('#detalleRechazadoModal').modal('show');

        $.get('modal/DetalleSalidaTemp.php', { folio: folio, solo_lectura: 1 }, function(html){
            $('#detalle-rechazado-contenido').html(html);
        }).fail(function(){
            $('#detalle-rechazado-contenido').html('<div class="text-danger text-center">Error al cargar el detalle.</div>');
        });
    });
</script>