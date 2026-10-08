<?php
    // Configuración inicial
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set('America/Monterrey');

    // Sesión
    session_start();

    // Dependencias
    require_once("controller/ClassOps.php");
    require_once(__DIR__ . '/../config/ContratistaContexto.php');
    require_once(__DIR__ . "/controller/ClassConsultasProduccion.php");

    // Instancias
    $objProduccion = new ClassConsultasProduccion();
    $listaContratistas = $objProduccion->obtenerTodosContratistas();
    $listaAreas = $objProduccion->obtenerTodasAreas();



    // Validación de sesión y contexto
    $Contratista = $_SESSION['Contratista'] ?? null;
    requerirSesionAlmacen();
    $idContratistas = obtenerIdContratistaContexto();

    // Parámetros de entrada
    $Fecha1 = $_POST['Fecha1'] ?? '';
    $Fecha2 = $_POST['Fecha2'] ?? '';
    $mes_anterior = date('Y-m-d', strtotime('-3 month'));
    //$tbTipoTarea = $_POST['TipoTarea'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">    
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CONTRATISTAS - ALMACEN</title>

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

    <!-- CONTENEDOR PRINCIPAL DE LA PÁGINA -->
    <div id="wrapper">

        <!-- SIDEBAR IMPORTADA DESDE COMPONENTES -->
        <?php include_once(__DIR__ . '/../components/SidebarAlmacen.php'); ?>
        <!-- FIN DE LA SIDEBAR -->

        <!-- CONTENEDOR DEL ÁREA DE CONTENIDO -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- CONTENIDO PRINCIPAL -->
            <div id="content">

                <!-- TOPBAR CON FILTROS Y ACCIONES GLOBALES -->
                <?php
                    
                    include_once(__DIR__ . '/../components/TopBarControl.php');
                ?>

                <!-- INICIO DEL CONTENIDO DE LA PÁGINA -->
                <div class="container-fluid">

                    <!-- ENCABEZADO DE LA SECCIÓN -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">PRODUCCION GENERAL</h1>
                    </div>

<div class="card shadow mb-4">
    <div class="card-body py-3">
        <div class="row align-items-center">

            <!-- FECHA INICIAL -->
            <div class="col-md-3 col-lg-3 mb-2 mb-md-0">
                <label for="fechascard1" class="font-weight-bold text-primary mb-1">
                    <i class="fas fa-calendar-alt mr-1"></i> Fecha Inicial:
                </label>
                <input type="date" class="form-control" id="fechascard1" name="fechascard1" onchange="LoadKpisControl();">
            </div>

            <!-- FECHA FINAL -->
            <div class="col-md-3 col-lg-3 mb-2 mb-md-0">
                <label for="fechascard2" class="font-weight-bold text-primary mb-1">
                    <i class="fas fa-calendar-alt mr-1"></i> Fecha Final:
                </label>
                <input type="date" class="form-control" id="fechascard2" name="fechascard2" onchange="LoadKpisControl();">
            </div>

            <!-- CONTRATISTA -->
            <div class="col-md-3 col-lg-3">
                <label for="selectContratista" class="font-weight-bold text-primary mb-1">
                    <i class="fas fa-user-tie mr-1"></i> Seleccionar Contratista:
                </label>
                <select class="form-control select2-busqueda" id="selectContratista" name="selectContratista" data-placeholder="-- Seleccionar Contratista --" onchange="LimpiarSelectoresUbicacion(); LoadKpisControl();">
                    <option value=""></option>
                    <?php foreach ($listaContratistas as $row): ?>
                        <option value="<?= htmlspecialchars($row['idContratistas']) ?>">
                            <?= htmlspecialchars($row['nombre_completo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

          <!-- Option Select de Tipo de Tarea -->
<div class="col-md-3 col-lg-3">
    <label for="TipoTarea" class="font-weight-bold text-primary mb-1">
        <i class="fas fa-tasks mr-1"></i> Seleccione Tipo de tarea:
    </label>
    <select name="TipoTarea" id="TipoTarea" class="form-control select2-busqueda" onchange="LoadKpisControl();">
        <option value="">-- Seleccionar Tipo de Tareas --</option>
        <option value="QUEJAS">QUEJAS</option>
        <option value="ALTAS">ALTAS</option>
        <option value="MIGRACIONES">MIGRACIONES</option>
        <option value="A4">A4</option>
        <option value="CAMBIOS DE DOMICILIO">CAMBIOS DE DOMICILIO</option>
    </select>
</div>

            <!-- ÁREA -->
            <div class="col-md-3 col-lg-3">
                <label for="selectArea" class="font-weight-bold text-primary mb-1">
                    <i class="fas fa-building mr-1"></i> Seleccionar Área:
                </label>
                <select class="form-control select2-busqueda" id="selectArea" name="selectArea" data-placeholder="-- Seleccionar Área --" onchange="CargarDependenciasArea(); LoadKpisControl();">
                    <option value=""></option>
                    <?php foreach ($listaAreas as $row): ?>
                        <option value="<?= htmlspecialchars($row['idAreas']) ?>">
                            <?= htmlspecialchars($row['area']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- DIVISIÓN -->
            <div class="col-md-3 col-lg-3">
                <label for="selectDivision" class="font-weight-bold text-primary mb-1">
                    <i class="fas fa-sitemap mr-1"></i> Seleccionar División:
                </label>
                <select class="form-control select2-busqueda" id="selectDivision" name="selectDivision" data-placeholder="-- Seleccionar División --" onchange="LoadKpisControl();" disabled>
                    <option value=""></option>
                </select>
            </div>

            <!-- COPE -->
            <div class="col-md-3 col-lg-3">
                <label for="selectCope" class="font-weight-bold text-primary mb-1">
                    <i class="fas fa-network-wired mr-1"></i> Seleccionar Cope:
                </label>
                <select class="form-control select2-busqueda" id="selectCope" name="selectCope" data-placeholder="-- Seleccionar Cope --" onchange="LoadKpisControl();" disabled>
                    <option value=""></option>
                </select>
            </div>

        </div>
    </div>
</div>

                    
                    <!-- CONTENEDOR DINÁMICO QUE CARGA LOS KPI Y GRÁFICAS -->
                    <div id="LoadKpisControl">

                    </div>

                </div>
                <!-- FIN DEL CONTENIDO -->

            </div>
            <!-- FIN DEL CONTENIDO PRINCIPAL -->

            <!-- PIE DE PÁGINA -->
            <?php include_once(__DIR__ . '/../components/FooterContratistas.php'); ?>
            <!-- FIN DEL PIE -->

        </div>
        <!-- FIN DEL CONTENEDOR DEL CONTENIDO -->

    </div>
    <!-- FIN DEL CONTENEDOR PRINCIPAL -->

    <!-- BOTÓN PARA VOLVER AL INICIO -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- MODAL DE CIERRE DE SESION -->
    <?php include_once(__DIR__ . '/../components/ModalCierreSesion.php'); ?>

    <div class="modal fade" id="modalKpiDetalle" tabindex="-1" role="dialog" aria-labelledby="modalKpiDetalleTitulo" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient text-white">
                    <h5 class="modal-title" id="modalKpiDetalleTitulo">Detalle</h5>
                    <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalKpiDetalleContenido">
                </div>
            </div>
        </div>
    </div>

    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>
    <!-- CARGAR CHART.JS -->
    
    <script>
        $(document).ready(function(){
            LoadKpisControl();
        });

       
    </script>

    <!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.js"></script>


<script>
    $(document).ready(function(){
        // Inicializar todos los select2 con su propio data-placeholder
        $('.select2-busqueda').select2({
            theme: 'bootstrap4',
            allowClear: true,
            width: '100%'
        });

        LoadKpisControl();
    });
</script>
</body>
</html>

