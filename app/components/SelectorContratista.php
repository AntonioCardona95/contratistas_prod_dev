<?php
require_once __DIR__ . '/../config/ContratistaContexto.php';

if (!esAdministradorContratista()) {
    return;
}

$selectorContratistas = obtenerContratistasParaSelector();
$selectorActual = obtenerIdContratistaContexto();
$selectorCallback = $selectorCallback ?? '';
?>
<style>
    .selector-contratista-wrap {
        width: 300px;
        max-width: 300px;
    }

    .selector-contratista-wrap .select2-container {
        width: 100% !important;
        max-width: 300px;
    }

    @media (max-width: 576px) {
        .selector-contratista-wrap {
            width: 100%;
            min-width: 0;
            max-width: 300px;
            margin-left: 0 !important;
            margin-top: 10px;
        }
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<div class="selector-contratista-wrap ml-3 mt-2 mt-sm-0">
    <label for="selectorContratista" class="sr-only">Contratista</label>
    <select id="selectorContratista" class="form-control form-control-sm" data-placeholder="Selecciona contratista">
        <option value="0" <?= $selectorActual === 0 ? 'selected' : '' ?>>Todos los contratistas</option>
        <?php foreach ($selectorContratistas as $selectorContratista): ?>
            <option value="<?= (int)$selectorContratista['idContratistas'] ?>" <?= $selectorActual === (int)$selectorContratista['idContratistas'] ? 'selected' : '' ?>>
                <?= htmlspecialchars(trim($selectorContratista['nombre'])) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if ($.fn.select2 && !$('#selectorContratista').hasClass('select2-hidden-accessible')) {
        $('#selectorContratista').select2({
            placeholder: 'Selecciona contratista',
            allowClear: true,
            width: '100%'
        });
    }

    $('#selectorContratista').on('change', function () {
        $.post('seleccionar_contratista.php', {
            idContratista: $(this).val()
        }).done(function (respuesta) {
            if (!respuesta || !respuesta.ok) {
                alert((respuesta && respuesta.mensaje) || 'No fue posible cambiar el contratista.');
                return;
            }
            <?php if ($selectorCallback !== ''): ?>
            if (typeof window[<?= json_encode($selectorCallback) ?>] === 'function') {
                window[<?= json_encode($selectorCallback) ?>]();
            }
            <?php else: ?>
            window.location.reload();
            <?php endif; ?>
        }).fail(function () {
            alert('No fue posible cambiar el contratista.');
        });
    });
});
</script>