<?php
// Topbar shared component. Works when included from app/almacen/* or app/ordenes/*
if (session_status() === PHP_SESSION_NONE) session_start();
$Contratista = $_SESSION['Contratista'] ?? 'Usuario';

// Resolver valores de fecha
$tbFecha1 = null;
$tbFecha2 = null;
if (!empty($topbar_defaults) && is_array($topbar_defaults)) {
    $tbFecha1 = $topbar_defaults['Fecha1'] ?? null;
    $tbFecha2 = $topbar_defaults['Fecha2'] ?? null;
}
if (empty($tbFecha1)) $tbFecha1 = $defaultFecha1 ?? ($_POST['Fecha1'] ?? '');
if (empty($tbFecha2)) $tbFecha2 = $defaultFecha2 ?? ($_POST['Fecha2'] ?? '');

// Nombre de la función JS a invocar
$onchange_fn = !empty($topbar_onchange) ? $topbar_onchange : 'LoadKpisControl';
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
            <div class="input-group align-items-center topbar-date-group">
                <input type="date" name="Fecha1" id="Fecha1" value="<?= htmlspecialchars($tbFecha1) ?>" 
                       class="form-control bg-light border-0 small mr-2 topbar-date-input" 
                       onchange="validarFechasBtn();" aria-label="DESDE">

                <input type="date" name="Fecha2" id="Fecha2" value="<?= htmlspecialchars($tbFecha2) ?>" 
                       class="form-control bg-light border-0 small mr-2 topbar-date-input" 
                       onchange="validarFechasBtn();" aria-label="HASTA">

                <!-- En el Topbar, actualizamos el grupo del botón -->
<div class="input-group-append mr-2 align-items-center position-relative">
    <button class="btn btn-primary font-weight-bold d-flex align-items-center" type="button" id="btnEjecutarProduccion" disabled
            onclick="ejecutarFiltradoProduccion();">
        <i class="fas fa-play fa-sm mr-1" id="iconEjecutar"></i> 
        <span id="textEjecutar">Ejecutar Producción</span>
    </button>
    <!-- Contenedor del mensaje de estado al terminar -->
    <div id="statusMensajeProduccion" class="ml-2 font-weight-bold text-success small align-items-center" style="display: none; white-space: nowrap;">
        <i class="fas fa-check-circle mr-1" style="font-size: 1.1rem;"></i> Ejecución Finalizada
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

<!-- Script para la validación activa de fechas -->
<script>
function validarFechasBtn() {
    var f1 = document.getElementById('Fecha1') ? document.getElementById('Fecha1').value : '';
    var f2 = document.getElementById('Fecha2') ? document.getElementById('Fecha2').value : '';
    var btn = document.getElementById('btnEjecutarProduccion');

    if (btn) {
        btn.disabled = !(f1.trim() !== '' && f2.trim() !== '');
    }
}

function ejecutarFiltradoProduccion() {
    var btn = document.getElementById('btnEjecutarProduccion');
    var icon = document.getElementById('iconEjecutar');
    var text = document.getElementById('textEjecutar');
    var msgStatus = document.getElementById('statusMensajeProduccion');

    // Estado 1: Cargando / Procesando
    btn.disabled = true;
    icon.className = 'fas fa-spinner fa-spin mr-1';
    text.innerText = 'Procesando...';
    if (msgStatus) msgStatus.style.display = 'none';

    // Disparar la función de recarga Ajax (por defecto LoadKpisControl o $onchange_fn)
    var targetFn = "<?= $onchange_fn ?>";
    
    if (typeof window[targetFn] === 'function') {
        // Ejecutar función de recarga y esperar resolución (si devuelve una promesa)
        Promise.resolve(window[targetFn]()).then(function() {
            mostrarExito();
        }).catch(function() {
            mostrarExito(); // En caso de que LoadKpisControl no retorne promesa explícita
        });
    } else {
        mostrarExito();
    }

    function mostrarExito() {
        // Estado 2: Restaurar botón y mostrar palomita / mensaje
        btn.disabled = false;
        icon.className = 'fas fa-play fa-sm mr-1';
        text.innerText = 'Ejecutar Producción';

        if (msgStatus) {
            msgStatus.style.display = 'inline-flex';
            // Ocultar automáticamente el mensaje después de 4 segundos
            setTimeout(function() {
                msgStatus.style.display = 'none';
            }, 4000);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    validarFechasBtn();
});
</script>