<?php
require_once(__DIR__ . "/../controller/ClassConsultasProduccion.php");

// Recibir parámetros mediante POST
$fechaInicio   = $_POST['fecha_inicio'] ?? '';
$fechaFin      = $_POST['fecha_fin'] ?? '';
$idContratista = !empty($_POST['contratista']) ? $_POST['contratista'] : null;
$tipoTarea     = !empty($_POST['tipo_tarea']) ? $_POST['tipo_tarea'] : null;
$area          = !empty($_POST['area']) ? $_POST['area'] : null;
$division      = !empty($_POST['division']) ? $_POST['division'] : null;
$cope          = !empty($_POST['cope']) ? $_POST['cope'] : null;

// Instanciar la clase y realizar la consulta
$modal = new ClassConsultasProduccion();
$filas = $modal->ExportarFechas($fechaInicio, $fechaFin, $idContratista, $tipoTarea, $area, $division, $cope);

// Nombre del archivo que se sugiere en la respuesta
$nombreArchivo = "Reporte_Liquidadas_" . date('Ymd_His') . ".xls";

// Encabezados HTTP
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename={$nombreArchivo}");
header("Pragma: no-cache");
header("Expires: 0");

// BOM para UTF-8 (mantiene caracteres especiales como acentos y Ñ)
echo "\xEF\xBB\xBF";
?>
<table border="1">
    <thead>
        <tr style="background-color: #004085; color: #ffffff; font-weight: bold;">
            <th>Expediente</th>
            <th>Folio Pisa</th>
            <th>Teléfono</th>
            <th>Tipo de Tarea</th>
            <th>Cope</th>
            <th>Fecha Liquidación</th>
            <th>Semana</th>
            <th>Año</th>
            <th>ID Contratista</th>
            <th>Contratista</th>
            <th>Técnico</th>
            <th>División</th>
            <th>Área</th>
            <th>Num. Serie</th>
            <th>Serie ECI</th>
            <th>Existe Serie Prod Almacén</th>
            <th>Existe Serie ECI Almacén</th>
            <th>Distrito</th>
            <th>Terminal</th>
            <th>Tiene Señalamiento</th>
            <th>Tipo Señalamiento</th>
            <th>Detalle Señalamiento</th>
            <th>Estatus Navegación</th>
            <th>Flag Navega</th>
            <th>Fecha Último Evento</th>
            <th>Contratista Asignado</th>
            <th>Técnico Asignado</th>
            <th>ONT Coordiapp</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($filas)): ?>
            <?php foreach ($filas as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Expediente'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['TELEFONO'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Tipo_de_Tarea'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['NOM_CT'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['FECHA_LIQ'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Semana'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Anio'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['idContratista'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Contratista'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Tecnico'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['NOM_DIVISION'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['NOM_AREA'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Num__Serie'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Serie_Eci'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Existe_Serie_Prod_Almacen'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Existe_Serie_Eci_Almacen'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['DISTRITO'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['TERMINAL'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Tiene_Senalamiento'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Tipo_Senalamiento'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Detalle_Senalamiento'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Estatus_Navegacion'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Flag_Navega'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['FECHA_ULTIMO_EVENTO'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Contratista_Asignado'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Tecnico_Asignado'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Ont_Coordiapp'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="28">No se encontraron registros con los filtros seleccionados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>