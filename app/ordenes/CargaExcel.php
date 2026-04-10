<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');
    session_start();

    $Contratista     = $_SESSION['Contratista'] ?? null;
    $idContratistas  = $_SESSION['idContratistas'] ?? null;

    if (empty($Contratista)) {
        header("location: ../Login/modal/poweroff.php");
        exit;
    }

    require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassOps.php");
    $modal = new Ops();

    /* =====================================================
       CONTROL DE SEMANA
    ===================================================== */

    $semanaSeleccionada = null;
    $totalOrdenes       = 0;
    $totalCoordiapp     = 0;
    $totalRechazadas    = 0;

    if (isset($_GET['semana']) && $_GET['semana'] != '') {
        // Si el usuario selecciona semana, usamos esa
        $semanaSeleccionada = (int)$_GET['semana'];
    } else {
        // Si no selecciona semana, obtenemos la semana de la última Fecha_Carga
        $semanaSeleccionada = $modal->ObtenerSemanaPorUltimaCarga($idContratistas);
    }

    if ($semanaSeleccionada !== null) {
        $totalOrdenes   = $modal->CountOrdenesProduccion($idContratistas, $semanaSeleccionada);
        $totalCoordiapp = $modal->ConteoOrdenesCoordiapp($idContratistas, $semanaSeleccionada);
        $totalRechazadas= $modal->ConteoOrdenesRechazadas($idContratistas, $semanaSeleccionada);
    } else {
        $totalOrdenes   = 0;
        $totalCoordiapp = 0;
        $totalRechazadas= 0;
    }
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>ERP - OPERACIONES</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <script src="https://kit.fontawesome.com/b5137b0dd6.js" crossorigin="anonymous"></script>

    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

</head>

<body id="page-top">

<div id="wrapper">

    <!-- SIDEBAR -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-text mx-3">
                OPERACIONES
            </div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item active">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>TABLERO DE INDICADORES</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">
            ACCION
        </div>

        <li class="nav-item">

            <a class="nav-link collapsed"
               href="#"
               data-toggle="collapse"
               data-target="#collapsePages">

                <i class="fas fa-fw fa-folder"></i>
                <span>MODULOS</span>

            </a>

            <div id="collapsePages"
                 class="collapse"
                 data-parent="#accordionSidebar">

                <div class="bg-white py-2 collapse-inner rounded">

                    <h6 class="collapse-header">
                        MODULOS
                    </h6>

                    <a class='collapse-item' href='../almacen/index.php'>
                        <i class="fa-solid fa-box-open"></i> ALMACEN
                    </a>

                    <a class='collapse-item' href='index.php'>
                        <i class="fa-solid fa-school-circle-check"></i> OPERACIONES
                    </a>

                </div>

            </div>

        </li>

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
                <a class="nav-link" href="Produccion.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>PRODUCCION CARGADA</span>
                </a>
            </li>

                    <li class="nav-item">
                <a class="nav-link" href="OrdenesNoCargadasCoordiapp.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>ORDENES NO CARGADAS COORDIAPP</span>
                </a>
            </li>

        <?php if ($Contratista == 'USER'): ?>

            <li class="nav-item">
                <a class="nav-link" href="Prenomina.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>PRENOMINA</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="NominaContratista.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>NOMINA CONTRATISTA</span>
                </a>
            </li>

        <?php endif; ?>

    </ul>

    <div id="content-wrapper" class="d-flex flex-column">

        <div id="content">

            <!-- TOPBAR -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-12 static-top shadow">

                <ul class="navbar-nav ml-auto">

                    <li class="nav-item dropdown no-arrow">

                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">

                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php echo $Contratista; ?>
                            </span>

                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">

                        </a>

                    </li>

                </ul>

            </nav>

            <!-- CONTENIDO -->

            <div class="container-fluid" style="margin-top:40px;">

                <h1 class="h3 mb-4 text-gray-800">
                    Cargar Producción desde Excel
                </h1>

                <form action="import_excel.php" method="post" enctype="multipart/form-data">

                    <div class="form-group d-flex align-items-center" style="gap:20px;">

                        <div>
                            <label>Selecciona el archivo Excel</label>
                            <input type="file" name="excelFile" class="form-control-file" required>
                        </div>

                        <div>
                            <label>Semanas</label>

                           


                                    <select name="semana"
        class="form-control"
        style="width:250px;">


                                <option value="">Seleccionar Semana</option>

                                <?php
                                    for ($i = 1; $i <= 52; $i++) {

                                        $selected = ($semanaSeleccionada == $i) ? 'selected' : '';

                                        echo "<option value='$i' $selected>$i</option>";
                                    }
                                ?>

                            </select>
                        </div>

                        <div>
                            <label>Año</label>

                            <input type="text"
                                   name="anio"
                                   class="form-control"
                                   style="width:250px;"
                                   value="<?php echo date('Y'); ?>"
                                   readonly>
                        </div>

                    </div>

                    <button type="submit"
                            name="submit"
                            class="btn btn-success">

                        <i class="fas fa-file-excel"></i>
                        Subir e Importar

                    </button>

                </form>

                <h2 style="margin-top:30px;font-weight:600;">
                    ANALISIS ORDENES DE SERVICIO REPORTE PRODUCCION
                </h2>

                <div class="row" style="margin-top:20px;">

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">

                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    ÓRDENES DE PRODUCCIÓN
                                </div>

                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $totalOrdenes; ?>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">

                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    ÓRDENES CON REGISTRO EN COORDIAPP
                                </div>

                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $totalCoordiapp; ?>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">

                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    ÓRDENES SIN PROCESO A PAGO
                                </div>

                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo $totalRechazadas; ?>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="container-fluid">

                    <h1 class="h3 mb-2 text-gray-800">
                        DETALLE ORDENES SIN REGISTRO EN COORDIAPP
                        <?php if($semanaSeleccionada){ echo " - SEMANA ".$semanaSeleccionada; } ?>
                    </h1>

                    <div id="LoadOrdenesRechazadas"></div>

                </div>

            </div>

        </div>

    </div>

</div>


<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>

<script src="js/apprechazadas.js"></script>

<script>

$(document).ready(function(){

    const urlParams = new URLSearchParams(window.location.search);
    const semana    = urlParams.get('semana'); // puede venir vacío ""
    const anio      = urlParams.get('anio');

    console.log("Semana URL:", semana);
    console.log("Año URL:", anio);

    // 🔥 Ejecutar SIEMPRE aunque semana venga vacía
    if(anio){
        LoadOrdenesRechazadas(semana, anio);
        sessionStorage.setItem("resultadosMostrados","1");
    }

});

window.addEventListener("pageshow", function(){

    const resultados = sessionStorage.getItem("resultadosMostrados");

    if(resultados){

        const url = new URL(window.location.href);

        // Solo limpia si existe el parámetro semana (aunque esté vacío)
        if(url.searchParams.has("semana")){

            sessionStorage.removeItem("resultadosMostrados");

            url.searchParams.delete("semana");

            window.history.replaceState({}, document.title, url.pathname);

        }

    }

});

</script>

</body>
</html>
