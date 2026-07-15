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

require_once(__DIR__ . '/../../controller/ClassSubModulos.php');

    $subModulos = new SubModulos();
    $submodulos_operaciones = $subModulos->ObtenerOperaciones();
    $submodulos_almacen = $subModulos->ObtenerAlmacen();
?>

<!DOCTYPE html>
<html lang="en">

<!-- INSERTAR EL CSS -->
<?php
$css = ['../css/Bienvenida.css']
?>

<!-- IMPORTAR EL HEAD -->
<?php include __DIR__ . '/../components/HeadContratistas.php'; ?>

<body>

    <div id="wrapper">

        <?php include_once __DIR__ . '/../components/SidebarContratistas.php'; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- TOP BAR -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow" style="padding: 0.75rem 1.75rem;">
                    <div class="container-fluid p-0 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="m-0 font-weight-bold" style="color: #0f172a;">Portal de Contratistas</h6>
                        </div>
                        <ul class="navbar-nav ml-auto align-items-center">
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($Contratista) ?></span>
                                    <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
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
                    </div>
                </nav>

                <!-- CONTENIDO PRINCIPAL -->
                <div class="container-fluid welcome-card-container">
                    <div class="row justify-content-start" style="margin-left: 20px !important;">
                        <div class="card shadow welcome-card">
                            <div class="welcome-body">
                                <div class="welcome-logo">
                                    <img src="../img/enlace.png" alt="Enlace Digital">
                                </div>
                                <div>
                                    <h1 class="welcome-title">¡BIENVENIDO AL PORTAL DE CONTRATISTAS!</h1>
                                    <p class="welcome-text">Desde este portal podrás acceder a los módulos y herramientas disponibles para gestionar tus actividades. Utiliza el menú lateral para navegar entre las diferentes opciones.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php include_once __DIR__ . '/../components/FooterContratistas.php'; ?>

        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- MODAL DE CIERRE DE SESIÓN -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-gradient text-white border-0 align-items-center">
                    <h5 class="modal-title" id="logoutModalLabel"><i class="fas fa-sign-out-alt"></i> CERRAR SESION</h5>
                    <button class="btn btn-light" type="button" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">PRESIONA SALIR, PARA CERRAR LA SESIÓN.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">CANCELAR</button>
                    <a class="btn btn-primary" href="../Login/modal/poweroff.php">SALIR</a>
                </div>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../components/ScriptContratistas.php'; ?>
    
</body>
</html>