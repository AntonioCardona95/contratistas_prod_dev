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

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <?php if (!$topbar_hide_search): ?>
        <!-- Topbar Search -->
        <form id="FormQueryUnidades" class="d-flex form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search topbar-search-form" onsubmit="event.preventDefault(); if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();">
            <div class="input-group topbar-date-group">
                
                <!-- Campos de fecha primero -->
                <input type="date" name="Fecha1" id="Fecha1" value="<?= htmlspecialchars($tbFecha1) ?>" 
                       class="form-control bg-light border-0 small mr-2 topbar-date-input" 
                       aria-label="DESDE" aria-describedby="basic-addon2">

                <input type="date" name="Fecha2" id="Fecha2" value="<?= htmlspecialchars($tbFecha2) ?>" 
                       class="form-control bg-light border-0 small mr-2 topbar-date-input" 
                       aria-label="HASTA" aria-describedby="basic-addon2">

                <!-- Botón de búsqueda (Lupita) -->
                <div class="input-group-append mr-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>

                <!-- Select buscador para Tipo de tarea (después de la lupita) -->
                <input list="listaTipoTarea" name="TipoTarea" id="TipoTarea" value="<?= htmlspecialchars($tbTipoTarea) ?>" 
                       class="form-control bg-light border-0 small mr-2 topbar-select-input" 
                       placeholder="Escriba para buscar tipo de tarea..." style="max-width: 230px;" autocomplete="off">
                
                <datalist id="listaTipoTarea">
                    <option value="QUEJAS"></option>
                    <option value="ALTAS"></option>
                    <option value="MIGRACIONES"></option>
                    <option value="A4"></option>
                    <option value="CAMBIOS DE DOMICILIO"></option>
                </datalist>

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