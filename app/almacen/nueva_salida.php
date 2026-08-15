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

    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">

    <!-- ESTILO PERSONALIZADO PARA EL MENU ACTIVO DE LA SIDEBAR -->
    <link rel="stylesheet" href="css/sidebar-active.css">

    <style>
        .selected-option {
            background-color: #d4edda !important;
            /* Verde claro */
            color: #155724 !important;
            /* Texto verde oscuro */
        }
        .modal-header-gradient {
            background: linear-gradient(90deg, #007BFF 0%, #007 100%) !important;
        }
        @media (max-width: 768px) {

           h1{
            text-align: center;
            font-size: 20px;
           }
            
        }
    </style>
</head>

<body id="page-top">

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
                    <h1 class="h3 mb-2 text-gray-800">ASIGNAR MATERIAL A TECNICO</h1>

                    <!-- filepath: c:\Users\Axel Garcia OPS\Desktop\HTDOCS\contratistas_dash\nueva_salida.php -->
                    <form action="modal/newsalida2.php" method="post" id="FormQueryUnidades" enctype="multipart/form-data" name="formulario" onsubmit="return confirm('¿Está seguro de registrar?');">

                        <div class="container mt-4">

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
                                <select id="Num_Serie_T" class="form-control selectpicker" multiple data-live-search="true" title="Selecciona series">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script src="js/app-2.js"></script>

    <script>
        $(document).ready(function() {
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
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm removeRow">
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
                    $('#Num_Serie_T').selectpicker('refresh');
                } else {
                    alert("No hay series seleccionadas.");
                }
            });

            // Eliminar fila de la tabla y devolver la serie al select
            $(document).on("click", ".removeRow", function() {
                let serieValue = $(this).closest("tr").find('input[name="numeros_series[]"]').val();
                
                // Devolver la serie al select múltiple
                $("#Num_Serie_T").append(`<option value="${serieValue}">${serieValue}</option>`);
                $('#Num_Serie_T').selectpicker('refresh');
                
                // Eliminar la fila de la tabla
                $(this).closest("tr").remove();
            });
        });
    </script>

    <!-- Bootstrap Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

</body>

</html>

