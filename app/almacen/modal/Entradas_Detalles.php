<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../controller/ClassEntradas.php');

if (empty($_SESSION['idContratistas'])) {
    http_response_code(401);
    echo '<div class="alert alert-warning mb-0">Sesion no valida.</div>';
    exit;
}

$Folio = filter_input(INPUT_GET, 'folio', FILTER_VALIDATE_INT);

if (!$Folio) {
    http_response_code(400);
    echo '<div class="alert alert-warning mb-0">El folio no es valido.</div>';
    exit;
}

$modal = new Entradas();
$materiales = $modal->ObtenerMaterialesEntrada($Folio) ?? [];
$series = $modal->ObtenerSeriesEntrada($Folio) ?? [];
?>

<div class="entrada-detalle-contenido">
    <!-- BOTON DE EXCEL -->
    <div id="entradas-det-buttons-container" class="mb-3"></div>

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
                <table class="table table-bordered display nowrap entradas-data-table" id="ventasMateriales<?= $Folio ?>">
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
                <table class="table table-bordered display nowrap entradas-data-table" id="ventasSeries<?= $Folio ?>">
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
            ordering: true,
            searching: true,
            scrollX: true,
            pageLength: 10,
            initComplete: function () {
                this.api().columns.adjust();
            }
        });

        if (series.length > 0) {
            new DataTable('#ventasSeries' + folio, {
                responsive: true,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
                ordering: false,
                searching: true,
                scrollX: true,
                pageLength: 10,
                initComplete: function () {
                    this.api().columns.adjust();
                }
            });
        }

        const botones = new DataTable.Buttons(tablaMateriales, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success btn-sm'
                }
            ]
        });
        botones.container().appendTo('#entradas-det-buttons-container');
    }
}());
</script>