<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../controller/ClassOps.php');
session_start();

$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

if (empty($Contratista) || empty($idContratistas)) {
    header('location: ../../Login/modal/poweroff.php');
    exit;
}

$Folio = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($Folio <= 0) {
    header('location: ../entradas.php');
    exit;
}

$modal = new Ops();
$materiales = $modal->ObtenerMaterialesEntrada($Folio);
$series = $modal->ObtenerSeriesEntrada($Folio);
$topbar_onchange = 'noop';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Detalle de entrada de material">
    <meta name="author" content="">

    <title>ERP - ALMACEN | Entrada <?= htmlspecialchars($Folio) ?></title>

        <!-- CARGA DE ICONOS Y FUENTES -->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <!-- ESTILOS BASE DE LA PLANTILLA -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- ESTILOS PARA TABLAS Y RESPONSIVIDAD -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <link href="../../css/responsive_custom.css" rel="stylesheet">

    <!-- ESTILO PERSONALIZADO PARA EL MENU ACTIVO DE LA SIDEBAR -->
    <link rel="stylesheet" href="../css/sidebar-active.css">
    
    <style>
        .dataTables_wrapper .dt-buttons .btn {
            margin-right: .25rem;
        }

        /* ── Wrapper tabla ── */
        #entradas-det-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        /* ── Cards container ── */
        #entradas-det-cards-container {
            display: none;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        /* ── Card ── */
        .entradas-det-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .entradas-det-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
        }

        .entradas-det-card-header {
            background: linear-gradient(90deg, #0f172a 0%, #003d99 100%);
            color: #fff;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .entradas-det-card-header .badge-folio {
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .entradas-det-card-body {
            padding: 0 18px 18px;
        }

        .entradas-det-field {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #f1f3f5;
            gap: 10px;
        }

        .entradas-det-field:last-child {
            border-bottom: none;
        }

        .entradas-det-field span {
            color: #6c757d;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex: 1;
            white-space: nowrap;
        }

        .entradas-det-field strong {
            color: #1f2937;
            font-size: 0.92rem;
            font-weight: 600;
            text-align: right;
            min-width: 100px;
            word-break: break-word;
        }

        /* ── TABS STYLING ── */
        .nav-tabs {
            border-bottom: 2px solid #e0e0e0 !important;
        }

        .nav-tabs .nav-link {
            color: #6c757d !important;
            border: none !important;
            border-bottom: 3px solid transparent !important;
            padding: 10px 20px !important;
            font-weight: 500 !important;
            transition: all 0.25s ease !important;
        }

        .nav-tabs .nav-link:hover {
            color: #007BFF !important;
            border-bottom: 3px solid rgba(0, 123, 255, 0.2) !important;
        }

        .nav-tabs .nav-link.active {
            color: #007BFF !important;
            border-bottom: 3px solid #007BFF !important;
            background: none !important;
        }

        .tab-content {
            margin-top: 15px;
        }

        #entradas-det-series-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        #entradasTab .nav-link i {
            margin-right: 6px;
        }

        /* ── Responsive ── */
        @media (max-width: 1200px) {
            #entradas-det-cards-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            #entradas-det-table-wrapper {
                display: none;
            }

            #entradas-det-cards-container {
                display: grid;
                grid-template-columns: 1fr;
            }

            h1.font-weight-bold {
                font-size: 1rem !important;
            }
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #007BFF; background-image: linear-gradient(180deg, #0f172a 100%, #224abe 0%); background-size: cover;">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../index.php">
                <div class="sidebar-brand-text mx-3">ALMACEN</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">ACCION</div>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>MODULOS</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">MODULOS</h6>
                        <a class='collapse-item' href='../index.php'><i class="fa-solid fa-box-open"></i> ALMACEN</a>
                        <a class='collapse-item' href='../../ops/index.php'><i class="fa-solid fa-school-circle-check"></i> OPERACIONES</a>
                    </div>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../entradas.php">
                    <i class="fas fa-fw fa-clipboard-check"></i>
                    <span>ENTRADAS DE MATERIALES</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../salidas.php">
                    <i class="fas fa-fw fa-circle-plus"></i>
                    <span>MATERIAL ASIGNADO A MIS TECNICOS</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="sidebar-footer text-center">
                <button class="rounded-circle border-0" id="sidebarToggle" aria-label="Cerrar menú">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once(__DIR__ . '/../../components/TopbarContratistas.php'); ?>
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h1 class="m-0 font-weight-bold text-gray-800">ENTRADA MATERIAL FOLIO: <?= htmlspecialchars($Folio) ?></h1>
                            <a href="../entradas.php" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Regresar
                            </a>
                        </div>
                        <div class="card-body">

                            <!-- TABS NAVIGATION -->
                            <ul class="nav nav-tabs mb-4" id="entradasTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="materiales-tab" data-bs-toggle="tab" data-toggle="tab" href="#materiales-panel" role="tab" aria-controls="materiales-panel" aria-selected="true">
                                        <i class="fas fa-box"></i> Materiales
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="series-tab" data-bs-toggle="tab" data-toggle="tab" href="#series-panel" role="tab" aria-controls="series-panel" aria-selected="false">
                                        <i class="fas fa-barcode"></i> Números de Serie
                                    </button>
                                </li>
                            </ul>

                            <!-- TAB CONTENT -->
                            <div class="tab-content" id="entradasTabContent">

                                <!-- TAB 1: MATERIALES -->
                                <div class="tab-pane fade show active" id="materiales-panel" role="tabpanel" aria-labelledby="materiales-tab">
                                    <div id="entradas-det-table-wrapper">
                                        <table class="table table-bordered display nowrap" style="width:100%" id="ventasMateriales">
                                            <thead>
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Modelo</th>
                                                    <th>Tecnología</th>
                                                    <th>Presentación</th>
                                                    <th>UM</th>
                                                    <th>Total Piezas</th>
                                                    <th>Total Metros</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Modelo</th>
                                                    <th>Tecnología</th>
                                                    <th>Presentación</th>
                                                    <th>UM</th>
                                                    <th>Total Piezas</th>
                                                    <th>Total Metros</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php 
                                                    if (!empty($materiales)) {
                                                        foreach ($materiales as $mat) {
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($mat['Producto'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($mat['Modelo'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($mat['Tecnologia'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($mat['Presentacion'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($mat['UM'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($mat['Total_Piezas'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($mat['Total_Metros'] ?? '') ?></td>
                                                    </tr>
                                                <?php 
                                                        }
                                                    } else {
                                                ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">No se encontraron registros.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB 2: NÚMEROS DE SERIE -->
                                <div class="tab-pane fade" id="series-panel" role="tabpanel" aria-labelledby="series-tab">
                                    <div id="entradas-det-series-wrapper">
                                        <table class="table table-bordered display nowrap" style="width:100%" id="ventasSeries">
                                            <thead>
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Modelo</th>
                                                    <th>Número de Serie</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Modelo</th>
                                                    <th>Número de Serie</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php if (!empty($series)): foreach ($series as $row): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['Producto'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($row['Modelo'] ?? '') ?></td>
                                                        <td><span class="badge badge-primary"><?= htmlspecialchars($row['Num_Serie_S'] ?? 'SIN SERIE') ?></span></td>
                                                    </tr>
                                                <?php endforeach; else: ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">No se encontraron números de serie.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>

                            <!-- CARDS (móvil) -->
                            <div id="entradas-det-cards-container"></div>

                        </div>
                    </div>
                </div>
            </div>
            <?php include_once(__DIR__ . '/../../components/FooterContratistas.php'); ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include_once(__DIR__ . '/../../components/ModalCierreSesion.php'); ?>
    <?php include_once(__DIR__ . '/../../components/ScriptContratistas.php'); ?>

    <script>
        function noop() {}
        $(document).ready(function () {
            // TABLA DE MATERIALES
            let tableMateriales = new DataTable('#ventasMateriales', {
                fixedColumns: { start: 1, end: 1 },
                responsive: true,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
                ordering: false,
                searching: true,
                scrollY: true,
                scrollX: true
            });

            // TABLA DE SERIES
            let tableSeries = new DataTable('#ventasSeries', {
                fixedColumns: { start: 1, end: 1 },
                responsive: true,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
                ordering: false,
                searching: true,
                scrollY: true,
                scrollX: true
            });

            new DataTable.Buttons(tableMateriales, {
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> ',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i> ',
                        titleAttr: 'Exportar a PDF',
                        className: 'btn btn-danger'
                    }
                ]
            });

            tableMateriales.buttons(0, null).container().prependTo(tableMateriales.table().container());
        });
    </script>
</body>
</html>
