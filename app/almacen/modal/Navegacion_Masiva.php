<?php
require_once(__DIR__ . '/../controller/ClassNavegacion.php');
require_once(__DIR__ . '/../../config/ContratistaContexto.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['idContratistas']) || !esAdministradorContratista()) {
    http_response_code(403);
    echo '<div class="alert alert-warning m-3">No tienes permisos para consultar esta vista.</div>';
    exit;
}

$fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-d', strtotime('-3 month'));
$fechaFin = $_POST['fecha_fin'] ?? date('Y-m-d');
$contratistaId = obtenerIdContratistaContexto();
$navegacion = new Navegacion();
$folios = $navegacion->FoliosContratista($contratistaId, $fechaInicio, $fechaFin);
$seriesMasivas = array();
$foliosIds = array_column($folios, 'Folio_Salida_ST');
$detallesMasivos = $navegacion->ObtenerDetalleFolios($foliosIds, $contratistaId);
$fechasPorFolio = array();

foreach ($folios as $folio) {
    $fechasPorFolio[(string)$folio['Folio_Salida_ST']] = $folio['Fecha_Salida_ST'] ?? '';
}

foreach ($detallesMasivos as $serie) {
    $folioSerie = (string)($serie['Folio_Salida_Material_Det_S'] ?? '');
    $serie['Folio_Masivo'] = $folioSerie;
    $serie['Fecha_Folio_Masivo'] = $fechasPorFolio[$folioSerie] ?? '';
    $seriesMasivas[] = $serie;
}

$totalSeries = count($seriesMasivas);
$totalNavegando = count(array_filter($seriesMasivas, function ($serie) {
    return (int)($serie['Navegando'] ?? 0) === 1;
}));
$totalPendientes = $totalSeries - $totalNavegando;
?>

<div class="navegacion-masiva-contenedor">
    <style>
        #tabla-navegacion-masiva tbody tr.estado-navegando {
            background: #dff6ff !important;
            animation: pulso-navegacion 2s ease-in-out infinite;
        }
        #tabla-navegacion-masiva tbody tr.estado-rojo { background: #ffe1e1 !important; }
        #tabla-navegacion-masiva tbody tr.estado-gris { background: #e9ecef !important; }
        #tabla-navegacion-masiva tbody tr.estado-amarillo { background: #fff3cd !important; }
        #tabla-navegacion-masiva tbody tr.estado-verde { background: #d4edda !important; }
        #tabla-navegacion-masiva th,
        #tabla-navegacion-masiva td { white-space: nowrap; vertical-align: middle; text-align: center; }
        #filtro-estado-masivo { max-width: 280px; }

        .custom-badge {
            background-color: transparent !important;
            color: #007BFF !important;
            font-size: 130% !important;
        }

        .estado-navegacion {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            line-height: 1;
        }

        .duplicado-registro-elegido,
        .duplicado-registro-elegido td {
            background-color: #dff6ff !important;
            color: #0056b3 !important;
            font-weight: 700 !important;
        }

        .modal-header-gradient {
            background: linear-gradient(90deg, #0f172a 0%, #003d99 100%) !important;
        }

        @keyframes pulso-navegacion {
            0%, 100% { box-shadow: inset 3px 0 0 #0aa2c0; }
            50% { box-shadow: inset 7px 0 0 #0aa2c0; }
        }

        #contenedor-carga-masiva {
            position: relative;
            min-height: 120px;
        }

        #contenido-masivo-listo {
            visibility: hidden;
        }

        #cargando-masiva {
            position: absolute;
            inset: 0;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .8);
            z-index: 2;
        }

        .tac-resumen-series {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 20px;
        }

        .tac-resumen-tarjeta {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-left: 4px solid #4e73df;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
            padding: 14px 16px;
        }

        .tac-resumen-tarjeta.navegando {
            border-left-color: #0aa2c0;
        }

        .tac-resumen-tarjeta.pendientes {
            border-left-color: #f6c23e;
        }

        .tac-resumen-etiqueta {
            color: #4e73df;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .tac-resumen-tarjeta.navegando .tac-resumen-etiqueta {
            color: #0aa2c0;
        }

        .tac-resumen-tarjeta.pendientes .tac-resumen-etiqueta {
            color: #d39e00;
        }

        .tac-resumen-valor {
            color: #6c757d;
            font-size: 1.75rem;
            font-weight: 600;
            line-height: 1.2;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .tac-resumen-series {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <?php if (!empty($seriesMasivas)): ?>
    <div id="contenedor-carga-masiva">
        <div id="cargando-masiva">
            <div class="text-center text-muted">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p class="mt-2 mb-0">Preparando tabla masiva...</p>
            </div>
        </div>
        <div id="contenido-masivo-listo">
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:12px;">
            <div id="botones-navegacion-masiva"></div>
            <select id="filtro-estado-masivo" class="form-control">
            <option value="">Todos (<?= $totalSeries ?>)</option>
            <option value="navegando">Navegando (<?= $totalNavegando ?>)</option>
            <option value="no-navegando">No navegando (<?= $totalPendientes ?>)</option>
            <option value="rojo">Rojo: seguimiento</option>
            <option value="gris">Gris: telefonos diferentes</option>
            <option value="amarillo">Amarillo: Navegacion reciente</option>
            <option value="verde">Verde: pendiente reciente</option>
            <option value="blanco">Blanco: sin liquidacion</option>
            </select>
        </div>

        <table id="tabla-navegacion-masiva" class="table table-bordered table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>Folio Salida</th>
                    <th>Modelo</th>
                    <th>Numero de serie</th>
                    <th>Folio asignacion</th>
                    <th>Fecha asignacion</th>
                    <th>Tecnico</th>
                    <th>Telefono Coordiapp</th>
                    <th>Fecha Coordiapp</th>
                    <th>Telefono TAC</th>
                    <th>Fecha liquidacion TAC</th>
                    <th>Telefono Navegacion</th>
                    <th>Fecha consulta Navegacion</th>
                    <th>Navegando</th>
                    <th>Motivo</th>
                    <th>Duplicados</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($seriesMasivas as $indice => $serie): ?>
                    <?php
                        $estado = $serie['Estado_Navegacion'] ?? 'blanco';
                        $navegando = (int)($serie['Navegando'] ?? 0) === 1;
                        $motivos = $serie['Motivos_No_Navegacion'] ?? array();
                    ?>
                    <tr class="estado-<?= htmlspecialchars($estado) ?>" data-estado="<?= htmlspecialchars($estado) ?>">
                        <td><?= htmlspecialchars($serie['Folio_Masivo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($serie['Producto'] ?? '') ?></td>
                        <td><?= htmlspecialchars($serie['Num_Serie_S'] ?? '') ?></td>
                        <td><?= htmlspecialchars($serie['Folio_Salida_Det'] ?? 'Sin asignar') ?></td>
                        <td><?= htmlspecialchars($serie['Fecha_Salida_Det'] ?? 'Sin asignar') ?></td>
                        <td><?= htmlspecialchars(trim($serie['Tecnico'] ?? '') ?: 'Sin asignar') ?></td>
                        <td><?= htmlspecialchars($serie['Telefono_Coordiapp'] ?? 'Sin dato') ?></td>
                        <td><?= htmlspecialchars($serie['Fecha_Coordiapp'] ?? 'Sin dato') ?></td>
                        <td><?= htmlspecialchars($serie['Telefono_Tac'] ?? 'Sin dato') ?></td>
                        <td><?= htmlspecialchars($serie['Fecha_Liquidacion_Tac'] ?? 'Sin dato') ?></td>
                        <td><?= htmlspecialchars($serie['Telefono_Eci'] ?? 'Sin dato') ?></td>
                        <td><?= htmlspecialchars($serie['Fecha_Consulta_Eci'] ?? 'Sin dato') ?></td>
                        <td>
                            <span class="badge estado-navegacion <?= $navegando ? 'custom-badge' : 'badge-secondary' ?>">
                                <?php if ($navegando): ?>
                                    <i class="fa-brands fa-tailwind-css fa-float mr-1 " style="--fa-animation-duration: 2s;" aria-hidden="true"></i>
                                    <span class="sr-only">Si</span>
                                <?php else: ?>
                                    <span class="sr-only">No</span>
                                    No
                                <?php endif; ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($navegando ? 'Navegando correctamente' : implode('; ', $motivos)) ?></td>
                        <td>
                            <?php if (!empty($serie['Duplicado_Tac'])): ?>
                                <button type="button" class="btn btn-warning btn-sm ver-duplicado-masivo mr-1" data-tipo-duplicado="tac" data-indice-serie="<?= $indice ?>">
                                    <i class="fas fa-list mr-1" aria-hidden="true"></i>Duplicado TAC
                                </button>
                            <?php endif; ?>
                            <?php if (!empty($serie['Duplicado_Coordiapp'])): ?>
                                <button type="button" class="btn btn-info btn-sm ver-duplicado-masivo" data-tipo-duplicado="coordiapp" data-indice-serie="<?= $indice ?>">
                                    <i class="fas fa-list mr-1" aria-hidden="true"></i>Duplicado Coordiapp
                                </button>
                            <?php endif; ?>
                            <?php if (empty($serie['Duplicado_Tac']) && empty($serie['Duplicado_Coordiapp'])): ?>
                                <span class="text-muted">Sin duplicados</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="tac-resumen-series">
            <div class="tac-resumen-tarjeta">
                <div class="tac-resumen-etiqueta">
                    <i class="fas fa-barcode mr-1" aria-hidden="true"></i>
                    Total de series
                </div>
                <div class="tac-resumen-valor">
                    <span id="total-series-valor"><?= $totalSeries ?></span>
                </div>
            </div>

            <div class="tac-resumen-tarjeta navegando">
                <div class="tac-resumen-etiqueta">
                    <i class="fas fa-wifi mr-1" aria-hidden="true"></i>
                    Series navegando
                </div>
                <div class="tac-resumen-valor">
                    <span id="total-navegando-valor"><?= $totalNavegando ?></span>
                </div>
            </div>

            <div class="tac-resumen-tarjeta pendientes">
                <div class="tac-resumen-etiqueta">
                    <i class="fas fa-hourglass-half mr-1" aria-hidden="true"></i>
                    Series pendientes
                </div>
                <div class="tac-resumen-valor">
                    <span id="total-pendientes-valor"><?= $totalPendientes ?></span>
                </div>
            </div>
        </div>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-info">No hay series en el periodo seleccionado.</div>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalDuplicadosNavegacionMasiva" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient text-white border-0">
                <h5 class="modal-title" id="modalDuplicadosNavegacionMasivaTitulo">Detalle de duplicados</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="modalDuplicadosNavegacionMasivaContenido"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        
        if (typeof DataTable === 'undefined' || !$('#tabla-navegacion-masiva').length) {
            return;
        }

        const seriesMasivas = <?= json_encode($seriesMasivas) ?>;

        function escaparHtml(valor) {
            return $('<div>').text(valor ?? '').html();
        }

        function mostrarDetallesDuplicadoMasivo(tipo, indice) {
            const serie = seriesMasivas[indice] || {};
            const esTac = tipo === 'tac';
            const registros = esTac
                ? (serie.Detalles_Duplicado_Tac || [])
                : (serie.Detalles_Duplicado_Coordiapp || []);
            const telefonoNavegacion = String(serie.Telefono_Eci || '').replace(/\D/g, '');
            const nombreFuente = esTac ? 'TAC' : 'Coordiapp';
            let contenido = '<p><strong>Numero de serie:</strong> ' + escaparHtml(serie.Num_Serie_S) + '</p>';
            contenido += '<p><strong>Fuente:</strong> ' + nombreFuente + '</p>';
            contenido += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead><tr>';
            contenido += '<th>Numero de serie</th><th>Telefono</th><th>Clasificacion</th>';
            contenido += esTac ? '<th>Fecha liquidacion</th><th>Estado</th>' : '<th>Fecha Coordiapp</th>';
            contenido += '</tr></thead><tbody>';

            registros.forEach(function (registro) {
                const telefonoRegistro = String(registro.TELEFONO || registro.Telefono || '').replace(/\D/g, '');
                const elegido = telefonoNavegacion !== '' && telefonoRegistro === telefonoNavegacion;
                contenido += '<tr class="' + (elegido ? 'duplicado-registro-elegido' : '') + '">';
                contenido += '<td>' + escaparHtml(esTac ? registro.Num__Serie : registro.Ont) + '</td>';
                contenido += '<td>' + escaparHtml(registro.TELEFONO || registro.Telefono || 'Sin dato') + '</td>';
                contenido += '<td>' + escaparHtml(esTac ? registro.Tipo_de_Tarea_Clasificado : registro.Tipo_Tarea_Clasificado || 'Otros') + '</td>';
                contenido += esTac
                    ? '<td>' + escaparHtml(registro.FECHA_LIQ || 'Sin dato') + '</td><td>' + escaparHtml(registro.Calificador_Edo || 'Sin dato') + '</td>'
                    : '<td>' + escaparHtml(registro.Fecha_Coordiapp || 'Sin dato') + '</td>';
                contenido += '</tr>';
            });

            contenido += '</tbody></table></div>';
            $('#modalDuplicadosNavegacionMasivaTitulo').text('Detalle duplicado ' + nombreFuente);
            $('#modalDuplicadosNavegacionMasivaContenido').html(contenido);
            $('#modalDuplicadosNavegacionMasiva').modal('show');
        }

        $('#tabla-navegacion-masiva').on('click', '.ver-duplicado-masivo', function () {
            mostrarDetallesDuplicadoMasivo($(this).data('tipo-duplicado'), Number($(this).data('indice-serie')));
        });

        const tablaMasiva = new DataTable('#tabla-navegacion-masiva', {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: true,
            order: [[0, 'desc']],
            searching: true,
            paging: true,
            lengthChange: true,
            scrollX: true,
            pageLength: 10,
            info: true,
            dom: '<"d-flex justify-content-between align-items-center mb-2" lf>rt<"row"<"col-md-6 text-left mt-2"i><"col-md-6 text-right mt-2"p>>',
            initComplete: function () {
                $('#cargando-masiva').fadeOut(150, function () {
                    $('#contenido-masivo-listo').css('visibility', 'visible');
                    tablaMasiva.columns.adjust();
                });
            }
        });

        const botones = new DataTable.Buttons(tablaMasiva, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Navegacion masiva de Numeros de Serie',
                    customize: function (xlsx) {

                        const sheet = xlsx.xl.worksheets['sheet1.xml'];

                        // CENTRAR HORIZONTALMENTE
                        $('row c', sheet).attr('s', '51');
                    }
                }
            ]
        });

        botones.container().appendTo('#botones-navegacion-masiva');

        let filtroMasivo = '';
        tablaMasiva.search.fixed('estadoNavegacionMasiva', function (textoBusqueda, datos, indiceFila) {
            if (filtroMasivo === '') {
                return true;
            }

            const fila = tablaMasiva.row(indiceFila).node();
            const estado = fila ? fila.getAttribute('data-estado') : '';
            return filtroMasivo === 'navegando'
                ? estado === 'navegando'
                : filtroMasivo === 'no-navegando'
                    ? estado !== 'navegando'
                    : estado === filtroMasivo;
        });

        $('#filtro-estado-masivo').on('change', function () {
            filtroMasivo = $(this).val();
            tablaMasiva.draw();
        });
    });
</script>
