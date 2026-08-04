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

    <!-- CARGA DE ICONOS Y FUENTES -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- ESTILOS BASE DE LA PLANTILLA -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/tecnicos.css" rel="stylesheet">

    <!-- ESTILOS PARA TABLAS Y RESPONSIVIDAD -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../css/responsive_custom.css" rel="stylesheet">

    <!-- ESTILO PERSONALIZADO PARA EL MENU ACTIVO DE LA SIDEBAR -->
    <link rel="stylesheet" href="css/sidebar-active.css">

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

                    <!-- TARJETA PRINCIPAL DE LISTADO -->
                    <div class="card shadow mb-4 table-container">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h2 class="m-0 font-weight-bold text-gray-800">TECNICOS</h2>
                        </div>
                        <div class="card-body p-0">
                            <!-- CONTENEDOR DONDE SE CARGA LA TABLA DINÁMICAMENTE -->
                            <div id="LoadTecnicos" class="p-3">
                                <!-- Aquí se renderiza el contenido con AJAX -->
                            </div>
                        </div> 
                    </div>

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

    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>

    <script>
        let tecnicosTableInitialized = false;
        let tecnicosLoadInProgress = false;

        function relocateControls(table) {
            const topControls = $('#tecnicos-top-controls').empty().css({
                'display': 'flex',
                'justify-content': 'space-between',
                'align-items': 'center',
                'gap': '12px',
                'flex-wrap': 'wrap'
            });

            const bottomControls = $('#tecnicos-bottom-controls').empty().css({
                'display': 'flex',
                'justify-content': 'space-between',
                'align-items': 'center',
                'gap': '12px',
                'flex-wrap': 'wrap'
            });

            const searchWrap = $('<div class="d-flex align-items-center gap-2" style="flex: 1 1 280px; min-width: 220px;"></div>');
            const searchIcon = $('<i class="fas fa-search text-muted"></i>');
            const searchInput = $('<input type="search" class="form-control form-control-sm" placeholder="Buscar por nombre, expediente o teléfono...">');
            searchInput.on('input', function () {
                table.search(this.value).draw();
            });
            searchWrap.append(searchIcon, searchInput);
            topControls.append(searchWrap);

            const actionsWrap = $('<div class="d-flex align-items-center gap-2"></div>');
            const excelButton = $('<button type="button" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</button>');
            excelButton.on('click', function () {
                if (table.button) {
                    table.button(0).trigger();
                }
            });
            actionsWrap.append(excelButton);
            topControls.append(actionsWrap);

            const pageSizeSelect = $('<select class="form-control form-control-sm" style="width: auto; min-width: 120px;"></select>');
            pageSizeSelect.append('<option value="9">9 por página</option>');
            pageSizeSelect.append('<option value="18">18 por página</option>');
            pageSizeSelect.append('<option value="27">27 por página</option>');
            pageSizeSelect.val(table.page.len());
            pageSizeSelect.on('change', function () {
                table.page.len(Number(this.value)).draw();
            });
            bottomControls.append(pageSizeSelect);

            const infoText = $('<div class="text-muted small"></div>');
            bottomControls.append(infoText);

            const pager = $('<nav><ul class="pagination pagination-sm mb-0"></ul></nav>');
            bottomControls.append(pager);

            function refreshControls() {
                const info = table.page.info();
                const totalPages = info.pages || 1;
                const currentPage = info.page + 1;
                infoText.text(`Mostrando ${info.start + 1} a ${info.end} de ${info.recordsTotal} técnicos`);

                pager.empty();
                const prevItem = $('<li class="page-item"><a class="page-link" href="#" aria-label="Anterior"><span aria-hidden="true">&laquo;</span></a></li>');
                prevItem.on('click', function (e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        table.page('previous').draw(false);
                    }
                });
                pager.find('ul').append(prevItem);

                for (let i = 1; i <= totalPages; i++) {
                    const isActive = i === currentPage;
                    const pageItem = $('<li class="page-item' + (isActive ? ' active' : '') + '"></li>');
                    const pageLink = $('<a class="page-link" href="#">' + i + '</a>');
                    pageLink.on('click', function (e) {
                        e.preventDefault();
                        table.page(i - 1).draw(false);
                    });
                    pageItem.append(pageLink);
                    pager.find('ul').append(pageItem);
                }

                const nextItem = $('<li class="page-item"><a class="page-link" href="#" aria-label="Siguiente"><span aria-hidden="true">&raquo;</span></a></li>');
                nextItem.on('click', function (e) {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        table.page('next').draw(false);
                    }
                });
                pager.find('ul').append(nextItem);

                pager.find('.page-link').css({
                    'border-radius': '6px',
                    'padding': '0.45rem 0.8rem',
                    'font-size': '0.9rem'
                });
            }

            refreshControls();
            table.on('draw.dt search.dt page.dt', refreshControls);

            const dataTablesWrapper = $(table.table().container()).closest('.dataTables_wrapper');
            if (dataTablesWrapper.length) {
                dataTablesWrapper.hide();
            }
        }

        function renderCards(table) {
            const container = $('#tecnicos-grid-container');
            container.empty();

            const visibleRows = table.rows({ page: 'current' }).nodes();

            if (visibleRows.length === 0) {
                container.html('<div class="col-12 text-center"><p>No se encontraron técnicos.</p></div>');
                return;
            }

            $(visibleRows).each(function () {
                const row = $(this);
                const nombre = row.data('nombre');
                const expediente = row.data('expediente');
                const telefono = row.data('telefono');
                const cope = row.data('cope');

                const card = `
                    <div class="tecnico-card">
                        <div class="card-body">
                            <div class="tecnico-nombre">${nombre}</div>

                            <div class="tecnico-info-item">
                                <span class="tecnico-info-label">Expediente</span>
                                <span class="tecnico-info-value">${expediente}</span>
                            </div>

                            <div class="tecnico-info-item">
                                <span class="tecnico-info-label">Teléfono</span>
                                <span class="tecnico-info-value">${telefono}</span>
                            </div>

                            <div class="tecnico-info-item">
                                <span class="tecnico-info-label">COPE</span>
                                <span class="tecnico-info-value">${cope}</span>
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });
        }

        function initTecnicosTable() {
            if (tecnicosTableInitialized) {
                return;
            }

            const $table = $('#TecnicosTable');
            if (!$table.length || !$.fn.DataTable) {
                return;
            }

            if ($.fn.DataTable.isDataTable('#TecnicosTable')) {
                $('#TecnicosTable').DataTable().destroy();
            }

            const table = $('#TecnicosTable').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.0/i18n/es-ES.json'
                },
                ordering: false,
                searching: true,
                pageLength: 9,
                layout: {
                    topStart: 'search',
                    topEnd: 'buttons',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: ':visible'
                    }
                }],
                initComplete: function () {
                    relocateControls(this.api());
                    renderCards(this.api());
                    $('#TecnicosTable').hide();
                    tecnicosTableInitialized = true;
                }
            });

            table.on('draw.dt search.dt page.dt', function () {
                renderCards(table);
            });
        }

        function LoadTecnicos() {
            if (tecnicosLoadInProgress) {
                return;
            }

            tecnicosLoadInProgress = true;

            $.ajax({
                url: 'modal/TablaTecnicos.php',
                type: 'GET',
                success: function (html) {
                    $('#LoadTecnicos').html(html);
                    setTimeout(initTecnicosTable, 50);
                },
                error: function (xhr, status) {
                    console.error('Error al cargar técnicos:', status, xhr);
                    $('#LoadTecnicos').html('<div class="alert alert-danger">No se pudo cargar la vista de técnicos.</div>');
                },
                complete: function () {
                    tecnicosLoadInProgress = false;
                }
            });
        }

        $(document).ready(function () {
            LoadTecnicos();
        });
    </script>

</body>

</html>
