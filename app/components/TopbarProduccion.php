<?php
// Topbar shared component. Works when included from app/almacen/* or app/ordenes/*
if (session_status() === PHP_SESSION_NONE) session_start();
$Contratista = $_SESSION['Contratista'] ?? 'Usuario';

// Resolver valores de fecha y tipo de tarea: prioridad -> $topbar_defaults (si la vista lo define),
// luego variables locales usadas antiguamente ($defaultFecha1/$defaultFecha2/$defaultTipoTarea),
// luego POST y finalmente la fecha actual.
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

<!-- Incluir Select2 (CSS y JS) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
/* Estilos para el contenedor de filtros justo abajito del Topbar */
.card-filtros-container {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.05);
}
.card-filtros-container .card-header-filtros {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    padding: 0.75rem 1.25rem;
    font-weight: bold;
    color: #4e73df;
    font-size: 0.95rem;
}
.card-filtros-container .filter-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #4e73df;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
}

/* Personalización de Select2 para integrarse con Bootstrap */
.select2-container--default .select2-selection--single {
    background-color: #ffffff !important;
    border: 1px solid #d1d3e2 !important;
    height: calc(1.5em + .75rem + 2px) !important;
    padding: .375rem .75rem !important;
    border-radius: .35rem !important;
    display: flex !important;
    align-items: center !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #6e707e !important;
    padding-left: 0 !important;
    line-height: normal !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
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

<!-- Formulario principal de consulta -->
<form id="FormQueryUnidades" class="mw-100" onsubmit="event.preventDefault(); if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();">

    <!-- Topbar (Respetando tu estructura base intacta) -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" type="button">
            <i class="fa fa-bars"></i>
        </button>

        <?php if (!$topbar_hide_search): ?>
            <!-- Topbar Search -->
            <div class="d-flex form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search topbar-search-form">
                <div class="input-group topbar-date-group">
                    <!-- Se quitaron los eventos onchange para no disparar la búsqueda al seleccionar la primera fecha -->
                    <input type="date" name="Fecha1" id="Fecha1" value="<?= htmlspecialchars($tbFecha1) ?>" 
                           class="form-control bg-light border-0 small mr-2 topbar-date-input" 
                           aria-label="DESDE" aria-describedby="basic-addon2">

                    <input type="date" name="Fecha2" id="Fecha2" value="<?= htmlspecialchars($tbFecha2) ?>" 
                           class="form-control bg-light border-0 small mr-2 topbar-date-input" 
                           aria-label="HASTA" aria-describedby="basic-addon2">

                    <div class="input-group-append mr-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
                <?php if(!empty($topbar_actions)): ?>
                    <div class="topbar-actions-container d-flex align-items-center ml-2">
                        <?= $topbar_actions ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php if(!empty($topbar_actions)): ?>
                <div class="topbar-actions-container d-flex align-items-center ml-2 mr-auto">
                    <?= $topbar_actions ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </nav>

    <?php if (!$topbar_hide_search): ?>
        <!-- Contenedor adicional JUSTO ABAJITO del Topbar (Filtros de Búsqueda) -->
        <div class="card card-filtros-container mb-4">
            <div class="card-header-filtros d-flex align-items-center">
                <i class="fas fa-filter mr-2"></i> Filtros de búsqueda
            </div>
            <div class="card-body bg-white py-3">
                <div class="row">
                    <!-- Columna para el Option Select de Tipo de Tarea -->
                    <div class="col-md-4 col-sm-6">
                        <label class="filter-label">Seleccione Tipo de tarea</label>
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
        </div>
    <?php endif; ?>

</form>

<!-- Scripts de inicialización de Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        $('#TipoTarea').select2({
            placeholder: "Escriba tipo de tarea...",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                }
            }
        });

        // Opcional: Desencadenar la búsqueda al cambiar el select
        $('#TipoTarea').on('change', function() {
            var fnName = '<?= $onchange_fn ?>';
            if (typeof window[fnName] === 'function') {
                window[fnName]();
            }
        });
    }
});
</script>