<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');
    
    require_once("controller/ClassEntradas.php");
    require_once(__DIR__ . '/../config/ContratistaContexto.php');
    require_once("modal/cargar.php");

    session_start();
    $Contratista = $_SESSION['Contratista'];
    requerirSesionAlmacen();
    $idContratistas = obtenerIdContratistaContexto();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">

    <!-- ESTILOS BASE DE LA PLANTILLA -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- ESTILOS PARA TABLAS Y RESPONSIVIDAD -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../css/responsive_custom.css" rel="stylesheet">

    <!-- ESTILO PERSONALIZADO PARA EL MENU ACTIVO DE LA SIDEBAR -->
    <link rel="stylesheet" href="css/sidebar-active.css">

    <style>
        .modal-header-gradient {
            background: linear-gradient(90deg, #0f172a 0%, #003d99 100%) !important;
        }
    </style>

</head>

<body id="page-top">

    <!-- CONTENEDOR DE LA PAGINA -->
    <div id="wrapper">

        <!-- SIDEBAR IMPORTADA DESDE COMPONENTES -->
        <?php  include_once(__DIR__ . '/../components/TopbarContratistas.php'); ?>
        <?php include_once(__DIR__ . '/../components/SidebarAlmacen.php'); ?>

        <!-- CONTENEDOR -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- CONTENIDO PRINCIPAL -->
            <div id="content">

                <!-- TOPBAR -->
          
               
               
               

                <!-- CONTENIDO DE LA PAGINA -->
                <div class="container-fluid">

                    <!-- CONTENEDOR DE DATOS OPTIMIZADO -->
                    <div class="card shadow mb-4 table-container">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h2 class="m-0 font-weight-bold text-gray-800">ENTRADAS DE MATERIAL <i class="fa-solid fa-clipboard-check"></i></h2>
                            <?php $selectorCallback = 'LoadEntradas'; include_once(__DIR__ . '/../components/SelectorContratista.php'); ?>
                        </div>
                        <div class="card-body p-0">
                            <!-- CONTENEDOR DE LA TABLA -->
                            <div id="LoadEntradas" class="p-3">
                                <!-- AQUI SE CARGAN LOS DATOS VIA AJAX -->
                            </div>
                        </div> 
                    </div>

                </div>

            </div>
            <!-- FIN DEL CONTENIDO PRINCIPAL -->

            <!-- IMPORTAR EL FOOTER DE CONTRATISTAS -->
            <?php include_once(__DIR__ . '/../components/FooterContratistas.php'); ?>

        </div>
        <!-- FIN DEL CONTENEDOR -->

    </div>
    <!-- FIN DEL CONTENEDOR DE LA PAGINA -->

    <!-- BOTON DE DESPLAZAMIENTO HACIA ARRIBA -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    

    <!-- MODAL DE CIERRE DE SESION -->
    <?php include_once(__DIR__ . '/../components/ModalCierreSesion.php'); ?>

    
    <!-- SCRIPTS DE CONTRATISTAS -->
    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>
</body>
</html>

