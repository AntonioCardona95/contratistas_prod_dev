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

        <!-- SIDEBAR IMPORTADA DESDE COMPONENTES -->
        <?php include_once(__DIR__ . '/../components/SidebarAlmacen.php'); ?>

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
            const wrapper = $(table.table().container());
            wrapper.find('.dt-search').appendTo('#search-detalles');
            wrapper.find('.dt-buttons').appendTo('#botones-detalles');
            wrapper.find('.dt-paging').appendTo('#pagination-detalles');
            wrapper.find('.dt-info').appendTo('#info-detalles');
            $('.dt-search input').attr('placeholder', 'Buscar por nombre, expediente o teléfono');

            const dataTablesWrapper = wrapper.closest('.dataTables_wrapper');
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
                const nombre = row.data('nombre') || 'Sin nombre';
                const expediente = row.data('expediente') || 'N/A';
                const telefono = row.data('telefono') || 'N/A';
                const cope = row.data('cope') || 'SIN COPE';

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
            if (!$table.length) {
                return;
            }

            if (window.DataTable && typeof window.DataTable.isDataTable === 'function' && window.DataTable.isDataTable('#TecnicosTable')) {
                $('#TecnicosTable').DataTable().destroy();
            }

            const table = new DataTable('#TecnicosTable', {
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
                },
                ordering: false,
                searching: true,
                pageLength: 12,
                layout: {
                    topStart: 'buttons',
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success'
                }],
                initComplete: function () {
                    const api = this.api();
                    $('#tecnicos-grid-container').insertBefore($('#TecnicosTable'));
                    $('.dt-search input').attr('placeholder', 'Buscar por nombre, expediente o telefono');
                    renderCards(api);
                    $('#TecnicosTable').hide();
                    tecnicosTableInitialized = true;
                }
            });

            let isRendering = false;
            table.on('search.dt page.dt order.dt', function () {
                if (isRendering) {
                    return;
                }

                isRendering = true;
                renderCards(table);
                setTimeout(function () {
                    isRendering = false;
                }, 100);
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
