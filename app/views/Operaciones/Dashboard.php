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

<!-- INSERTAR EL CSS -->
<?php  $css = ['/../../css/DashboardOperaciones.css'] ?>
<!-- IMPORTAR EL HEAD -->
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
                            
                            <input type="date" name="Fecha1" id="Fecha1" value="<?= htmlspecialchars($defaultFecha1) ?>" class="form-control bg-light border-0 small"
                                aria-label="AL" aria-describedby="basic-addon2">

                            <input type="date" name="Fecha2" id="Fecha2" value="<?= htmlspecialchars($defaultFecha2) ?>" class="form-control bg-light border-0 small"
                                aria-label="AL" aria-describedby="basic-addon2">

                            <div class="input-group-append">

                                <button id="btnLoadKPIS" class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>

                            </div>
                        
                        </div>

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

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- DataTales Example -->
                    <div id="LoadDatosOps">

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
    <!-- Page level custom scripts -->
    <script src="js/app.js"></script>
    <script>
        $(document).ready(function(){
            // Trigger KPIS load when user clicks the search button (no longer on date change)
            $('#btnLoadKPIS').on('click', function(e){
                e.preventDefault();
                LoadDatosOps();
            });

            // Initial load on page ready (remove this line if you want empty state until user clicks)
            LoadDatosOps();
        });
    </script>

</body>

</html>