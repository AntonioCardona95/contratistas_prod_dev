<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Monterrey');

require_once __DIR__ . '/controller/ClassTraspasos.php';
require_once __DIR__ . '/../config/ContratistaContexto.php';

session_start();
$Contratista = $_SESSION['Contratista'] ?? '';
requerirSesionAlmacen(true);
$idContratistaTraspaso = obtenerIdContratistaContexto();
$traspasos = new Traspasos();
$ultimoTraspaso = $traspasos->UltimaTraspaso();
$folioTraspaso = !empty($ultimoTraspaso) ? ((int)$ultimoTraspaso[0]['Folio_Traspaso_T'] + 1) : 1;
$tecnicosTraspaso = $idContratistaTraspaso > 0
    ? $traspasos->Tecnicos($idContratistaTraspaso)
    : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CONTRATISTAS - TRASPASOS</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="../css/responsive_custom.css" rel="stylesheet">
    <link rel="stylesheet" href="css/sidebar-active.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <style>
        .modal-header-gradient {
            background: linear-gradient(90deg, #0f172a 0%, #003d99 100%) !important;
        }

        @media (max-width: 768px) {
            .module-heading {
                font-size: 1.1rem;
                text-align: center;
            }
        }

        #traspasoSeries {
            min-height: 120px;
        }

        .card-form-shell {
            background: #f3f5f8;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 18px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
            padding: 1.25rem 1.25rem 1rem;
        }

        #formGuardarTraspaso label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: .45rem;
        }

        #formGuardarTraspaso .form-control,
        #formGuardarTraspaso .select2-selection {
            border: 1px solid #dfe3ea;
            border-radius: 10px;
            min-height: 38px;
        }

        #formGuardarTraspaso textarea.form-control {
            min-height: 75px;
            resize: vertical;
        }

        #formGuardarTraspaso .select2-container {
            width: 100% !important;
        }

        #formGuardarTraspaso .select2-selection--single {
            height: 38px;
            padding: 4px 8px;
        }

        #formGuardarTraspaso .select2-selection__arrow {
            height: 36px;
        }

        #formGuardarTraspaso #traspasoSeriesSeleccionadas {
            background: rgba(255, 255, 255, .72);
        }

        @media (max-width: 768px) {
            .card-form-shell {
                padding: .85rem .75rem;
            }
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php include_once __DIR__ . '/../components/SidebarAlmacen.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php
                    $topbar_defaults = [
                        'Fecha1' => date('Y-m-d', strtotime('-3 months')),
                        'Fecha2' => date('Y-m-d')
                    ];
                    $topbar_onchange = 'LoadTraspasos';
                    $topbar_actions = '<button type="button" class="btn btn-success" id="btnNuevoTraspaso"><i class="fas fa-plus"></i> NUEVO TRASPASO</button>';
                    include_once __DIR__ . '/../components/TopbarContratistas.php';
                ?>

                <div class="container-fluid">
                    <div class="card shadow mb-4 table-container">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h2 class="module-heading m-0 font-weight-bold text-gray-800">TRASPASOS DE MATERIAL ENTRE TECNICOS <i class="fa-solid fa-right-left"></i></h2> 
                            <?php $selectorCallback = 'RecargarTraspasos'; include_once __DIR__ . '/../components/SelectorContratista.php'; ?>
                        </div>
                        <div class="card-body p-0">
                            <div id="LoadTraspasos" class="p-3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include_once __DIR__ . '/../components/FooterContratistas.php'; ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include_once __DIR__ . '/../components/ModalCierreSesion.php'; ?>

    <div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient text-white">
                    <h5 class="modal-title">Detalle de traspaso</h5>
                    <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalTraspasoContenido"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNuevoTraspaso" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient text-white">
                    <h5 class="modal-title">Nuevo traspaso entre técnicos</h5>
                    <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalNuevoTraspasoContenido">
                    <?php if ($idContratistaTraspaso <= 0): ?>
                        <div class="alert alert-warning mb-0">
                            Selecciona un contratista específico antes de registrar un traspaso.
                        </div>
                    <?php else: ?>
                        <form id="formGuardarTraspaso">
                            <input type="hidden" name="folio" value="<?= $folioTraspaso ?>">
                            <input type="hidden" name="FK_Contratista_Salida" value="<?= $idContratistaTraspaso ?>">

                            <div class="card-form-shell">
                                <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="traspasoTecnicoOrigen">Técnico origen</label>
                                    <select name="FK_Tecnico_Salida_Det" id="traspasoTecnicoOrigen" class="form-control" required>
                                        <option value="">Selecciona técnico</option>
                                        <?php foreach ($tecnicosTraspaso as $tecnico): ?>
                                            <option value="<?= (int)$tecnico['idTecnico'] ?>">
                                                <?= htmlspecialchars($tecnico['Nombre_T'] . ' ' . $tecnico['Apellidos_T']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="traspasoTecnicoDestino">Técnico destino</label>
                                    <select name="FK_Tecnico_Salida_Destino" id="traspasoTecnicoDestino" class="form-control" required>
                                        <option value="">Selecciona técnico</option>
                                        <?php foreach ($tecnicosTraspaso as $tecnico): ?>
                                            <option value="<?= (int)$tecnico['idTecnico'] ?>">
                                                <?= htmlspecialchars($tecnico['Nombre_T'] . ' ' . $tecnico['Apellidos_T']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="traspasoSeries">Series disponibles</label>
                                    <select id="traspasoSeries" class="form-control" multiple></select>
                                </div>
                                </div>

                                <div class="form-group">
                                <label>Series seleccionadas</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Número de serie</th>
                                                <th class="text-center" style="width: 100px;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="traspasoSeriesSeleccionadas"></tbody>
                                    </table>
                                </div>
                                </div>

                                <div class="form-group mb-0">
                                <label for="traspasoComentarios">Descripción</label>
                                <textarea name="Descripcion_Salida_Det" id="traspasoComentarios" class="form-control" rows="3" placeholder="Descripción de la transferencia"></textarea>
                                </div>
                            </div>

                            <div id="traspasoMensaje" class="mb-3"></div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check mr-1"></i> Procesar traspaso
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../components/ScriptContratistas.php'; ?>
    <script src="js/traspasos.js"></script>
    <script>
        $(document).ready(function () {
            LoadTraspasos();

            window.RecargarTraspasos = function () {
                window.location.reload();
            };

            $('#btnNuevoTraspaso').on('click', function () {
                $('#modalNuevoTraspaso').modal('show');
            });

            function inicializarSelect2($select, placeholder) {
                if (!$select.length || $select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                $select.select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modalNuevoTraspaso')
                });
            }

            $('#modalNuevoTraspaso').on('shown.bs.modal', function () {
                inicializarSelect2($('#traspasoTecnicoOrigen'), 'Selecciona técnico origen');
                inicializarSelect2($('#traspasoTecnicoDestino'), 'Selecciona técnico destino');
                inicializarSelect2($('#traspasoSeries'), 'Selecciona series');
            });

            var seriesSeleccionadas = {};

            function renderizarSeries() {
                var $contenedor = $('#traspasoSeriesSeleccionadas');
                $contenedor.empty();
                Object.keys(seriesSeleccionadas).forEach(function (serie) {
                    $('<tr>').append(
                        $('<td>').text(serie),
                        $('<td class="text-center">').append(
                            $('<button type="button" class="btn btn-danger btn-sm">')
                                .attr('data-serie', serie)
                                .html('<i class="fas fa-trash"></i>')
                        )
                    ).appendTo($contenedor);
                });
            }

            $('#traspasoTecnicoOrigen').on('change', function () {
                var $series = $('#traspasoSeries');
                $series.empty().trigger('change');
                seriesSeleccionadas = {};
                renderizarSeries();

                if (!$(this).val()) {
                    return;
                }

                $.post('modal/LoadSeriesTraspaso.php', {
                    FK_Tecnico_Salida_Det: $(this).val(),
                    FK_Contratista_Salida: <?= (int)$idContratistaTraspaso ?>
                }).done(function (html) {
                    $series.html(html).trigger('change');
                });
            });

            $('#traspasoSeries').on('change', function () {
                $(this).find('option:selected').each(function () {
                    if ($(this).val()) {
                        seriesSeleccionadas[$(this).val()] = true;
                    }
                });
                renderizarSeries();
                $(this).val([]);
            });

            $('#traspasoSeriesSeleccionadas').on('click', 'button[data-serie]', function () {
                delete seriesSeleccionadas[$(this).data('serie')];
                renderizarSeries();
            });

            $('#formGuardarTraspaso').on('submit', function (event) {
                event.preventDefault();
                var datos = $(this).serializeArray();
                Object.keys(seriesSeleccionadas).forEach(function (serie) {
                    datos.push({name: 'numeros_series[]', value: serie});
                });

                $('#traspasoMensaje').html('<div class="alert alert-info">Procesando traspaso...</div>');
                $.post('modal/Traspasos_GuardarNuevoTraspaso.php', datos, null, 'json')
                    .done(function (respuesta) {
                        if (!respuesta.ok) {
                            $('#traspasoMensaje').html('<div class="alert alert-danger">' + respuesta.mensaje + '</div>');
                            return;
                        }
                        $('#modalNuevoTraspaso').modal('hide');
                        LoadTraspasos();
                    })
                    .fail(function () {
                        $('#traspasoMensaje').html('<div class="alert alert-danger">No fue posible registrar el traspaso.</div>');
                    });
            });
        });

        $(document).on('click', '#LoadTraspasos a[href*="Traspasos_Detalles.php"]', function (event) {
            event.preventDefault();
            const url = $(this).attr('href');
            $('#modalTraspasoContenido').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
            $('#modalForm').modal('show');
            $('#modalTraspasoContenido').load(url);
        });
    </script>
</body>
</html>
