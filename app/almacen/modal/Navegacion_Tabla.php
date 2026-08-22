<?php
require_once(__DIR__ . '/../controller/ClassNavegacion.php');

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$folio = isset($_POST['folio']) ? filter_var($_POST['folio'], FILTER_VALIDATE_INT) : false;
$contratistaId = $_SESSION['idContratistas'] ?? 0;

if (!$folio || !$contratistaId) {
	http_response_code(400);
	echo '<div class="alert alert-warning m-3">El folio no es valido.</div>';
	exit;
}

$navegacion = new Navegacion();
$series = $navegacion->ObtenerDetalleFolio($folio, $contratistaId);

if (!$series) {
	echo '<div class="alert alert-info m-3">No hay numeros de serie para este folio.</div>';
	exit;
}
?>

<div class="table-responsive">
	<table id="tac_series_tabla" class="table table-bordered table-hover" style="width:100%">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Numero de serie</th>
				<th>Folio asignacion</th>
				<th>Tecnico</th>
				<th>Liquidado en Coordiapp</th>
				<th>Telefono</th>
				<th>Fecha Coordiapp</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($series as $serie): ?>
				<?php
					$numeroSerie = $serie['Num_Serie_S'] ?? '';
					$liquidado = (int)($serie['Liquidado'] ?? 0) === 1;
				?>
				<tr>
					<td><?= htmlspecialchars($serie['Producto'] ?? '') ?></td>
					<td><?= htmlspecialchars($numeroSerie) ?></td>
					<td><?= htmlspecialchars($serie['Folio_Salida_Det'] ?? 'Sin asignar') ?></td>
					<td><?= htmlspecialchars(trim($serie['Tecnico'] ?? '') ?: 'Sin asignar') ?></td>
					<td>
						<span class="badge <?= $liquidado ? 'badge-success' : 'badge-secondary' ?>">
							<?= $liquidado ? 'Si' : 'No' ?>
						</span>
					</td>
					<td><?= htmlspecialchars($serie['Telefono'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Fecha_Coordiapp'] ?? 'Sin dato') ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<script>
	$(document).ready(function () {
		if (typeof DataTable === 'undefined') {
			return;
		}

		if ($.fn.DataTable.isDataTable('#tac_series_tabla')) {
			$('#tac_series_tabla').DataTable().destroy();
		}

		const opcionesTabla = {
			responsive: true,
			language: {
				url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
			},
			ordering: true,
			searching: true,
			paging: true,
			scrollX: true,
			pageLength: 10,
			dom: '<"d-flex justify-content-between align-items-center mb-2" Bf>' +
				'rt' +
				'<"row"<"col-md-12 text-right mt-2"p>>'
		};

		const tabla = new DataTable('#tac_series_tabla', opcionesTabla);
		const botones = new DataTable.Buttons(tabla, {
			buttons: [
				{
					extend: 'excelHtml5',
					text: '<i class="fas fa-file-excel"></i> Exportar Excel',
					titleAttr: 'Exportar a Excel',
					className: 'btn btn-success btn-sm'
				}
			]
		});

		botones.container().prependTo(tabla.table().container());
	});
</script>
