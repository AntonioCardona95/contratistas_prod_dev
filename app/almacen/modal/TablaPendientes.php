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
    $folioFiltro = preg_replace('/\D/', '', $_POST['folio'] ?? '');
    $mes_anterior = date('Y-m-d', strtotime('-2 month'));

    if (empty($Fecha1)) {
        $Fecha1 = $mes_anterior . " 00:00:00";
        $Fecha2 = date('Y-m-d') . " 23:59:59";
    } else {
        $Fecha1 = $Fecha1 . " 00:00:00";
        $Fecha2 = $Fecha2 . " 23:59:59";
    }

    $filas = $modal->ObtenerPendientes($idContratistas, $Fecha1, $Fecha2);

    if ($folioFiltro !== '') {
        $filas = array_filter($filas, function($row) use ($folioFiltro) {
            return strpos((string)$row['Folio_Salida_ST'], $folioFiltro) !== false;
        });
    }
?>

<style>
    .modal-header-gradient {
        background: linear-gradient(90deg, #0f172a 0%, #003d99 100%) !important;
    }
    td { align-content: center; }

    #pendientes-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    #pendientes-cards-container {
        display: none;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .pendiente-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .pendiente-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    .pendiente-card-header {
        padding: 18px 18px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .pendiente-card-body {
        padding: 0 18px 18px;
    }

    .pendiente-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f3f5;
        gap: 10px;
    }

    .pendiente-field:last-child {
        border-bottom: none;
    }

    .pendiente-field span {
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        flex: 1;
    }

    .pendiente-field strong {
        color: #1f2937;
        font-size: 0.95rem;
        font-weight: 600;
        text-align: right;
        min-width: 120px;
    }

    @media (max-width: 1200px) {
        #pendientes-cards-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* ── BUSCADOR MOVIL ── */
    #pendientes-mobile-search {
        display: none;
    }

    @media (max-width: 768px) {
        #pendientes-table-wrapper {
            display: none;
        }

        #pendientes-cards-container {
            display: grid;
            grid-template-columns: 1fr;
        }

        #pendientes-mobile-search {
            display: block;
            margin-bottom: 14px;
        }

        .modal-title{
            font-size: 15px !important;
        }

        h2{
            font-size: 15px !important;
        }

    }
</style>

<div id="pendientes-loading" style="text-align: center; padding: 20px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Cargando datos, por favor espere...</p>
</div>

<div id="contenedor-tabla-pendientes" style="opacity: 0; transition: opacity 0.3s ease;">
    <div id="pendientes-table-wrapper">
        <table class="table table-bordered display nowrap" style="width:100%" id="pendientes">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Almacén</th>
                    <th>Unidad de Negocio</th>
                    <th>Solicitante</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
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
                    <th>Fecha</th>
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
                        data-fecha="<?= htmlspecialchars(strtoupper($row['Fecha_Salida_ST'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <td><span class="badge badge-warning">SD-A-<?= $row['Folio_Salida_ST'] ?></span></td>
                        <td><?= strtoupper($row['Almacen'] ?? '') ?></td>
                        <td><?= strtoupper($row['Unidad_Negocio'] ?? '') ?></td>
                        <td><?= strtoupper($row['Solicitante_Salida_T'] ?? '') ?></td>
                        <td><?= number_format($row['Cantidad_ST']) ?></td>
                        <td><?= strtoupper($row['Fecha_Salida_ST'] ?? '') ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm ver-detalle-pendiente"
                                data-folio="<?= $row['Folio_Salida_ST'] ?>"
                                title="VER DETALLES">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div id="pendientes-mobile-search">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="pendientes-search-input" class="form-control" placeholder="Buscar pendientes...">
        </div>
    </div>

    <div id="pendientes-cards-container"></div>
</div>

<!-- MODAL DE DETALLES + RESPUESTA PARCIAL -->
<div class="modal fade" id="detallePendienteModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient text-white border-0 align-items-center">
                <h5 class="modal-title"><i class="fas fa-box-open"></i> Detalle de Salida <span id="detalle-pendiente-folio"></span></h5>
                <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalle-pendiente-contenido">
                <div class="text-center"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn-confirmar-respuesta">
                    <i class="fas fa-paper-plane"></i> Confirmar Respuesta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var table = new DataTable('#pendientes', {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: false,
            searching: true,
            scrollY: true,
            scrollX: true,
            initComplete: function(settings, json) {
                $('#pendientes-loading').hide();
                $('#contenedor-tabla-pendientes').css('opacity', '1');
            }
        });

        // AUTOFILTRAR SI SE LLEGO DESDE UNA NOTIFICACION
        const urlParams = new URLSearchParams(window.location.search);
        const folioBuscar = urlParams.get('folio');
        if (folioBuscar) {
            table.search(folioBuscar).draw();
        }

        function renderPendienteCards() {
            const container = $('#pendientes-cards-container');
            container.empty();

            const rows = table.rows({ search: 'applied' }).nodes();

            if (rows.length === 0) {
                container.html('<div class="text-center text-muted">No se encontraron pendientes.</div>');
                return;
            }

            rows.each(function(node) {
                const row = $(node);
                const folio = row.data('folio');
                const almacen = row.data('almacen');
                const unidad = row.data('unidad');
                const solicitante = row.data('solicitante');
                const cantidad = row.data('cantidad');
                const fecha = row.data('fecha');

                const card = `
                    <div class="pendiente-card">
                        <div class="pendiente-card-header">
                            <div>
                                <div class="font-weight-bold">SD-A-${folio}</div>
                                <div class="text-muted small">${almacen}</div>
                            </div>
                            <button type="button" class="btn btn-info btn-sm ver-detalle-pendiente"
                                data-folio="${folio}"
                                title="VER DETALLES">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="pendiente-card-body">
                            <div class="pendiente-field">
                                <span>Unidad de Negocio</span>
                                <strong>${unidad}</strong>
                            </div>
                            <div class="pendiente-field">
                                <span>Solicitante</span>
                                <strong>${solicitante}</strong>
                            </div>
                            <div class="pendiente-field">
                                <span>Cantidad</span>
                                <strong>${cantidad}</strong>
                            </div>
                            <div class="pendiente-field">
                                <span>Fecha</span>
                                <strong>${fecha}</strong>
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });
        }

        function relocatePendientesControls() {
            const wrapper = $('.dataTables_wrapper');
            const dtInfo = wrapper.find('.dt-info');
            const dtPaging = wrapper.find('.dt-paging');

            if ($('#pendientes table').is(':visible')) {
                $('#pendientes-loading').hide();
                $('#contenedor-tabla-pendientes').css('opacity', '1');
                dtInfo.appendTo(wrapper);
                dtPaging.appendTo(wrapper);
            }
        }

        renderPendienteCards();

        table.on('search.dt page.dt draw.dt', function() {
            renderPendienteCards();
        });

        // Buscador móvil
        $('#pendientes-search-input').on('input', function() {
            table.search($(this).val()).draw();
        });
    });

    var folioActivoPendiente = null;

    // ABRIR EL MODAL Y CARGAR EL DETALLE
    $(document).off('click', '.ver-detalle-pendiente').on('click', '.ver-detalle-pendiente', function(){
        var folio = $(this).data('folio');
        folioActivoPendiente = folio;

        $('#detalle-pendiente-folio').text('SD-A-' + folio);
        $('#detalle-pendiente-contenido').html('<div class="text-center"><div class="spinner-border text-primary"></div></div>');
        $('#detallePendienteModal').modal('show');

        $.get('modal/DetalleSalidaTemp.php', { folio: folio }, function(html){
            $('#detalle-pendiente-contenido').html(html);
        }).fail(function(){
            $('#detalle-pendiente-contenido').html('<div class="text-danger text-center">Error al cargar el detalle.</div>');
        });
    });

    // CONFIRMAR RESPUESTA (ACEPTACION TOTAL, PARCIAL O RECHAZO TOTAL)
    $(document).off('click', '#btn-confirmar-respuesta').on('click', '#btn-confirmar-respuesta', function(){
        if (!folioActivoPendiente) return;

        var seriesAceptadas = [];
        var seriesRechazadas = [];
        var ajustesCantidad = [];

        // SOLO SE LEE LA VERSION REALMENTE VISIBLE (TABLA EN DESKTOP, CARD EN MOVIL)
        // ASI SE EVITA LEER VALORES DE LA VERSION OCULTA QUE EL USUARIO NUNCA TOCO
        $('.fila-serie:visible').each(function(){
            var serie = $(this).data('serie');
            var valor = $(this).find('input[type="radio"]:checked').val();
            if (valor === 'aceptado') {
                seriesAceptadas.push(serie);
            } else if (valor === 'rechazado') {
                seriesRechazadas.push({ serie: serie, motivo: 'numero_serie_incorrecto' });
            }
        });

        $('.fila-linea:visible').each(function(){
            var idLinea = $(this).data('id-linea');
            var solicitada = parseInt($(this).data('cantidad-solicitada'), 10);
            var aceptada = parseInt($(this).find('.input-cantidad-aceptada').val(), 10) || 0;
            ajustesCantidad.push({
                id: idLinea,
                cantidad_solicitada: solicitada,
                cantidad_aceptada: aceptada,
                motivo: aceptada < solicitada ? 'cantidad_incorrecta' : null
            });
        });

        var hayDiferencias = seriesRechazadas.length > 0 || ajustesCantidad.some(a => a.cantidad_aceptada < a.cantidad_solicitada);
        var descripcion = hayDiferencias
            ? 'Vas a confirmar lo correcto y rechazar/ajustar el resto. El almacén será notificado de las diferencias.'
            : 'Vas a confirmar todo el material como correcto.';

        Swal.fire({
            position: 'top',
            icon: 'question',
            title: '¿Confirmar respuesta?',
            html: descripcion,
            showCancelButton: true,
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#1cc88a'
        }).then(function(result){
            if (!result.isConfirmed) return;

            $.ajax({
                url: 'modal/ProcesarRespuestaParcial.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    folio: folioActivoPendiente,
                    seriesAceptadas: seriesAceptadas,
                    seriesRechazadas: JSON.stringify(seriesRechazadas),
                    ajustesCantidad: JSON.stringify(ajustesCantidad)
                }
            }).done(function(response){
                if (response.success) {
                    Swal.fire({
                        position: 'top',
                        icon: 'success',
                        title: 'Respuesta procesada',
                        text: 'El almacén fue notificado del resultado.',
                        showConfirmButton: true
                    });
                    $('#detallePendienteModal').one('hidden.bs.modal', function() {
                        LoadPendientes();
                    });
                    $('#detallePendienteModal').modal('hide');
                } else {
                    Swal.fire({
                        position: 'top',
                        icon: 'error',
                        title: 'Error al procesar',
                        text: response.message || 'Ocurrió un error.',
                        showConfirmButton: true
                    });
                }
            }).fail(function(jqXHR, textStatus){
                Swal.fire({
                    position: 'top',
                    icon: 'error',
                    title: 'Error de red',
                    text: 'No se pudo procesar la respuesta. Intenta de nuevo. (' + textStatus + ')',
                    showConfirmButton: true
                });
            });
        });
    });
</script>