<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../controller/ClassTraspasos.php');

if (empty($_SESSION['idContratistas'])) {
    http_response_code(401);
    echo '<div class="alert alert-warning mb-0">Sesion no valida.</div>';
    exit;
}

$Folio = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$Folio) {
    http_response_code(400);
    echo '<div class="alert alert-warning mb-0">El folio no es valido.</div>';
    exit;
}

$modal = new Traspasos();
$traspasos = $modal->TraspasoDet($Folio) ?? [];
?>

<div class="traspaso-detalle-contenido">
    <div id="traspasos-det-buttons-container" class="mb-3"></div>

    <div id="traspasos-det-table-wrapper">
        <table class="table table-bordered display nowrap traspasos-data-table" id="traspasosDetalle<?= $Folio ?>">
            <thead>
                <tr>
                    <th>Folio Traspaso</th>
                    <th>Contratista</th>
                    <th>Tecnico Origen</th>
                    <th>Tecnico Destino</th>
                    <th>Numero Serie</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($traspasos as $traspaso): ?>
                    <tr>
                        <td><?= htmlspecialchars($traspaso['Folio_Traspaso'] ?? '') ?></td>
                        <td><?= htmlspecialchars($traspaso['Contratista'] ?? '') ?></td>
                        <td><?= htmlspecialchars($traspaso['TecnicoOrigen'] ?? '') ?></td>
                        <td><?= htmlspecialchars($traspaso['TecnicoDestino'] ?? '') ?></td>
                        <td><span class="badge badge-primary"><?= htmlspecialchars($traspaso['Numero_Serie_Tras'] ?? '') ?></span></td>
                        <td><?= htmlspecialchars($traspaso['Fecha_Tras'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="traspasos-det-cards-container"></div>
</div>

<script>
(function () {
    const traspasos = <?= json_encode($traspasos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const folio = <?= json_encode((string)$Folio) ?>;
    const tarjetas = $('#traspasos-det-cards-container');

    function crearCampo(etiqueta, valor) {
        return '<div class="traspasos-det-field">' +
            '<span class="traspasos-det-field-label">' + etiqueta + '</span>' +
            '<strong class="traspasos-det-field-value">' + (valor || 'N/A') + '</strong>' +
            '</div>';
    }

    traspasos.forEach(function (traspaso, indice) {
        tarjetas.append(
            '<div class="traspasos-det-card">' +
                '<div class="traspasos-det-card-header">' +
                    '<h5><i class="fas fa-right-left"></i> Traspaso ' + (indice + 1) + '</h5>' +
                    '<span class="badge-folio">#' + (indice + 1) + '</span>' +
                '</div>' +
                '<div class="traspasos-det-card-body">' +
                    crearCampo('Folio', traspaso.Folio_Traspaso) +
                    crearCampo('Contratista', traspaso.Contratista) +
                    crearCampo('Tecnico origen', traspaso.TecnicoOrigen) +
                    crearCampo('Tecnico destino', traspaso.TecnicoDestino) +
                    crearCampo('Numero de serie', traspaso.Numero_Serie_Tras) +
                    crearCampo('Fecha', traspaso.Fecha_Tras) +
                '</div>' +
            '</div>'
        );
    });

    if (typeof DataTable !== 'undefined' && traspasos.length > 0) {
        const tabla = new DataTable('#traspasosDetalle' + folio, {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'},
            ordering: true,
            searching: true,
            paging: true,
            scrollX: true,
            pageLength: 10,
            initComplete: function () {
                this.api().columns.adjust();
            }
        });

        if (typeof DataTable.Buttons !== 'undefined') {
            const botones = new DataTable.Buttons(tabla, {
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success btn-sm'
                }]
            });
            botones.container().appendTo('#traspasos-det-buttons-container');
        }
    }
}());
</script>
