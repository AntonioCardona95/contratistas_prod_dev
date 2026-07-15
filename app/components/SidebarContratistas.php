<!-- SIDE BAR DE CONTRATISTAS -->
<?php
require_once(__DIR__ . '/../config/cnx.php');
require_once(APP_PATH . '/controller/ClassSubModulos.php');

// OBTIENE LA RUTA ACTUAL SIN QUERY STRING PARA COMPARAR CON LAS RUTAS DE LOS SUBMÓDULOS
$ruta_actual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ruta_actual = rtrim((string)$ruta_actual, '/');
if($ruta_actual === ''){
    $ruta_actual = '/';
}
?>

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #007BFF; background-image: linear-gradient(180deg, #0f172a 100%, #224abe 0%); background-size: cover;">

    <!-- BARRA LATERAL TITULO -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= BASE_URL ?>/views/Bienvenida.php">
        <div class="sidebar-brand-text mx-3">Contratistas</div>
    </a>

    <!-- DIVISION -->
    <hr class="sidebar-divider">

    <!-- OPERACIONES -->
    <div class="sidebar-heading justify-content-center">
        OPERACIONES
    </div>
    
    <?php 
        if(!empty($submodulos_operaciones)) {
            foreach($submodulos_operaciones as $submod) {
                // CONSTRUYE LA RUTA COMPLETA A PARTIR DE LA RUTA BASE + LA RUTA GUARDADA EN BD
                $ruta_completa = BASE_URL . '/views/' . ltrim($submod['ruta_submodulo'], '/');

                // NORMALIZA PARA COMPARAR DE FORMA CONSISTENTE
                $ruta_submodulo = parse_url($ruta_completa, PHP_URL_PATH);
                $ruta_submodulo = rtrim((string)$ruta_submodulo, '/');

                if($ruta_submodulo === ''){
                    $ruta_submodulo = '/';
                }

                $archivo_actual = basename($ruta_actual);
                $archivo_submodulo = basename($ruta_submodulo);

                $es_activo = ($ruta_actual === $ruta_submodulo || $archivo_actual === $archivo_submodulo) ? 'active' : '';
                
                echo "<li class='nav-item'>";
                echo "  <a class='nav-link {$es_activo}' href='{$ruta_completa}'>";
                echo "    <i class='{$submod['icono_submodulo']}'></i>";
                echo "    <span>{$submod['nombre_submodulo']}</span>";
                echo "  </a>";
                echo "</li>";
            }
        } else {
            echo "<li class='nav-item'><a class='nav-link'>NO TIENE MÓDULOS ASIGNADOS</a></li>";
        }
    ?>

    <!-- DIVISION -->
    <hr class="sidebar-divider">

    <!-- ALMACEN -->
    <div class="sidebar-heading justify-content-center">
        ALMACEN
    </div>

    <?php 
        if(!empty($submodulos_almacen)) {
            foreach($submodulos_almacen as $submod) {
                $ruta_completa = BASE_URL . '/views/' . ltrim($submod['ruta_submodulo'], '/');

                $ruta_submodulo = parse_url($ruta_completa, PHP_URL_PATH);
                $ruta_submodulo = rtrim((string)$ruta_submodulo, '/');

                if($ruta_submodulo === ''){
                    $ruta_submodulo = '/';
                }

                $archivo_actual = basename($ruta_actual);
                $archivo_submodulo = basename($ruta_submodulo);

                $es_activo = ($ruta_actual === $ruta_submodulo || $archivo_actual === $archivo_submodulo) ? 'active' : '';
                
                echo "<li class='nav-item'>";
                echo "  <a class='nav-link {$es_activo}' href='{$ruta_completa}'>";
                echo "    <i class='{$submod['icono_submodulo']}'></i>";
                echo "    <span>{$submod['nombre_submodulo']}</span>";
                echo "  </a>";
                echo "</li>";
            }
        } else {
            echo "<li class='nav-item'><a class='nav-link'>NO TIENE MÓDULOS ASIGNADOS</a></li>";
        }
    ?>

    <!-- DIVISION -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- BOTON DE PLIEGUE DE LA BARRA LATERAL -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- FIN DE LA BARRA LATERAL DE CONTRATISTAS -->