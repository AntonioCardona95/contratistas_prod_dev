<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');
    
    require_once __DIR__ . '/../../controller/ClassOps.php';
    // require_once __DIR__ . '/../../modal/cargar.php';

    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];

    $fechaDefault = $_POST['fechaInicio'] ?? date('Y-m-d', strtotime('-2 months'));
    $fechaDefaultFinal = $_POST['fechaFin'] ?? date('Y-m-d');
    $hoy = date('Y-m-d');
    $maximo = "2022-08-01";

    if (empty($idContratistas)) {
        header("location: " . __DIR__ . "/../Login/modal/poweroff.php");
    }
?>
<!DOCTYPE html>
<html lang="en">

<!-- INSERTAR EL CSS -->
<?php  $css = ['/../../css/Entradas.css'] ?>
<?php include __DIR__ . '/../../components/HeadContratistas.php'; ?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- SIDEBAR -->
        <?php include_once __DIR__ . '/../../components/SidebarContratistas.php'; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <form id="FormQueryUnidades" style="display: contents;">
                        <div class="input-group">
                            <input type="date" name="fechaInicio" id="fechaInicio" value="<?= htmlspecialchars($fechaDefault) ?>" style="max-width: 400px; min-width: 150px;"
                                class="form-control bg-light border-0 small mr-2" min="<?= htmlspecialchars($maximo) ?>" max="<?= htmlspecialchars($hoy) ?>" onchange="LoadEntradas();"
                                aria-label="AL" aria-describedby="basic-addon2">

                            <input type="date" name="fechaFin" id="fechaFin" value="<?= htmlspecialchars($fechaDefaultFinal) ?>" style="max-width: 400px; min-width: 150px;"
                                class="form-control bg-light border-0 small mr-2" min="<?= htmlspecialchars($maximo) ?>" max="<?= htmlspecialchars($hoy) ?>" onchange="LoadEntradas();"
                                aria-label="AL" aria-describedby="basic-addon2">

                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm btn-danger mr-2" onclick="limpiarFiltros()" title="Limpiar filtros">
                                    <i class="fas fa-eraser"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $Contratista;?></span>
                                <img class="img-profile rounded-circle"
                                    src="../../img/undraw_profile.svg">
                            </a>
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
                    <h1 class="h3 mb-2 text-gray-800">ENTRADAS DE MATERIAL</h1>

                    <!-- DataTales Example -->
                    <div id="LoadEntradas">

                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- FOOTER -->
            <?php include_once __DIR__ . '/../../components/FooterContratistas.php'; ?>

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

    <!-- IMPORTAR SCRIPTS -->
    <?php include_once __DIR__ . '/../../components/ScriptContratistas.php'; ?>
    <!-- LA TABLA DE ENTRADAS -->
     <script src="../../js/entradas.js"></script>
    <script>
        $(document).ready(function(){
            LoadEntradas();
        });
    </script>

</body>

</html>