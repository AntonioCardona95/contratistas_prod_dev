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

    // OBTIENE LAS SALIDAS PENDIENTES ASOCIADAS AL CONTRATISTA ACTUAL.
    $filas = $modal->ObtenerPendientes($idContratistas, $Fecha1, $Fecha2);

    // SI SE RECIBIO UN FOLIO, SE FILTRAN LOS RESULTADOS PARA MOSTRAR SOLO LA SALIDA RELEVANTE.
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

<!-- CONTENEDOR DE CARGA QUE SE MUESTRA MIENTRAS SE PREPARAN LOS DATOS. -->
<div id="pendientes-loading" style="text-align: center; padding: 20px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Cargando datos, por favor espere...</p>
</div>

<!-- CONTENEDOR PRINCIPAL DE LA TABLA Y LAS TARJETAS RESPONSIVAS. -->
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
<!-- AL HACER CLIC EN VER DETALLES, SE ABRE ESTE MODAL PARA MOSTRAR EL CONTENIDO DE LA SALIDA Y PERMITIR LA RESPUESTA -->
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
                <button type="button" class="btn btn-danger" id="btn-rechazar-todo">
                    <i class="fas fa-ban"></i> Rechazar todo
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn-confirmar-respuesta">
                    <i class="fas fa-paper-plane"></i> Confirmar Respuesta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ESTO INICIALIZA LA TABLA DE PENDIENTES CON DATATABLES Y LA HACE RESPONSIVA.
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
                setTimeout(aplicarFiltroDesdeUrl, 50);
            }
        });

        var filtroAplicado = false;

        // APLICA EL FILTRO POR FOLIO CUANDO LA VISTA LLEGA DESDE UNA NOTIFICACION.
        function aplicarFiltroDesdeUrl() {
            if (filtroAplicado) return;

            const urlParams = new URLSearchParams(window.location.search);
            const folioBuscar = urlParams.get('folio');
            if (!folioBuscar) return;

            const folioNumerico = folioBuscar.replace(/\D/g, '');
            if (!folioNumerico.length) return;

            filtroAplicado = true;
            table.search(folioNumerico).draw();

            setTimeout(function() {
                const hayResultado = table.rows({ search: 'applied' }).data().length > 0;
                if (!hayResultado) {
                    table.search(folioBuscar).draw();
                }
            }, 100);
        }

        // GENERA LAS TARJETAS VISIBLES EN DISPOSITIVOS MOVILES A PARTIR DE LOS RESULTADOS FILTRADOS.
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
    var rechazarTodo = false;

    // CUANDO EL USUARIO PRESIONA VER DETALLES, SE GUARDA EL FOLIO ACTIVO Y SE CARGA EL MODAL CON LA INFORMACION DE LA SALIDA.
    $(document).off('click', '.ver-detalle-pendiente').on('click', '.ver-detalle-pendiente', function(){
        var folio = $(this).data('folio');
        folioActivoPendiente = folio;

        $('#detalle-pendiente-folio').text('SD-A-' + folio);
        $('#detalle-pendiente-contenido').html('<div class="text-center"><div class="spinner-border text-primary"></div></div>');
        $('#detallePendienteModal').modal('show');
        rechazarTodo = false;

        $.get('modal/DetalleSalidaTemp.php', { folio: folio }, function(html){
            $('#detalle-pendiente-contenido').html(html);
        }).fail(function(){
            $('#detalle-pendiente-contenido').html('<div class="text-danger text-center">Error al cargar el detalle.</div>');
        });
    });

    // RECHAZAR TODO COMO NO SOLICITADO.
    $(document).off('click', '#btn-rechazar-todo').on('click', '#btn-rechazar-todo', function(){
        if (!folioActivoPendiente) return;
        rechazarTodo = true;
        $('#btn-confirmar-respuesta').trigger('click');
    });

    // AL CONFIRMAR LA RESPUESTA, SE RECOGEN LAS SERIES Y CANTIDADES ACEPTADAS O RECHAZADAS PARA PROCESARLA.
    $(document).off('click', '#btn-confirmar-respuesta').on('click', '#btn-confirmar-respuesta', function(){
        if (!folioActivoPendiente) return;

        var seriesAceptadas = [];
        var seriesRechazadas = [];
        var ajustesCantidad = [];
        var seenSeries = {};
        var seenLineas = {};
        var motivoGeneral = rechazarTodo ? 'no_solicitado' : null;

        var rowsSerie = $('#detalle-pendiente-contenido').find('.fila-serie').toArray();
        rowsSerie.forEach(function(row){
            var $row = $(row);
            var serie = $row.data('serie');
            if (!serie || seenSeries[serie]) return;

            if (rechazarTodo) {
                seriesRechazadas.push({ serie: serie, motivo: motivoGeneral });
            } else {
                var inputName = $row.find('input[type="radio"]').attr('name');
                var valor = null;
                if (inputName) {
                    valor = $('#detalle-pendiente-contenido').find('input[name="' + inputName + '"]:checked').val();
                }

                if (!valor) {
                    var $activeLabel = $row.find('label.active');
                    if ($activeLabel.length) {
                        valor = $activeLabel.find('input[type="radio"]').val();
                    }
                }

                if (!valor) {
                    valor = $row.find('input[type="radio"]').first().val() || 'aceptado';
                }

                if (valor === 'aceptado') {
                    seriesAceptadas.push(serie);
                } else if (valor === 'rechazado') {
                    seriesRechazadas.push({ serie: serie, motivo: 'numero_serie_incorrecto' });
                }
            }

            seenSeries[serie] = true;
        });

        var rowsLinea = $('#detalle-pendiente-contenido').find('.fila-linea').toArray();
        rowsLinea.forEach(function(row){
            var $row = $(row);
            var idLinea = $row.data('id-linea');
            var seriesTotal = parseInt($row.data('series-total'), 10) || 0;
            if (!idLinea || seenLineas[idLinea] || seriesTotal > 0) return;

            var sameLineaRows = rowsLinea.filter(function(r){
                return $(r).data('id-linea') === idLinea;
            });
            var $selectedRow = $(sameLineaRows.find(function(r){
                return $(r).is(':visible');
            }) || sameLineaRows[0]);

            var solicitada = parseInt($selectedRow.data('cantidad-solicitada'), 10);
            var aceptada = rechazarTodo ? 0 : parseInt($selectedRow.find('.input-cantidad-aceptada').val(), 10) || 0;
            ajustesCantidad.push({
                id: idLinea,
                cantidad_solicitada: solicitada,
                cantidad_aceptada: aceptada,
                motivo: rechazarTodo ? motivoGeneral : (aceptada < solicitada ? 'cantidad_incorrecta' : null)
            });

            seenLineas[idLinea] = true;
        });

        var hayDiferencias = rechazarTodo || seriesRechazadas.length > 0 || ajustesCantidad.some(a => a.cantidad_aceptada < a.cantidad_solicitada);
        var descripcion = rechazarTodo
            ? 'Vas a rechazar toda la salida como no solicitada. El almacén será notificado del rechazo.'
            : (hayDiferencias
                ? 'Vas a confirmar lo correcto y rechazar/ajustar el resto. El almacén será notificado de las diferencias.'
                : 'Vas a confirmar todo el material como correcto.');

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
            if (!result.isConfirmed) {
                rechazarTodo = false;
                return;
            }

            $.ajax({
                url: 'modal/ProcesarRespuestaParcial.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    folio: folioActivoPendiente,
                    seriesAceptadas: JSON.stringify(seriesAceptadas),
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