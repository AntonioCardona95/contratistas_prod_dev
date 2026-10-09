<?php
require_once(__DIR__ . "/../controller/ClassConsultasProduccion.php");

// Recibir los parámetros mediante POST
$fechaInicio   = $_POST['fecha_inicio'] ?? '';
$fechaFin      = $_POST['fecha_fin'] ?? '';
$idContratista = !empty($_POST['contratista']) ? $_POST['contratista'] : null;
$tipoTarea     = !empty($_POST['tipo_tarea']) ? $_POST['tipo_tarea'] : null;
$area          = !empty($_POST['area']) ? $_POST['area'] : null;
$division      = !empty($_POST['division']) ? $_POST['division'] : null;
$cope          = !empty($_POST['cope']) ? $_POST['cope'] : null;

// Instanciar la clase y consultar datos
$modal = new ClassConsultasProduccion();
$filas = $modal->ConsultarProduccionDiaria($fechaInicio, $fechaFin, $idContratista, $tipoTarea, $area, $division, $cope);

$nombreArchivo = "Reporte_Liquidadas_" . date('Ymd_His') . ".xls";

// Encabezados para forzar la descarga de Excel nativo
header("Content-Type: application/vnd.ms-excel; charset=UTF-16LE");
header("Content-Disposition: attachment; filename={$nombreArchivo}");
header("Pragma: no-cache");
header("Expires: 0");

// Encabezados de la tabla
$headers = [
    'Expediente', 'Folio Pisa', 'Teléfono', 'Tipo de Tarea', 'Cope', 
    'Fecha Liquidación', 'Semana', 'Año', 'ID Contratista', 'Contratista', 
    'Técnico', 'División', 'Área', 'Num. Serie', 'Serie ECI', 
    'Existe Serie Prod Almacén', 'Existe Serie ECI Almacén', 'Distrito', 'Terminal', 
    'Tiene Señalamiento', 'Tipo Señalamiento', 'Detalle Señalamiento', 
    'Estatus Navegación', 'Flag Navega', 'Fecha Último Evento', 
    'Contratista Asignado', 'Técnico Asignado', 'ONT Coordiapp'
];

// Salida en formato UTF-16LE para que Excel lo abra directamente sin avisos de advertencia
$output = implode("\t", $headers) . "\r\n";

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
            $row['Estatus_Navegacion'] ?? '',
            $row['Flag_Navega'] ?? '',
            $row['FECHA_ULTIMO_EVENTO'] ?? '',
            $row['Contratista_Asignado'] ?? '',
            $row['Tecnico_Asignado'] ?? '',
            $row['Ont_Coordiapp'] ?? ''
        ];
        
        // Limpiar saltos de línea dentro de los campos
        $lineaLimpia = array_map(function($campo) {
            return str_replace(["\r", "\n", "\t"], ' ', $campo);
        }, $linea);

        $output .= implode("\t", $lineaLimpia) . "\r\n";
    }
} else {
    $output .= "No se encontraron registros con los filtros seleccionados.\r\n";
}

// Convertir codificación a UTF-16LE para compatibilidad transparente con Excel
echo mb_convert_encoding($output, 'UTF-16LE', 'UTF-8');
exit;