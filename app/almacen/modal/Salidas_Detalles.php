<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../controller/ClassSalidas.php');
require_once(__DIR__ . '/../../config/ContratistaContexto.php');

requerirSesionAlmacen();

$Folio = filter_input(INPUT_POST, 'folio', FILTER_VALIDATE_INT);
$idContratista = obtenerIdContratistaContexto();

if (!$Folio) {
    http_response_code(400);
    echo '<div class="alert alert-warning mb-0">El folio no es valido.</div>';
    exit;
}

if (esAdministradorContratista() && $idContratista === 0) {
    $idContratista = filter_input(INPUT_POST, 'contratista', FILTER_VALIDATE_INT);
}

if (!$idContratista || $idContratista < 1) {
    http_response_code(400);
    echo '<div class="alert alert-warning mb-0">El contratista de la salida no es valido.</div>';
    exit;
}

$modal = new Salidas();
$salidas_det = $modal->SalidasDet($Folio, $idContratista) ?? [];
?>

<div class="salidas-detalle-contenido">
    <!-- BOTON DE EXCEL -->
    <div id="salidas-det-buttons-container" class="mb-3"></div>

    <div id="salidas-det-table-wrapper">
        <table class="table table-bordered display nowrap salidas-data-table" id="salidasDetalleTabla">
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
            <tbody>
                <?php foreach ($salidas_det as $salida): ?>
                    <tr>
                        <td><?= htmlspecialchars($salida['Tecnico'] ?? '') ?></td>
                        <td><?= htmlspecialchars($salida['Producto'] ?? '') ?></td>
                        <td><?= htmlspecialchars($salida['Descripcion'] ?? '') ?></td>
                        <td><?= htmlspecialchars($salida['Tecnologia'] ?? '') ?></td>
                        <td><?= htmlspecialchars($salida['Modelo'] ?? '') ?></td>
                        <td><?= htmlspecialchars($salida['Num_Serie_Salida_Det'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="salidas-det-cards-container"></div>
</div>

<script>
(function () {
    const salidas = <?= json_encode($salidas_det) ?>;

    const tarjetas = $('#salidas-det-cards-container');

    salidas.forEach(function (salida, indice) {
        const tarjeta =
            '<div class="salidas-det-card">' +
                '<div class="salidas-det-card-header">' +
                    '<i class="fas fa-tools"></i>' +
                    '<span class="font-weight-bold salidas-det-card-product">' + (salida.Producto || '') + '</span>' +
                    '<span class="badge-folio">#' + (indice + 1) + '</span>' +
                '</div>' +
                '<div class="salidas-det-card-body">' +
                    '<div class="salidas-det-field"><span>Tecnico</span><strong>' + (salida.Tecnico || '') + '</strong></div>' +
                    '<div class="salidas-det-field"><span>SKU</span><strong>' + (salida.Modelo || '') + '</strong></div>' +
                    '<div class="salidas-det-field"><span>Numero de Serie</span><strong>' + (salida.Num_Serie_Salida_Det || '') + '</strong></div>' +
                '</div>' +
            '</div>';

        tarjetas.append(tarjeta);
    });

    if (typeof DataTable !== 'undefined' && salidas.length > 0) {
        const tabla = new DataTable('#salidasDetalleTabla', {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: true,
            searching: true,
            paging: true,
            scrollX: true,
            pageLength: 10,
            initComplete: function () {
                this.api().columns.adjust();
            }
        });

        const botones = new DataTable.Buttons(tabla, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success btn-sm'
                }
            ]
        });

        botones.container().appendTo('#salidas-det-buttons-container');
    }
}());
</script>