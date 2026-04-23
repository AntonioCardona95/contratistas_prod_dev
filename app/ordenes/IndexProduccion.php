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
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ERP - Operaciones</title>

    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>

<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-text mx-3">Operaciones</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-layer-group"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">ACCION</div>

            <?php if($tienePermisoEdicion == 1): ?>
                
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages">
                        <i class="fas fa-fw fa-cubes"></i>
                        <span>MODULOS</span>
                    </a>
                    <div id="collapsePages" class="collapse" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">MODULOS</h6>
                            <?php 
                                if(!empty($modulos)){
                                    foreach($modulos as $mod){
                                        echo "<a class='collapse-item' href='".$mod['ruta_modulo']."'>".$mod['icon_fav']." ".$mod['nombre_modulo']."</a>";
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </li>

                <hr class="sidebar-divider">

                <li class="nav-item">
                    <a class="nav-link collapsed">
                        <i class="fas fa-fw fa-cube"></i>
                        <span>SUB-MODULOS</span>
                    </a>
                </li>
                <?php 
                    if(!empty($submodulos)){
                        foreach($submodulos as $submod){
                            echo "<li class='nav-item'>";
                            echo '<a class="nav-link" href="'.$submod['ruta_submodulo'].'">';
                            echo '<span>'.$submod['icon_fav_submodulo'].' '.$submod['nombre_submodulo'].'</span>';
                            echo '</a>';
                            echo "</li>";
                        }
                    }
                ?>
                <hr class="sidebar-divider">
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="Ordenes_Completadas.php">
                    <i class="fas fa-fw fa-clipboard-check"></i>
                    <span>ORDENES COMPLETADAS</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="Ordenes_Incompletas.php">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>ORDENES INCOMPLETAS</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- FIN DE LA BARRA LATERAL -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar con Filtros -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Topbar Search -->
                    <form class="col-xl-10 col-md-10 mb-0" id="FormFiltros">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                            
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control bg-light border-0 small" 
                                   value="<?php echo date('Y-m-d'); ?>"
                                   aria-label="Fecha Inicio" placeholder="Fecha Inicio">
                            
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control bg-light border-0 small" 
                                   value="<?php echo date('Y-m-d'); ?>"
                                   aria-label="Fecha Fin" placeholder="Fecha Fin">
                            
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" onclick="cargarDashboard();">
                                    <i class="fas fa-search fa-sm"></i> Analizar
                                </button>
                                <button class="btn btn-outline-secondary" type="button" onclick="limpiarFiltros();" title="Limpiar filtros">
                                    <i class="fas fa-eraser fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($Nombres);?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
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

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h1 class="h3 mb-0 text-gray-800">
                                <i class="fas fa-chart-line text-primary"></i> Dashboard COORDIAPP
                            </h1>
                            <p class="text-muted mb-0" id="rango_fechas">Selecciona un rango de fechas para analizar</p>
                        </div>
                        <div class="text-right">
                            <small class="text-muted">Última actualización: <span id="ultima_actualizacion">Cargando...</span></small>
                            <br>
                            <span class="badge badge-info" id="total_dias">0 días analizados</span>
                        </div>
                    </div>

                    <div class="row">

    <!-- TOTAL TAC -->
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

    <!-- REGISTRADAS -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            REGISTRADAS
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="registradas">
                            0
                        </div>
                        <div class="text-xs text-muted">
                            Promedio: <span id="promedio_registradas">0</span>/día
                        </div>
                    </div>

                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- FALTANTES -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            FALTANTES
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="faltantes">
                            0
                        </div>
                        <div class="text-xs text-muted">
                            Promedio: <span id="promedio_faltantes">0</span>/día
                        </div>
                    </div>

                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- CUMPLIMIENTO -->
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
                    
                    

            </div>
            <!-- End of Main Content -->

            <!-- FOOTER -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; ENLACE DIGITAL 2025</span>
                    </div>
                </div>
            </footer>
            <!-- FIN DEL FOOTER -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- FIN DEL CONTENEDOR DE LA PAGINA -->

    <!-- BOTON DE SCROLL HACIA ARRIBA-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    

    
    <!-- MODAL DE CIERRE DE SESION -->
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

    <!-- Scripts modernos -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script>
    <script src="js/funcion.js"></script>

    
</body>
</html>