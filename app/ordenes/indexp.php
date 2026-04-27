<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

if (empty($Contratista)) {
    header("Location: ../index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ERP - OPERACIONES</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <script src="https://kit.fontawesome.com/b5137b0dd6.js" crossorigin="anonymous"></script>

    <style>
        /* Hacer el indicador de periodo más visible para el usuario */
        .periodo-consulta { font-size: 1.15rem; }
        .periodo-consulta .badge { font-size: 1.05rem; padding: .45rem .9rem; }
        .periodo-consulta .periodo-label { color: #6c757d; font-weight:600; margin-right: .8rem; }
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-text mx-3">OPERACIONES</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
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
                        <a class='collapse-item' href='../almacen/index.php'><i class="fa-solid fa-box-open"></i> ALMACEN</a>
                        <a class='collapse-item' href='index.php'><i class="fa-solid fa-school-circle-check"></i> OPERACIONES</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="OrdenesCoordiapp.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION COORDIAPP COMPLETAS</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="OrdenesIncompletas.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION COORDIAPP INCOMPLETAS</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="CargaExcel.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>CARGAR MASIVA PRODUCCION</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="Prenomina.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>PRENOMINA</span>
                </a>
            </li>

                  <li class="nav-item">
                <a class="nav-link" href="NominaContratista2.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>PRODUCCION ACEPTADA</span>
                </a>
            </li>

             <li class="nav-item">
                <a class="nav-link" href="OrdenesNoCargadasCoordiapp.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>NO CARGADAS COORDIAPP</span>
                </a>
            </li>

             <li class="nav-item">
                <a class="nav-link" href="Produccion.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>PRODUCCION CARGADA</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="col-xl-10 col-md-10 mb-10" id="FormQueryUnidades">
                        <div class="input-group">
                            <?php
                                // Valores por defecto: día actual (ambos campos)
                                $defaultFecha1 = $_POST['Fecha1'] ?? date('Y-m-d');
                                $defaultFecha2 = $_POST['Fecha2'] ?? date('Y-m-d');
                            ?>

                            <input type="date" name="Fecha1" id="Fecha1" 
                                   value="<?= htmlspecialchars($defaultFecha1) ?>" 
                                   class="form-control bg-light border-0 small">

                            <input type="date" name="Fecha2" id="Fecha2" 
                                   value="<?= htmlspecialchars($defaultFecha2) ?>" 
                                   class="form-control bg-light border-0 small">

                            <!-- Botón lupa: solo buscar -->
<div class="input-group-append mr-2">
    <button id="btnBuscar" class="btn btn-primary" type="button" onclick="actualizarCards();">
        <i class="fas fa-search fa-sm"></i> Buscar
    </button>
</div>

<!-- Botón procesar alertas: separado y con color distinto -->
<div class="input-group-append mr-2">
<button id="btnProcesar" class="btn btn-danger" type="button" onclick="mostrarModalProcesar();">
    <i class="fas fa-cogs fa-sm"></i> Procesar Alertas
</button>

</div>

                        </div>

                        <!-- Estado del proceso -->
                        <div id="estadoProceso" class="mt-2 text-sm text-gray-600"></div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $Contratista;?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    PERFIL
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    CERRAR SESION
                                </a>
                            </div>
                        </li>
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- TOTAL TAC -->
                <div class="row">
                    <!-- Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            TOTAL TAC
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total_tac">
                                            0
                                        </div>
                                        <div class="text-xs text-muted">
                                            Promedio: <span id="promedio_tac">0</span>/día
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card -->

                    <!-- Card -->
<!-- Tarjeta con redirección -->
<div class="col-xl-3 col-md-6 mb-4" onclick="redirectToAlertas();">
    <div class="card border-left-success shadow h-100 py-2" style="cursor: pointer;">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        ORDENES CON ALERTA POR RANGO DE FECHAS
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="alertas">
                        0
                    </div>
                    <div class="text-xs text-muted">
                        Promedio: <span id="promedio_alertas">0</span>/día
                    </div>
                </div>

                <div class="col-auto">
                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>


                

                    <!-- Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2"
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            TOTAL GENERAL
                                        </div>
                                        <!--<div class="h5 mb-0 font-weight-bold text-gray-800" id="faltantes">
                                            0
                                        </div> -->
                                        
                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            CUMPLIMIENTO
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="cumplimiento">
                                            0%
                                        </div>

                                        <!-- Barra -->
                                        <div class="progress mt-2" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar"
                                                style="width: 0%" id="barra_cumplimiento">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Cards -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; ENLACE DIGITAL 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CERRAR SESION</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">PRESIONA SALIR, PARA CERRAR LA SESSION.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">CANCELAR</button>
                    <a class="btn btn-primary" href="../Login/modal/poweroff.php">SALIR</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmación Procesar Alertas -->
<div class="modal fade" id="modalProcesarAlertas" tabindex="-1" role="dialog" aria-labelledby="modalProcesarAlertasLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalProcesarAlertasLabel">Confirmar procesamiento</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <p>¿Desea <strong>generar alertas nuevas</strong> para el rango de fechas seleccionado?</p>
        <p class="text-muted">Esto puede insertar registros adicionales en la base de datos.</p>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btnConfirmarProcesar">Sí, procesar alertas</button>
      </div>
      
    </div>
  </div>
</div>


    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.js" integrity="sha384-2Ul6oqy3mEjM7dBJzKOck1Qb/mzlO+k/0BQv3D3C7u+Ri9+7OBINGa24AeOv5rgu" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/fixedColumns.dataTables.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.4/js/dataTables.fixedColumns.js"></script> 
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
      <script src="js/funcion.js"></script>

<script>
// Función para redirigir con las fechas
function redirectToAlertas() {
    // Obtener los valores de las fechas desde los campos del formulario
    var fecha1 = document.getElementById('Fecha1').value;
    var fecha2 = document.getElementById('Fecha2').value;

    // Redirigir a Alertas.php pasando las fechas como parámetros en la URL
    window.location.href = 'Alertas.php?fecha_inicio=' + encodeURIComponent(fecha1) + '&fecha_fin=' + encodeURIComponent(fecha2);
}
</script>

<script>
    // Mostrar el modal cuando el usuario da clic en el botón "Procesar"
function mostrarModalProcesar() {
    $('#modalProcesarAlertas').modal('show');
}

// Cuando el usuario confirma en el modal
$(document).on('click', '#btnConfirmarProcesar', function() {
    $('#modalProcesarAlertas').modal('hide'); // cerrar modal
    procesarAlertas(); // ejecutar tu función original
});

</script>
    <!-- Page level custom scripts -->
   


</body>

</html>