<?php

    // CONFIGURACIÓN DE ERRORES
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // ZONA HORARIA
    date_default_timezone_set('America/Monterrey');

    // IMPORTAR CLASE
    require_once("controller/ClassOps.php");

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

    <!-- CSS DE FONT AWESOME -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- FUENTE GLOBAL DE GOOGLE FONTS NUNITO -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- COMPATIBILIDAD CON FONT AWESOME 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <!-- PLANTILLA GLOBAL DE CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- ESTILOS DE DATATABLES -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <!-- CSS GLOBAL PERSONALIZADO -->
    <link href="../css/responsive_custom.css" rel="stylesheet">
    <!-- CSS PERSONALIZADO PARA LA PÁGINA DE TÉCNICOS -->
    <link href="css/tecnicos.css" rel="stylesheet">

</head>
<body id="page-top">

    <!-- CONTENEDOR PRINCIPAL -->
    <div id="wrapper">

        <!-- SIDEBAR -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #007BFF; background-image: linear-gradient(180deg, #0f172a 100%, #224abe 0%); background-size: cover;">

            <!-- SIDEBAR TITULO -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-text mx-3">ALMACEN</div>
            </a>

            <!-- DIVISOR -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
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
                        <a class='collapse-item' href='index.php'><i class="fa-solid fa-box-open"></i> ALMACEN</a>
                        <a class='collapse-item' href='../ordenes/index.php'><i class="fa-solid fa-school-circle-check"></i> OPERACIONES</a>
                    </div>
                </div>
            </li>

            <!-- MATERIAL RECIBIDO -->
            <li class="nav-item">
                <a class="nav-link" href="entradas.php">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>MATERIAL RECIBIDO</span>
                </a>
            </li>

            <!-- ACEPTAR MATERIAL -->
             <li class="nav-item">
                <a class="nav-link" href="aceptarMaterial.php">
                    <i class="fa-solid fa-feather-pointed"></i>
                    <span>ACEPTAR MATERIAL</span>
                </a>
            </li>

            <!-- MATERIAL RECHAZADO -->
             <li class="nav-item">
                <a class="nav-link" href="materialRechazado.php">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>MATERIAL RECHAZADO</span>
                </a>
            </li>

            <!-- ASIGNAR MATERIAL -->
            <li class="nav-item">
                <a class="nav-link" href="salidas.php">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>ASIGNAR MATERIAL</span>
                </a>
            </li>
            
            <!-- TECNICOS -->
            <li class="nav-item">
                <a class="nav-link" href="tecnicos.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>TÉCNICOS</span>
                </a>
            </li>
           

            <!-- DIVISOR -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="sidebar-footer text-center">
                <button class="rounded-circle border-0" id="sidebarToggle" aria-label="Cerrar menú">
                    <i class="fas fa-times"></i>
                </button>
            </div>


        </ul>
        <!-- FIN DE LA SIDEBAR -->

        <!-- CONTENEDOR DE CONTENIDO -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- CONTENIDO PRINCIPAL -->
            <div id="content">

                <!-- BARRA SUPERIOR -->
                <?php include_once(__DIR__ . '/../components/TopbarContratistas.php'); ?>
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
            <?php include_once(__DIR__ . '/../components/FooterContratistas.php'); ?>

        </div>
        <!-- FIN CONTENEDOR DE CONTENIDO -->

    </div>
    <!-- FIN CONTENEDOR PRINCIPAL -->

    <!-- BOTÓN VOLVER ARRIBA -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- MODAL CERRAR SESIÓN -->
    <?php include_once(__DIR__ . '/../components/ModalCierreSesion.php'); ?>

    <!-- IMPORTAR SCRIPTS Y DATATABLES -->
    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>
    
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
