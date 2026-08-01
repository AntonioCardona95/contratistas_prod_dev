<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassOps.php");
    $modal = new Ops();

    $folio = (int)($_GET['folio'] ?? 0);

    $materiales = $modal->ObtenerDetalleMateriales($folio);
    $series = $modal->ObtenerDetalleSeries($folio);
?>

<style>
    .detalle-card-container {
        display: none;
        gap: 18px;
        margin-top: 15px;
    }

    .detalle-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,0.08);
        box-shadow: 0 12px 28px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .detalle-card-header {
        padding: 10px;
        background: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .detalle-card-header .titulo {
        font-size: 1rem;
        font-weight: 700;
        color: #212529;
    }

    .detalle-card-body {
        padding: 10px;
        display: grid;
        gap: 12px;
    }

    .detalle-card-field {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        border-bottom: 1px solid #e9ecef;
    }

    .detalle-card-field:last-child {
        border-bottom: none;
    }

    .detalle-card-field .label {
        color: #6c757d;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        flex: 1;
    }

    .detalle-card-field .value {
        color: #212529;
        font-weight: 600;
        text-align: right;
        min-width: 110px;
    }

    @media (max-width: 768px) {
        .detalle-table-wrapper {
            display: none;
        }

        .detalle-card-container {
            display: grid;
        }

        a{
            font-size: 15px !important;
        }

        .detalle-card-field .value {
            font-size: 15px;
        }
    }
</style>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#tab-materiales-<?= $folio ?>" role="tab">
            <i class="fas fa-box"></i> Materiales
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#tab-series-<?= $folio ?>" role="tab">
            <i class="fas fa-barcode"></i> Números de Serie <?= !empty($series) ? '(' . count($series) . ')' : '' ?>
        </a>
    </li>
</ul>

<div class="tab-content">

    <!-- TAB MATERIALES -->
    <div class="tab-pane fade show active" id="tab-materiales-<?= $folio ?>" role="tabpanel">
        <div class="detalle-table-wrapper">
            <table class="table table-bordered table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Producto</th>
                        <th>Modelo</th>
                        <th>Tecnología</th>
                        <th>Presentación</th>
                        <th>Total Piezas</th>
                        <th>Total Metros</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($materiales)): foreach ($materiales as $m): ?>
                        <tr>
                            <td><?= strtoupper($m['Producto'] ?? '') ?></td>
                            <td><?= strtoupper($m['Modelo'] ?? '') ?></td>
                            <td><?= strtoupper($m['Tecnologia'] ?? '') ?></td>
                            <td><?= strtoupper($m['Presentacion'] ?? '') ?></td>
                            <td><?= $m['Total_Piezas'] ?? 0 ?></td>
                            <td><?= $m['Total_Metros'] ?? 0 ?></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="6" class="text-center text-muted">Sin materiales registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="detalle-card-container">
            <?php if (!empty($materiales)): foreach ($materiales as $m): ?>
                <div class="detalle-card">
                    <div class="detalle-card-header">
                        <span class="titulo"><?= strtoupper($m['Producto'] ?? '') ?></span>
                        <span class="value"><?= strtoupper($m['Modelo'] ?? '') ?></span>
                    </div>
                    <div class="detalle-card-body">
                        <div class="detalle-card-field">
                            <span class="label">Tecnología</span>
                            <strong class="value"><?= strtoupper($m['Tecnologia'] ?? '') ?></strong>
                        </div>
                        <div class="detalle-card-field">
                            <span class="label">Presentación</span>
                            <strong class="value"><?= strtoupper($m['Presentacion'] ?? '') ?></strong>
                        </div>
                        <div class="detalle-card-field">
                            <span class="label">Total Piezas</span>
                            <strong class="value"><?= $m['Total_Piezas'] ?? 0 ?></strong>
                        </div>
                        <div class="detalle-card-field">
                            <span class="label">Total Metros</span>
                            <strong class="value"><?= $m['Total_Metros'] ?? 0 ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="detalle-card">
                    <div class="detalle-card-body text-center text-muted">Sin materiales registrados.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TAB SERIES -->
    <div class="tab-pane fade" id="tab-series-<?= $folio ?>" role="tabpanel">
        <?php if (!empty($series)): ?>
            <div class="detalle-table-wrapper">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Número de Serie</th>
                            <th>Producto</th>
                            <th>Modelo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($series as $s): ?>
                            <tr>
                                <td><?= strtoupper($s['Num_Serie_S'] ?? '') ?></td>
                                <td><?= strtoupper($s['Producto'] ?? '') ?></td>
                                <td><?= strtoupper($s['Modelo'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="detalle-card-container">
                <?php foreach ($series as $s): ?>
                    <div class="detalle-card">
                        <div class="detalle-card-header">
                            <span class="value"><?= strtoupper($s['Producto'] ?? '') ?></span>
                            <span class="label">Modelo: <strong class="value"><?= strtoupper($s['Modelo'] ?? '') ?></strong></span>
                        </div>
                        <div class="detalle-card-body">
                            <div class="detalle-card-field">
                                <span class="titulo">Serie: <?= strtoupper($s['Num_Serie_S'] ?? '') ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Esta salida no tiene números de serie asociados (material sin trazabilidad individual).</p>
        <?php endif; ?>
    </div>

</div>