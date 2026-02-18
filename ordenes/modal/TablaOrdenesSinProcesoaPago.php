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

$semana = $_POST['semana'] ?? null;

// Si no hay semana seleccionada, no mostrar tabla
if (empty($semana)) {
    echo ""; // No mostrar nada
    exit;
}

if (empty($Contratista) || empty($idContratistas)) {
    echo "<p>No se encontró información del contratista.</p>";
    exit;
}

// Instancia de la clase Ops
$modal = new Ops();

// Obtener expedientes de técnicos del contratista
$expedientesData = $modal->ObtenerExpedientesPorContratista($idContratistas);
$expedientesData = is_array($expedientesData) ? $expedientesData : [];

// Convertir los expedientes a un arreglo simple
$expedientes = [];
foreach ($expedientesData as $exp) {
    $expedientes[] = $exp['NExpediente'];
}


/*echo "<pre>";
print_r($expedientes);
echo "</pre>"; */

// Si no hay expedientes, no continuar
if (empty($expedientes)) {
    echo "<p>No se encontraron expedientes de técnicos para este contratista.</p>";
    exit;
}

// Ahora pasamos los expedientes a la función de Producción
$filas = $modal->OrdenesConIncidencias($idContratistas,$expedientes,$semana);
$filas = is_array($filas) ? $filas : [];


?>

<!-- Estilos personalizados para DataTable -->
<style>
/* Texto general dentro de la tabla */
table.dataTable,
table.dataTable td,
table.dataTable th {
    color: #000 !important; /* texto negro */
    font-weight: 600; /* negrito */
}

/* Encabezados */
table.dataTable thead th {
    background-color: #f8f9fa !important; /* gris muy claro */
    color: #000 !important; /* negro negrito */
    font-weight: 800 !important;
    text-align: center;
}

/* Filas al pasar mouse */
table.dataTable tbody tr:hover {
    background-color: #e9ecef !important; /* gris muy suave */
}

/* Botones de exportación */
.dt-buttons button {
    background-color: #4e73df !important; /* azul */
    color: #fff !important; /* texto blanco */
    font-weight: 600 !important;
    border-radius: 4px !important;
    border: none !important;
    padding: 5px 10px !important;
    margin-right: 5px !important;
}

/* Mantener color de botones igual al pasar mouse */
.dt-buttons button:hover,
.dt-buttons button:focus,
.dt-buttons button:active {
    background-color: #4e73df !important;
    color: #fff !important;
}

/* Etiqueta Search */
.dataTables_filter label {
    color: #000 !important;
    font-weight: 600 !important;
}

/* Input de búsqueda */
.dataTables_filter input {
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    padding: 3px 6px !important;
    font-weight: 600 !important;
    color: #000 !important;
    background-color: #fff !important;
}

/* Información de tabla (Showing x to y) */
.dataTables_info {
    color: #000 !important;
    font-weight: 600 !important;
}

/* Paginación */
.dataTables_paginate .paginate_button {
    color: #000 !important;
    font-weight: 600 !important;
    background: #fff !important;
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    margin: 0 2px !important;
    padding: 2px 6px !important;
}

/* Quitar remarcado azul al botón actual o hover */
.dataTables_paginate .paginate_button.current,
.dataTables_paginate .paginate_button:hover {
    background-color: #fff !important; /* fondo blanco */
    color: #000 !important; /* texto negro */
}
</style>
<table class="table table-bordered display nowrap" style="width:100%" id="ventas">
    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Tipo de tarea</th>
            <th>Cope</th>
            <th>Fecha de liquidacion</th>
            <th>Semana</th>
            <th>Alerta</th>
            <th>Calificador</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($filas as $row): ?>
    
    <tr>
        <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['Telefono'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['Tipo_Tarea'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['Cope'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['Fecha_Liquidacion'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['Semana'] ?? '') ?></td>
        <td style="color: red; font-weight: bold;"><?= htmlspecialchars($row['Alerta'] ?? '') ?></td>
        <td style="text-align: center; font-weight: bold;">
            <?= !empty($row['Alerta']) ? 'X' : '✓' ?>
        </td>
        
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- Script para inicializar DataTable con estilos y botones -->
<script>
$(document).ready(function () {
    new DataTable('#ventas', {
        fixedColumns: { start: 1, end: 1 },
        responsive:true,
        language: { URL:'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
        ordering: false,
        searching: true,
        scrollY: true,
        scrollX: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger'
            }
        ]
    });
});
</script>