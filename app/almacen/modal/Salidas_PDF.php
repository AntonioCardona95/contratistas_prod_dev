<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../controller/ClassSalidas.php');
require_once(__DIR__ . '/../../config/ContratistaContexto.php');

try {
	requerirSesionAlmacen();
	require_once(__DIR__ . '/../../vendor/autoload.php');

	$folio = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	if (!$folio || $folio < 1) {
		throw new InvalidArgumentException('El folio de salida no es valido.');
	}

	$modal = new Salidas();
	$idContratista = obtenerIdContratistaContexto();
	$encabezados = $modal->ObtenerEncabezadoSalidaPDF($folio, $idContratista);
	if (empty($encabezados)) {
		http_response_code(404);
		throw new RuntimeException('No se encontro la salida solicitada.');
	}

	$encabezado = $encabezados[0];
	$materiales = $modal->ObtenerMaterialesSalidaPDF($folio);
	$series = $modal->ObtenerSeriesSalidaPDF($folio);
	$escape = static function ($value) {
		return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
	};
	$fecha = !empty($encabezado['Fecha_T'])
		? date('d/m/Y H:i', strtotime($encabezado['Fecha_T']))
		: 'Sin fecha';
	$logoPath = realpath(__DIR__ . '/../img/enlace.png');
	$logo = $logoPath
		? '<img src="' . $escape($logoPath) . '" style="height:52px;width:auto;">'
		: '<span class="brand-name">ENLACE</span>';

	ob_start();
	?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Comprobante de salida</title>
	<style>
		body {
			font-family: sans-serif;
			font-size: 10pt;
			color: #263238;
			line-height: 1.55;
		}

		.header {
			width: 100%;
			border-bottom: 3px solid #1a6630;
			margin-bottom: 12px;
		}

		.header td {
			padding: 12px 8px;
			vertical-align: middle;
		}

		.logo { width: 24%; }

		.title {
			text-align: center;
			color: #1a6630;
			font-size: 15pt;
			font-weight: bold;
			line-height: 1.5;
		}

		.date {
			width: 24%;
			text-align: right;
			font-size: 9pt;
			color: #475569;
		}

		.brand-name {
			font-size: 16pt;
			font-weight: bold;
			color: #1a6630;
		}

		.folio {
			font-size: 11pt;
			color: #0f172a;
		}

		.section-title {
			margin: 20px 0 12px;
			padding: 0 0 7px;
			border-bottom: 2px solid #1a6630;
			color: #1a6630;
			font-size: 12pt;
			line-height: 1.5;
		}

		.info {
			width: 100%;
			margin: 12px 0;
			border-collapse: collapse;
		}

		.info td {
			width: 50%;
			padding: 11px 12px;
			border-bottom: 1px solid #d9e2dc;
			vertical-align: top;
		}

		.label {
			display: block;
			margin-bottom: 5px;
			color: #52635a;
			font-size: 7pt;
			font-weight: bold;
			line-height: 1.4;
			text-transform: uppercase;
		}

		.value {
			display: block;
			padding: 2px 0;
			color: #17211b;
			font-size: 9pt;
			line-height: 1.5;
		}

		table.data {
			width: 100%;
			margin: 8px 0 14px;
			border-collapse: collapse;
		}

		.data th {
			padding: 10px 8px;
			border: 1px solid #155528;
			background: #1a6630;
			color: #fff;
			font-weight: bold;
			line-height: 1.4;
			text-align: left;
		}

		.data td {
			padding: 9px 8px;
			border: 1px solid #d8dfda;
			line-height: 1.45;
			vertical-align: top;
		}

		.data tr:nth-child(even) { background: #f1f5f2; }
		.center { text-align: center; }

		.empty {
			padding: 10px;
			color: #64748b;
			font-style: italic;
			text-align: center;
		}
	</style>
</head>
<body>
	<table class="header" cellpadding="0" cellspacing="0">
		<tr>
			<td class="logo"><?= $logo ?></td>
			<td class="title">
				COMPROBANTE DE SALIDA DE MATERIAL<br>
				<span class="folio">Folio <?= $escape($folio) ?></span>
			</td>
			<td class="date">
				<strong>Fecha</strong><br>
				<?= $escape($fecha) ?>
			</td>
		</tr>
	</table>

	<br>

	<table class="info" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<div class="label">Contratista</div>
				<div class="value"><?= $escape($encabezado['Contratista'] ?? 'N/D') ?></div>
			</td>
			<td>
				<div class="label">Tecnico</div>
				<div class="value"><?= $escape($encabezado['Tecnico'] ?? 'N/D') ?></div>
			</td>
		</tr>
		<tr>
			<td>
				<div class="label">Cantidad registrada</div>
				<div class="value"><?= $escape($encabezado['Cantidad_T'] ?? 'N/D') ?></div>
			</td>
			<td>
				<div class="label">Folio de salida</div>
				<div class="value"><?= $escape($encabezado['Folio_Salida_T'] ?? $folio) ?></div>
			</td>
		</tr>
	</table>

	<h2 class="section-title">Materiales entregados</h2>
	<table class="data" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th>Material</th>
				<th>Modelo</th>
				<th>Descripcion</th>
				<th>Tecnologia</th>
				<th>Presentacion</th>
				<th>UM</th>
				<th class="center">Cantidad</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($materiales)): ?>
				<tr>
					<td class="empty" colspan="7">No hay materiales registrados para este folio.</td>
				</tr>
			<?php else: ?>
				<?php foreach ($materiales as $material): ?>
					<tr>
						<td><?= $escape($material['Producto'] ?? '') ?></td>
						<td><?= $escape($material['Modelo'] ?? '') ?></td>
						<td><?= $escape($material['Descripcion'] ?? '') ?></td>
						<td><?= $escape($material['Tecnologia'] ?? '') ?></td>
						<td><?= $escape($material['Presentacion'] ?? '') ?></td>
						<td><?= $escape($material['UM'] ?? '') ?></td>
						<td class="center"><?= $escape($material['Cantidad'] ?? '') ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<h2 class="section-title">Numeros de serie entregados</h2>
	<table class="data" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th class="center">#</th>
				<th>Numero de serie</th>
				<th>Material</th>
				<th>Modelo</th>
				<th>Descripcion</th>
				<th>Tecnico</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($series)): ?>
				<tr>
					<td class="empty" colspan="6">Este folio no tiene numeros de serie asociados.</td>
				</tr>
			<?php else: ?>
				<?php foreach ($series as $index => $serie): ?>
					<tr>
						<td class="center"><?= $index + 1 ?></td>
						<td><?= $escape($serie['Num_Serie_Salida_Det'] ?? '') ?></td>
						<td><?= $escape($serie['Producto'] ?? '') ?></td>
						<td><?= $escape($serie['Modelo'] ?? '') ?></td>
						<td><?= $escape($serie['Descripcion'] ?? '') ?></td>
						<td><?= $escape($serie['Tecnico'] ?? '') ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</body>
</html>
<?php
	$html = ob_get_clean();

	$mpdf = new \Mpdf\Mpdf(array('tempDir' => sys_get_temp_dir()));
	$mpdf->SetTitle('Salida de material - folio ' . $folio);
	$mpdf->SetFooter('ALMACEN | SALIDA ' . $folio . ' | Pagina {PAGENO} de {nbpg}');
	$mpdf->WriteHTML($html);
	$mpdf->Output('Salida_' . $folio . '.pdf', 'I');
	exit;
} catch (Throwable $error) {
	error_log('[Salidas_PDF] ' . $error->getMessage());
	if (!headers_sent()) {
		http_response_code(http_response_code() === 404 ? 404 : 400);
		header('Content-Type: text/plain; charset=utf-8');
	}
	echo 'No fue posible generar el comprobante de salida.';
}
