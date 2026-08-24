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

$Folio = filter_input(INPUT_POST, 'folio', FILTER_VALIDATE_INT);

if (!$Folio) {
    http_response_code(400);
    echo '<div class="alert alert-warning mb-0">El folio no es valido.</div>';
    exit;
}

$modal = new Ops();
$salidas_det = $modal->SalidasDet($Folio) ?? [];
?>

<style>
    .dataTables_wrapper .dt-buttons .btn {
        margin-right: .25rem;
    }

    #salidas-det-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    #salidas-det-cards-container {
        display: none;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .salidas-det-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .salidas-det-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    .salidas-det-card-header {
        background: linear-gradient(90deg, #0f172a 0%, #1a6630 100%);
        color: #fff;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .salidas-det-card-header .badge-folio {
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 4px 10px;
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .5px;
    }

    .salidas-det-card-body {
        padding: 0 18px 18px;
    }

    .salidas-det-field {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 10px 0;
        border-bottom: 1px solid #f1f3f5;
        gap: 10px;
    }

    .salidas-det-field:last-child {
        border-bottom: none;
    }

    .salidas-det-field span {
        color: #6c757d;
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .3px;
        flex: 1;
        white-space: nowrap;
    }

    .salidas-det-field strong {
        color: #1f2937;
        font-size: .92rem;
        font-weight: 600;
        text-align: right;
        min-width: 100px;
        word-break: break-word;
    }

    @media (max-width: 1200px) {
        #salidas-det-cards-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        #salidas-det-table-wrapper {
            display: none;
        }

        #salidas-det-cards-container {
            display: grid;
            grid-template-columns: 1fr;
        }

        .salidas-det-field {
            flex-direction: column;
        }

        .salidas-det-field strong {
            text-align: left;
            width: 100%;
        }
    }
</style>

<div class="salidas-detalle-contenido">
    <div id="salidas-det-table-wrapper">
        <table class="table table-bordered display nowrap" style="width:100%" id="salidasDetalleTabla">
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
                    '<span class="font-weight-bold" style="flex:1">' + (salida.Producto || '') + '</span>' +
                    '<span class="badge-folio">#' + (indice + 1) + '</span>' +
                '</div>' +
                '<div class="salidas-det-card-body">' +
                    '<div class="salidas-det-field"><span>Tecnico</span><strong>' + (salida.Tecnico || '') + '</strong></div>' +
                    '<div class="salidas-det-field"><span>Detalle</span><strong>' + (salida.Descripcion || '') + '</strong></div>' +
                    '<div class="salidas-det-field"><span>Tecnologia</span><strong>' + (salida.Tecnologia || '') + '</strong></div>' +
                    '<div class="salidas-det-field"><span>SKU</span><strong>' + (salida.Modelo || '') + '</strong></div>' +
                    '<div class="salidas-det-field"><span>Numero de Serie</span><strong>' + (salida.Num_Serie_Salida_Det || '') + '</strong></div>' +
                '</div>' +
            '</div>';

        tarjetas.append(tarjeta);
    });

    if (typeof DataTable !== 'undefined' && salidas.length > 0) {
        const tabla = new DataTable('#salidasDetalleTabla', {
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
            },
            ordering: false,
            searching: true,
            paging: true,
            scrollX: true,
            pageLength: 10
        });

        const botones = new DataTable.Buttons(tabla, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i>',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success btn-sm'
                }
            ]
        });

        botones.container().prependTo(tabla.table().container());
    }
}());
</script>