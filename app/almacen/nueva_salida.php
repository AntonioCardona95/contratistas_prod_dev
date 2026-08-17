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
        .selected-option {
            background-color: #d4edda !important;
            color: #155724 !important;
        }
        .modal-header-gradient {
            background: linear-gradient(90deg, #007BFF 0%, #007 100%) !important;
        }

        .page-nueva-salida #content {
            background: #eef1f6;
        }

        .page-nueva-salida .topbar {
            background: #fff !important;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            min-height: 72px;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
        }

        .page-nueva-salida .topbar-actions-container {
            margin-left: 0 !important;
            margin-right: auto !important;
            display: flex;
            align-items: center;
            order: 1;
        }

        .page-nueva-salida .topbar-actions-container a.btn {
            background: #a1a9b3;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.55rem 1rem;
            min-width: 136px;
            line-height: 1.2;
            box-shadow: none;
        }

        .page-nueva-salida .topbar-icons {
            margin-left: auto !important;
            order: 2;
        }

        .page-nueva-salida .topbar #sidebarToggleTop {
            display: inline-flex;
            background: transparent;
            border: none;
            color: #2c3e50;
            font-size: 1.3rem;
            padding: 0.4rem 0.7rem;
        }

        .page-nueva-salida .container-fluid {
            padding-bottom: 2rem;
        }

        .page-nueva-salida h1 {
            font-weight: 300;
            letter-spacing: -0.04em;
            color: rgba(31, 41, 55, 0.82);
            font-size: 32px;
            margin-bottom: 1.25rem;
            text-align: center;
        }

        .page-nueva-salida form {
            width: 100%;
        }

        .page-nueva-salida .container.mt-4 {
            max-width: 900px;
            margin: 0 auto 1.5rem auto;
            background: rgba(255, 255, 255, 0.28);
            border-radius: 18px;
            padding: 1rem 0 0.25rem 0;
        }

        .page-nueva-salida .card-form-shell {
            background: #f3f5f8;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 18px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
            padding: 1.25rem 1.25rem 1rem 1.25rem;
        }

        .page-nueva-salida label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.45rem;
            font-size: 1.05rem;
        }

        .page-nueva-salida input[type="text"],
        .page-nueva-salida input[type="date"],
        .page-nueva-salida select,
        .page-nueva-salida textarea,
        .page-nueva-salida .form-control {
            border: 1px solid #dfe3ea;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.82);
            min-height: 46px;
            padding: 0rem 0.9rem;
            font-size: 1rem;
            color: #2f3743;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.02);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .page-nueva-salida input[type="text"]:focus,
        .page-nueva-salida input[type="date"]:focus,
        .page-nueva-salida select:focus,
        .page-nueva-salida textarea:focus,
        .page-nueva-salida .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.12);
            outline: none;
        }

        .page-nueva-salida textarea.form-control {
            min-height: 110px;
            resize: vertical;
        }

        .page-nueva-salida .btn-primary,
        .page-nueva-salida #addRow {
            border: none;
            border-radius: 10px;
            font-weight: 700;
            min-height: 42px;
            padding: 0.7rem 1.2rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .page-nueva-salida #addRow {
            background: linear-gradient(180deg, #1a315c 0%, #163767 100%);
            box-shadow: 0 8px 18px rgba(22, 55, 103, 0.18);
            color: #fff;
        }

        .page-nueva-salida button[type="submit"] {
            background: linear-gradient(180deg, #22b56a 0%, #1d9f5d 100%);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            min-height: 42px;
            padding: 0.7rem 1.2rem;
            box-shadow: 0 8px 18px rgba(29, 159, 93, 0.18);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            color: #fff;
        }

        .page-nueva-salida .btn-primary:hover,
        .page-nueva-salida button[type="submit"]:hover,
        .page-nueva-salida #addRow:hover {
            transform: translateY(-1px);
        }

        .page-nueva-salida .table {
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid #dfe3ea;
            margin-bottom: 0;
        }

        .page-nueva-salida .table thead th {
            background: #e7ebf0;
            color: #364152;
            font-weight: 700;
            border-bottom: 1px solid #dfe3ea;
            padding: 0.9rem 0.8rem;
        }

        .page-nueva-salida .table tbody td {
            vertical-align: middle;
            padding: 0.8rem;
            border-top: 1px solid #eef2f7;
            background: rgba(255,255,255,0.45);
        }

        .page-nueva-salida #selectedSeries tr {
            background: transparent;
        }

        .page-nueva-salida #selectedSeries input[name="numeros_series[]"] {
            background: #fff;
            border: 1px solid #dfe3ea;
            border-radius: 10px;
            min-height: 42px;
            padding: 0.6rem 0.8rem;
            font-size: 0.96rem;
            color: #2f3743;
        }

        .page-nueva-salida .removeRow {
            border-radius: 10px;
            min-width: 42px;
            min-height: 42px;
            background: #e74c3c;
            border: none;
            color: #fff;
        }

        @media (max-width: 768px) {
            .page-nueva-salida .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .page-nueva-salida h1 {
                font-size: 2rem;
            }

            .page-nueva-salida .topbar {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                padding: 0.5rem 0.75rem;
            }

            .page-nueva-salida .topbar-icons {
                margin-left: auto !important;
            }

            .page-nueva-salida .topbar-actions-container {
                order: 3;
                width: 100%;
                margin: 0.5rem 0 0 0 !important;
            }

            .page-nueva-salida .topbar-actions-container a.btn {
                display: block;
                width: 100%;
                text-align: center;
                font-size: 14px;
            }

            .page-nueva-salida .container.mt-2 {
                max-width: 100%;
                margin-left: 0;
                margin-right: 0;
                padding: 0.75rem 0.25rem 0.25rem 0.25rem;
            }

            .page-nueva-salida label {
                font-size: 0.95rem;
            }
        }
    </style>
</head>

<body id="page-top" class="page-nueva-salida">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- SIDEBAR -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #007BFF; background-image: linear-gradient(180deg, #0f172a 100%, #224abe 0%); background-size: cover;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-text mx-3">ALMACEN</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- DASHBOARD -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                ACCION
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
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

            <!-- MATERIAL RECIBIDO -->
            <li class="nav-item">
                <a class="nav-link" href="entradas.php">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>MATERIAL RECIBIDO</span>
                </a>
            </li>

            <!-- ACEPTAR MATERIAL -->
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

            <!-- ASIGNAR MATERIAL -->
            <li class="nav-item active">
                <a class="nav-link" href="salidas.php">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>ASIGNAR MATERIAL</span>
                </a>
            </li>

            <!-- TECNICOS -->
            <li class="nav-item">
                <a class="nav-link" href="tecnicos.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>TÉCNICOS</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="sidebar-footer text-center">
                <button class="rounded-circle border-0" id="sidebarToggle" aria-label="Cerrar menu">
                    <i class="fas fa-times"></i>
                </button>
            </div>


        </ul>
        <!-- FIN DE LA SIDEBAR -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php
                    $topbar_defaults = [];
                    $topbar_onchange = '';
                    $topbar_hide_search = true;
                    $topbar_actions = '<a href="salidas.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Regresar</a>';
                    include_once(__DIR__ . '/../components/TopbarContratistas.php');
                ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 m-0 font-weight-bold text-gray-800">ASIGNAR MATERIAL A TECNICO</h1>

                    <!-- filepath: c:\Users\Axel Garcia OPS\Desktop\HTDOCS\contratistas_dash\nueva_salida.php -->
                    <form action="modal/newsalida2.php" method="post" id="FormQueryUnidades" enctype="multipart/form-data" name="formulario" onsubmit="return confirm('¿Está seguro de registrar?');">

                        <div class="container mt-2">
                            <div class="card-form-shell">

                            <!-- Folio Salida -->
                            <div class="form-group">
                                <label for="Folio_Salida">Folio Salida</label>
                                <?php UltimaSalida(); ?>
                            </div>

                            <!-- Contratista -->
                            <input type="hidden" name="FK_Contratista_Salida" id="FK_Contratista_Salida" value="<?php echo $idContratistas; ?>">

                            <!-- Técnico -->
                            <div class="form-group">
                                <label for="FK_Tecnico_Salida_Det">Técnico</label>
                                <select name="FK_Tecnico_Salida_Det" id="FK_Tecnico_Salida_Det" class="form-control" required>
                                    <option value="">Selecciona Técnico</option>
                                    <?php Tecnicos($idContratistas); ?>
                                </select>
                            </div>

                            <!-- Material -->
                            <div class="form-group">
                                <label for="FK_Material_Salidas_Contratistas">Material</label>
                                <select name="FK_Material_Salidas_Contratistas" id="FK_Material_Salidas_Contratistas" class="form-control" onchange="LoadSeries();" required>
                                    <option value="">Selecciona Material</option>
                                    <?php Materiales($idContratistas); ?>
                                </select>
                            </div>

                            <!-- Series -->
                            <div class="form-group">
                                <label for="Num_Serie_T">Series Disponibles</label>
                                <select id="Num_Serie_T" class="form-control" multiple>
                                    <!-- Las series se cargarán dinámicamente -->
                                </select>
                            </div>
                            <!-- ...existing code... -->

                            <!-- Botón para agregar series -->
                            <div class="form-group text-center">
                                <button id="addRow" type="button" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Agregar Serie
                                </button>
                            </div>
                            <!-- ...existing code... -->

                            <!-- Tabla de series seleccionadas -->
                            <div class="form-group">
                                <label>Series Seleccionadas</label>
                                <table class="table table-bordered">
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

                            <!-- Botón para procesar -->
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check"></i> Procesar Salida
                                </button>
                            </div>

                            </div>
                        </div>
                    </form>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- IMPORTAR EL FOOTER DE CONTRATISTAS -->
            <?php include_once(__DIR__ . '/../components/FooterContratistas.php'); ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- MODAL DE CIERRE DE SESION -->
    <?php include_once(__DIR__ . '/../components/ModalCierreSesion.php'); ?>

    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>
    <script src="js/app-2.js"></script>

    <script>
        $(document).ready(function() {
            function inicializarSelect2($select, placeholder) {
                if (!$select.length || $select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                const opciones = {
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%'
                };

                // Esta vista es una pÃ¡gina completa. Si el select se reutiliza
                // dentro de un modal Bootstrap, el desplegable queda dentro de Ã©l.
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

            inicializarSelect2($('#FK_Tecnico_Salida_Det'), 'Selecciona TÃ©cnico');
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
        });
    </script>

</body>

</html>

