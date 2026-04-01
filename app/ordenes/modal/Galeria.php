<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');

    require_once("../controller/ClassOps.php");

    // Inicio de sesión y obtención de variables de sesión
    session_start();
    $Contratista = $_SESSION['Contratista'] ?? null;
    $idContratistas = $_SESSION['idContratistas'] ?? null;
    $Folio_Pisa = $_GET['Folio_Pisa'] ?? '';

    if (empty($idContratistas)) {
        header("location: ../../Login/modal/poweroff.php");
        exit;
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
    $Foto_INE = '';

    // Variables para coordenadas del recorrido
    $Latitud = '';
    $Longitud = '';
    $Latitud_Terminal = '';
    $Longitud_Terminal = '';

    foreach ($Fotos as $row) {
        // Normalizar rutas removiendo prefijos './' o '.\' si existen
        $Foto_Ont = !empty($row['Foto_Ont']) ? ltrim($row['Foto_Ont'], "./\\") : '';
        $Foto_Casa_Cliente = !empty($row['Foto_Casa_Cliente']) ? ltrim($row['Foto_Casa_Cliente'], "./\\") : '';
        $No_Serie_ONT = !empty($row['No_Serie_ONT']) ? ltrim($row['No_Serie_ONT'], "./\\") : '';
        $Foto_Puerto = !empty($row['Foto_Puerto']) ? ltrim($row['Foto_Puerto'], "./\\") : '';
        $Foto_INE = !empty($row['Foto_INE']) ? ltrim($row['Foto_INE'], "./\\") : '';
        
        // Obtener coordenadas para el mapa
        $Latitud = !empty($row['Latitud']) ? $row['Latitud'] : '';
        $Longitud = !empty($row['Longitud']) ? $row['Longitud'] : '';
        $Latitud_Terminal = !empty($row['Latitud_Terminal']) ? $row['Latitud_Terminal'] : '';
        $Longitud_Terminal = !empty($row['Longitud_Terminal']) ? $row['Longitud_Terminal'] : '';
    }

    // Verificar si tenemos coordenadas válidas para mostrar el mapa
    $has_coordinates = !empty($Latitud) && !empty($Longitud);
    $has_terminal_coordinates = !empty($Latitud_Terminal) && !empty($Longitud_Terminal);

    function url_exists($url) {
        if (empty($url)) {
            return false;
        }
        $headers = @get_headers($url);
        return is_array($headers) && strpos($headers[0], '200') !== false;
    }

    $ruta = "https://vps.ed-intra.com/API/";
    $ruta2 = "https://api.ed-intra.com/";

    // Buscar primera URL disponible entre rutas (prioriza ruta2)
    $url_foto_ont = null;
    if (!empty($Foto_Ont)) {
        $c1 = $ruta2 . $Foto_Ont;
        $c2 = $ruta . $Foto_Ont;
        if (url_exists($c1)) {
            $url_foto_ont = $c1;
        } elseif (url_exists($c2)) {
            $url_foto_ont = $c2;
        }
    }

    $url_foto_casa_cliente = null;
    if (!empty($Foto_Casa_Cliente)) {
        $c1 = $ruta2 . $Foto_Casa_Cliente;
        $c2 = $ruta . $Foto_Casa_Cliente;
        if (url_exists($c1)) {
            $url_foto_casa_cliente = $c1;
        } elseif (url_exists($c2)) {
            $url_foto_casa_cliente = $c2;
        }
    }

    $url_no_serie_ont = null;
    if (!empty($No_Serie_ONT)) {
        $c1 = $ruta2 . $No_Serie_ONT;
        $c2 = $ruta . $No_Serie_ONT;
        if (url_exists($c1)) {
            $url_no_serie_ont = $c1;
        } elseif (url_exists($c2)) {
            $url_no_serie_ont = $c2;
        }
    }

    $url_foto_puerto = null;
    if (!empty($Foto_Puerto)) {
        $c1 = $ruta2 . $Foto_Puerto;
        $c2 = $ruta . $Foto_Puerto;
        if (url_exists($c1)) {
            $url_foto_puerto = $c1;
        } elseif (url_exists($c2)) {
            $url_foto_puerto = $c2;
        }
    }

    $url_foto_ine = null;
    if (!empty($Foto_INE)) {
        $c1 = $ruta2 . $Foto_INE;
        $c2 = $ruta . $Foto_INE;
        if (url_exists($c1)) {
            $url_foto_ine = $c1;
        } elseif (url_exists($c2)) {
            $url_foto_ine = $c2;
        }
    }

    $exists_foto_ont = !empty($Foto_Ont) && $url_foto_ont !== null;
    $exists_no_serie = !empty($No_Serie_ONT) && $url_no_serie_ont !== null;
    $exists_ine = !empty($Foto_INE) && $url_foto_ine !== null;
    $exists_casa = !empty($Foto_Casa_Cliente) && $url_foto_casa_cliente !== null;
    $exists_puerto = !empty($Foto_Puerto) && $url_foto_puerto !== null;

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

        /* Mejor ajuste para imágenes */
        .foto-card img {
            width: 100%;
            max-height: 420px;
            object-fit: contain;
            background: #000;
        }

        /* Evita leyenda superpuesta (marca de agua) */
        .carousel-caption {
            display: none;
        }

        /* Leyendas visibles debajo de la imagen */
        .foto-card .caption-title {
            font-weight: 700;
            color: #222;
            margin-top: 0.75rem;
            text-align: center;
        }
        .foto-card .caption-sub {
            font-size: 0.9rem;
            color: #666;
            text-align: center;
            margin-bottom: 0.75rem;
        }

        /* Alertas ocupan todo ancho del card */
        .foto-card .alert {
            margin-top: 0.75rem;
        }

        /* Estilos para el mini mapa */
        .map-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            overflow: hidden;
        }
        
        #miniMap {
            width: 100%;
            height: 420px;
            background: #e9ecef;
        }
        
        .map-legend {
            padding: 10px 15px;
            background: #f8f9fc;
            border-top: 1px solid #e3e6f0;
        }
        
        .map-legend .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            font-size: 0.85rem;
        }
        
        .map-legend .legend-item:last-child {
            margin-bottom: 0;
        }
        
        .legend-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            display: inline-block;
        }
        
        .legend-marker.cliente {
            background: #4e73df;
            border: 2px solid #2e59d9;
        }
        
        .legend-marker.terminal {
            background: #1cc88a;
            border: 2px solid #17a673;
        }
        
        .legend-line {
            width: 20px;
            height: 3px;
            background: #e74a3b;
            margin-right: 8px;
            display: inline-block;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../index.php">
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
                        <?php 
                            if(!empty($modulos)){
                                foreach($modulos as $mod){
                                    echo "<a class='collapse-item' href='".$mod['ruta_modulo']."'>".$mod['icon_fav']." ".$mod['nombre_modulo']."</a>";
                                }
                            }else{  
                                echo "<a class='collapse-item'>NO TIENE MODULO ASIGNADOS</a>";
                            }
                        ?>
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

            <li class="nav-item">
                <a class="nav-link" href="../OrdenesTac.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>LIQUIDACION TAC</span>
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
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($Contratista, ENT_QUOTES, 'UTF-8');?></span>
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
                    <h1 class="h3 mb-2 text-gray-800">FOTOGRAFIAS ORDEN: <?php echo htmlspecialchars($Folio_Pisa, ENT_QUOTES, 'UTF-8'); ?></h1>

                    <!-- Fotogalería -->
                    <div class="row">

                        <div class="col-xl-4 col-md-6 mb-4 foto-card">
                            <?php if ($exists_foto_ont): ?>
                                <a href="<?php echo htmlspecialchars($url_foto_ont, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo htmlspecialchars($url_foto_ont, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="Foto ONT"
                                         onerror="this.onerror=null; this.src='../img/no-image.png';">
                                </a>
                                <!-- debug-url: <?php echo htmlspecialchars($url_foto_ont, ENT_QUOTES, 'UTF-8'); ?> -->
                            <?php else: ?>
                                <div class="alert alert-warning" role="alert">
                                    No existe fotografía ONT.
                                </div>
                            <?php endif; ?>

                            <div class="caption-title">Fotografía ONT</div>
                            <div class="caption-sub">Parte frontal de la ONT</div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4 foto-card">
                            <?php if ($exists_no_serie): ?>
                                <a href="<?php echo htmlspecialchars($url_no_serie_ont, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo htmlspecialchars($url_no_serie_ont, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="Foto No. Serie ONT"
                                         onerror="this.onerror=null; this.src='../img/no-image.png';">
                                </a>
                                <!-- debug-url: <?php echo htmlspecialchars($url_no_serie_ont, ENT_QUOTES, 'UTF-8'); ?> -->
                            <?php else: ?>
                                <div class="alert alert-warning" role="alert">
                                    No existe fotografía (No. Serie ONT).
                                </div>
                            <?php endif; ?>

                            <div class="caption-title">Fotografía No. Serie ONT</div>
                            <div class="caption-sub">Parte trasera/serie de la ONT</div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4 foto-card">
                            <?php if ($exists_ine): ?>
                                <a href="<?php echo htmlspecialchars($url_foto_ine, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo htmlspecialchars($url_foto_ine, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="Foto INE"
                                         onerror="this.onerror=null; this.src='../img/no-image.png';">
                                </a>
                                <!-- debug-url: <?php echo htmlspecialchars($url_foto_ine, ENT_QUOTES, 'UTF-8'); ?> -->
                            <?php else: ?>
                                <div class="alert alert-warning" role="alert">
                                    No existe fotografía INE.
                                </div>
                            <?php endif; ?>

                            <div class="caption-title">Fotografía Documento</div>
                            <div class="caption-sub">Hoja de servicio / INE</div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-xl-4 col-md-6 mb-4 foto-card">
                            <?php if ($exists_casa): ?>
                                <a href="<?php echo htmlspecialchars($url_foto_casa_cliente, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo htmlspecialchars($url_foto_casa_cliente, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="Foto Casa Cliente"
                                         onerror="this.onerror=null; this.src='../img/no-image.png';">
                                </a>
                                <!-- debug-url: <?php echo htmlspecialchars($url_foto_casa_cliente, ENT_QUOTES, 'UTF-8'); ?> -->
                            <?php else: ?>
                                <div class="alert alert-warning" role="alert">
                                    No existe fotografía Casa Cliente.
                                </div>
                            <?php endif; ?>

                            <div class="caption-title">Fotografía Casa Cliente</div>
                            <div class="caption-sub">Exterior/ubicación del domicilio</div>
                        </div>

                        <div class="col-xl-4 col-md-6 mb-4 foto-card">
                            <?php if ($exists_puerto): ?>
                                <a href="<?php echo htmlspecialchars($url_foto_puerto, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo htmlspecialchars($url_foto_puerto, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="Foto Puerto"
                                         onerror="this.onerror=null; this.src='../img/no-image.png';">
                                </a>
                                <!-- debug-url: <?php echo htmlspecialchars($url_foto_puerto, ENT_QUOTES, 'UTF-8'); ?> -->
                            <?php else: ?>
                                <div class="alert alert-warning" role="alert">
                                    No existe fotografía Terminal/Puerto.
                                </div>
                            <?php endif; ?>

                            <div class="caption-title">Fotografía Terminal</div>
                            <div class="caption-sub">Terminal / Puerto utilizado</div>
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

    <?php if ($has_coordinates): ?>
    <?php endif; ?>

</body>

</html>