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

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form id="FormQueryUnidades" class="d-none d-sm-flex form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
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

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Notificaciones (campana) -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Contador de alertas -->
                <span id="alertsCount" class="badge badge-danger badge-counter">0</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Alertas</h6>
                <div id="alertsList">
                    <a class="dropdown-item text-center small text-gray-500">No hay alertas</a>
                </div>
                <a class="dropdown-item text-center small text-gray-500" href="../ordenes/AlertasGenerales.php">Ver todas</a>
            </div>
        </li>

        <!-- Buzón (sobre) -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <span id="messagesCount" class="badge badge-danger badge-counter">0</span>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">Buzón</h6>
                <div id="messagesList">
                    <a class="dropdown-item text-center small text-gray-500">No hay mensajes</a>
                </div>
                <a class="dropdown-item text-center small text-gray-500" href="#">Ver buzón</a>
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
// Intento simple de cargar contadores si existe el endpoint
(function(){
    function tryFetch(url, elCount, elList){
        fetch(url).then(r=>r.json()).then(data=>{
            if(data && typeof data.count !== 'undefined'){
                document.getElementById(elCount).textContent = data.count;
            }
            if(data && data.items){
                var html = '';
                data.items.slice(0,5).forEach(it=>{
                    html += '<a class="dropdown-item d-flex align-items-center" href="#">'+(it.title||'Item')+'<div class="small text-gray-500">'+(it.date||'')+'</div></a>';
                });
                if(html) document.getElementById(elList).innerHTML = html;
            }
        }).catch(e=>{});
    }
    // rutas relativas desde app/almacen o app/ordenes
    var base = window.location.pathname.replace(/\/[^/]*$/, '');
    // probar rutas comunes
    tryFetch(base + '/get_conteo_alertas.php', 'alertsCount', 'alertsList');
    tryFetch(base + '/../ordenes/get_conteo_alertas.php', 'alertsCount', 'alertsList');
})();

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
