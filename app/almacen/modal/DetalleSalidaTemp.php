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
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th>Producto</th>
                    <th>Modelo</th>
                    <th>Tecnología</th>
                    <th>Presentación</th>
                    <th>Cantidad</th>
                    <th>Piezas Sueltas</th>
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
                        <td><?= $m['Cantidad_S'] ?? 0 ?></td>
                        <td><?= $m['Piezas_Sueltas'] ?? 0 ?></td>
                        <td><?= $m['Total_Piezas'] ?? 0 ?></td>
                        <td><?= $m['Total_Metros'] ?? 0 ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="8" class="text-center text-muted">Sin materiales registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- TAB SERIES -->
    <div class="tab-pane fade" id="tab-series-<?= $folio ?>" role="tabpanel">
        <?php if (!empty($series)): ?>
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
        <?php else: ?>
            <p class="text-muted text-center">Esta salida no tiene números de serie asociados (material sin trazabilidad individual).</p>
        <?php endif; ?>
    </div>

</div>