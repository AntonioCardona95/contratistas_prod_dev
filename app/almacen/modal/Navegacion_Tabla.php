<?php
require_once(__DIR__ . '/../controller/ClassNavegacion.php');
require_once(__DIR__ . '/../../config/ContratistaContexto.php');

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$folio = isset($_POST['folio']) ? filter_var($_POST['folio'], FILTER_VALIDATE_INT) : false;
$contratistaId = obtenerIdContratistaContexto();

if (!$folio || empty($_SESSION['idContratistas']) || !esAdministradorContratista()) {
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
$conteoEstados = array(
	'navegando-observacion' => 0,
	'rojo' => 0,
	'gris' => 0,
	'amarillo' => 0,
	'verde' => 0,
	'blanco' => 0
);
foreach ($series as $serie) {
	$estado = $serie['Estado_Navegacion'] ?? 'blanco';
	if (isset($conteoEstados[$estado])) {
		$conteoEstados[$estado]++;
	}
}
$etiquetasEstado = array(
	'navegando' => 'Navegando correctamente',
	'navegando-observacion' => 'Navegando con observacion: posible serie mal capturada en Coordiapp',
	'rojo' => 'No navegando: requiere seguimiento por mas de 14 dias',
	'gris' => 'No navegando: telefonos no coinciden',
	'amarillo' => 'No navegando: en espera de cumplir 3 dias desde la liquidacion',
	'verde' => 'No navegando: liquidacion reciente',
	'blanco' => 'Sin liquidacion para clasificar'
);
?>

<div class="navegacion-tabla-contenedor">
	<style>
		#tac_series_tabla tbody tr.serie-navegando {
			background-color: #dff6ff !important;
			animation: pulso-navegacion 2s ease-in-out infinite;
		}

		#tac_series_tabla tbody tr.serie-navegando-observacion {
			background-color: #dce5f1 !important;
			box-shadow: inset 5px 0 0 #17365d;
			color: #17365d;
		}

		#tac_series_tabla tbody tr.serie-rojo {
			background-color: #ffe1e1 !important;
			box-shadow: inset 5px 0 0 #e74a3b;
		}

		#tac_series_tabla tbody tr.serie-gris {
			background-color: #e9ecef !important;
			box-shadow: inset 5px 0 0 #6c757d;
		}

		#tac_series_tabla tbody tr.serie-amarillo {
			background-color: #fff3cd !important;
			box-shadow: inset 5px 0 0 #f6c23e;
		}

		#tac_series_tabla tbody tr.serie-verde {
			background-color: #d4edda !important;
			box-shadow: inset 5px 0 0 #28a745;
		}

		.duplicado-registro-elegido {
			background-color: #dff6ff !important;
			color: #0056b3;
			font-weight: 700;
		}

		.modal-header-gradient {
			background: linear-gradient(90deg, #0f172a 0%, #003d99 100%) !important;
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
			max-width: 260px;
		}

		#tac_series_tabla {
			min-width: 1900px;
		}

		/* CENTRAR ENCABEZADOS Y CELDAS DE LA TABLA, INCLUYENDO EL ENCABEZADO DE DATATABLES. */
		#tac_series_tabla_wrapper .dt-scroll-head th,
		#tac_series_tabla_wrapper .dt-scroll-foot th,
		#tac_series_tabla_wrapper .dt-scroll-body td,
		#tac_series_tabla thead th,
		#tac_series_tabla tfoot th,
		#tac_series_tabla tbody td {
			text-align: center !important;
			vertical-align: middle !important;
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

		.tac-serie-card.navegando-observacion {
			border-left-color: #17365d;
			background: #dce5f1;
			color: #17365d;
		}

		.icon-navegacion-observacion {
			color: #17365d;
			font-size: 1.15rem;
		}

		.tac-serie-card.alerta-eci {
			border-left-color: #e74a3b;
			background: #ffe1e1;
		}

		.tac-serie-card.gris {
			border-left-color: #6c757d;
			background: #e9ecef;
		}

		.tac-serie-card.amarillo {
			border-left-color: #f6c23e;
			background: #fff3cd;
		}

		.tac-serie-card.verde {
			border-left-color: #28a745;
			background: #d4edda;
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
			min-width: 70px;
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
			min-width: 200px;
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

		#tac_series_tabla th:nth-child(13),
		#tac_series_tabla td:nth-child(13) {
			min-width: 230px;
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
			<option value="navegando-observacion">Azul oscuro: navegando con observacion (<?= $conteoEstados['navegando-observacion'] ?>)</option>
			<option value="no-navegando">No navegando (<?= $totalPendientes ?>)</option>
			<option value="rojo">Rojo: seguimiento (<?= $conteoEstados['rojo'] ?>)</option>
			<option value="gris">Gris: telefonos diferentes (<?= $conteoEstados['gris'] ?>)</option>
			<option value="amarillo">Amarillo: Navegacion reciente (<?= $conteoEstados['amarillo'] ?>)</option>
			<option value="verde">Verde: pendiente reciente (<?= $conteoEstados['verde'] ?>)</option>
			<option value="blanco">Blanco: sin liquidacion (<?= $conteoEstados['blanco'] ?>)</option>
		</select>
	</div>

	<table id="tac_series_tabla" class="table table-bordered table-hover" style="width:100%">
		<thead>
			<tr>
				<th>Folio Salida</th>
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
				<th>Fecha Ultima Navegacion</th>
				<th>Navegando</th>
				<th>Motivo</th>
				<th>Duplicados</th>
				<th class="columna-estado-exportacion">Estado de la serie</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($series as $indice => $serie): ?>
				<?php
					$numeroSerie = $serie['Num_Serie_S'] ?? '';
					$navegando = (int)($serie['Navegando'] ?? 0) === 1;
					$estadoNavegacion = $serie['Estado_Navegacion'] ?? 'blanco';
					$estadoTexto = $etiquetasEstado[$estadoNavegacion] ?? 'No navegando: estado sin clasificar';
					$motivos = $serie['Motivos_No_Navegacion'] ?? array();
				?>
				<tr class="serie-<?= htmlspecialchars($estadoNavegacion) ?>" data-estado-navegacion="<?= htmlspecialchars($estadoNavegacion) ?>">
					<td><?= htmlspecialchars($serie['Folio_Salida_Material_Det_S'] ?? 'Sin folio') ?></td>
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
					<td><?= htmlspecialchars($serie['Fecha_Ultimo_Evento_Eci'] ?? 'Sin dato') ?></td>
					<td>
						<?php if ($navegando && $estadoNavegacion === 'navegando-observacion'): ?>
							<i class="fa-brands fa-tailwind-css fa-float icon-navegacion-observacion" style="--fa-animation-duration: 2s;" aria-label="Navegando con observacion"></i>
						<?php elseif ($navegando): ?>
							<span class="badge estado-navegacion custom-badge">
								<i class="fa-brands fa-tailwind-css fa-float mr-1 " style="--fa-animation-duration: 2s;" aria-hidden="true"></i>
								<span class="sr-only">Si</span>
							</span>
						<?php else: ?>
							<span class="badge estado-navegacion badge-secondary">No</span>
						<?php endif; ?>
					</td>
					<td class="motivo-navegacion">
						<?= htmlspecialchars($serie['Observacion_Navegacion'] ?? ($navegando ? 'Navegando correctamente' : implode('; ', $motivos))) ?>
					</td>
					<td>
						<?php if (!empty($serie['Duplicado_Tac'])): ?>
							<button type="button" class="btn btn-warning btn-sm ver-duplicado mr-1" data-tipo-duplicado="tac" data-indice-serie="<?= $indice ?>">
								<i class="fas fa-list mr-1" aria-hidden="true"></i>Duplicado TAC
							</button>
						<?php endif; ?>
						<?php if (!empty($serie['Duplicado_Coordiapp'])): ?>
							<button type="button" class="btn btn-info btn-sm ver-duplicado" data-tipo-duplicado="coordiapp" data-indice-serie="<?= $indice ?>">
								<i class="fas fa-list mr-1" aria-hidden="true"></i>Duplicado Coordiapp
							</button>
						<?php endif; ?>
						<?php if (empty($serie['Duplicado_Tac']) && empty($serie['Duplicado_Coordiapp'])): ?>
							<span class="text-muted">Sin duplicados</span>
						<?php endif; ?>
					</td>
					<td class="columna-estado-exportacion"><?= htmlspecialchars($estadoTexto) ?></td>
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

<div class="modal fade" id="modalDuplicadosNavegacion" tabindex="-1" role="dialog" aria-labelledby="modalDuplicadosNavegacionTitulo" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header modal-header-gradient text-white border-0">
				<h5 class="modal-title" id="modalDuplicadosNavegacionTitulo">Detalle de duplicados</h5>
				<button class="btn btn-light" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
			</div>
			<div class="modal-body" id="modalDuplicadosNavegacionContenido"></div>
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
			language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
			ordering: true,
			searching: true,
			paging: true,
			lengthChange: true,
			scrollX: true,
			columnDefs: [{ targets: 15, visible: false, searchable: false }],
			pageLength: 10,
			info: true,
			dom: '<"d-flex justify-content-between align-items-center mb-2" lf>' +
				'rt' +
				'<"row"<"col-md-6 text-left mt-2"i><"col-md-6 text-right mt-2"p>>'
		};

		const tabla = new DataTable('#tac_series_tabla', opcionesTabla);
		const botones = new DataTable.Buttons(tabla, {
			buttons: [
				{
					extend: 'excelHtml5',
					text: '<i class="fas fa-file-excel"></i> Exportar Excel',
					titleAttr: 'Exportar a Excel',
					className: 'btn btn-success btn-sm',
					title: 'Navegacion de Numeros de Serie',
					exportOptions: { columns: ':visible, .columna-estado-exportacion' },
					customize: function (xlsx) {

            			var sheet = xlsx.xl.worksheets['sheet1.xml'];

            			// CENTRAR HORIZONTALMENTE
            			$('row c', sheet).attr('s', '51');
        			},
				}
			]
		});

		botones.container().appendTo('#tac-buttons-container');

		let filtroEstado = '';

		function escaparHtml(valor) {
			return $('<div>').text(valor ?? '').html();
		}

		function mostrarDetallesDuplicado(tipo, indice) {
			const serie = series[indice] || {};
			const esTac = tipo === 'tac';
			const registros = esTac
				? (serie.Detalles_Duplicado_Tac || [])
				: (serie.Detalles_Duplicado_Coordiapp || []);
			const telefonoEci = String(serie.Telefono_Eci || '').replace(/\D/g, '');
			const nombreFuente = esTac ? 'TAC' : 'Coordiapp';
			let contenido = '<p><strong>Numero de serie:</strong> ' + escaparHtml(serie.Num_Serie_S) + '</p>';
			contenido += '<p><strong>Fuente:</strong> ' + nombreFuente + '</p>';
			contenido += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0"><thead><tr>';
			contenido += '<th>Numero de serie</th><th>Telefono</th><th>Tipo de tarea</th>';
			contenido += esTac ? '<th>Fecha liquidacion</th><th>Estado</th>' : '<th>Fecha Coordiapp</th>';
			contenido += '</tr></thead><tbody>';

			registros.forEach(function (registro) {
				const telefonoRegistro = String(registro.TELEFONO || registro.Telefono || '').replace(/\D/g, '');
				const esRegistroElegido = telefonoEci !== '' && telefonoRegistro === telefonoEci;
				contenido += '<tr class="' + (esRegistroElegido ? 'duplicado-registro-elegido' : '') + '">';
				contenido += '<td>' + escaparHtml(esTac ? registro.Num__Serie : registro.Ont) + '</td>';
				contenido += '<td>' + escaparHtml(registro.TELEFONO || registro.Telefono || 'Sin dato') + '</td>';
				contenido += '<td>' + escaparHtml(esTac ? registro.Tipo_de_Tarea_Clasificado : registro.Tipo_Tarea_Clasificado || 'Otros') + '</td>';
				contenido += esTac
					? '<td>' + escaparHtml(registro.FECHA_LIQ || 'Sin dato') + '</td><td>' + escaparHtml(registro.Calificador_Edo || 'Sin dato') + '</td>'
					: '<td>' + escaparHtml(registro.Fecha_Coordiapp || 'Sin dato') + '</td>';
				contenido += '</tr>';
			});

			contenido += '</tbody></table></div>';
			$('#modalDuplicadosNavegacionTitulo').text('Detalle duplicado ' + nombreFuente);
			$('#modalDuplicadosNavegacionContenido').html(contenido);
			$('#modalDuplicadosNavegacion').modal('show');
		}

		$('#tac_series_tabla').on('click', '.ver-duplicado', function () {
			mostrarDetallesDuplicado($(this).data('tipo-duplicado'), Number($(this).data('indice-serie')));
		});

		function renderizarTarjetas(filtroActual) {
			const tarjetas = $('#tac-series-cards-container');
			tarjetas.empty();

			series.forEach(function (serie, indice) {
				const estaNavegando = Number(serie.Navegando || 0) === 1;
				const estadoNavegacion = serie.Estado_Navegacion || 'blanco';

				if (filtroActual === 'navegando' && !estaNavegando) {
					return;
				}

				if (filtroActual === 'no-navegando' && estaNavegando) {
					return;
				}

				if (filtroActual === 'navegando-observacion' && estadoNavegacion !== 'navegando-observacion') {
					return;
				}

				if (['rojo', 'gris', 'amarillo', 'verde', 'blanco'].indexOf(filtroActual) !== -1 && estadoNavegacion !== filtroActual) {
					return;
				}

				const tieneObservacion = estadoNavegacion === 'navegando-observacion';
				const estado = tieneObservacion ? 'Navegando con observacion' : (estaNavegando ? 'Navegando' : 'No navegando');
				const claseTarjeta = tieneObservacion ? ' navegando-observacion' : (estaNavegando ? ' navegando' : ' ' + (estadoNavegacion === 'rojo' ? 'alerta-eci' : estadoNavegacion));
				const motivos = (serie.Motivos_No_Navegacion || []).join('; ');
				const textoMotivo = serie.Observacion_Navegacion || (estaNavegando ? 'Navegando correctamente' : motivos);

				const indicadorEstado = tieneObservacion
					? '<span class="estado-observacion-texto"><i class="fa-brands fa-tailwind-css fa-float icon-navegacion-observacion mr-1" style="--fa-animation-duration: 2s;" aria-hidden="true"></i>' + escaparHtml(estado) + '</span>'
					: '<span class="badge ' + (estaNavegando ? 'badge-info' : 'badge-secondary') + '">' +
						(estaNavegando ? '<i class="fas fa-wifi mr-1"></i>' : '') + estado +
					'</span>';

				const tarjeta =
					'<article class="tac-serie-card' + claseTarjeta + '">' +
						'<div class="tac-serie-card-header">' +
							'<span class="tac-serie-card-title">' + escaparHtml(serie.Num_Serie_S) + '</span>' +
							indicadorEstado +
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
							'<div class="tac-serie-card-field"><span>Fecha Ultima Navegacion</span><strong>' + escaparHtml(serie.Fecha_Ultimo_Evento_Eci || 'Sin dato') + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Motivo</span><strong>' + escaparHtml(textoMotivo) + '</strong></div>' +
							'<div class="tac-serie-card-field"><span>Duplicados</span><strong>' +
								(serie.Duplicado_Tac ? '<button type="button" class="btn btn-warning btn-sm ver-duplicado mr-1" data-tipo-duplicado="tac" data-indice-serie="' + indice + '">Duplicado TAC</button>' : '') +
								(serie.Duplicado_Coordiapp ? '<button type="button" class="btn btn-info btn-sm ver-duplicado" data-tipo-duplicado="coordiapp" data-indice-serie="' + indice + '">Duplicado Coordiapp</button>' : '') +
								(!serie.Duplicado_Tac && !serie.Duplicado_Coordiapp ? 'Sin duplicados' : '') +
							'</strong></div>' +
						'</div>' +
					'</article>';

				tarjetas.append(tarjeta);
			});
		}

		$('#tac-series-cards-container').on('click', '.ver-duplicado', function () {
			mostrarDetallesDuplicado($(this).data('tipo-duplicado'), Number($(this).data('indice-serie')));
		});

		renderizarTarjetas(filtroEstado);

		tabla.search.fixed('estadoNavegacion', function (textoBusqueda, datos, indiceFila) {
			if (filtroEstado === '') {
				return true;
			}

			const fila = tabla.row(indiceFila).node();
			const estadoFila = fila ? fila.getAttribute('data-estado-navegacion') : '';

			if (filtroEstado === 'navegando') {
				return estadoFila === 'navegando' || estadoFila === 'navegando-observacion';
			}

			if (filtroEstado === 'no-navegando') {
				return estadoFila !== 'navegando' && estadoFila !== 'navegando-observacion';
			}

			return estadoFila === filtroEstado;
		});

		$('#filtro-estado-series').on('change', function () {
			filtroEstado = $(this).val();
			tabla.draw();
			renderizarTarjetas(filtroEstado);
		});
	});
</script>
