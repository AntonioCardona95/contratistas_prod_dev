<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassOps.php");
    $modal = new Ops();

    $folio = (int)($_GET['folio'] ?? 0);
    $soloLectura = isset($_GET['solo_lectura']) && $_GET['solo_lectura'] == 1;

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
        padding-bottom: 8px;
    }

    .detalle-card-field:last-child {
        border-bottom: none;
        padding-bottom: 0;
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

    .motivo-select { display: none; margin-top: 6px; }
    .fila-rechazada { background-color: #fff5f5; }
    .detalle-card.card-rechazada { border-color: #e74a3b; background-color: #fff8f8; }
    .detalle-card.card-aceptada-parcial { border-color: #f6c23e; }

    .input-cantidad-aceptada { max-width: 90px; display: inline-block; }

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

<form id="formRespuestaParcial" data-folio="<?= $folio ?>">

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

        <!-- VERSION DESKTOP: TABLA -->
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
                        <?php if (!$soloLectura): ?><th style="width:170px;">Cantidad Recibida</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($materiales)): foreach ($materiales as $m): ?>
                        <tr class="fila-linea" data-id-linea="<?= $m['idTransferencias'] ?>" data-cantidad-solicitada="<?= $m['Total_Piezas'] ?>">
                            <td><?= strtoupper($m['Producto'] ?? '') ?></td>
                            <td><?= strtoupper($m['Modelo'] ?? '') ?></td>
                            <td><?= strtoupper($m['Tecnologia'] ?? '') ?></td>
                            <td><?= strtoupper($m['Presentacion'] ?? '') ?></td>
                            <td><?= $m['Total_Piezas'] ?? 0 ?></td>
                            <td><?= $m['Total_Metros'] ?? 0 ?></td>
                            <?php if (!$soloLectura): ?>
                            <td>
                                <input type="number" class="form-control form-control-sm input-cantidad-aceptada"
                                    min="0" max="<?= $m['Total_Piezas'] ?>" value="<?= $m['Total_Piezas'] ?>">
                                <select class="form-control form-control-sm motivo-select-cantidad motivo-select">
                                    <option value="cantidad_incorrecta">Cantidad incorrecta / faltante</option>
                                </select>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="<?= $soloLectura ? 6 : 7 ?>" class="text-center text-muted">Sin materiales registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- VERSION MOVIL: CARDS -->
        <div class="detalle-card-container">
            <?php if (!empty($materiales)): foreach ($materiales as $m): ?>
                <div class="detalle-card fila-linea" data-id-linea="<?= $m['idTransferencias'] ?>" data-cantidad-solicitada="<?= $m['Total_Piezas'] ?>">
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
                        <?php if (!$soloLectura): ?>
                        <div class="detalle-card-field">
                            <span class="label">Cantidad Recibida</span>
                            <div class="text-right">
                                <input type="number" class="form-control form-control-sm input-cantidad-aceptada"
                                    min="0" max="<?= $m['Total_Piezas'] ?>" value="<?= $m['Total_Piezas'] ?>">
                                <select class="form-control form-control-sm motivo-select-cantidad motivo-select mt-1">
                                    <option value="cantidad_incorrecta">Cantidad incorrecta / faltante</option>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
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

            <?php if (!$soloLectura): ?>
            <div class="mb-2 d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success" id="btn-marcar-todas-aceptadas">
                    <i class="fas fa-check-double"></i> Marcar todas como correctas
                </button>
            </div>
            <?php endif; ?>

            <!-- VERSION DESKTOP: TABLA -->
            <div class="detalle-table-wrapper">
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Número de Serie</th>
                            <th>Producto</th>
                            <th>Modelo</th>
                            <?php if (!$soloLectura): ?><th style="width:200px;">Respuesta</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($series as $s): ?>
                            <tr class="fila-serie" data-serie="<?= htmlspecialchars($s['Num_Serie_S']) ?>">
                                <td><?= strtoupper($s['Num_Serie_S'] ?? '') ?></td>
                                <td><?= strtoupper($s['Producto'] ?? '') ?></td>
                                <td><?= strtoupper($s['Modelo'] ?? '') ?></td>
                                <?php if (!$soloLectura): ?>
                                <td>
                                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                        <label class="btn btn-outline-success btn-sm active">
                                            <input type="radio" name="serie_<?= md5($s['Num_Serie_S']) ?>" value="aceptado" checked> Correcta
                                        </label>
                                        <label class="btn btn-outline-danger btn-sm">
                                            <input type="radio" name="serie_<?= md5($s['Num_Serie_S']) ?>" value="rechazado"> No coincide
                                        </label>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- VERSION MOVIL: CARDS -->
            <div class="detalle-card-container">
                <?php foreach ($series as $s): ?>
                    <div class="detalle-card fila-serie" data-serie="<?= htmlspecialchars($s['Num_Serie_S']) ?>">
                        <div class="detalle-card-header">
                            <span class="value"><?= strtoupper($s['Producto'] ?? '') ?></span>
                            <span class="label">SKU: <strong class="value" style="color: #212529;"><?= strtoupper($s['Modelo'] ?? '') ?></strong></span>
                        </div>
                        <div class="detalle-card-body">
                            <div class="detalle-card-field">
                                <span class="titulo">Serie: <strong class="value" style="color: #212529;"><?= strtoupper($s['Num_Serie_S'] ?? '') ?></strong></span>
                            </div>
                            <?php if (!$soloLectura): ?>
                            <div class="detalle-card-field">
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label class="btn btn-outline-success btn-sm active">
                                        <input type="radio" name="serie_<?= md5($s['Num_Serie_S']) ?>" value="aceptado" checked> Correcta
                                    </label>
                                    <label class="btn btn-outline-danger btn-sm">
                                        <input type="radio" name="serie_<?= md5($s['Num_Serie_S']) ?>" value="rechazado"> No coincide
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <p class="text-muted text-center">Esta salida no tiene números de serie asociados (material sin trazabilidad individual).</p>
        <?php endif; ?>
    </div>

</div>

</form>

<?php if (!$soloLectura): ?>
<script>
    // MARCAR TODAS LAS SERIES COMO ACEPTADAS (AFECTA TABLA Y CARDS A LA VEZ, YA QUE COMPARTEN EL name DEL RADIO)
    $(document).off('click', '#btn-marcar-todas-aceptadas').on('click', '#btn-marcar-todas-aceptadas', function(){
        $('.fila-serie input[value="aceptado"]').prop('checked', true);
        $('.fila-serie .btn-group label').removeClass('active');
        $('.fila-serie input[value="aceptado"]').closest('label').addClass('active');
        $('.fila-serie').removeClass('card-rechazada');
    });

    // CUANDO SE ELIGE "NO COINCIDE" EN CUALQUIER VISTA, RESALTAR LA CARD (SI APLICA)
    $(document).off('change', '.fila-serie input[type="radio"]').on('change', '.fila-serie input[type="radio"]', function(){
        var fila = $(this).closest('.fila-serie');
        if ($(this).val() === 'rechazado') {
            fila.addClass('card-rechazada');
        } else {
            fila.removeClass('card-rechazada');
        }
    });

    // AL CAMBIAR LA CANTIDAD ACEPTADA (TABLA O CARD), MOSTRAR/OCULTAR EL MOTIVO
    $(document).off('input', '.input-cantidad-aceptada').on('input', '.input-cantidad-aceptada', function(){
        var fila = $(this).closest('.fila-linea');
        var solicitada = parseInt(fila.data('cantidad-solicitada'), 10);
        var aceptada = parseInt($(this).val(), 10) || 0;

        if (aceptada < solicitada) {
            fila.addClass('fila-rechazada card-aceptada-parcial');
            fila.find('.motivo-select-cantidad').show();
        } else {
            fila.removeClass('fila-rechazada card-aceptada-parcial');
            fila.find('.motivo-select-cantidad').hide();
        }
    });
</script>
<?php endif; ?>