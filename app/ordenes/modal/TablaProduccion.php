<?php
/**
 * Tabla de Producción Contratista
 * Muestra las órdenes de Producción para un contratista.
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusión de clases necesarias
require_once("../controller/ClassOps.php");

// Inicio de sesión y obtención de variables de sesión
session_start();
$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

if (empty($Contratista) || empty($idContratistas)) {
    echo "<p>No se encontró información del contratista.</p>";
    exit;
}

// Instancia de la clase Ops
$modal = new Ops();

// Consulta de Producción
//$filas = $modal->OrdenesProduccion($idContratistas);
$filas = $modal->OrdenesNoProcesadasPago($idContratistas);
$filas = is_array($filas) ? $filas : [];

?>

<table class="table table-bordered display nowrap" style="width:100%" id="ventas">
    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Tipo de tarea</th>
            <th>Cope</th>
            <th>Alerta</th>
            <th>Comparativo Telefonos</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($filas as $row): ?>
    <?php 
        $folio    = $row['Folio_Pisa'] ?? '';
        $telefono = $row['Telefono'] ?? '';

        // Llamamos la función pasando folio y teléfono
        $resultado = $folio ? $modal->VerificarFolios($folio, $telefono) : ['alertas'=>[], 'comparativo'=>''];

        $alerta = implode(" | ", $resultado['alertas']);
        $comparativo = $resultado['comparativo'];
    ?>
    <tr>
        <td><?= htmlspecialchars($folio) ?></td>
        <td><?= htmlspecialchars($telefono) ?></td>
        <td><?= htmlspecialchars($row['Tipo_Tarea'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['Cope'] ?? '') ?></td>
        <td><?= htmlspecialchars($alerta) ?></td>
        <td><?= $comparativo ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
    $(document).ready(function () {
        let table = new DataTable('#ventas', {
            fixedColumns: { start: 1, end: 1 },
            responsive:true,
            language: { URL:'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: false,
            searching: true,
            scrollY: true,
            scrollX: true
        });

        new DataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> ',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> ',
                    titleAttr: 'Exportar a PDF',
                    className: 'btn btn-danger'
                }
            ],
        });

        table.buttons(0, null).container().prependTo(table.table().container());
    });
</script>
