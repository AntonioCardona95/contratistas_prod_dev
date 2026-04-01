<?php
// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusión de clases necesarias
//require_once("../controller/ClassOps.php");
require_once("../controller/ClassEmulador.php");

// Inicio de sesión y obtención de variables de sesión
session_start();
$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

// Recepción y validación de parámetros de fechas
$Fecha1 = $_POST['Fecha1'] ?? '';
$Fecha2 = $_POST['Fecha2'] ?? '';
$mes_anterior = date('Y-m-d', strtotime('-1 month'));

// También puedes usar var_dump para ver tipo y contenido
/*echo '<pre>';
var_dump($Fecha1);
var_dump($Fecha2);
echo '</pre>'; */

// Debug con etiquetas
//echo '<h3>Debug variables POST y cálculo</h3>';
//echo 'Fecha1: ' . ($Fecha1 !== '' ? $Fecha1 : 'VACÍO') . '<br>';
//echo 'Fecha2: ' . ($Fecha2 !== '' ? $Fecha2 : 'VACÍO') . '<br>';
//echo 'Mes anterior: ' . $mes_anterior . '<br>';

if (empty($Fecha1)) {
    $Fecha1 = $mes_anterior . " 00:00:00";
    $Fecha2 = date('Y-m-d') . " 23:59:59";
} else {
    $Fecha1 = $Fecha1 . " 00:00:00";
    $Fecha2 = $Fecha2 . " 23:59:59";
}

// Consulta de órdenes
$modal = new Emulador();
$filas = $modal->ProduccionContratista($idContratistas, $Fecha1, $Fecha2);
$filas = is_array($filas) ? $filas : [];
?>
<table class="table table-bordered display nowrap" style="width:100%" id="ventas">
    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Tipo Tarea</th>
            <th>Cope</th>
            <th>Contratista</th>
            <th>Fecha Liquidacion</th>
            <th>Semana</th>
            <th>Año</th>
            <th>Metraje</th>
            <th>Fecha Carga</th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Tipo Tarea</th>
            <th>Cope</th>
            <th>Contratista</th>
            <th>Fecha Liquidacion</th>
            <th>Semana</th>
            <th>Año</th>
            <th>Metraje</th>
            <th>Fecha Carga</th>
        </tr>
    </tfoot>

    <tbody>
    <?php foreach ($filas as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Telefono'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Tipo_Tarea'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Cope'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['FK_Contratista'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Fecha_Liquidacion'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Semana'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['año'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['metraje'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Fecha_Carga'] ?? '') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
    $(document).ready(function () {
        let table = new DataTable('#ventas', {
            fixedColumns: {
                start: 1,
                end: 1
            },
            responsive:true,
            'language':{
                'URL':'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
            },
            "ordering": false,
            "searching": true,
                "scrollY": true,
                "scrollX": true
        });
    
        new DataTable.Buttons(table, {
                buttons: [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fas fa-file-excel"></i> ',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend:    'pdfHtml5',
                        text:      '<i class="fas fa-file-pdf"></i> ',
                        titleAttr: 'Exportar a PDF',
                        className: 'btn btn-danger'
                    }
                ],
        });
        
        table
            .buttons(0, null)
            .container()
            .prependTo(table.table().container());
    });
</script>