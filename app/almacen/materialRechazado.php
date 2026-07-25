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
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ERP - ALMACEN</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/responsive_custom.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-text mx-3">ALMACEN</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
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

            <!-- MATERIAL RECHAZADO -->
             <li class="nav-item active">
                <a class="nav-link" href="materialRechazado.php">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>MATERIAL RECHAZADO</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="salidas.php">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>ASIGNAR MATERIAL</span>
                </a>
            </li>
            
            <!--
            <li class="nav-item">
                <a class="nav-link" href="traspasos.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>TRASPASO MATERIAL</span>
                </a>
            </li>
            -->

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="sidebar-footer text-center">
                <button class="rounded-circle border-0" id="sidebarToggle" aria-label="Cerrar menú">
                    <i class="fas fa-times"></i>
                </button>
            </div>


        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include_once(__DIR__ . '/../components/TopbarContratistas.php'); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">MATERIAL RECHAZADO</h1>

                    <!-- DataTales Example -->
                    <div id="LoadEntradas">

                    </div>

                </div>
                <!-- /.container-fluid -->

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

<?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>
    <script>
        $(document).ready(function(){
            LoadEntradas();
        });
    </script>

</body>
</html>


