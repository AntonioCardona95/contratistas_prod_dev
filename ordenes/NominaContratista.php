<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Monterrey');
session_start();

$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

if (empty($Contratista)) {
    header("location: ../Login/modal/poweroff.php");
    exit;
}

require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassOps.php");
$modal = new Ops();


// consulta listado emuladores
$emuladores = $modal->ObtenerEmuladorContratista();
$tecemuladores = $modal->ObtenerTecnologiaEmuladorContratista(); 


// Cantidad de órdenes de producción del contratista logueado

//$totalCoordiapp = $modal->ConteoOrdenesCoordiapp($idContratistas);

?>
<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ERP - OPERACIONES</title>

    <!-- Custom fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <script src="https://kit.fontawesome.com/b5137b0dd6.js" crossorigin="anonymous"></script>

    <!-- Custom styles -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<style type="text/css">
    
    
    .contenedor-nomina {
    max-width: 95%;
    margin: 0 auto;
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
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item active">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>TABLERO DE INDICADORES</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">ACCION</div>

        <!-- Nav Item - Modules -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                aria-expanded="true" aria-controls="collapsePages">
                <i class="fas fa-fw fa-folder"></i>
                <span>MODULOS</span>
            </a>
            <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">MODULOS</h6>
                    <a class='collapse-item' href='../almacen/index.php'><i class="fa-solid fa-box-open"></i> ALMACEN</a>
                    <a class='collapse-item' href='index.php'><i class="fa-solid fa-school-circle-check"></i> OPERACIONES</a>
                    <!-- Módulo solo visible si el contratista es USER -->
            <?php if ($Contratista == 'USER'): ?>
                <a class='collapse-item' href='../ordenes/CargaExcel.php'>
                    <i class="fa-solid fa-microchip"></i> EMULADORES
                </a>
            <?php endif; ?>
                </div>
            </div>
        </li>

        <!-- Nav Item - Tables -->
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

        <?php if ($Contratista == 'USER'): ?>
    <!-- Nav Item - Tables -->
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

        <hr class="sidebar-divider d-none d-md-block">
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
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-12 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $Contratista;?></span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> PERFIL
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> CERRAR SESION
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

<!-- Page Content -->
<div class="container-fluid" style="margin-top: 40px;">

    <!-- Datos del contratista para JS -->
    <div id="infoContratista"
         data-nombre="<?= htmlspecialchars($Contratista) ?>"
         data-id="<?= htmlspecialchars($idContratistas) ?>">
    </div>

    <!-- Fila con los selects alineados a la izquierda -->
    <div class="row mt-3" style="margin-left: 10px;">

        <!-- Select de Emulador -->
        <div class="col-auto">
    <label for="emuladorSelect">Selecciona Emulador:</label>
    <select name="emulador" id="emuladorSelect" class="form-control" style="width:250px;">
        <option value="" disabled selected>Selecciona un emulador</option>
        <?php foreach ($emuladores as $emulador): ?>
            <option value="<?= htmlspecialchars($emulador['nombre']) ?>">
                <?= htmlspecialchars($emulador['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

        <!-- Select Tecnología -->
<div class="col-auto text-center">
    <label for="tecnologiaSelect">Selecciona Tecnología:</label>
    <select id="tecnologiaSelect" class="form-control" disabled>
        <option value="" disabled selected>Selecciona una tecnología</option>
        <?php foreach ($tecemuladores as $tec): ?>
            <option value="<?= htmlspecialchars($tec['tecnologia']) ?>">
                <?= htmlspecialchars($tec['tecnologia']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

        <!-- Select de Semana -->
        <div class="col-auto">
            <label for="semana">Semana:</label>
            <select name="semana" id="semana" class="form-control" style="width:250px;">
                <option value="">Seleccionar Semana</option>
                <?php
                $numSemanas = 52;
                for ($i = 1; $i <= $numSemanas; $i++) {
                    echo "<option value='$i'>$i</option>";
                }
                ?>
            </select>
        </div>

        <!-- Botón Liberar Pago -->
<div class="col-auto d-flex align-items-end">
    <button type="button" 
            id="btnLiberarPago" 
            class="btn btn-primary"
            style="height: 38px; min-width: 150px;">
        <i class="fas fa-unlock-alt"></i> Liberar Pago
    </button>
</div> 
    </div> <!-- Fin fila -->


</div>


<br>
<br>


<!-- CONTENEDOR DE DATOS OPTIMIZADO -->
<div class="card shadow mb-4 table-container contenedor-nomina">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table"></i> DETALLE ORDENES PARA NOMINA
        </h6>
    </div>
    <div class="card-body p-0">
        <!-- CONTENEDOR DE LA TABLA -->
        <div id="LoadNomina" class="p-3">
            <!-- Aquí se cargan todos los datos que vienen de NOMINA.JS -->
        </div>
    </div>
</div>



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
                <h5 class="modal-title">CERRAR SESION</h5>
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

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<!-- <script src="js/appnominacontratista.js"></script> -->
<script src="js/nomina.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const emuladorSelect = document.getElementById('emuladorSelect');
    const tecnologiaSelect = document.getElementById('tecnologiaSelect');

    if (!emuladorSelect || !tecnologiaSelect) {
        console.error('❌ No se encontraron los selects');
        return;
    }

    emuladorSelect.addEventListener('change', function () {
        const emulador = this.value;
        let tecnologia = '';

        if (emulador === 'FIBRA') tecnologia = 'FIBRA';
        else if (emulador === 'COBRE') tecnologia = 'COBRE';
        else if (emulador === 'MIGRACION') tecnologia = 'FIBRA';

        if (tecnologia) {
            tecnologiaSelect.value = tecnologia;
        }
    });

});
</script>

<!--<script>
    $(document).ready(function(){
        LoadSinProcesoaPago();
    });
</script> -->
</body>
</html>