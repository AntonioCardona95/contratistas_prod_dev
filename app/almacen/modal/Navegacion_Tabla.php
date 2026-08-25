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

<div class="navegacion-tabla-contenedor">
	<style>
		#tac_series_tabla tbody tr.serie-navegando {
			background-color: #dff6ff !important;
			animation: pulso-navegacion 2s ease-in-out infinite;
		}

		#tac_series_tabla {
			min-width: 1900px;
		}

		#tac_series_tabla th,
		#tac_series_tabla td {
			white-space: nowrap;
			vertical-align: middle;
		}

		#tac_series_tabla th:nth-child(1),
		#tac_series_tabla td:nth-child(1) {
			min-width: 220px;
		}

		#tac_series_tabla th:nth-child(2),
		#tac_series_tabla td:nth-child(2) {
			min-width: 190px;
		}

		#tac_series_tabla th:nth-child(3),
		#tac_series_tabla td:nth-child(3) {
			min-width: 150px;
		}

		#tac_series_tabla th:nth-child(4),
		#tac_series_tabla td:nth-child(4) {
			min-width: 260px;
			white-space: normal;
		}

		#tac_series_tabla th:nth-child(5),
		#tac_series_tabla td:nth-child(5),
		#tac_series_tabla th:nth-child(7),
		#tac_series_tabla td:nth-child(7),
		#tac_series_tabla th:nth-child(9),
		#tac_series_tabla td:nth-child(9) {
			min-width: 160px;
		}

		#tac_series_tabla th:nth-child(6),
		#tac_series_tabla td:nth-child(6),
		#tac_series_tabla th:nth-child(8),
		#tac_series_tabla td:nth-child(8),
		#tac_series_tabla th:nth-child(10),
		#tac_series_tabla td:nth-child(10) {
			min-width: 190px;
		}

		#tac_series_tabla th:nth-child(11),
		#tac_series_tabla td:nth-child(11) {
			min-width: 140px;
			text-align: center;
		}

		@keyframes pulso-navegacion {
			0%, 100% { box-shadow: inset 3px 0 0 #0aa2c0; }
			50% { box-shadow: inset 7px 0 0 #0aa2c0; }
		}
	</style>

	<div id="tac-buttons-container" class="mb-3"></div>

	<table id="tac_series_tabla" class="table table-bordered table-hover" style="width:100%">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Numero de serie</th>
				<th>Folio asignacion</th>
				<th>Tecnico</th>
				<th>Telefono Coordiapp</th>
				<th>Fecha Coordiapp</th>
				<th>Telefono TAC</th>
				<th>Fecha liquidacion TAC</th>
				<th>Telefono ECI</th>
				<th>Fecha consulta ECI</th>
				<th>Navegando</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($series as $serie): ?>
				<?php
					$numeroSerie = $serie['Num_Serie_S'] ?? '';
					$navegando = (int)($serie['Navegando'] ?? 0) === 1;
				?>
				<tr class="<?= $navegando ? 'serie-navegando' : '' ?>">
					<td><?= htmlspecialchars($serie['Producto'] ?? '') ?></td>
					<td><?= htmlspecialchars($numeroSerie) ?></td>
					<td><?= htmlspecialchars($serie['Folio_Salida_Det'] ?? 'Sin asignar') ?></td>
					<td><?= htmlspecialchars(trim($serie['Tecnico'] ?? '') ?: 'Sin asignar') ?></td>
					<td><?= htmlspecialchars($serie['Telefono_Coordiapp'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Fecha_Coordiapp'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Telefono_Tac'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Fecha_Liquidacion_Tac'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Telefono_Eci'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Fecha_Consulta_Eci'] ?? 'Sin dato') ?></td>
					<td>
						<span class="badge <?= $navegando ? 'badge-info' : 'badge-secondary' ?>">
							<?php if ($navegando): ?>
								<i class="fa-brands fa-tailwind-css fa-float mr-1 " style="--fa-animation-duration: 2s;" aria-hidden="true"></i>
							<?php else: ?>
								No
							<?php endif; ?>
						</span>
					</td>
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
			lengthChange: true,
			scrollX: true,
			lengthMenu: [10, 25, 50, 100],
			pageLength: 10,
			dom: '<"d-flex justify-content-between align-items-center mb-2" lf>' +
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

		botones.container().appendTo('#tac-buttons-container');
	});
</script>
