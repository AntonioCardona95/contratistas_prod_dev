<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../controller/ClassConsultasProduccion.php");

$consultas = new ClassConsultasProduccion();

$Fecha1 = $_GET['Fecha1'] ?? '';
$Fecha2 = $_GET['Fecha2'] ?? '';

$filas = [];

if (!empty($Fecha1) && !empty($Fecha2)) {
    $Fecha1 = $Fecha1 . " 00:00:00";
    $Fecha2 = $Fecha2 . " 23:59:59";

    // Consulta única que trae producción + navegación + señalamientos
    $filas = $consultas->VisualizarTablaFechas($Fecha1, $Fecha2);
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<table class="table table-bordered display nowrap" style="width:100%" id="ventas">
    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Teléfono</th>
            <th>Tipo Tarea</th>
            <th>Contratista</th>
            <th>Técnico</th>
            <th>División</th>
            <th>Área</th>
            <th>Número Serie Prod</th>
            <th>Número Serie Eci</th>
            <th>Prod en Almacén</th>
            <th>Eci en Almacén</th>
            <th>Fecha Liquidación</th>
            <th>Semana</th>
            <th>Año</th>
            <th>Navegación</th>
            <th>Último Evento</th>
            <th>Señalamiento</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($filas)): ?>
            <?php foreach ($filas as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['TELEFONO'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Tipo_de_Tarea'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Contratista'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Tecnico'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['NOM_DIVISION'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['NOM_AREA'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Num__Serie'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Serie_Eci'] ?? '') ?></td>
                    <td>
                        <?php
                        $badgeClass = ($row['Existe_Serie_Prod_Almacen'] ?? 0) == 1 ? 'badge-success' : 'badge-danger';
                        $texto      = ($row['Existe_Serie_Prod_Almacen'] ?? 0) == 1 ? 'Sí' : 'No';
                        echo '<span class="badge '.$badgeClass.'">'.$texto.'</span>';
                        ?>
                    </td>
                    <td>
                        <?php
                        $badgeClass = ($row['Existe_Serie_Eci_Almacen'] ?? 0) == 1 ? 'badge-success' : 'badge-danger';
                        $texto      = ($row['Existe_Serie_Eci_Almacen'] ?? 0) == 1 ? 'Sí' : 'No';
                        echo '<span class="badge '.$badgeClass.'">'.$texto.'</span>';
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['FECHA_LIQ'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Semana'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Anio'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Estatus_Navegacion'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['FECHA_ULTIMO_EVENTO'] ?? '') ?></td>
                    <td>
                        <?php
                        if (!empty($row['Tipo_Senalamiento'])) {
                            echo '<span class="badge badge-warning">'.htmlspecialchars($row['Tipo_Senalamiento']).'</span>';
                            if (!empty($row['Detalle_Senalamiento'])) {
                                echo ' - '.htmlspecialchars($row['Detalle_Senalamiento']);
                            }
                        } else {
                            echo '<span class="badge badge-success">Sin señalamiento</span>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="17">No hay registros en el rango seleccionado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    $(document).ready(function () {
        let table = new DataTable('#ventas', {
            fixedColumns: {
                start: 1,
                end: 1
            },
            responsive: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
            },
            ordering: false,
            searching: true,
            scrollX: true,
            scrollY: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i>',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i>',
                    titleAttr: 'Exportar a PDF',
                    className: 'btn btn-danger btn-sm',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                }
            ]
        });

        table.buttons().container().prependTo(table.table().container());
    });
</script>
