<?php
// Incluir el controlador
require_once(__DIR__ . "/../controller/ClassConsultasProduccion.php");

// Recibir los parámetros desde el enlace JS
$fechaInicio   = !empty($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d');
$fechaFin      = !empty($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');
$idContratista = !empty($_GET['contratista']) ? $_GET['contratista'] : null;
$tipoTarea     = !empty($_GET['tipo_tarea']) ? $_GET['tipo_tarea'] : null;
$area          = !empty($_GET['area']) ? $_GET['area'] : null;
$division      = !empty($_GET['division']) ? $_GET['division'] : null;
$cope          = !empty($_GET['cope']) ? $_GET['cope'] : null;

// Instanciar clase de consultas
$modal = new ClassConsultasProduccion();

// Consultar los datos con los filtros aplicados
$filas = $modal->ConsultarProduccionDiaria($fechaInicio, $fechaFin, $idContratista, $tipoTarea, $area, $division, $cope);

// Nombre dinámico para la descarga
$nombreArchivo = "Reporte_Liquidadas_" . date('Ymd_His') . ".xls";

// Configuración de encabezados HTTP para forzar la descarga en Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename={$nombreArchivo}");
header("Pragma: no-cache");
header("Expires: 0");

// BOM para mantener codificación UTF-8 correcta (acentos, Ñ, etc.)
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