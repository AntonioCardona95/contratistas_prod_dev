<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');

    require_once("../../Login/controller/Class_Login.php");
    require_once("../controller/ClassOps.php");

    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];
    $Folio_Pisa = $_GET['Folio_Pisa'];

    if (empty($Contratista)) {
        header("location: ../Login/modal/poweroff.php");
    }
	
    $modal = new Ops();
	$Fotos = $modal->GetFotos($Folio_Pisa);
    
    if (!is_array($Fotos)) {
        $Fotos = [];
    }

	$Foto_Ont = '';
	$Foto_Casa_Cliente = '';
	$No_Serie_ONT = '';
	$Foto_Puerto = '';
	
    foreach ($Fotos as $row) {
        $Foto_Ont = !empty($row['Foto_Ont']) ? substr($row['Foto_Ont'], 2) : '';
        $Foto_Casa_Cliente = !empty($row['Foto_Casa_Cliente']) ? substr($row['Foto_Casa_Cliente'], 2) : '';
        $No_Serie_ONT = !empty($row['No_Serie_ONT']) ? substr($row['No_Serie_ONT'], 2) : '';
        $Foto_Puerto = !empty($row['Foto_Puerto']) ? substr($row['Foto_Puerto'], 2) : '';
        $Foto_INE = !empty($row['Foto_INE']) ? substr($row['Foto_INE'], 2) : '';
    }
    
    function url_exists($url) {
        $headers = @get_headers($url);
        return is_array($headers) && strpos($headers[0], '200') !== false;
    }


    $ruta = "https://vps.ed-intra.com/API/";
    $ruta2 = "https://api.ed-intra.com/";

    $url_foto_ont = url_exists($ruta . $Foto_Ont) ? $ruta . $Foto_Ont : $ruta2 . $Foto_Ont;
    $url_foto_casa_cliente = url_exists($ruta . $Foto_Casa_Cliente) ? $ruta . $Foto_Casa_Cliente : $ruta2 . $Foto_Casa_Cliente;
    $url_no_serie_ont = url_exists($ruta . $No_Serie_ONT) ? $ruta . $No_Serie_ONT : $ruta2 . $No_Serie_ONT;
    $url_foto_puerto = url_exists($ruta . $Foto_Puerto) ? $ruta . $Foto_Puerto : $ruta2 . $Foto_Puerto;
    $url_foto_ine = url_exists($ruta . $Foto_INE) ? $ruta . $Foto_INE : $ruta2 . $Foto_INE;
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
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <script src="https://kit.fontawesome.com/b5137b0dd6.js" crossorigin="anonymous"></script>

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <script src="https://kit.fontawesome.com/b5137b0dd6.js" crossorigin="anonymous"></script>
	<style>
		.carousel-item{
			width: 500px;
			height: 600px;
			background-position: center center;
			background-repeat: no-repeat;
			background-color: #000;
			margin: auto;
		}

		.image-cover{
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
		}

		.image-contain{
			-webkit-background-size: contain;
			-moz-background-size: contain;
			-o-background-size: contain;
			background-size: contain;
		}

		.image-custom1{
			background-size: 100%;
		}

		@media (max-width: 600px) {
			.carousel-item {
				height: 200px;
			}
		}
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
                <a class="nav-link" href="../index.php">
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
                        <a class='collapse-item' href='../../almacen/index.php'><i class="fa-solid fa-box-open"></i> ALMACEN</a>
                        <a class='collapse-item' href='../index.php'><i class="fa-solid fa-school-circle-check"></i> OPERACIONES</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="../OrdenesCoordiapp.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION COORDIAPP COMPLETAS</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="../OrdenesIncompletas.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION COORDIAPP INCOMPLETAS</span>
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

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $Contratista;?></span>
                                <img class="img-profile rounded-circle"
                                    src="../img/undraw_profile.svg">
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
                    <h1 class="h3 mb-2 text-gray-800">FOTOGRAFIAS ORDEN: <?php echo $Folio_Pisa; ?></h1>

                    <!-- DataTales Example -->
					<div class="row">

						<div class="col-xl-4 col-md-6 mb-4">

                            <img class="d-flex justify-content-center w-100" src="<?php echo $url_foto_ont; ?>">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Fotografia ONT</h5>
                                <p>Una fotografia de la parte frontal de la ONT</p>
                            </div>
                        
                        </div>
                        
						<div class="col-xl-4 col-md-6 mb-4">

                            <img class="d-flex justify-content-center w-100" src="<?php echo $url_no_serie_ont; ?>">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Fotografia ONT</h5>
                                <p>Una fotografia de la parte trasera de la ONT</p>
                            </div>

                        </div>

                        <div class="col-xl-4 col-md-6 mb-4">

                            <img class="d-flex justify-content-center w-100" src="<?php echo $url_foto_ine; ?>">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Fotografia SO</h5>
                                <p>Una fotografia de la hoja de servicio</p>
                            </div>

                        </div>
                    
                    </div>
                    
                    <div class="row">
                        
						<div class="col-xl-4 col-md-6 mb-4">

                            <img class="d-flex justify-content-center w-100" src="<?php echo $url_foto_casa_cliente; ?>">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Fotografia Casa Cliente</h5>
                                <p>Una fotografia de la casa del cliente</p>
                            </div>
                        
                        </div>
                        
						<div class="col-xl-4 col-md-6 mb-4">

                            <img class="d-flex justify-content-center w-100" src="<?php echo $url_foto_puerto; ?>">
                            <div class="carousel-caption d-none d-md-block">
                                <h5>Fotografia Terminal</h5>
                                <p>Una fotografia de la terminal</p>
                            </div>
                        
                        </div>
                    
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
                    <a class="btn btn-primary" href="../../Login/modal/poweroff.php">SALIR</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

</body>

</html>