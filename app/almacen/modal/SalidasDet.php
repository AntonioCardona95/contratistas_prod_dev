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
    header('location: ../salidas.php');
    exit;
}

$modal = new Ops();
$salidas_det = $modal->SalidasDet($Folio);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Detalle de salida de material">
    <meta name="author" content="">

    <title>ERP - ALMACEN | Salida <?= htmlspecialchars($Folio) ?></title>

    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet" integrity="sha384-2vMryTPZxTZDZ3GnMBDVQV8OtmoutdrfJxnDTg0bVam9mZhi7Zr3J1+lkVFRr71f" crossorigin="anonymous">
    <style>
        .dataTables_wrapper .dt-buttons .btn {
            margin-right: .25rem;
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
                <?php include_once(__DIR__ . '/../components/TopbarContratistas.php'); ?>
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h1 class="m-0 font-weight-bold text-gray-800">MATERIAL ASIGNADO FOLIO SALIDA: <?= htmlspecialchars($Folio) ?></h1>
                            <a href="../salidas.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Regresar</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered display nowrap" style="width:100%" id="ventas">
                                    <thead>
                                        <tr>
                                            <th>Tecnico</th>
                                            <th>Material</th>
                                            <th>Detalle</th>
                                            <th>Tecnologia</th>
                                            <th>SKU</th>
                                            <th>Numero de Serie</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Tecnico</th>
                                            <th>Material</th>
                                            <th>Detalle</th>
                                            <th>Tecnologia</th>
                                            <th>SKU</th>
                                            <th>Numero de Serie</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php if (!empty($salidas_det)): foreach ($salidas_det as $row): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['Tecnico'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['Producto'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['Descripcion'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['Tecnologia'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['Modelo'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['Num_Serie_Salida_Det'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No se encontraron registros.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once(__DIR__ . '/../components/FooterContratistas.php'); ?>
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php include_once(__DIR__ . '/../components/ModalCierreSesion.php'); ?>
    <?php include_once(__DIR__ . '/../components/ScriptContratistas.php'); ?>

    <script>
        $(document).ready(function () {
            let table = new DataTable('#ventas', {
                fixedColumns: { start: 1, end: 1 },
                responsive: true,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
                ordering: false,
                searching: true,
                scrollY: true,
                scrollX: true
            });

            new DataTable.Buttons(table, {
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

            table.buttons(0, null).container().prependTo(table.table().container());
        });
    </script>
</body>
</html>

