<?php

    // CONFIGURACIÓN DE ERRORES
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // ZONA HORARIA
    date_default_timezone_set('America/Monterrey');

    // IMPORTAR CLASE
    require_once __DIR__ . '/../../controller/ClassOps.php';

    // SESIÓN
    session_start();
    // VARIABLES DE SESIÓN
    $Contratista = $_SESSION['Contratista'] ?? '';
    $idContratistas = $_SESSION['idContratistas'] ?? null;

    // VALIDAR SESIÓN
    if (empty($idContratistas)) {
        header('location: ../Login/modal/poweroff.php');
        exit;
    }

    // OBTENER DATOS DE TÉCNICOS
    $operaciones = new Ops();
    $tecnicos = $operaciones->Tecnicos($idContratistas) ?: [];
    $totalTecnicos = count($tecnicos);


    require_once(__DIR__ . '/../../controller/ClassSubModulos.php');

    $subModulos = new SubModulos();
    $submodulos_operaciones = $subModulos->ObtenerOperaciones();
    $submodulos_almacen = $subModulos->ObtenerAlmacen();

?>
<!DOCTYPE html>

<html lang="es">

<!-- IMPORTAR CSS PERSONALIZADO -->
<?php $css = ['/../../css/Tecnicos.css'] ?>
<?php include __DIR__ . '/../../components/HeadContratistas.php'; ?>

<body id="page-top">

    <!-- CONTENEDOR PRINCIPAL -->
    <div id="wrapper">

        <!-- BARRA LATERAL -->
        <?php include_once __DIR__ . '/../../components/SidebarContratistas.php'; ?>

        <!-- CONTENEDOR DE CONTENIDO -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- CONTENIDO PRINCIPAL -->
            <div id="content">

                <!-- BARRA SUPERIOR -->
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
                <!-- FIN BARRA SUPERIOR -->

                <!-- INICIO CONTENIDO PRINCIPAL -->
                <div class="container-fluid">

                    <!-- ENCABEZADO CON TÍTULO Y TOTAL -->
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
                        <!-- BÚSQUEDA Y INFORMACIÓN -->
                        <div class="card shadow mb-4" style="border: none; border-radius: 12px;">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6" id="search-container"></div>
                                    <div class="col-md-6 d-flex justify-content-end" id="info-container"></div>
                                </div>
                            </div>
                        </div>

                        <!-- GRID DINÁMICO DE TARJETAS -->
                        <div id="tecnicos-grid-container" class="tecnicos-grid">
                            <!-- TARJETAS RENDERIZADAS DINÁMICAMENTE -->
                        </div>

                        <!-- CONTROLES DE PAGINACIÓN -->
                        <div class="card shadow mt-4" style="border: none; border-radius: 12px;">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6" id="pagination-info"></div>
                                    <div class="col-md-6 d-flex justify-content-end" id="pagination-container"></div>
                                </div>
                            </div>
                        </div>

                        <!-- TABLA INVISIBLE PARA DATATABLES -->
                        <table class="table table-bordered display nowrap datatable-init" style="width:100%; display:none;" id="TecnicosTable">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Expediente</th>
                                    <th>Teléfono</th>
                                    <th>COPE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($tecnicos as $tecnico) : ?>
                                    <?php
                                        // DATOS DEL TÉCNICO
                                        $nombreCompleto = trim(($tecnico['Nombre_T'] ?? '') . ' ' . ($tecnico['Apellidos_T'] ?? ''));
                                        $telefono = $tecnico['Celular_T'] ?? $tecnico['Telefono'] ?? 'N/A';
                                        $expediente = $tecnico['NExpediente'] ?? 'N/A';
                                        $cope = $tecnico['Cope'] ?? 'SIN COPE';
                                    ?>
                                    <tr data-nombre="<?= htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') ?>"
                                        data-expediente="<?= htmlspecialchars($expediente, ENT_QUOTES, 'UTF-8') ?>"
                                        data-telefono="<?= htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') ?>"
                                        data-cope="<?= htmlspecialchars($cope, ENT_QUOTES, 'UTF-8') ?>">
                                        <td><?= htmlspecialchars($nombreCompleto) ?></td>
                                        <td><?= htmlspecialchars($expediente) ?></td>
                                        <td><?= htmlspecialchars($telefono) ?></td>
                                        <td><?= htmlspecialchars($cope) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                </div>
                <!-- FIN CONTENIDO PRINCIPAL -->

            </div>
            <!-- FIN CONTENIDO PRINCIPAL -->

            <!-- PIE DE PÁGINA -->
            <?php include_once __DIR__ . '/../../components/FooterContratistas.php'; ?>

        </div>
        <!-- FIN CONTENEDOR DE CONTENIDO -->

    </div>
    <!-- FIN CONTENEDOR PRINCIPAL -->

    <!-- BOTÓN VOLVER ARRIBA -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- MODAL CERRAR SESIÓN -->
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

    <!-- IMPORTAR SCRIPTS Y DATATABLES -->
    <?php include_once __DIR__ . '/../../components/ScriptContratistas.php'; ?>
    
    <script>
        $(document).ready(function() {
            // INICIALIZAR DATATABLES
            const table = new DataTable('#TecnicosTable', {
                responsive: true,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
                ordering: false,
                searching: true,
                pageLength: 9,
                dom: '<"row"<"col-md-6"f><"col-md-6">>',
                drawCallback: function() {
                    renderCards();
                }
            });

            // MOVER CONTROLES A SUS CONTENEDORES
            function relocateControls() {
                const wrapper = $(table.table().container());
                
                // BUSCAR Y MOVER ELEMENTOS DE DATATABLES
                const dtSearch = wrapper.find('.dt-search');
                const dtInfo = wrapper.find('.dt-info');
                const dtPaging = wrapper.find('.dt-paging');
                
                // LIMPIAR CONTENEDORES ANTES DE MOVER
                $('#search-container').empty();
                $('#pagination-info').empty();
                $('#pagination-container').empty();
                
                // MOVER ELEMENTOS
                dtSearch.appendTo('#search-container');
                dtInfo.appendTo('#pagination-info');
                dtPaging.appendTo('#pagination-container');
                
                // AGREGAR PLACEHOLDER AL BUSCADOR
                $('.dt-search input').attr('placeholder', 'Buscar por nombre, expediente o teléfono...');
                $('.dt-search input').css({
                    'width': '100% !important',
                    'max-width': '500px'
                });
            }

            relocateControls();

            // FUNCIÓN PARA RENDERIZAR TARJETAS
            function renderCards() {
                const container = $('#tecnicos-grid-container');
                container.empty();

                // OBTENER FILAS VISIBLES DE LA PÁGINA ACTUAL
                const visibleRows = table.rows({ page: 'current' }).nodes();

                if (visibleRows.length === 0) {
                    container.html('<div class="col-12 text-center"><p>No se encontraron técnicos.</p></div>');
                    return;
                }

                // CREAR TARJETA PARA CADA FILA VISIBLE
                $(visibleRows).each(function(index) {
                    const row = $(this);
                    const nombre = row.data('nombre');
                    const expediente = row.data('expediente');
                    const telefono = row.data('telefono');
                    const cope = row.data('cope');

                    const card = `
                        <div class="tecnico-card">
                            <div class="card-body">
                                <div class="tecnico-nombre">${nombre}</div>
                                
                                <div class="tecnico-info-item info-expediente">
                                    <span class="tecnico-info-label">Expediente</span>
                                    <span class="tecnico-info-value">${expediente}</span>
                                </div>
                                
                                <div class="tecnico-info-item info-telefono">
                                    <span class="tecnico-info-label">Teléfono</span>
                                    <span class="tecnico-info-value">${telefono}</span>
                                </div>
                                
                                <div class="tecnico-info-item info-cope">
                                    <span class="tecnico-info-label">COPE</span>
                                    <span class="tecnico-info-value">${cope}</span>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(card);
                });
            }

            // RENDERIZAR AL CARGAR
            renderCards();

            // RENDERIZAR TARJETAS AL CAMBIAR PÁGINA O BÚSQUEDA
            table.on('search.dt page.dt', function() {
                // LIMPIAR Y REUBICAR CONTROLES
                relocateControls();
                // RENDERIZAR NUEVAS TARJETAS
                renderCards();
            });
        });
    </script>

</body>

</html>
