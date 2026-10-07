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
$foliosIds = array_column($folios, 'Folio_Salida_ST');
$jsonDatosTablaMasiva = fopen('php://temp/maxmemory:2097152', 'w+b');
$jsonDetallesSeriesMasivas = fopen('php://temp/maxmemory:2097152', 'w+b');
if ($jsonDatosTablaMasiva === false || $jsonDetallesSeriesMasivas === false) {
    throw new RuntimeException('No fue posible preparar la salida de Navegacion masiva.');
}

$escribirTextoJson = static function ($stream, $texto) {
    $longitud = strlen($texto);
    $escritos = 0;
    while ($escritos < $longitud) {
        $resultado = fwrite($stream, substr($texto, $escritos));
        if ($resultado === false || $resultado === 0) {
            throw new RuntimeException('No fue posible escribir la salida de Navegacion masiva.');
        }
        $escritos += $resultado;
    }
};
$escribirValorJson = static function ($stream, $valor) use ($escribirTextoJson) {
    $json = json_encode($valor, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    if ($json === false) {
        throw new RuntimeException('No fue posible codificar la salida de Navegacion masiva: ' . json_last_error_msg());
    }
    $escribirTextoJson($stream, $json);
};

$escribirTextoJson($jsonDatosTablaMasiva, '[');
$escribirTextoJson($jsonDetallesSeriesMasivas, '{');
$primeraFila = true;
$primerDetalle = true;
$totalSeries = 0;
$totalNavegando = 0;
$conteoEstados = array(
    'navegando-observacion' => 0,
    'negro' => 0,
    'rojo' => 0,
    'gris' => 0,
    'naranja' => 0,
    'amarillo' => 0,
    'verde' => 0,
    'blanco' => 0
);
$etiquetasEstado = array(
    'navegando' => 'Navegando correctamente',
    'navegando-observacion' => 'Navegando con observacion',
    'negro' => 'No navegando: perdida de navegacion, ECI se apago',
    'rojo' => 'No navegando: requiere seguimiento por mas de 14 dias',
    'gris' => 'No navegando: telefonos no coinciden',
    'naranja' => 'No navegando: pendiente por mas de 3 semanas desde la liquidacion',
    'amarillo' => 'No navegando: en espera de cumplir 3 dias desde la liquidacion',
    'verde' => 'No navegando: liquidacion reciente',
    'blanco' => 'Sin liquidacion para clasificar'
);

foreach (array_chunk($foliosIds, 25) as $foliosLote) {
    $detallesLote = $navegacion->ObtenerDetalleFolios($foliosLote, $contratistaId);

    foreach ($detallesLote as $serie) {
        $indice = $totalSeries++;
        $estado = $serie['Estado_Navegacion'] ?? 'blanco';
        $navegando = (int)($serie['Navegando'] ?? 0) === 1;
        if ($navegando) {
            $totalNavegando++;
        }
        if (isset($conteoEstados[$estado])) {
            $conteoEstados[$estado]++;
        }

        $duplicadosHtml = '';
        if (!empty($serie['Duplicado_Tac'])) {
            $duplicadosHtml .= '<button type="button" class="btn btn-warning btn-sm ver-duplicado-masivo mr-1"'
                . ' data-tipo-duplicado="tac" data-indice-serie="' . $indice . '">'
                . '<i class="fas fa-list mr-1" aria-hidden="true"></i>Duplicado TAC</button>';
        }
        if (!empty($serie['Duplicado_Coordiapp'])) {
            $duplicadosHtml .= '<button type="button" class="btn btn-info btn-sm ver-duplicado-masivo"'
                . ' data-tipo-duplicado="coordiapp" data-indice-serie="' . $indice . '">'
                . '<i class="fas fa-list mr-1" aria-hidden="true"></i>Duplicado Coordiapp</button>';
        }
        if ($duplicadosHtml === '') {
            $duplicadosHtml = '<span class="text-muted">Sin duplicados</span>';
        }

        if (!$primeraFila) {
            $escribirTextoJson($jsonDatosTablaMasiva, ',');
        }
        $primeraFila = false;
        $escribirValorJson($jsonDatosTablaMasiva, array(
            'folio' => $serie['Folio_Salida_Material_Det_S'] ?? '',
            'modelo' => $serie['Producto'] ?? '',
            'numero_serie' => $serie['Num_Serie_S'] ?? '',
            'folio_asignacion' => $serie['Folio_Salida_Det'] ?? 'Sin asignar',
            'fecha_asignacion' => $serie['Fecha_Salida_Det'] ?? 'Sin asignar',
            'tecnico' => trim($serie['Tecnico'] ?? '') ?: 'Sin asignar',
            'telefono_coordiapp' => $serie['Telefono_Coordiapp'] ?? 'Sin dato',
            'fecha_coordiapp' => $serie['Fecha_Coordiapp'] ?? 'Sin dato',
            'telefono_tac' => $serie['Telefono_Tac'] ?? 'Sin dato',
            'fecha_tac' => $serie['Fecha_Liquidacion_Tac'] ?? 'Sin dato',
            'telefono_eci' => $serie['Telefono_Eci'] ?? 'Sin dato',
            'fecha_eci' => $serie['Fecha_Ultimo_Evento_Eci'] ?? 'Sin dato',
            'navegacion_html' => $navegando
                ? ($estado === 'navegando-observacion'
                    ? '<i class="fa-brands fa-tailwind-css fa-float icon-navegacion-observacion"'
                        . ' style="--fa-animation-duration: 2s;"'
                        . ' aria-label="Navegando con observacion"></i>'
                    : '<span class="badge estado-navegacion custom-badge">'
                        . '<i class="fa-brands fa-tailwind-css fa-float mr-1"'
                        . ' style="--fa-animation-duration: 2s;" aria-hidden="true"></i>'
                        . '<span class="sr-only">Si</span></span>')
                : '<span class="badge estado-navegacion badge-secondary">No</span>',
            'motivo' => $serie['Observacion_Navegacion'] ?? (
                $navegando
                    ? 'Navegando correctamente'
                    : implode('; ', $serie['Motivos_No_Navegacion'] ?? array())
            ),
            'duplicados_html' => $duplicadosHtml,
            'estado_texto' => $etiquetasEstado[$estado] ?? 'No navegando: estado sin clasificar',
            'estado' => $estado
        ));

        if (!empty($serie['Duplicado_Tac']) || !empty($serie['Duplicado_Coordiapp'])) {
            if (!$primerDetalle) {
                $escribirTextoJson($jsonDetallesSeriesMasivas, ',');
            }
            $primerDetalle = false;
            $escribirValorJson($jsonDetallesSeriesMasivas, (string)$indice);
            $escribirTextoJson($jsonDetallesSeriesMasivas, ':');
            $escribirValorJson($jsonDetallesSeriesMasivas, array(
                'Num_Serie_S' => $serie['Num_Serie_S'] ?? '',
                'Telefono_Eci' => $serie['Telefono_Eci'] ?? '',
                'Detalles_Duplicado_Tac' => $serie['Detalles_Duplicado_Tac'] ?? array(),
                'Detalles_Duplicado_Coordiapp' => $serie['Detalles_Duplicado_Coordiapp'] ?? array()
            ));
        }
    }

    unset($serie, $detallesLote);
}
unset($folios, $foliosIds, $navegacion);
$totalPendientes = $totalSeries - $totalNavegando;
$escribirTextoJson($jsonDatosTablaMasiva, ']');
$escribirTextoJson($jsonDetallesSeriesMasivas, '}');
rewind($jsonDatosTablaMasiva);
rewind($jsonDetallesSeriesMasivas);

$haySeriesMasivas = $totalSeries > 0;
?>

<div class="navegacion-masiva-contenedor">
    <style>
        #tabla-navegacion-masiva tbody tr.estado-navegando {
            background: #dff6ff !important;
            animation: pulso-navegacion 2s ease-in-out infinite;
        }
        #tabla-navegacion-masiva tbody tr.estado-navegando-observacion {
            background: #dce5f1 !important;
            box-shadow: inset 5px 0 0 #17365d;
            color: #17365d;
        }

        #tabla-navegacion-masiva tbody tr.estado-negro {
            background: #343a40 !important;
            box-shadow: inset 5px 0 0 #000;
            color: #fff;
        }

        #tabla-navegacion-masiva tbody tr.estado-rojo {
            background: #ffe1e1 !important;
        }

        #tabla-navegacion-masiva tbody tr.estado-gris {
            background: #e9ecef !important;
        }

        #tabla-navegacion-masiva tbody tr.estado-naranja {
            background: #ffe2cc !important;
            box-shadow: inset 5px 0 0 #fd7e14;
        }

        #tabla-navegacion-masiva tbody tr.estado-amarillo {
            background: #fff3cd !important;
        }

        #tabla-navegacion-masiva tbody tr.estado-verde {
            background: #d4edda !important;
        }

        #tabla-navegacion-masiva th,
        #tabla-navegacion-masiva td {
            white-space: nowrap;
            vertical-align: middle;
            text-align: center;
        }

        #tabla-navegacion-masiva_wrapper .dt-scroll-head th,
        #tabla-navegacion-masiva_wrapper .dt-scroll-foot th,
        #tabla-navegacion-masiva thead th,
        #tabla-navegacion-masiva tfoot th {
            text-align: center !important;
            vertical-align: middle !important;
        }

        #filtro-estado-masivo {
            max-width: 280px;
        }

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

        .icon-navegacion-observacion {
            color: #17365d;
            font-size: 1.15rem;
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

    <?php if ($haySeriesMasivas): ?>
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
                        <option value="navegando-observacion">Azul oscuro: navegando con observacion (<?= $conteoEstados['navegando-observacion'] ?>)</option>
                        <option value="no-navegando">No navegando (<?= $totalPendientes ?>)</option>
                        <option value="negro">Negro: perdida de navegacion (<?= $conteoEstados['negro'] ?>)</option>
                        <option value="rojo">Rojo: seguimiento (<?= $conteoEstados['rojo'] ?>)</option>
                        <option value="gris">Gris: telefonos diferentes (<?= $conteoEstados['gris'] ?>)</option>
                        <option value="naranja">Naranja: mas de 3 semanas (<?= $conteoEstados['naranja'] ?>)</option>
                        <option value="amarillo">Amarillo: Navegacion reciente (<?= $conteoEstados['amarillo'] ?>)</option>
                        <option value="verde">Verde: pendiente reciente (<?= $conteoEstados['verde'] ?>)</option>
                        <option value="blanco">Blanco: sin liquidacion (<?= $conteoEstados['blanco'] ?>)</option>
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
                            <th>Fecha Ultima Navegacion</th>
                            <th>Navegando</th>
                            <th>Motivo</th>
                            <th>Duplicados</th>
                            <th class="columna-estado-exportacion">Estado de la serie</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
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

        const seriesMasivas = <?php fpassthru($jsonDetallesSeriesMasivas); ?>;
        const datosTablaMasiva = <?php fpassthru($jsonDatosTablaMasiva); ?>;

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

            let contenido = '<p><strong>Numero de serie:</strong> '
                + escaparHtml(serie.Num_Serie_S)
                + '</p>';
            contenido += '<p><strong>Fuente:</strong> ' + nombreFuente + '</p>';
            contenido += '<div class="table-responsive">'
                + '<table class="table table-sm table-bordered mb-0">'
                + '<thead><tr>'
                + '<th>Numero de serie</th>'
                + '<th>Telefono</th>'
                + '<th>Clasificacion</th>';
            contenido += esTac
                ? '<th>Fecha liquidacion</th><th>Estado</th>'
                : '<th>Fecha Coordiapp</th>';
            contenido += '</tr></thead><tbody>';

            registros.forEach(function (registro) {
                const telefonoRegistro = String(registro.TELEFONO || registro.Telefono || '').replace(/\D/g, '');
                const elegido = telefonoNavegacion !== '' && telefonoRegistro === telefonoNavegacion;
                contenido += '<tr class="' + (elegido ? 'duplicado-registro-elegido' : '') + '">';
                contenido += '<td>' + escaparHtml(esTac ? registro.Num__Serie : registro.Ont) + '</td>';
                contenido += '<td>'
                    + escaparHtml(registro.TELEFONO || registro.Telefono || 'Sin dato')
                    + '</td>';
                const clasificacion = esTac
                    ? registro.Tipo_de_Tarea_Clasificado
                    : (registro.Tipo_Tarea_Clasificado || 'Otros');
                contenido += '<td>' + escaparHtml(clasificacion) + '</td>';
                contenido += esTac
                    ? '<td>' + escaparHtml(registro.FECHA_LIQ || 'Sin dato')
                        + '</td><td>' + escaparHtml(registro.Calificador_Edo || 'Sin dato') + '</td>'
                    : '<td>' + escaparHtml(registro.Fecha_Coordiapp || 'Sin dato') + '</td>';
                contenido += '</tr>';
            });

            contenido += '</tbody></table></div>';
            $('#modalDuplicadosNavegacionMasivaTitulo').text('Detalle duplicado ' + nombreFuente);
            $('#modalDuplicadosNavegacionMasivaContenido').html(contenido);
            $('#modalDuplicadosNavegacionMasiva').modal('show');
        }

        $('#tabla-navegacion-masiva').on('click', '.ver-duplicado-masivo', function () {
            const tipo = $(this).data('tipo-duplicado');
            const indice = Number($(this).data('indice-serie'));
            mostrarDetallesDuplicadoMasivo(tipo, indice);
        });

        const renderTexto = DataTable.render.text();
        let exportacionExcelEnCurso = false;
        const accionExcelHtml5 = DataTable.ext.buttons.excelHtml5.action;
        const tablaMasiva = new DataTable('#tabla-navegacion-masiva', {
            data: datosTablaMasiva,
            deferRender: true,
            columns: [
                { data: 'folio', render: renderTexto },
                { data: 'modelo', render: renderTexto },
                { data: 'numero_serie', render: renderTexto },
                { data: 'folio_asignacion', render: renderTexto },
                { data: 'fecha_asignacion', render: renderTexto },
                { data: 'tecnico', render: renderTexto },
                { data: 'telefono_coordiapp', render: renderTexto },
                { data: 'fecha_coordiapp', render: renderTexto },
                { data: 'telefono_tac', render: renderTexto },
                { data: 'fecha_tac', render: renderTexto },
                { data: 'telefono_eci', render: renderTexto },
                { data: 'fecha_eci', render: renderTexto },
                { data: 'navegacion_html', orderable: false, searchable: false },
                { data: 'motivo', render: renderTexto },
                { data: 'duplicados_html', orderable: false, searchable: false },
                { data: 'estado_texto', searchable: false }
            ],
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
            },
            ordering: true,
            order: [[0, 'desc']],
            searching: true,
            paging: true,
            lengthChange: true,
            scrollX: true,
            columnDefs: [
                { targets: 15, visible: false, searchable: false }
            ],
            pageLength: 10,
            info: true,
            dom: '<"d-flex justify-content-between align-items-center mb-2" lf>'
                + 'rt'
                + '<"row"<"col-md-6 text-left mt-2"i>'
                + '<"col-md-6 text-right mt-2"p>>',
            createdRow: function (row, data) {
                row.classList.add('estado-' + data.estado);
                row.dataset.estado = data.estado;
            },
            initComplete: function () {
                const tablaLista = this.api();
                $('#cargando-masiva').fadeOut(150, function () {
                    $('#contenido-masivo-listo').css('visibility', 'visible');
                    tablaLista.columns.adjust();
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
                    exportOptions: {
                        columns: ':visible, .columna-estado-exportacion'
                    },
                    action: function (evento, tabla, nodo, configuracion, finalizarAccion) {
                        if (exportacionExcelEnCurso) {
                            return;
                        }

                        exportacionExcelEnCurso = true;
                        const boton = $(nodo);
                        const contenidoOriginal = boton.html();
                        boton
                            .prop('disabled', true)
                            .attr('aria-busy', 'true')
                            .html('<i class="fas fa-spinner fa-spin mr-1" aria-hidden="true"></i>Generando...');

                        const restaurarBoton = function () {
                            exportacionExcelEnCurso = false;
                            boton
                                .prop('disabled', false)
                                .removeAttr('aria-busy')
                                .html(contenidoOriginal);
                        };

                        window.setTimeout(function () {
                            try {
                                accionExcelHtml5.call(
                                    this,
                                    evento,
                                    tabla,
                                    nodo,
                                    configuracion,
                                    function () {
                                        restaurarBoton();
                                        if (typeof finalizarAccion === 'function') {
                                            finalizarAccion();
                                        }
                                    }
                                );
                            } catch (error) {
                                restaurarBoton();
                                if (typeof finalizarAccion === 'function') {
                                    finalizarAccion();
                                }
                                console.error('No se pudo generar el Excel masivo.', error);
                            }
                        }.bind(this), 0);
                    },
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

            const datosFila = tablaMasiva.row(indiceFila).data();
            const estado = datosFila ? datosFila.estado : '';

            if (filtroMasivo === 'navegando') {
                return estado === 'navegando' || estado === 'navegando-observacion';
            }

            if (filtroMasivo === 'no-navegando') {
                return estado !== 'navegando' && estado !== 'navegando-observacion';
            }

            return estado === filtroMasivo;
        });

        $('#filtro-estado-masivo').on('change', function () {
            filtroMasivo = $(this).val();
            tablaMasiva.draw();
        });
    });
</script>
<?php
fclose($jsonDetallesSeriesMasivas);
fclose($jsonDatosTablaMasiva);
?>
