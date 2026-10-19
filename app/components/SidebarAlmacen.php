<?php
require_once __DIR__ . '/../config/ContratistaContexto.php';
requerirSesionAlmacen();
$paginaActual = basename($_SERVER['PHP_SELF']);
?>

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #007BFF; background-image: linear-gradient(180deg, #0f172a 100%, #224abe 0%); background-size: cover;">

	<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
		<div class="sidebar-brand-text mx-3">ALMACEN</div>
	</a>

	<hr class="sidebar-divider my-0">

	<li class="nav-item <?php echo $paginaActual === 'index.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="index.php">
			<i class="fas fa-fw fa-tachometer-alt"></i>
			<span>Dashboard</span>
		</a>
	</li>

	<hr class="sidebar-divider">

	<div class="sidebar-heading">ACCION</div>

	<li class="nav-item">
		<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
			aria-expanded="true" aria-controls="collapsePages">
			<i class="fas fa-fw fa-folder"></i>
			<span>MODULOS</span>
		</a>
		<div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
			<div class="bg-white py-2 collapse-inner rounded">
				<h6 class="collapse-header">MODULOS</h6>
				<a class="collapse-item" href="index.php"><i class="fa-solid fa-box-open"></i> ALMACEN</a>
				<a class="collapse-item" href="../ordenes/index.php"><i class="fa-solid fa-school-circle-check"></i> OPERACIONES</a>
			</div>
		</div>
	</li>

	<?php if (esAdministradorContratista()): ?>
	<li class="nav-item <?php echo $paginaActual === 'Navegacion.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="Navegacion.php">
			<i class="fa-brands fa-tailwind-css"></i>
			<span>NAVEGACION</span>
		</a>
	</li>
	<?php endif; ?>

	
	<?php if (esAdministradorContratista()): ?>
	<li class="nav-item <?php echo $paginaActual === 'Produccion.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="Produccion.php">
			<i class="fa-brands fa-tailwind-css"></i>
			<span>PRODUCCIÓN</span>
		</a>
	</li>
	<?php endif; ?>


		<?php if (esAdministradorContratista()): ?>
	<li class="nav-item <?php echo $paginaActual === 'Reporte.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="Reporte.php">
			<i class="fa-brands fa-tailwind-css"></i>
			<span>REPORTE</span>
		</a>
	</li>
	<?php endif; ?>



	<li class="nav-item <?php echo $paginaActual === 'Entradas.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="Entradas.php">
			<i class="fa-solid fa-clipboard-check"></i>
			<span>MATERIAL RECIBIDO</span>
		</a>
	</li>

	<li class="nav-item <?php echo $paginaActual === 'AceptarMaterial.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="AceptarMaterial.php">
			<i class="fa-solid fa-feather-pointed"></i>
			<span>ACEPTAR MATERIAL</span>
		</a>
	</li>

	<li class="nav-item <?php echo $paginaActual === 'MaterialRechazado.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="MaterialRechazado.php">
			<i class="fa-solid fa-school-circle-xmark"></i>
			<span>MATERIAL RECHAZADO</span>
		</a>
	</li>

	<li class="nav-item <?php echo $paginaActual === 'Salidas.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="Salidas.php">
			<i class="fa-solid fa-square-plus"></i>
			<span>ASIGNAR MATERIAL</span>
		</a>
	</li>

	<li class="nav-item <?php echo $paginaActual === 'Tecnicos.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="Tecnicos.php">
			<i class="fa-solid fa-user"></i>
			<span>TÉCNICOS</span>
		</a>
	</li>

	<?php if (esAdministradorContratista()): ?>
	<li class="nav-item <?php echo $paginaActual === 'Traspasos.php' ? 'active' : ''; ?>">
		<a class="nav-link" href="Traspasos.php">
			<i class="fa-solid fa-right-left"></i>
			<span>TRASPASOS</span>
		</a>
	</li>
	<?php endif; ?>

	<hr class="sidebar-divider d-none d-md-block">

	<div class="sidebar-footer text-center">
		<button class="rounded-circle border-0" id="sidebarToggle" aria-label="Cerrar menú">
			<i class="fas fa-times"></i>
		</button>
	</div>
</ul>
