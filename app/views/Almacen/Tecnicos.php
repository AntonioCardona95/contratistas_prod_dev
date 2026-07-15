<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');

    require_once __DIR__ . '/../../controller/ClassOps.php';

    session_start();
    $Contratista = $_SESSION['Contratista'] ?? '';
    $idContratistas = $_SESSION['idContratistas'] ?? null;

    if (empty($idContratistas)) {
        header('location: ../Login/modal/poweroff.php');
        exit;
    }

    $operaciones = new Ops();
    $tecnicos = $operaciones->Tecnicos($idContratistas) ?: [];
    $totalTecnicos = count($tecnicos);

?>
<!DOCTYPE html>

<html lang="es">

<!-- INSERTAR EL CSS -->
<?php $css = ['/../../css/Tecnicos.css'] ?>
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

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($Contratista); ?></span>
                                <img class="img-profile rounded-circle" src="../../img/undraw_profile.svg">
                            </a>
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

                    <!-- TITULO -->
                    <div class="prod-hero">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <h1 class="h3 mb-0 font-weight-bold mt-2">TÉCNICOS</h1>
                            <span class="badge-total">Total: <?php echo $totalTecnicos; ?></span>
                        </div>
                    </div>

                    <?php if ($totalTecnicos === 0) : ?>
                        <div class="alert alert-empty" role="alert">
                            <i class="fas fa-users"></i>
                            No se encontraron técnicos activos para este contratista.
                        </div>
                    <?php else : ?>
                        <div class="tecnicos-grid">
                            <?php foreach ($tecnicos as $tecnico) : ?>
                                <?php
                                    $nombreCompleto = trim(($tecnico['Nombre_T'] ?? '') . ' ' . ($tecnico['Apellidos_T'] ?? ''));
                                    $telefono = $tecnico['Celular_T'] ?? $tecnico['Telefono'] ?? 'N/A';
                                    $expediente = $tecnico['NExpediente'] ?? 'N/A';
                                    $cope = $tecnico['Cope'] ?? 'SIN COPE';
                                ?>
                                <div class="tecnico-card">
                                    <div class="card-body">
                                        <div class="tecnico-nombre"><?php echo htmlspecialchars($nombreCompleto ?: 'Técnico sin nombre'); ?></div>
                                        
                                        <div class="tecnico-info-item info-expediente">
                                            <span class="tecnico-info-label">Expediente</span>
                                            <span class="tecnico-info-value"><?php echo htmlspecialchars($expediente); ?></span>
                                        </div>
                                        
                                        <div class="tecnico-info-item info-telefono">
                                            <span class="tecnico-info-label">Teléfono</span>
                                            <span class="tecnico-info-value"><?php echo htmlspecialchars($telefono); ?></span>
                                        </div>
                                        
                                        <div class="tecnico-info-item info-cope">
                                            <span class="tecnico-info-label">COPE</span>
                                            <span class="tecnico-info-value"><?php echo htmlspecialchars($cope); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
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
    
    <script>
        // Efecto de carga suave
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.tecnico-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.05) + 's';
            });
        });
    </script>

</body>

</html>
