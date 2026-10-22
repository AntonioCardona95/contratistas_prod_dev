<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');
    
    require_once("controller/ClassSalidas.php");
    require_once(__DIR__ . '/../config/ContratistaContexto.php');
    require_once("modal/cargar.php");

    session_start();
    $Contratista = $_SESSION['Contratista'];
    requerirSesionAlmacen();
    $idContratistas = obtenerIdContratistaContexto();

    $modal = new Salidas();

?>
<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CONTRATISTAS</title>

    <!-- CARGA DE ICONOS Y FUENTES -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">

    <!-- ESTILOS BASE DE LA PLANTILLA -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- ESTILOS PARA TABLAS Y RESPONSIVIDAD -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../css/responsive_custom.css" rel="stylesheet">
    <link href="css/salidas.css" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- ESTILO PERSONALIZADO PARA EL MENU ACTIVO DE LA SIDEBAR -->
    <link rel="stylesheet" href="css/sidebar-active.css">

</head>

<body id="page-top" class="salidas-page">

    <!-- CONTENEDOR PRINCIPAL DE LA PÁGINA -->
    <div id="wrapper">

        <!-- SIDEBAR IMPORTADA DESDE COMPONENTES -->
        <?php include_once(__DIR__ . '/../components/SidebarAlmacen.php'); ?>

        <!-- CONTENEDOR DEL ÁREA DE CONTENIDO -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- PRINCIPAL: CONTENIDO DE LA PÁGINA -->
            <div id="content">

                <!-- TOPBAR CON ACCIONES DEL MODULO -->
                <?php
                    // Esta pantalla define su acción del topbar y agrega el botón para registrar una nueva salida.
                    $topbar_defaults = [
                        'Fecha1' => date('Y-m-d', strtotime('-3 months')),
                        'Fecha2' => date('Y-m-d')
                    ];
                    $topbar_onchange = 'LoadSalidas';
                    $topbar_actions = '<a type="button" class="btn btn-success" data-toggle="modal" data-target="#modalNuevaSalida"><i class="fas fa-plus"></i> NUEVA SALIDA</a>';
                    include_once(__DIR__ . '/../components/TopbarContratistas.php');
                ?>
                <div class="container-fluid">

                    <!-- TARJETA PRINCIPAL DE LISTADO -->
                    <div class="card shadow mb-4 table-container">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h2 class="m-0 font-weight-bold text-gray-800"> MATERIAL ASIGNADO A MIS TECNICOS <i class="fa-solid fa-square-plus"></i></h2>
                            <?php $selectorCallback = 'LoadSalidas'; include_once(__DIR__ . '/../components/SelectorContratista.php'); ?>
                        </div>
                        <div class="card-body p-0">
                            <!-- CONTENEDOR DONDE SE CARGA LA TABLA DINÁMICAMENTE -->
                            <div id="LoadSalidas" class="p-3">
                                <!-- Aquí se renderiza el contenido con AJAX -->
                            </div>
                        </div> 
                    </div>

                </div>

            </div>
            <!-- FIN DEL CONTENIDO PRINCIPAL -->

            <!-- PIE DE PÁGINA DEL PORTAL -->
            <?php include_once(__DIR__ . '/../components/FooterContratistas.php'); ?>

        </div>
        <!-- FIN DEL CONTENEDOR DEL CONTENIDO -->

    </div>
    <!-- FIN DEL CONTENEDOR PRINCIPAL -->

    <!-- MODAL DE NUEVA SALIDA -->
    <div class="modal fade" id="modalNuevaSalida" tabindex="-1" role="dialog" aria-labelledby="modalNuevaSalidaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient text-white">
                    <h5 class="modal-title font-weight-bold" id="modalNuevaSalidaLabel"><i class="fas fa-plus-circle"></i> ASIGNAR MATERIAL A TÉCNICO</h5>
                    <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- filepath: c:\Users\Axel Garcia OPS\Desktop\HTDOCS\contratistas_dash\nueva_salida.php -->
                <form action="modal/Salidas_GuardarNuevaSalida.php" method="post" id="FormNuevaSalida" enctype="multipart/form-data" name="formulario">
                    <div class="modal-body">
                        <div class="card-form-shell">

                            <!-- Folio Salida -->
                            <div class="form-group">
                                <label for="Folio_Salida">Folio Salida</label>
                                <?php $folio = $modal->FolioProvisionalSalida(); ?>
                                <input type="number" name="Folio_Salida" id="Folio_Salida" class="form-control" value="<?php echo $folio; ?>" readonly>
                            </div>

                            <!-- Contratista -->
                            <input type="hidden" name="FK_Contratista_Salida" id="FK_Contratista_Salida" value="<?php echo $idContratistas; ?>">

                            <!-- Técnico -->
                            <div class="form-group" id="grupoTecnico">
                                <label for="FK_Tecnico_Salida_Det">Técnico</label>
                                <select name="FK_Tecnico_Salida_Det" id="FK_Tecnico_Salida_Det" class="form-control">
                                    <option value="">Selecciona Tecnico</option>
                                    <?php Tecnicos($idContratistas); ?>
                                </select>
                            </div>

                            <!-- Material -->
                            <div class="form-group" id="grupoMaterial">
                                <label for="FK_Material_Salidas_Contratistas">Material</label>
                                <select name="FK_Material_Salidas_Contratistas" id="FK_Material_Salidas_Contratistas" class="form-control" onchange="LoadSeries();">
                                    <option value="">Selecciona Material</option>
                                    <?php Materiales($idContratistas); ?>
                                </select>
                            </div>

                            <!-- Series disponibles y seleccionadas -->
                            <div class="form-group series-transfer" id="grupoSeriesSeleccionadas">
                                <div class="series-transfer-layout">
                                    <section class="series-panel" aria-labelledby="seriesDisponiblesTitulo">
                                        <header class="series-panel-header">
                                            <h6 id="seriesDisponiblesTitulo"><i class="fas fa-boxes"></i> Series Disponibles</h6>
                                            <span class="series-count" id="availableSeriesCount">0</span>
                                        </header>
                                        <label class="series-filter">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                            <input type="search" id="filterAvailableSeries" placeholder="Filtrar disponibles..." aria-label="Filtrar series disponibles">
                                        </label>
                                        <div class="series-list" id="availableSeriesList" role="listbox" aria-label="Series disponibles" aria-multiselectable="true"></div>
                                    </section>

                                    <div class="series-transfer-actions" aria-label="Mover series">
                                        <button type="button" id="moveAllToSelected" class="series-transfer-button" aria-label="Mover todas las series disponibles a seleccionadas" title="Mover todas a seleccionadas">
                                            <i class="fas fa-angle-double-right" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" id="moveAllToAvailable" class="series-transfer-button" aria-label="Devolver todas las series seleccionadas a disponibles" title="Devolver todas a disponibles">
                                            <i class="fas fa-angle-double-left" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <section class="series-panel" aria-labelledby="seriesSeleccionadasTitulo">
                                        <header class="series-panel-header">
                                            <h6 id="seriesSeleccionadasTitulo"><i class="fas fa-check-circle"></i> Series Seleccionadas</h6>
                                            <span class="series-count series-count-selected" id="selectedSeriesCount">0</span>
                                        </header>
                                        <label class="series-filter">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                            <input type="search" id="filterSelectedSeries" placeholder="Filtrar seleccionadas..." aria-label="Filtrar series seleccionadas">
                                        </label>
                                        <div class="series-list" id="selectedSeriesList" role="listbox" aria-label="Series seleccionadas" aria-multiselectable="true"></div>
                                    </section>
                                </div>

                                <select id="Num_Serie_T" class="series-options-source" multiple aria-hidden="true" tabindex="-1"></select>
                                <table id="selectedSeriesTable" class="series-form-values" aria-hidden="true">
                                    <tbody id="selectedSeries"></tbody>
                                </table>
                            </div>

                            <!-- Descripción -->
                            <div class="form-group">
                                <label for="Descripcion_Salida_Det">Descripción</label>
                                <textarea name="Descripcion_Salida_Det" id="Descripcion_Salida_Det" class="form-control" placeholder="Descripción de la salida"></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <!-- Botón para procesar -->
                        <button type="submit" id="btnProcesarSalida" class="btn btn-primary">
                            <i class="fas fa-check"></i> Procesar Salida
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- BOTÓN PARA REGRESAR AL INICIO -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- MODAL DE CIERRE DE SESIÓN -->
    <?php include_once(__DIR__ . '/../components/ModalCierreSesion.php'); ?>

    <!-- MODAL DE DETALLE DE SALIDA -->
    <div class="modal fade" id="modalSalidaDetalle" tabindex="-1" role="dialog"
        aria-labelledby="modalSalidaDetalleTitulo" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient text-white">
                    <h5 class="modal-title" id="modalSalidaDetalleTitulo">Detalle de salida: Folio </h5>
                    <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalSalidaDetalleContenido">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS DE CONTRATISTAS -->
    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>
    <script src="js/salidas.js"></script>
    <script src="js/app-2.js"></script>

    <script>
        $(document).ready(function(){
            LoadSalidas();

            function inicializarSelect2($select, placeholder) {
                if (!$select.length || $select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                const opciones = {
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%'
                };

                // Esta vista es una página completa. Si el select se reutiliza
                // dentro de un modal Bootstrap, el desplegable queda dentro de él.
                const $modalPadre = $select.closest('.modal');
                if ($modalPadre.length) {
                    opciones.dropdownParent = $modalPadre;
                }

                $select.select2(opciones);
                $select.on('select2:open.nuevaSalida', function() {
                    setTimeout(function() {
                        $('.select2-container--open .select2-search__field').focus().select();
                    }, 100);
                });
            }

            $('#modalNuevaSalida').on('shown.bs.modal', function() {
                inicializarSelect2($('#FK_Tecnico_Salida_Det'), 'Selecciona Tecnico');
                inicializarSelect2($('#FK_Material_Salidas_Contratistas'), 'Selecciona Material');
            });

            inicializarSelect2($('#FK_Tecnico_Salida_Det'), 'Selecciona Tecnico');
            inicializarSelect2($('#FK_Material_Salidas_Contratistas'), 'Selecciona Material');

            function renderAvailableSeries() {
                const $source = $('#Num_Serie_T');
                const $list = $('#availableSeriesList').empty();
                const filtro = $('#filterAvailableSeries').val().trim().toLocaleLowerCase();
                const opciones = $source.find('option').filter(function() {
                    return $(this).val() !== '';
                });

                $('#availableSeriesCount').text(opciones.length);
                opciones.each(function() {
                    const serie = $(this).val();
                    if (!serie.toLocaleLowerCase().includes(filtro)) {
                        return;
                    }

                    const $item = $('<div class="series-item">');
                    const $selectButton = $('<button type="button" class="series-item-label">')
                        .attr('role', 'option')
                        .attr('aria-selected', 'false')
                        .text(serie)
                        .on('click', function() {
                            const seleccionado = !$(this).hasClass('is-active');
                            $(this).toggleClass('is-active', seleccionado)
                                .attr('aria-selected', seleccionado ? 'true' : 'false');
                        });
                    const $addButton = $('<button type="button" class="series-item-action" aria-label="Agregar serie">')
                        .attr('title', 'Agregar serie')
                        .html('<i class="fas fa-plus" aria-hidden="true"></i>')
                        .on('click', function() {
                            moveSeriesToSelected([serie]);
                        });

                    $item.append($selectButton, $addButton).appendTo($list);
                });

                if (!$list.children().length) {
                    $('<p class="series-empty">').text(opciones.length ? 'No hay coincidencias.' : 'No hay series disponibles.').appendTo($list);
                }
            }

            function renderSelectedSeries() {
                const $list = $('#selectedSeriesList').empty();
                const filtro = $('#filterSelectedSeries').val().trim().toLocaleLowerCase();
                const $inputs = $('#selectedSeries input[name="numeros_series[]"]');

                $('#selectedSeriesCount').text($inputs.length);
                $inputs.each(function() {
                    const serie = $(this).val();
                    if (!serie.toLocaleLowerCase().includes(filtro)) {
                        return;
                    }

                    const $item = $('<div class="series-item series-item-selected">');
                    $('<span class="series-item-label">').text(serie).appendTo($item);
                    $('<button type="button" class="series-item-action" aria-label="Quitar serie">')
                        .attr('title', 'Quitar serie')
                        .html('<i class="fas fa-times" aria-hidden="true"></i>')
                        .on('click', function() {
                            moveSeriesToAvailable(serie);
                        })
                        .appendTo($item);
                    $item.appendTo($list);
                });

                if (!$list.children().length) {
                    $('<p class="series-empty">').text($inputs.length ? 'No hay coincidencias.' : 'No hay series seleccionadas.').appendTo($list);
                }
            }

            function moveSeriesToSelected(series) {
                const $tbody = $('#selectedSeries');
                const existentes = new Set($tbody.find('input[name="numeros_series[]"]').map(function() {
                    return $(this).val();
                }).get());

                series.forEach(function(serie) {
                    if (existentes.has(serie)) {
                        return;
                    }

                    const $input = $('<input type="hidden" name="numeros_series[]">').val(serie);
                    $('<tr>').append($('<td>').append($input)).appendTo($tbody);
                    $('#Num_Serie_T option').filter(function() {
                        return $(this).val() === serie;
                    }).remove();
                });

                $('#grupoSeriesSeleccionadas').removeClass('campo-obligatorio-error');
                renderAvailableSeries();
                renderSelectedSeries();
            }

            function moveSeriesToAvailable(serie) {
                $('<option>').val(serie).text(serie).appendTo('#Num_Serie_T');
                $('#selectedSeries input[name="numeros_series[]"]').filter(function() {
                    return $(this).val() === serie;
                }).closest('tr').remove();
                renderAvailableSeries();
                renderSelectedSeries();
            }

            $('#Num_Serie_T').on('change', renderAvailableSeries);
            $('#filterAvailableSeries').on('input', renderAvailableSeries);
            $('#filterSelectedSeries').on('input', renderSelectedSeries);
            $('#moveAllToSelected').on('click', function() {
                const series = $('#Num_Serie_T option').map(function() {
                    return $(this).val();
                }).get().filter(Boolean);
                moveSeriesToSelected(series);
            });
            $('#moveAllToAvailable').on('click', function() {
                $('#selectedSeries input[name="numeros_series[]"]').each(function() {
                    moveSeriesToAvailable($(this).val());
                });
            });

            renderAvailableSeries();
            renderSelectedSeries();

            function marcarCampoObligatorio($campo, mostrarError) {
                $campo.toggleClass('campo-obligatorio-error', mostrarError)
                    .attr('aria-invalid', mostrarError ? 'true' : 'false');
                $campo.next('.select2-container').toggleClass('campo-obligatorio-error', mostrarError);
            }

            function enfocarCampo($campo) {
                const $grupo = $campo.closest('.form-group');
                if ($grupo.length) {
                    $('#modalNuevaSalida').animate({ scrollTop: $grupo.offset().top - 90 }, 250);
                }
                $campo.blur();
                if ($campo.data('select2')) {
                    $campo.select2('close');
                }
            }

            $('#FK_Tecnico_Salida_Det, #FK_Material_Salidas_Contratistas').on('change', function() {
                if ($(this).val()) {
                    marcarCampoObligatorio($(this), false);
                }
            });

            // El folio definitivo se asigna en el servidor. El formulario se
            // envía por AJAX para poder mostrarlo al usuario con SweetAlert.
            $(document).on('submit', '#FormNuevaSalida', function(event) {
                event.preventDefault();

                const $tecnico = $('#FK_Tecnico_Salida_Det');
                const $material = $('#FK_Material_Salidas_Contratistas');
                const tecnicoVacio = !$tecnico.val();
                const materialVacio = !$material.val();

                // E-001 tiene prioridad: primero se deben seleccionar Técnico
                // y Material antes de validar las series.
                marcarCampoObligatorio($tecnico, tecnicoVacio);
                marcarCampoObligatorio($material, materialVacio);
                if (tecnicoVacio || materialVacio) {
                    Swal.fire({
                        icon: 'error',
                        title: 'E-001 CAMPOS OBLIGATORIOS',
                        text: 'Completa todos los campos obligatorios resaltados en rojo.',
                        confirmButtonText: 'Entendido'
                    }).then(function() {
                        enfocarCampo(tecnicoVacio ? $tecnico : $material);
                    });
                    return;
                }

                const $grupoSeries = $('#grupoSeriesSeleccionadas');
                const sinSeries = $('#selectedSeries input[name="numeros_series[]"]').length === 0;
                $grupoSeries.toggleClass('campo-obligatorio-error', sinSeries);
                if (sinSeries) {
                    Swal.fire({
                        icon: 'error',
                        title: 'E-002 SERIES OBLIGATORIAS',
                        text: 'Agrega al menos un número de serie para continuar.',
                        confirmButtonText: 'Entendido'
                    }).then(function() {
                        $('#modalNuevaSalida').animate({ scrollTop: $grupoSeries.offset().top - 90 }, 250);
                    });
                    return;
                }

                const formulario = this;

                const $formulario = $(formulario);
                const $boton = $('#btnProcesarSalida');
                const textoOriginal = $boton.html();

                Swal.fire({
                    icon: 'question',
                    title: '¿Procesar salida?',
                    text: 'Las series seleccionadas serán asignadas al técnico.',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, procesar',
                    cancelButtonText: 'Cancelar',
                    allowOutsideClick: false
                }).then(function(resultado) {
                    if (!resultado.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        type: 'POST',
                        url: $formulario.attr('action'),
                        data: $formulario.serialize(),
                        dataType: 'json',
                        beforeSend: function() {
                            $boton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#modalNuevaSalida').modal('hide');
                                $formulario[0].reset();
                                $('#FK_Tecnico_Salida_Det').val(null).trigger('change');
                                $('#FK_Material_Salidas_Contratistas').val(null).trigger('change');
                                $('#Num_Serie_T').empty().trigger('change');
                                $('#selectedSeries').empty();
                                $('.campo-obligatorio-error').removeClass('campo-obligatorio-error');

                                LoadSalidas();

                                Swal.fire({
                                    position: "top",
                                    icon: "success",
                                    title: "Salida guardada correctamente",
                                    html: "<strong>Folio asignado:</strong> " + response.folio_real +
                                        (response.cambio_folio
                                            ? "<br><br><strong>Nota:</strong> El folio provisional fue actualizado correctamente."
                                            : ""),
                                    showConfirmButton: true,
                                    allowOutsideClick: false
                                });
                                return;
                            }

                            Swal.fire('No se pudo guardar la salida', response.message || 'Intente nuevamente.', 'error');
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON || {};
                            Swal.fire('No se pudo guardar la salida', response.message || 'Ocurrió un error al procesar la salida.', 'error');
                        },
                        complete: function() {
                            $boton.prop('disabled', false).html(textoOriginal);
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>
