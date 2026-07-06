<!-- SIDE BAR DE CONTRATISTAS -->
<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style= "background-color: #007BFF; background-image: linear-gradient(180deg, #007B 10%, #224abe 100%); background-size: cover;">

    <!-- BARRA LATERAL TITULO -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="Bienvenida.php">
        <div class="sidebar-brand-text mx-3">Contratistas</div>
    </a>

    <!-- DIVISION -->
    <hr class="sidebar-divider">

    <!-- OPERACIONES -->
    <div class="sidebar-heading justify-content-center">
        OPERACIONES
    </div>
    
        <li class="nav-item">
            <a class="nav-link" href="views/Operaciones/Dashboard.php">
                <i class="fa-solid fa-chart-column"></i>
                <span>DASHBOARD COORDIAPP/TAC</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="views/Operaciones/OrdenesCoordiapp.php">
                <i class="fa-solid fa-check-double"></i>
                <span>LIQ. COORDIAPP COMPLETAS</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="views/Operaciones/OrdenesIncompletas.php">
               <i class="fa-solid fa-bullhorn"></i>
                <span>LIQ. COORDIAPP PENDIENTES</span>
            </a>
        </li>

     <!-- DIVISION -->
    <hr class="sidebar-divider">

    <!-- ALMACEN -->
    <div class="sidebar-heading justify-content-center">
        ALMACEN
    </div>

        <li class="nav-item">
            <a class="nav-link" href="Almacen/Dashboard.php">
                <i class="fa-solid fa-chart-column"></i>
                <span>DASHBOARD ALMACÉN</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="Almacen/Entradas.php">
               <i class="fa-solid fa-clipboard-check"></i>
                <span>MATERIAL RECIBIDO</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="Almacen/AceptarMaterial.php">
                <i class="fa-solid fa-feather-pointed"></i>
                <span>ACEPTAR MATERIAL</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="Almacen/MaterialRechazado.php">
                <i class="fa-solid fa-circle-xmark"></i>
                <span>MATERIAL RECHAZADO</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="Almacen/Salidas.php">
                <i class="fa-solid fa-circle-plus"></i>
                <span>ASIGNAR MATERIAL</span>
            </a>
        </li>


        <li class="nav-item">
            <a class="nav-link" href="Almacen/Tecnicos.php">
                <i class="fa-solid fa-clipboard-user"></i>
                <span>TECNICOS</span>
            </a>
        </li>

     <!-- DIVISION -->
    <hr class="sidebar-divider">

    <!-- BOTON DE PLIEGUE DE LA BARRA LATERAL -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- FIN DE LA BARRA LATERAL DE CONTRATISTAS -->