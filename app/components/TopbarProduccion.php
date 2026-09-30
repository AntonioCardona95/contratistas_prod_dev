<?php
// Topbar shared component. Works when included from app/almacen/* or app/ordenes/*
if (session_status() === PHP_SESSION_NONE) session_start();
$Contratista = $_SESSION['Contratista'] ?? 'Usuario';

// Resolver valores de fecha y tipo de tarea: prioridad -> $topbar_defaults (si la vista lo define),
// luego variables locales usadas antiguamente ($defaultFecha1/$defaultFecha2/$defaultTipoTarea),
// luego POST.
$tbFecha1 = null;
$tbFecha2 = null;
$tbTipoTarea = null;

if (!empty($topbar_defaults) && is_array($topbar_defaults)) {
    $tbFecha1 = $topbar_defaults['Fecha1'] ?? null;
    $tbFecha2 = $topbar_defaults['Fecha2'] ?? null;
    $tbTipoTarea = $topbar_defaults['TipoTarea'] ?? null;
}
if (empty($tbFecha1)) $tbFecha1 = $defaultFecha1 ?? ($_POST['Fecha1'] ?? '');
if (empty($tbFecha2)) $tbFecha2 = $defaultFecha2 ?? ($_POST['Fecha2'] ?? '');
if (empty($tbTipoTarea)) $tbTipoTarea = $defaultTipoTarea ?? ($_POST['TipoTarea'] ?? '');

// Nombre de la función JS que se debe invocar al presionar la lupa o enviar el formulario.
$onchange_fn = !empty($topbar_onchange) ? $topbar_onchange : 'LoadProduccion';
$topbar_hide_search = !empty($topbar_hide_search);
?>

<!-- Incluir Select2 (CSS y JS) si no están cargados previamente -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* Estilos para integrar Select2 perfectamente con el diseño de SB Admin 2 / Bootstrap */
.topbar-select2-wrapper {
    display: flex;
    flex-direction: column;
}
.topbar-select2-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #4e73df;
    margin-bottom: 2px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.topbar-select2-container .select2-container--default .select2-selection--single {
    background-color: #f8f9fc !important;
    border: 0 !important;
    height: calc(1.5em + .75rem + 2px) !important;
    padding: .375rem .75rem !important;
    border-radius: .35rem !important;
    display: flex !important;
    align-items: center !important;
}
.topbar-select2-container .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #6e707e !important;
    padding-left: 0 !important;
    line-height: normal !important;
}
.topbar-select2-container .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100% !important;
    right: 8px !important;
}
.select2-dropdown {
    border: 1px solid #d1d3e2 !important;
    border-radius: .35rem !important;
    box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
    z-index: 1050 !important;
}
</style>

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <?php if (!$topbar_hide_search): ?>
        <!-- Topbar Search -->
        <form id="FormQueryUnidades" class="d-flex form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search topbar-search-form" onsubmit="event.preventDefault(); if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();">
            <div class="input-group topbar-date-group align-items-end">
                
                <!-- Campos de fecha primero -->
                <input type="date" name="Fecha1" id="Fecha1" value="<?= htmlspecialchars($tbFecha1) ?>" 
                       class="form-control bg-light border-0 small mr-2 topbar-date-input" 
                       aria-label="DESDE" aria-describedby="basic-addon2">

                <input type="date" name="Fecha2" id="Fecha2" value="<?= htmlspecialchars($tbFecha2) ?>" 
                       class="form-control bg-light border-0 small mr-2 topbar-date-input" 
                       aria-label="HASTA" aria-describedby="basic-addon2">

                <!-- Botón de búsqueda (Lupita) -->
                <div class="input-group-append mr-3">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>

                <!-- Contenedor con título y Select2 (después de la lupita) -->
                <div class="topbar-select2-wrapper mr-2">
                    <label class="topbar-select2-label">Seleccione Tipo de tarea</label>
                    <div class="topbar-select2-container" style="width: 230px;">
                        <select name="TipoTarea" id="TipoTarea" class="form-control select2-topbar">
                            <option value=""></option>
                            <option value="QUEJAS" <?= $tbTipoTarea === 'QUEJAS' ? 'selected' : '' ?>>QUEJAS</option>
                            <option value="ALTAS" <?= $tbTipoTarea === 'ALTAS' ? 'selected' : '' ?>>ALTAS</option>
                            <option value="MIGRACIONES" <?= $tbTipoTarea === 'MIGRACIONES' ? 'selected' : '' ?>>MIGRACIONES</option>
                            <option value="A4" <?= $tbTipoTarea === 'A4' ? 'selected' : '' ?>>A4</option>
                            <option value="CAMBIOS DE DOMICILIO" <?= $tbTipoTarea === 'CAMBIOS DE DOMICILIO' ? 'selected' : '' ?>>CAMBIOS DE DOMICILIO</option>
                        </select>
                    </div>
                </div>

            </div>
            <?php if(!empty($topbar_actions)): ?>
                <div class="topbar-actions-container d-flex align-items-center ml-2">
                    <?= $topbar_actions ?>
                </div>
            <?php endif; ?>
        </form>
    <?php else: ?>
        <?php if(!empty($topbar_actions)): ?>
            <div class="topbar-actions-container d-flex align-items-center ml-2 mr-auto">
                <?= $topbar_actions ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</nav>

<!-- Scripts de inicialización de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        $('#TipoTarea').select2({
            placeholder: "Escriba tipo de tarea...",
            allowClear: true,
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            }
        });
    }
});
</script>