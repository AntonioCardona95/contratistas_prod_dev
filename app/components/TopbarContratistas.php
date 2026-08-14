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

<style>
    /* ESTILOS RESPONSIVOS PARA TOPBAR */
    .topbar {
        padding: 0 !important;
    }

    .topbar-search {
        flex-wrap: wrap;
    }

    .topbar-date-input {
        flex: 0 1 auto;
        min-width: 140px !important;
    }

    /* FILTROS DESKTOP */
    .topbar-filters-desktop {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    /* FILTROS MÓVIL */
    .topbar-filters-mobile {
        display: none;
        width: 100%;
        padding: 12px;
        background: #f8f9fa;
        border-top: 1px solid #e3e6f0;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* BOTÓN TOGGLE FILTROS MÓVIL */
    .btn-toggle-filters {
        display: none;
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }

    /* ACCIONES DEL TOPBAR */
    .topbar-actions-container {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
        margin-left: auto;
    }

    .topbar-actions-container .btn {
        white-space: nowrap;
        font-size: 0.85rem;
        padding: 0.375rem 0.75rem;
    }

    /* MEDIA QUERIES - DISPOSITIVOS MEDIANOS Y GRANDES */
    @media (min-width: 576px) {
        .topbar-filters-desktop {
            display: flex;
        }
        .btn-toggle-filters {
            display: none;
        }
        .topbar-filters-mobile {
            display: none !important;
        }
    }

    /* MEDIA QUERIES - DISPOSITIVOS PEQUEÑOS */
    @media (max-width: 575px) {
        /* En móvil, mostrar solo un toggle para filtros */
        .topbar-search {
            width: 100%;
            margin: 0 !important;
            padding: 0.5rem;
            gap: 8px;
        }

        .topbar-filters-desktop {
            display: none;
        }

        .btn-toggle-filters {
            display: inline-flex;
            gap: 4px;
            align-items: center;
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
        }

        .topbar-filters-mobile {
            display: none;
            flex-wrap: wrap;
            gap: 8px;
            width: 100%;
            padding: 10px 0.5rem;
            background: #f8f9fa;
            border-top: 1px solid #e3e6f0;
        }

        .topbar-filters-mobile.show {
            display: flex !important;
        }

        .topbar-filters-mobile .form-control {
            flex: 0 1 auto;
            min-width: 100px;
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
        }

        .topbar-filters-mobile .btn {
            flex: 0 1 auto;
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .topbar-actions-container {
            display: flex;
            gap: 6px;
            margin-left: auto;
            margin-right: 0.5rem;
        }

        .topbar-actions-container .btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .navbar-search {
            margin-right: 0 !important;
        }
    }
</style>

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search (Desktop) -->
    <form id="FormQueryUnidades" class="d-none d-sm-flex form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="topbar-filters-desktop">
            <input type="date" name="Fecha1" id="Fecha1" value="<?= htmlspecialchars($tbFecha1) ?>" class="form-control bg-light border-0 small topbar-date-input" onchange="if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();" aria-label="Desde" aria-describedby="basic-addon2">
            <input type="date" name="Fecha2" id="Fecha2" value="<?= htmlspecialchars($tbFecha2) ?>" class="form-control bg-light border-0 small topbar-date-input" onchange="if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();" aria-label="Hasta" aria-describedby="basic-addon2">
            <button class="btn btn-primary btn-sm" type="button" onclick="if(typeof window['<?= $onchange_fn ?>']=='function') window['<?= $onchange_fn ?>']();">
                <i class="fas fa-search fa-sm"></i>
            </button>
        </div>
        <?php if(!empty($topbar_actions)): ?>
            <div class="topbar-actions-container d-flex align-items-center">
                <?= $topbar_actions ?>
            </div>
        <?php endif; ?>
    </form>

    <!-- Topbar Search (Mobile) -->
    <form id="FormQueryUnidadesMobile" class="d-sm-none d-flex form-inline navbar-search" style="width: 100%; gap: 8px;">
        <button type="button" class="btn-toggle-filters" onclick="toggleMobileFilters();">
            <i class="fas fa-filter"></i>
            <span>Filtros</span>
        </button>
        
        <?php if(!empty($topbar_actions)): ?>
            <div class="topbar-actions-container">
                <?= $topbar_actions ?>
            </div>
        <?php endif; ?>
    </form>

    <!-- Filtros Móvil (Colapsables) -->
    <div class="topbar-filters-mobile" id="mobileFiltersPanel">
        <input type="date" name="Fecha1Mobile" id="Fecha1Mobile" value="<?= htmlspecialchars($tbFecha1) ?>" class="form-control bg-light border-0 small topbar-date-input" aria-label="Desde">
        <input type="date" name="Fecha2Mobile" id="Fecha2Mobile" value="<?= htmlspecialchars($tbFecha2) ?>" class="form-control bg-light border-0 small topbar-date-input" aria-label="Hasta">
        <button class="btn btn-primary btn-sm" type="button" onclick="aplicarFiltrosMobile();">
            <i class="fas fa-search fa-sm"></i> Buscar
        </button>
    </div>

    <!-- Topbar Navbar (Derecha) -->
    <ul class="navbar-nav ml-auto align-items-center d-none d-sm-flex">

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
function toggleMobileFilters() {
    const panel = document.getElementById('mobileFiltersPanel');
    if (panel) {
        panel.classList.toggle('show');
    }
}

function aplicarFiltrosMobile() {
    const fecha1Mobile = document.getElementById('Fecha1Mobile');
    const fecha2Mobile = document.getElementById('Fecha2Mobile');
    const fecha1 = document.getElementById('Fecha1');
    const fecha2 = document.getElementById('Fecha2');
    
    if (fecha1Mobile && fecha1) {
        fecha1.value = fecha1Mobile.value;
    }
    if (fecha2Mobile && fecha2) {
        fecha2.value = fecha2Mobile.value;
    }
    
    const funcName = '<?= $onchange_fn ?>';
    if (typeof window[funcName] === 'function') {
        window[funcName]();
    }
    
    // Cerrar panel
    toggleMobileFilters();
}

// Cerrar filtros al hacer clic fuera
document.addEventListener('click', function(e) {
    const panel = document.getElementById('mobileFiltersPanel');
    const btnToggle = document.querySelector('.btn-toggle-filters');
    if (panel && panel.classList.contains('show')) {
        if (!panel.contains(e.target) && !btnToggle.contains(e.target)) {
            panel.classList.remove('show');
        }
    }
});
</script>

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
