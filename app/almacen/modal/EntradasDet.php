<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../controller/ClassOps.php');

if (empty($_SESSION['idContratistas'])) {
    http_response_code(401);
    echo '<div class="alert alert-warning mb-0">Sesion no valida.</div>';
    exit;
}

$Folio = filter_input(INPUT_GET, 'Folio', FILTER_VALIDATE_INT);
if (!$Folio) {
    $Folio = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
}

if (!$Folio) {
    http_response_code(400);
    echo '<div class="alert alert-warning mb-0">El folio no es valido.</div>';
    exit;
}

$modal = new Ops();
$materiales = $modal->ObtenerMaterialesEntrada($Folio) ?? [];
$series = $modal->ObtenerSeriesEntrada($Folio) ?? [];
?>

<style>
    .dataTables_wrapper .dt-buttons .btn {
        margin-right: .25rem;
    }

    #entradas-det-table-wrapper,
    #entradas-det-series-wrapper {
        width: 100%;
        overflow-x: auto;
    }
    #entradas-det-cards-container, #entradas-det-series-cards-container {
        display: none;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
        width: 100%;
    }
    .entradas-det-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid #e9ecef;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
    }
    .entradas-det-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .entradas-det-card-header {
        background: linear-gradient(90deg, #0f172a 0%, #003d99 100%);
        color: #fff;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .entradas-det-card-header h5 {
        margin: 0;
        font-size: .95rem;
        font-weight: 600;
        flex: 1;
    }
    .entradas-det-card-header .badge-folio {
        background: rgba(255,255,255,.2);
        border-radius: 6px;
        padding: 3px 8px;
        font-size: .75rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .entradas-det-card-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .entradas-det-field {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 6px 0;
        font-size: .9rem;
    }

    .entradas-det-field-label {
        color: #6c757d;
        font-size: .8rem;
        text-transform: uppercase;
        font-weight: 500;
        min-width: 120px;
    }

    .entradas-det-field-value {
        color: #1f2937;
        font-size: .9rem;
        font-weight: 600;
        text-align: right;
        word-break: break-word;
    }

    .nav-tabs {
        border-bottom: 2px solid #e0e0e0 !important;
        display: flex;
        flex-wrap: wrap;
    }

    .nav-tabs .nav-link {
        color: #6c757d !important;
        border: none !important;
        border-bottom: 3px solid transparent !important;
        padding: 10px 16px !important;
        font-weight: 500 !important;
    }

    .nav-tabs .nav-link:hover {
        color: #007BFF !important;
        border-bottom-color: rgba(0,123,255,.3) !important;
    }

    .nav-tabs .nav-link.active {
        color: #007BFF !important;
        border-bottom-color: #007BFF !important;
        background: none !important;
    }

    .nav-tabs .nav-link i {
        margin-right: 6px;
    }

    .tab-content {
        margin-top: 15px;
    }
    @media (max-width: 1200px) {
        #entradas-det-cards-container,
        #entradas-det-series-cards-container {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
    }
    @media (max-width: 768px) {
        #entradas-det-table-wrapper,
        #entradas-det-series-wrapper {
            display: none !important;
        }

        #entradas-det-cards-container,
        #entradas-det-series-cards-container {
            display: grid;
            grid-template-columns: 1fr;
        }

        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
        }
    }
    @media (max-width: 576px) {
        .entradas-det-card-header {
            padding: 10px 12px;
        }

        .entradas-det-card-body {
            padding: 12px;
            gap: 6px;
        }

        .entradas-det-field {
            flex-direction: column;
            align-items: flex-start;
        }

        .entradas-det-field-value {
            text-align: left;
            width: 100%;
        }

        .nav-tabs .nav-link {
            padding: 8px 12px !important;
            font-size: .85rem !important;
        }
    }
</style>

<div class="entrada-detalle-contenido">
    <ul class="nav nav-tabs mb-4" id="entradasTab<?= $Folio ?>" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-toggle="tab" data-target="#materiales-panel-<?= $Folio ?>" type="button">
                <i class="fas fa-box"></i> Materiales
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-toggle="tab" data-target="#series-panel-<?= $Folio ?>" type="button">
                <i class="fas fa-barcode"></i> Numeros de Serie
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="materiales-panel-<?= $Folio ?>">
            <div id="entradas-det-table-wrapper">
                <table class="table table-bordered display nowrap" style="width:100%" id="ventasMateriales<?= $Folio ?>">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Modelo</th>
                            <th>Tecnologia</th>
                            <th>Presentacion</th>
                            <th>UM</th>
                            <th>Total Piezas</th>
                            <th>Total Metros</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materiales as $material): ?>
                            <tr>
                                <td><?= htmlspecialchars($material['Producto'] ?? '') ?></td>
                                <td><?= htmlspecialchars($material['Modelo'] ?? '') ?></td>
                                <td><?= htmlspecialchars($material['Tecnologia'] ?? '') ?></td>
                                <td><?= htmlspecialchars($material['Presentacion'] ?? '') ?></td>
                                <td><?= htmlspecialchars($material['UM'] ?? '') ?></td>
                                <td><?= htmlspecialchars($material['Total_Piezas'] ?? '') ?></td>
                                <td><?= htmlspecialchars($material['Total_Metros'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="entradas-det-cards-container"></div>
        </div>

        <div class="tab-pane fade" id="series-panel-<?= $Folio ?>">
            <div id="entradas-det-series-wrapper">
                <table class="table table-bordered display nowrap" style="width:100%" id="ventasSeries<?= $Folio ?>">
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th>Modelo</th>
                            <th>Numero de Serie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($series as $serie): ?>
                            <tr>
                                <td><?= htmlspecialchars($serie['Producto'] ?? '') ?></td>
                                <td><?= htmlspecialchars($serie['Modelo'] ?? '') ?></td>
                                <td><span class="badge badge-primary"><?= htmlspecialchars($serie['Num_Serie_S'] ?? 'SIN SERIE') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div id="entradas-det-series-cards-container"></div>
        </div>
    </div>
</div>

<script>
(function () {
    const materiales = <?= json_encode($materiales) ?>;
    const series = <?= json_encode($series) ?>;
    const folio = <?= json_encode((string)$Folio) ?>;

    function crearCampo(etiqueta, valor) {
        return '<div class="entradas-det-field">' +
            '<span class="entradas-det-field-label">' + etiqueta + '</span>' +
            '<strong class="entradas-det-field-value">' + (valor || 'N/A') + '</strong>' +
            '</div>';
    }

    const tarjetasMateriales = $('#entradas-det-cards-container');
    materiales.forEach(function (material, indice) {
        const tarjeta =
            '<div class="entradas-det-card">' +
                '<div class="entradas-det-card-header">' +
                    '<h5><i class="fas fa-box"></i> Material ' + (indice + 1) + '</h5>' +
                    '<span class="badge-folio">#' + (indice + 1) + '</span>' +
                '</div>' +
                '<div class="entradas-det-card-body">' +
                    crearCampo('Producto', material.Producto) +
                    crearCampo('Modelo', material.Modelo) +
                    crearCampo('Tecnologia', material.Tecnologia) +
                    crearCampo('Presentacion', material.Presentacion) +
                    crearCampo('UM', material.UM) +
                    crearCampo('Total Piezas', material.Total_Piezas) +
                    crearCampo('Total Metros', material.Total_Metros) +
                '</div>' +
            '</div>';

        tarjetasMateriales.append(tarjeta);
    });

    const tarjetasSeries = $('#entradas-det-series-cards-container');
    series.forEach(function (serie, indice) {
        const tarjeta =
            '<div class="entradas-det-card">' +
                '<div class="entradas-det-card-header">' +
                    '<h5><i class="fas fa-barcode"></i> Serie ' + (indice + 1) + '</h5>' +
                    '<span class="badge-folio">#' + (indice + 1) + '</span>' +
                '</div>' +
                '<div class="entradas-det-card-body">' +
                    crearCampo('Producto', serie.Producto) +
                    crearCampo('Modelo', serie.Modelo) +
                    crearCampo('Numero de Serie', serie.Num_Serie_S) +
                '</div>' +
            '</div>';

        tarjetasSeries.append(tarjeta);
    });

    if (typeof DataTable !== 'undefined') {
        const tablaMateriales = new DataTable('#ventasMateriales' + folio, {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: false,
            searching: true,
            scrollX: true,
            pageLength: 10
        });

        if (series.length > 0) {
            new DataTable('#ventasSeries' + folio, {
                responsive: true,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
                ordering: false,
                searching: true,
                scrollX: true,
                pageLength: 10
            });
        }

        const botones = new DataTable.Buttons(tablaMateriales, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i>',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success btn-sm'
                }
            ]
        });
        botones.container().prependTo(tablaMateriales.table().container());
    }
}());
</script>