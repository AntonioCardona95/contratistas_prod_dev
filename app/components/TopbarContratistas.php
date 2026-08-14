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
?>

<!-- ESTILOS RESPONSIVOS PARA TOPBAR MEJORADA EN MÓVILES -->
<style>
    .topbar {
        padding: 0.75rem 1rem;
    }

    .topbar-search-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        width: 100%;
        align-items: center;
    }

    .topbar-date-input {
        min-width: 140px;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.875rem;
    }

    .topbar-search-btn {
        padding: 0.5rem 0.75rem !important;
        font-size: 0.875rem;
    }

    .topbar-actions-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        width: 100%;
    }

    /* DESKTOP: TODO EN UNA LÍNEA */
    @media (min-width: 992px) {
        .topbar-search-wrapper {
            flex-wrap: nowrap;
            width: auto;
            margin-right: auto;
            margin-left: 1rem;
        }

        .topbar-actions-container {
            width: auto;
            margin-left: 0.5rem;
        }

        .topbar-date-input {
            min-width: 150px;
            font-size: 0.9rem;
        }
    }

    /* TABLET: DOS LÍNEAS */
    @media (min-width: 768px) and (max-width: 991px) {
        .topbar-search-wrapper {
            margin-left: 1rem;
            flex-basis: 100%;
        }

        .topbar-actions-container {
            flex-basis: 100%;
        }

        .topbar {
            padding: 1rem;
        }
    }

    /* MÓVIL: MÚLTIPLES LÍNEAS */
    @media (max-width: 767px) {
        .topbar {
            padding: 1rem 0.75rem;
            flex-wrap: wrap;
        }

        .navbar {
            flex-wrap: wrap !important;
        }

        .topbar-search-wrapper {
            order: 2;
            flex-basis: 100%;
            margin-top: 0.75rem;
        }

        .topbar .navbar-nav {
            order: 3;
            flex-basis: 100%;
            flex-direction: row;
            justify-content: flex-end;
            margin-top: 0.75rem;
        }

        .topbar-date-input {
            min-width: 120px;
            font-size: 0.8rem;
            flex: 1 1 calc(50% - 0.25rem);
        }

        .topbar-actions-container {
            flex-basis: 100%;
            margin-top: 0.5rem;
        }

        .topbar-search-btn {
            flex: 1;
        }
    }
</style>

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search / Filters -->
    <form id="FormQueryUnidades" class="topbar-search-wrapper w-100 w-lg-auto">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; flex: 1; align-items: center;">
            <input type="date" name="Fecha1" id="Fecha1" value="<?= htmlspecialchars($tbFecha1) ?>" class="form-control bg-light border-0 topbar-date-input" onchange="if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();" aria-label="Desde" title="Desde">
            <input type="date" name="Fecha2" id="Fecha2" value="<?= htmlspecialchars($tbFecha2) ?>" class="form-control bg-light border-0 topbar-date-input" onchange="if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();" aria-label="Hasta" title="Hasta">
            <button class="btn btn-primary topbar-search-btn" type="button" onclick="if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();" title="Buscar">
                <i class="fas fa-search fa-sm"></i>
            </button>
        </div>
        <?php if(!empty($topbar_actions)): ?>
            <div class="topbar-actions-container">
                <?= $topbar_actions ?>
            </div>
        <?php endif; ?>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto align-items-center">

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
    function closeMobileSidebar(){
        var sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;
        document.body.classList.remove('mobile-sidebar-open');
        sidebar.classList.remove('mobile-open');
    }

    document.addEventListener('click', function(event){
        if (!document.body.classList.contains('mobile-sidebar-open')) return;
        var sidebar = document.querySelector('.sidebar');
        var sidebarToggleTop = document.getElementById('sidebarToggleTop');
        if (!sidebar) return;
        if (sidebar.contains(event.target)) return;
        if (sidebarToggleTop && sidebarToggleTop.contains(event.target)) return;
        closeMobileSidebar();
    });

    function attachMobileSidebarHandler() {
        var sidebarToggleTop = document.getElementById('sidebarToggleTop');
        var sidebar = document.querySelector('.sidebar');
        if (!sidebarToggleTop || !sidebar) return;
        sidebarToggleTop.addEventListener('click', function(event){
            if (window.innerWidth >= 768) return;
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            if (document.body.classList.contains('mobile-sidebar-open')) {
                closeMobileSidebar();
            } else {
                document.body.classList.add('mobile-sidebar-open');
                sidebar.classList.add('mobile-open');
            }
        });

        var sidebarCloseBtn = document.getElementById('sidebarToggle');
        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', function(event){
                if (window.innerWidth >= 768) return;
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                closeMobileSidebar();
            });
        }
    }

    attachMobileSidebarHandler();
    document.addEventListener('DOMContentLoaded', attachMobileSidebarHandler);
})();
</script>

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
