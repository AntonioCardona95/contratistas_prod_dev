<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    date_default_timezone_set('America/Monterrey');
    
    require_once __DIR__ . '/../../controller/ClassOps.php';

    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];

    if (empty($idContratistas)) {
        header("location: ../../Login/modal/poweroff.php");
        exit;
    }
    
    $Fecha1 = $_POST['Fecha1'] ?? '';
    $Fecha2 = $_POST['Fecha2'] ?? '';
    $mes_anterior = date('Y-m-d', strtotime('-1 month'));

    require_once(__DIR__ . '/../../config/cnx.php');
    require_once(APP_PATH . '/controller/ClassSubModulos.php');
    
?>
<!DOCTYPE html>
<html lang="en">    

<!-- INSERTAR EL CSS -->
<?php  $css = ['/../../css/DashboardAlmacen.css'] ?>

<!-- IMPORTAR EL HEAD -->
<?php include __DIR__ . '/../../components/HeadContratistas.php'; ?>

<body id="page-top">

    <!-- CONTENEDOR PRINCIPAL -->
    <div id="wrapper">

        <!-- SIDEBAR -->
        <?php include_once __DIR__ . '/../../components/SidebarContratistas.php'; ?>

        <!-- CONTENEDOR DE CONTENIDO -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- CONTENIDO PRINCIPAL -->
            <div id="content">

                <!-- TOPBAR -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-12 static-top shadow">

                    <!-- FILTROS DE BUSQUEDA DEL TOPBAR -->
                    <form id="FormQueryUnidades" style="display: contents;">
                        <div class="input-group">

                            <?php
                                $fechaDefault = $_POST['fecha'] ?? date('Y-m-d', strtotime('-1 month'));
                                $fechaDefaultFinal = $_POST['fechaFinal'] ?? date('Y-m-d');
                                $hoy = date('Y-m-d');
                                $maximo = "2022-08-01";
                            ?>
                            <!-- FILTRO DE FECHA INICIO DE BUSQUEDA -->
                            <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($fechaDefault) ?>" style="max-width: 400px; min-width: 150px;"
                                class="form-control bg-light border-0 small mr-2" min="<?= htmlspecialchars($maximo) ?>" max="<?= htmlspecialchars($hoy) ?>" onchange="LoadKPIS();" 
                                aria-label="AL" aria-describedby="basic-addon2">
                            <!-- FILTRO DE FECHA FINAL DE BUSQUEDA -->
                            <input type="date" name="fechaFinal" id="fechaFinal" value="<?= htmlspecialchars($fechaDefaultFinal) ?>" style="max-width: 400px; min-width: 150px;"
                                class="form-control bg-light border-0 small mr-2" min="<?= htmlspecialchars($maximo) ?>" max="<?= htmlspecialchars($hoy) ?>" onchange="LoadKPIS();" 
                                aria-label="AL" aria-describedby="basic-addon2">
                            <!-- BOTON DE LIMPIAR FILTROS -->
                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm btn-danger mr-2" onclick="limpiarFiltros()" title="Limpiar filtros">
                                    <i class="fas fa-eraser"></i>
                                </button>
                            </div>
                        
                        </div>

                    </form>

                    
                    <ul class="navbar-nav ml-auto">

                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $Contratista;?></span>
                                <img class="img-profile rounded-circle"
                                    src="../../img/undraw_profile.svg">
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
                <!-- FIN DEL TOPBAR -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- TITULO -->
                    <div class="prod-hero">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <h1 class="h3 mb-0 font-weight-bold mt-2">GESTIÓN OPERATIVA DE EQUIPO Y SERVICIOS (ONT)</h1>
                        </div>
                    </div>

                    <!-- CONTENEDOR DE KPIs Y GRAFICAS -->
                    <div id="LoadKPIS" class="dashboard-container">
                        <!-- Cargado dinámicamente vía AJAX -->
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- FIN DEL CONTENIDO PRINCIPAL -->

            <!-- FOOTER -->
            <?php include_once __DIR__ . '/../../components/FooterContratistas.php'; ?>

        </div>
        <!-- FIN DEL CONTENEDOR DE CONTENIDO -->

    </div>
    <!-- FIN DEL CONTENEDOR PRINCIPAL -->

    <!-- BOTON DE SCROLL HACIA ARRIBA -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
     <!-- MODAL DE CIERRE DE SESION -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient text-white border-0 align-items-center">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-sign-out-alt"></i> CERRAR SESION</h5>
                    <button class="btn btn-light" type="button" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
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

    <!-- IMPORTAR SCRIPTS -->
    <?php include_once __DIR__ . '/../../components/ScriptContratistas.php'; ?>
    <!-- CARGAR CHART.JS v2 para compatibilidad con el código existente -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- CARGAR HIGCHARTS LOCAL -->
    <script src="/charts/code/highcharts.js"></script>
    <script src="../../js/almacen.js"></script>
    <script>
        $(document).ready(function(){
            LoadKPIS();
        });
    </script>

</body>
</html>