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

// TOTAL DE SERIES
$totalSeries = count($series);
// TOTAL NAVEGANDO
$totalNavegando = 0;

foreach ($series as $serie) {
	if ((int)($serie['Navegando'] ?? 0) === 1) {
		$totalNavegando++;
	}
}

// TOTAL PENDIENTES
$totalPendientes = $totalSeries - $totalNavegando;
?>

<div class="navegacion-tabla-contenedor">
	<style>
		#tac_series_tabla tbody tr.serie-navegando {
			background-color: #dff6ff !important;
			animation: pulso-navegacion 2s ease-in-out infinite;
		}

		#tac-controls-container {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 16px;
		}

		#titulo-panel-folios {
			display: flex;
			align-items: center;
			justify-content: space-between;
			width: 100%;
			gap: 16px;
		}

		#titulo-panel-folios .fecha-folio {
			margin-left: auto;
			text-align: right;
			white-space: nowrap;
		}

		#tac-buttons-container {
			flex: 1;
		}

		#filtro-estado-series {
			max-width: 220px;
		}

		#tac_series_tabla {
			min-width: 1900px;
		}

		#tac-series-cards-container {
			display: none;
			grid-template-columns: 1fr;
			gap: 14px;
		}

		.tac-serie-card {
			border: 1px solid #e3e6f0;
			border-left: 4px solid #adb5bd;
			border-radius: 8px;
			background: #fff;
			box-shadow: 0 3px 10px rgba(0, 0, 0, .06);
			overflow: hidden;
		}

		.tac-serie-card.navegando {
			border-left-color: #0aa2c0;
			background: #dff6ff;
		}

		.tac-serie-card-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 10px;
			padding: 12px 14px;
			border-bottom: 1px solid #e3e6f0;
		}

		.tac-serie-card-title {
			color: #3a3b45;
			font-weight: 700;
			word-break: break-word;
		}

		.tac-serie-card-body {
			display: grid;
			gap: 8px;
			padding: 12px 14px;
		}

		.tac-serie-card-field {
			display: grid;
			grid-template-columns: 42% 58%;
			gap: 8px;
			font-size: .82rem;
		}

		.tac-serie-card-field span {
			color: #6c757d;
			font-weight: 600;
		}

		.tac-serie-card-field strong {
			color: #1f2937;
			font-weight: 600;
			text-align: right;
			word-break: break-word;
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

		#tac_series_tabla th:nth-child(12),
		#tac_series_tabla td:nth-child(12) {
			min-width: 140px;
			text-align: center;
		}

		#tac_series_tabla td:nth-child(12) .estado-navegacion {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-width: 28px;
			line-height: 1;
		}

		.custom-badge {
			background-color: none;
			color: #007BFF;
			font-size: 130%;
		}

		@keyframes pulso-navegacion {
			0%, 100% { box-shadow: inset 3px 0 0 #0aa2c0; }
			50% { box-shadow: inset 7px 0 0 #0aa2c0; }
		}

		.tac-resumen-series {
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: 16px;
			margin-top: 20px;
		}

		.tac-resumen-tarjeta {
			background: #fff;
			border: 1px solid #e3e6f0;
			border-left: 4px solid #4e73df;
			border-radius: 6px;
			box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
			padding: 14px 16px;
		}

		.tac-resumen-tarjeta.navegando {
			border-left-color: #0aa2c0;
		}

		.tac-resumen-tarjeta.pendientes {
			border-left-color: #f6c23e;
		}

		.tac-resumen-etiqueta {
			color: #4e73df;
			font-size: .75rem;
			font-weight: 700;
			text-transform: uppercase;
		}

		.tac-resumen-tarjeta.navegando .tac-resumen-etiqueta {
			color: #0aa2c0;
		}

		.tac-resumen-tarjeta.pendientes .tac-resumen-etiqueta {
			color: #d39e00;
		}

		.tac-resumen-valor {
			color: #6c757d;
			font-size: 1.75rem;
			font-weight: 600;
			line-height: 1.2;
			margin-top: 4px;
		}

		@media (max-width: 768px) {
			#tac_series_tabla_wrapper {
				display: none;
			}

			.navegacion-tabla-contenedor .dt-container {
				display: none;
			}

			#tac-series-cards-container {
				display: grid;
			}

			#tac-controls-container {
				align-items: stretch;
				flex-direction: column;
			}

			#filtro-estado-series {
				max-width: none;
			}

			.tac-resumen-series {
				grid-template-columns: 1fr;
			}
		}
	</style>

	<div id="tac-controls-container" class="mb-3">
		<div id="tac-buttons-container"></div>
		<select id="filtro-estado-series" class="form-control">
			<option value="">Todos</option>
			<option value="navegando">Navegando (<?= $totalNavegando ?>)</option>
			<option value="pendiente">No navegando (<?= $totalPendientes ?>)</option>
		</select>
	</div>

	<table id="tac_series_tabla" class="table table-bordered table-hover" style="width:100%">
		<thead>
			<tr>
				<th>Modelo</th>
				<th>Numero de serie</th>
				<th>Folio asignacion</th>
				<th>Fecha asignacion</th>
				<th>Tecnico</th>
				<th>Telefono Coordiapp</th>
				<th>Fecha Coordiapp</th>
				<th>Telefono TAC</th>
				<th>Fecha liquidacion TAC</th>
				<th>Telefono Navegando</th>
				<th>Fecha consulta Navegando</th>
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
					<td><?= htmlspecialchars($serie['Fecha_Salida_Det'] ?? 'Sin asignar') ?></td>
					<td><?= htmlspecialchars(trim($serie['Tecnico'] ?? '') ?: 'Sin asignar') ?></td>
					<td><?= htmlspecialchars($serie['Telefono_Coordiapp'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Fecha_Coordiapp'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Telefono_Tac'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Fecha_Liquidacion_Tac'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Telefono_Eci'] ?? 'Sin dato') ?></td>
					<td><?= htmlspecialchars($serie['Fecha_Consulta_Eci'] ?? 'Sin dato') ?></td>
					<td>
						<span class="badge estado-navegacion <?= $navegando ? 'custom-badge' : 'badge-secondary' ?>">
							<?php if ($navegando): ?>
								<i class="fa-brands fa-tailwind-css fa-float mr-1 " style="--fa-animation-duration: 2s;" aria-hidden="true"></i>
								<span class="sr-only">Si</span>
							<?php else: ?>
								<span class="sr-only">No</span>
								No
							<?php endif; ?>
						</span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div id="tac-series-cards-container"></div>

	<div class="tac-resumen-series">
		<div class="tac-resumen-tarjeta">
			<div class="tac-resumen-etiqueta">
				<i class="fas fa-barcode mr-1" aria-hidden="true"></i>
				Total de series
			</div>
			<div class="tac-resumen-valor">
				<span id="total-series-valor"><?= $totalSeries ?></span>
			</div>
		</div>

		<div class="tac-resumen-tarjeta navegando">
			<div class="tac-resumen-etiqueta">
				<i class="fas fa-wifi mr-1" aria-hidden="true"></i>
				Series navegando
			</div>
			<div class="tac-resumen-valor">
				<span id="total-navegando-valor"><?= $totalNavegando ?></span>
			</div>
		</div>

		<div class="tac-resumen-tarjeta pendientes">
			<div class="tac-resumen-etiqueta">
				<i class="fas fa-hourglass-half mr-1" aria-hidden="true"></i>
				Series pendientes
			</div>
			<div class="tac-resumen-valor">
				<span id="total-pendientes-valor"><?= $totalPendientes ?></span>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		if (typeof DataTable === 'undefined') {
			return;
		}

		const series = <?= json_encode($series) ?>;

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

		let filtroEstado = '';

		function escaparHtml(valor) {
			return $('<div>').text(valor ?? '').html();
		}

		function renderizarTarjetas(filtroActual) {
			const tarjetas = $('#tac-series-cards-container');
			tarjetas.empty();

			series.forEach(function (serie, indice) {
				const estaNavegando = Number(serie.Navegando || 0) === 1;

				if (filtroActual === 'navegando' && !estaNavegando) {
					return;
				}

				if (filtroActual === 'pendiente' && estaNavegando) {
					return;
				}

				const estado = estaNavegando ? 'Navegando' : 'No navegando';
				const claseTarjeta = estaNavegando ? ' navegando' : '';

				const tarjeta =
					'<article class="tac-serie-card' + claseTarjeta + '">' +
						'<div class="tac-serie-card-header">' +
							'<span class="tac-serie-card-title">' + escaparHtml(serie.Num_Serie_S) + '</span>' +
							'<span class="badge ' + (estaNavegando ? 'badge-info' : 'badge-secondary') + '">' +
								(estaNavegando ? '<i class="fas fa-wifi mr-1"></i>' : '') + estado +
							'</span>' +
						'</div>' +
						'<div class="tac-serie-card-body">' +
							'<div class="tac-serie-card-field"><span>Producto</span><strong>' + escaparHtml(serie.Producto) + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Folio asignacion</span><strong>' + escaparHtml(serie.Folio_Salida_Det || 'Sin asignar') + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Fecha asignacion</span><strong>' + escaparHtml(serie.Fecha_Salida_Det || 'Sin asignar') + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Tecnico</span><strong>' + escaparHtml(serie.Tecnico || 'Sin asignar') + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Telefono Coordiapp</span><strong>' + escaparHtml(serie.Telefono_Coordiapp || 'Sin dato') + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Fecha Coordiapp</span><strong>' + escaparHtml(serie.Fecha_Coordiapp || 'Sin dato') + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Telefono TAC</span><strong>' + escaparHtml(serie.Telefono_Tac || 'Sin dato') + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Fecha liquidacion TAC</span><strong>' + escaparHtml(serie.Fecha_Liquidacion_Tac || 'Sin dato') + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Telefono ECI</span><strong>' + escaparHtml(serie.Telefono_Eci || 'Sin dato') + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Fecha consulta ECI</span><strong>' + escaparHtml(serie.Fecha_Consulta_Eci || 'Sin dato') + '</strong></div>' +
						'</div>' +
					'</article>';

				tarjetas.append(tarjeta);
			});
		}

		renderizarTarjetas(filtroEstado);

		$.fn.dataTable.ext.search.push(function (configuracion, datos, indiceFila) {
			if (configuracion.nTable.id !== 'tac_series_tabla' || filtroEstado === '') {
				return true;
			}

			const fila = configuracion.aoData[indiceFila].nTr;
			const estaNavegando = fila.classList.contains('serie-navegando');

			return filtroEstado === 'navegando'
				? estaNavegando
				: !estaNavegando;
		});

		$('#filtro-estado-series').on('change', function () {
			filtroEstado = $(this).val();
			tabla.draw();
			renderizarTarjetas(filtroEstado);
		});
	});
</script>
