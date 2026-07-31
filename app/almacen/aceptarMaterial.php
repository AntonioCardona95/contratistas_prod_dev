<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');

    require_once("controller/ClassOps.php");

    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];

    if (empty($idContratistas)) {
        header("location: ../Login/modal/poweroff.php");
    }

    // FUNCION JS QUE EL TOPBAR VA A LLAMAR AL CAMBIAR FECHAS O BUSCAR
    $topbar_onchange = 'LoadPendientes';
?>
<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CONTRATISTAS</title>

    <!-- CARGA DE ICONOS Y FUENTES GENERALES -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- ESTILOS BASE DEL PORTAL -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- ESTILOS PARA TABLAS Y COMPONENTES DINÁMICOS -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../css/responsive_custom.css" rel="stylesheet">

    <!-- ESTILO PERSONALIZADO PARA EL ITEM ACTIVO DE LA SIDEBAR -->
    <link rel="stylesheet" href="css/sidebar-active.css">

    <style>
        .modal-header-gradient 
        {
            background: linear-gradient(90deg, #0f172a 0%, #003d99 100%) !important;
        }
        .badge-pendiente { 
            background-color: #ffc107; color: #212529; 
        }
    </style>

</head>

<body id="page-top">

    <!-- CONTENEDOR PRINCIPAL DE LA PÁGINA -->
    <div id="wrapper">

        <!-- SIDEBAR DEL MÓDULO DE ALMACÉN -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #007BFF; background-image: linear-gradient(180deg, #0f172a 100%, #224abe 0%); background-size: cover;">

            <!-- ENCABEZADO DEL MODULO -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-text mx-3">ALMACEN</div>
            </a>

            <!-- SEPARADOR SUPERIOR -->
            <hr class="sidebar-divider my-0">

            <!-- OPCIÓN DE DASHBOARD -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- SEPARADOR ENTRE SECCIONES -->
            <hr class="sidebar-divider">

            <!-- TÍTULO DE LA SECCIÓN DE ACCIONES -->
            <div class="sidebar-heading">ACCION</div>

            <!-- MENÚ DE MÓDULOS -->
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

            <!-- MÓDULO: MATERIAL RECIBIDO -->
            <li class="nav-item">
                <a class="nav-link" href="entradas.php">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>MATERIAL RECIBIDO</span>
                </a>
            </li>

            <!-- MÓDULO ACTIVO: ACEPTAR MATERIAL -->
            <li class="nav-item active">
                <a class="nav-link active" href="aceptarMaterial.php">
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

            <!-- MÓDULO: ASIGNAR MATERIAL -->
            <li class="nav-item">
                <a class="nav-link" href="salidas.php">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>ASIGNAR MATERIAL</span>
                </a>
            </li>

            <!-- MÓDULO: TÉCNICOS -->
            <li class="nav-item">
                <a class="nav-link" href="tecnicos.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>TÉCNICOS</span>
                </a>
            </li>

            <!-- SEPARADOR FINAL -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- BOTÓN DE CIERRE DEL MENÚ -->
            <div class="sidebar-footer text-center">
                <button class="rounded-circle border-0" id="sidebarToggle" aria-label="Cerrar menú">
                    <i class="fas fa-times"></i>
                </button>
            </div>

        </ul>
        <!-- FIN DE LA SIDEBAR -->

        <!-- CONTENEDOR DEL ÁREA DE CONTENIDO -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- BLOQUE PRINCIPAL DEL CONTENIDO -->
            <div id="content">

                <!-- TOPBAR GLOBAL DEL PORTAL -->
                <?php include_once(__DIR__ . '/../components/TopbarContratistas.php'); ?>

                <div class="container-fluid">

                    <!-- TARJETA PRINCIPAL DEL LISTADO DE PENDIENTES -->
                    <div class="card shadow mb-4 table-container">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h2 class="m-0 font-weight-bold text-gray-800"> MATERIAL PENDIENTE DE ACEPTAR <i class="fa-solid fa-feather-pointed"></i></h2>
                        </div>
                        <div class="card-body p-0">
                            <!-- CONTENEDOR DONDE SE CARGA LA TABLA MEDIANTE AJAX -->
                            <div id="LoadPendientes" class="p-3">
                                <!-- Aquí se renderiza el contenido dinámico -->
                            </div>
                        </div> 
                    </div>

                </div>

            </div>

            <!-- PIE DE PÁGINA DEL PORTAL -->
            <?php include_once(__DIR__ . '/../components/FooterContratistas.php'); ?>

        </div>

    </div>

    <!-- BOTÓN PARA VOLVER AL INICIO -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- MODAL DE CIERRE DE SESIÓN -->
    <?php include_once(__DIR__ . '/../components/ModalCierreSesion.php'); ?>

    <!-- SCRIPTS GENERALES DEL PORTAL -->
    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>

    <script>
        $(document).ready(function(){
            LoadPendientes();
        });

        function LoadPendientes(){
            var parametros = $("#FormQueryUnidades").serialize();
            var urlParams = new URLSearchParams(window.location.search);
            var folioBuscar = urlParams.get('folio');
            if (folioBuscar) {
                var folioNumerico = folioBuscar.replace(/\D/g, '');
                if (folioNumerico.length) {
                    parametros += (parametros.length ? '&' : '') + 'folio=' + encodeURIComponent(folioNumerico);
                }
            }

            $.ajax({
                type: 'POST',
                url: 'modal/TablaPendientes.php',
                data: parametros,
                beforeSend: function(){
                    $("#LoadPendientes").html("<center><img src='../image/loading-46.gif'></center>");
                },
                success: function(data){
                    $("#LoadPendientes").html(data);
                }
            });
        }
    </script>

</body>
</html>