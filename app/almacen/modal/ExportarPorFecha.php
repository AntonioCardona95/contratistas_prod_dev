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

// Instanciar la clase y consultar datos
$modal = new ClassConsultasProduccion();
$filas = $modal->ExportarFechas($fechaInicio, $fechaFin, $idContratista, $tipoTarea, $area, $division, $cope);

// Extensión .csv para Excel
$nombreArchivo = "Reporte_Liquidadas_" . date('Ymd_His') . ".csv";

// Encabezados HTTP para forzar la descarga del archivo CSV
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename={$nombreArchivo}");
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen('php://output', 'w');
fprintf($output, "\xEF\xBB\xBF"); // UTF-8 BOM para acentos y Ñ

// Encabezados del archivo CSV (sin los de navegación)
$headers = [
    'Expediente', 'Folio Pisa', 'Teléfono', 'Tipo de Tarea', 'Cope', 
    'Fecha Liquidación', 'Semana', 'Año', 'ID Contratista', 'Contratista', 
    'Técnico', 'División', 'Área', 'Num. Serie', 'Serie ECI', 
    'Existe Serie Prod Almacén', 'Existe Serie ECI Almacén', 'Distrito', 'Terminal', 
    'Tiene Señalamiento', 'Tipo Señalamiento', 'Detalle Señalamiento', 
    'Contratista Asignado', 'Técnico Asignado', 'ONT Coordiapp'
];

fputcsv($output, $headers);

if (!empty($filas)) {
    foreach ($filas as $row) {
        $linea = [
            $row['Expediente'] ?? '',
            $row['Folio_Pisa'] ?? '',
            $row['TELEFONO'] ?? '',
            $row['Tipo_de_Tarea'] ?? '',
            $row['NOM_CT'] ?? '',
            $row['FECHA_LIQ'] ?? '',
            $row['Semana'] ?? '',
            $row['Anio'] ?? '',
            $row['idContratista'] ?? '',
            $row['Contratista'] ?? '',
            $row['Tecnico'] ?? '',
            $row['NOM_DIVISION'] ?? '',
            $row['NOM_AREA'] ?? '',
            $row['Num__Serie'] ?? '',
            $row['Serie_Eci'] ?? '',
            $row['Existe_Serie_Prod_Almacen'] ?? '',
            $row['Existe_Serie_Eci_Almacen'] ?? '',
            $row['DISTRITO'] ?? '',
            $row['TERMINAL'] ?? '',
            $row['Tiene_Senalamiento'] ?? '',
            $row['Tipo_Senalamiento'] ?? '',
            $row['Detalle_Senalamiento'] ?? '',
            $row['Contratista_Asignado'] ?? '',
            $row['Tecnico_Asignado'] ?? '',
            $row['Ont_Coordiapp'] ?? ''
        ];
        fputcsv($output, $linea);
    }
} else {
    fputcsv($output, ['No se encontraron registros con los filtros seleccionados.']);
}

fclose($output);
exit;