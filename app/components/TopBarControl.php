<?php
// Topbar shared component. Works when included from app/almacen/* or app/ordenes/*
if (session_status() === PHP_SESSION_NONE) session_start();
$Contratista = $_SESSION['Contratista'] ?? 'Usuario';

// Resolver valores de fecha: prioridad -> $topbar_defaults (si la vista lo define),
// luego variables locales usadas antiguamente ($defaultFecha1/$defaultFecha2),
// luego POST y finalmente la fecha actual.
$tbFecha1 = null;
$tbFecha2 = null;
if (!empty($topbar_defaults) && is_array($topbar_defaults)) {
    $tbFecha1 = $topbar_defaults['Fecha1'] ?? null;
    $tbFecha2 = $topbar_defaults['Fecha2'] ?? null;
}
if (empty($tbFecha1)) $tbFecha1 = $defaultFecha1 ?? ($_POST['Fecha1'] ?? '');
if (empty($tbFecha2)) $tbFecha2 = $defaultFecha2 ?? ($_POST['Fecha2'] ?? '');

// Nombre de la función JS que se debe invocar al cambiar las fechas o al buscar.
$onchange_fn = !empty($topbar_onchange) ? $topbar_onchange : 'LoadEntradas';
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
        <form id="FormQueryUnidades" class="d-flex form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search topbar-search-form">
            <div class="input-group topbar-date-group">
                <input type="date" name="Fecha1" id="Fecha1" value="<?= htmlspecialchars($tbFecha1) ?>" class="form-control bg-light border-0 small mr-2 topbar-date-input" onchange="if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();" aria-label="AL" aria-describedby="basic-addon2">
                <input type="date" name="Fecha2" id="Fecha2" value="<?= htmlspecialchars($tbFecha2) ?>" class="form-control bg-light border-0 small mr-2 topbar-date-input" onchange="if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();" aria-label="AL" aria-describedby="basic-addon2">
                <div class="input-group-append mr-2">
                    <button class="btn btn-primary" type="button" onclick="if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
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

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto align-items-center topbar-icons">

        <!-- NOTIFICACIONES (CAMPANA) -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span id="alertsCount" class="badge badge-danger badge-counter">0</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-bell mr-1"></i> Notificaciones</span>
                    <span id="alertsCountHeader" class="badge badge-light text-primary" style="display:none;"></span>
                </h6>
                <div id="alertsList" class="notif-list-scroll">
                    <div class="text-center small text-gray-500 py-4">
                        <i class="fas fa-inbox fa-2x mb-2 d-block text-gray-300"></i>
                        No hay notificaciones
                    </div>
                </div>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($Contratista);?></span>
                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    PERFIL
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    CERRAR SESION
                </a>
            </div>
        </li>

    </ul>

</nav>
<!-- End of Topbar -->



<script>
(function(){
    // Ajusta el margin-left de #content-wrapper según el ancho actual de la sidebar
    // Evita forzar un margin inline cuando la sidebar está explícitamente colapsada
    function adjustContentForSidebar(){
        var sidebar = document.querySelector('.sidebar');
        var content = document.getElementById('content-wrapper');
        if(!sidebar || !content) return;

        // En pantallas grandes, tomar el ancho real de la sidebar, excepto
        // cuando la sidebar esté colapsada (clase "toggled") o el body tenga
        // la clase `sidebar-toggled`. En esos casos dejamos que las reglas CSS
        // manejen el espacio (no aplicar margin inline).
        if(window.innerWidth >= 768){
            if (sidebar.classList.contains('toggled') || document.body.classList.contains('sidebar-toggled')) {
                content.style.marginLeft = '';
                return;
            }

            var w = Math.round(sidebar.getBoundingClientRect().width) || 0;
            // si el ancho es muy pequeño (0) dejamos que CSS controle el layout
            if (w <= 0) {
                content.style.marginLeft = '';
            } else {
                content.style.marginLeft = w + 'px';
            }
        } else {
            content.style.marginLeft = '';
        }
    }

    // Ejecutar en eventos relevantes
    window.addEventListener('resize', adjustContentForSidebar);
    document.addEventListener('DOMContentLoaded', adjustContentForSidebar);
    setTimeout(adjustContentForSidebar, 80);

    // Observar cambios de tamaño de la sidebar (cuando existan)
    try{
        var sidebarEl = document.querySelector('.sidebar');
        if(window.ResizeObserver && sidebarEl){
            var ro = new ResizeObserver(function(){ adjustContentForSidebar(); });
            ro.observe(sidebarEl);
        }
    }catch(e){ /* ignore */ }
})();
</script>