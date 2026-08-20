<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');
    
    require_once("controller/ClassOps.php");
    require_once("modal/cargar.php");

    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];

    if (empty($idContratistas)) {
        header("location: ../Login/modal/poweroff.php");
    }

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- ESTILOS BASE DE LA PLANTILLA -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- ESTILOS PARA TABLAS Y RESPONSIVIDAD -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../css/responsive_custom.css" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- ESTILO PERSONALIZADO PARA EL MENU ACTIVO DE LA SIDEBAR -->
    <link rel="stylesheet" href="css/sidebar-active.css">

    <style>
        /* ESTILOS GENERALES DEL FORMULARIO Y DE LA CABECERA. */
        .selected-option {
            background-color: #d4edda !important;
            color: #155724 !important;
        }
        .modal-header-gradient {
            background: linear-gradient(90deg, #0f172a 0%, #003d99 100%) !important;
        }

        /* DA FORMA A LA TARJETA Y A LOS CAMPOS DEL FORMULARIO. */
        .card-form-shell {
            background: #f3f5f8;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 18px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
            padding: 1.25rem 1.25rem 1rem 1.25rem;
        }

        #modalNuevaSalida label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.45rem;
            font-size: 1.05rem;
        }

        #modalNuevaSalida input[type="text"],
        #modalNuevaSalida input[type="date"],
        #modalNuevaSalida select,
        #modalNuevaSalida textarea,
        #modalNuevaSalida .form-control {
            border: 1px solid #dfe3ea;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.82);
            min-height: 30px;
            padding: 0rem 0.9rem;
            font-size: 1rem;
            color: #2f3743;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.02);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        #modalNuevaSalida textarea.form-control {
            min-height: 110px;
            resize: vertical;
        }

        /* PERSONALIZA LOS BOTONES DE AGREGAR Y PROCESAR LA SALIDA. */
        #modalNuevaSalida .btn-primary,
        #modalNuevaSalida #addRow {
            border: none;
            border-radius: 10px;
            font-weight: 700;
            min-height: 42px;
            padding: 5px 15px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        #modalNuevaSalida #addRow {
            background: linear-gradient(180deg, #1a315c 0%, #163767 100%);
            box-shadow: 0 8px 18px rgba(22, 55, 103, 0.18);
            color: #fff;
        }

        /* BOTON DE PROCESAR SALIDA */
        #modalNuevaSalida button[type="submit"] {
            background: linear-gradient(180deg, #22b56a 0%, #1d9f5d 100%);
        }

        #modalNuevaSalida .btn-primary:hover,
        #modalNuevaSalida button[type="submit"]:hover,
        #modalNuevaSalida #addRow:hover {
            transform: translateY(-1px);
        }

        /* DA FORMATO A LA TABLA DE SERIES SELECCIONADAS. */
        #modalNuevaSalida .table {
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid #dfe3ea;
            margin-bottom: 0;
        }

        /* TITULO DE LA COLUMNA */
        #modalNuevaSalida .table thead th {
            background: #e7ebf0;
            color: #364152;
            font-weight: 700;
            border-bottom: 1px solid #dfe3ea;
        }

        /* REGISTROS DE LA TABLA*/
        #modalNuevaSalida .table tbody td {
            vertical-align: middle;
            border-top: 1px solid #eef2f7;
            background: rgba(255,255,255,0.45);
        }

        #modalNuevaSalida #selectedSeries input[name="numeros_series[]"] {
            background: #fff;
            border: 1px solid #dfe3ea;
            border-radius: 10px;
            min-height: 42px;
            padding: 0.6rem 0.8rem;
            font-size: 0.96rem;
            color: #2f3743;
        }

        #modalNuevaSalida .removeRow {
            border-radius: 10px;
            min-width: 42px;
            min-height: 42px;
            background: #e74c3c;
            border: none;
            color: #fff;
        }

        /* RESALTA LOS CAMPOS Y EL BLOQUE DE SERIES CON ERRORES DE VALIDACIÓN. */
        #modalNuevaSalida .campo-obligatorio-error,
        #modalNuevaSalida .select2-container.campo-obligatorio-error .select2-selection {
            border-color: #e74a3b !important;
            box-shadow: 0 0 0 0.2rem rgba(231, 74, 59, 0.16) !important;
        }

        #modalNuevaSalida #grupoSeriesSeleccionadas.campo-obligatorio-error {
            border: 1px solid #e74a3b;
            border-radius: 12px;
            padding: 0.8rem;
            background: #fff4f3;
        }
    </style>

</head>

<body id="page-top">

    <!-- CONTENEDOR PRINCIPAL DE LA PÁGINA -->
    <div id="wrapper">

        <!-- SIDEBAR PRINCIPAL DEL MODULO DE ALMACEN -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #007BFF; background-image: linear-gradient(180deg, #0f172a 100%, #224abe 0%); background-size: cover;">

            <!-- ENCABEZADO DEL MODULO -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-text mx-3">ALMACEN</div>
            </a>

            <!-- SEPARADOR SUPERIOR -->
            <hr class="sidebar-divider my-0">

            <!-- OPCIÓN DE DASHBOARD -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- SEPARADOR DE SECCIÓN -->
            <hr class="sidebar-divider">

            <!-- TÍTULO DE SECCIÓN -->
            <div class="sidebar-heading">
                ACCION
            </div>

            <!-- MENU DE MODULOS -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>MODULOS</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">MODULOS</h6>
                        <a class='collapse-item' href='index.php'><i class="fa-solid fa-box-open"></i> ALMACEN</a>
                        <a class='collapse-item' href='../ordenes/index.php'><i class="fa-solid fa-school-circle-check"></i> OPERACIONES</a>
                    </div>
                </div>
            </li>

            <!-- MÓDULO: MATERIAL RECIBIDO -->
            <li class="nav-item">
                <a class="nav-link" href="entradas.php">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>MATERIAL RECIBIDO</span>
                </a>
            </li>

            <!-- MÓDULO: ACEPTAR MATERIAL -->
             <li class="nav-item">
                <a class="nav-link" href="aceptarMaterial.php">
                    <i class="fa-solid fa-feather-pointed"></i>
                    <span>ACEPTAR MATERIAL</span>
                </a>
            </li>

            <!-- MÓDULO: MATERIAL RECHAZADO -->
             <li class="nav-item">
                <a class="nav-link" href="materialRechazado.php">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>MATERIAL RECHAZADO</span>
                </a>
            </li>

            <!-- MÓDULO: ASIGNAR MATERIAL -->
            <li class="nav-item active">
                <a class="nav-link" href="salidas.php">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>ASIGNAR MATERIAL</span>
                </a>
            </li>

            <!-- MÓDULO: TÉCNICOS -->
            <li class="nav-item">
                <a class="nav-link" href="tecnicos.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>TÉCNICOS</span>
                </a>
            </li>

            <!-- SEPARADOR FINAL DEL SIDEBAR -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- BOTÓN DE COLLAPSE PARA DISPOSITIVOS MÓVILES -->
            <div class="sidebar-footer text-center">
                <button class="rounded-circle border-0" id="sidebarToggle" aria-label="Cerrar menú">
                    <i class="fas fa-times"></i>
                </button>
            </div>

        </ul>
        <!-- FIN DE LA SIDEBAR -->

        <!-- CONTENEDOR DEL ÁREA DE CONTENIDO -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- PRINCIPAL: CONTENIDO DE LA PÁGINA -->
            <div id="content">

                <!-- TOPBAR CON ACCIONES DEL MODULO -->
                <?php
                    // Esta pantalla define su acción del topbar y agrega el botón para registrar una nueva salida.
                    $topbar_onchange = 'LoadSalidas';
                    $topbar_actions = '<a type="button" class="btn btn-success" data-toggle="modal" data-target="#modalNuevaSalida"><i class="fas fa-plus"></i> NUEVA SALIDA</a>';
                    include_once(__DIR__ . '/../components/TopbarContratistas.php');
                ?>
                <div class="container-fluid">

                    <!-- TARJETA PRINCIPAL DE LISTADO -->
                    <div class="card shadow mb-4 table-container">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h2 class="m-0 font-weight-bold text-gray-800"> MATERIAL ASIGNADO A MIS TECNICOS</h2>
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
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- filepath: c:\Users\Axel Garcia OPS\Desktop\HTDOCS\contratistas_dash\nueva_salida.php -->
                <form action="modal/newsalida2.php" method="post" id="FormNuevaSalida" enctype="multipart/form-data" name="formulario">
                    <div class="modal-body">
                        <div class="card-form-shell">

                            <!-- Folio Salida -->
                            <div class="form-group">
                                <label for="Folio_Salida">Folio Salida</label>
                                <?php UltimaSalida(); ?>
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

                            <!-- Series -->
                            <div class="form-group" id="grupoSeriesSeleccionadas">
                                <label for="Num_Serie_T">Series Disponibles</label>
                                <select id="Num_Serie_T" class="form-control" multiple>
                                    <!-- Las series se cargarán dinámicamente -->
                                </select>
                            </div>

                            <!-- Botón para agregar series -->
                            <div class="form-group text-center">
                                <button id="addRow" type="button" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Agregar Serie
                                </button>
                            </div>

                            <!-- Tabla de series seleccionadas -->
                            <div class="form-group">
                                <label>Series Seleccionadas</label>
                                <table id="selectedSeriesTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Número de Serie</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedSeries">
                                        <!-- Las series seleccionadas se agregarán aquí -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Descripción -->
                            <div class="form-group">
                                <label for="Descripcion_Salida_Det">Descripción</label>
                                <textarea name="Descripcion_Salida_Det" id="Descripcion_Salida_Det" class="form-control" placeholder="Descripción de la transferencia"></textarea>
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

    <!-- SCRIPTS DE CONTRATISTAS -->
    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>
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
                inicializarSelect2($('#Num_Serie_T'), 'Selecciona series');
            });

            inicializarSelect2($('#FK_Tecnico_Salida_Det'), 'Selecciona Tecnico');
            inicializarSelect2($('#FK_Material_Salidas_Contratistas'), 'Selecciona Material');
            inicializarSelect2($('#Num_Serie_T'), 'Selecciona series');

            // No inicializar automáticamente el selectpicker para Num_Serie_T
            // porque se reinicializa dinámicamente en LoadSeries()
            
            // Agregar series seleccionadas a la tabla
            $("#addRow").click(function() {
                let opcionesSeleccionadas = $("#Num_Serie_T").val();
                let tbody = $("#selectedSeries");

                if (opcionesSeleccionadas && opcionesSeleccionadas.length > 0) {
                    // Obtener series ya existentes en la tabla para evitar duplicados
                    let seriesExistentes = [];
                    tbody.find('input[name="numeros_series[]"]').each(function() {
                        seriesExistentes.push($(this).val());
                    });

                    opcionesSeleccionadas.forEach(function(opcion) {
                        // Solo agregar si no existe ya en la tabla
                        if (!seriesExistentes.includes(opcion)) {
                            let nuevaFila = `
                        <tr>
                            <td>
                                <input type="text" class="form-control" name="numeros_series[]" value="${opcion}" readonly>
                            </td>
                            <td class="text-center" style="width: 80px;">
                                <button type="button" class="btn btn-danger btn-sm removeRow" aria-label="Eliminar serie">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>`;
                            tbody.append(nuevaFila);
                        }
                    });

                    // Eliminar las opciones seleccionadas del select múltiple
                    $("#Num_Serie_T option:selected").each(function() {
                        $(this).remove();
                    });
                    $('#Num_Serie_T').val(null).trigger('change');
                    $('#grupoSeriesSeleccionadas').removeClass('campo-obligatorio-error');
                } else {
                    alert("No hay series seleccionadas.");
                }
            });

            // Eliminar fila de la tabla y devolver la serie al select
            $(document).on("click", ".removeRow", function() {
                let serieValue = $(this).closest("tr").find('input[name="numeros_series[]"]').val();
                
                // Devolver la serie al select múltiple
                $("#Num_Serie_T").append(`<option value="${serieValue}">${serieValue}</option>`);
                $('#Num_Serie_T').trigger('change');
                
                // Eliminar la fila de la tabla
                $(this).closest("tr").remove();
            });

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

