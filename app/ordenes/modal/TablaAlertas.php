<?php
require_once("../controller/ClassOps.php");

session_start();

$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin    = $_POST['fecha_fin'] ?? null;

if (empty($fecha_inicio) || empty($fecha_fin)) {
    echo "<p>No se han recibido fechas válidas.</p>";
    exit;
}

if (empty($Contratista) || empty($idContratistas)) {
    echo "<p>No se encontró información del contratista.</p>";
    exit;
}

$modal   = new Ops();
$alertas = $modal->ObtenerAlertas($fecha_inicio, $fecha_fin);
$alertas = is_array($alertas) ? $alertas : [];

if (empty($alertas)) {
    echo "<p>No se encontraron alertas en este rango de fechas.</p>";
    exit;
}
?>

<!-- Bootstrap CSS--> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/EstilosTablaSinProcesoPago.css">
<link rel="stylesheet" type="text/css" href="css/modalprenomina.css">

<table class="table table-bordered display nowrap" style="width:100%" id="ventas">
    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Tipo Tarea</th>
            <th>Cope</th>
            <th>Fecha Liquidación</th>
            <th>Fecha Carga</th>
            <th>Semana</th>
            <th>Año</th>
            <th>Metraje</th>
            <th>Alerta</th>
            <th>Detalle Esci</th>
            <th>Revisión</th>
            <th>Galería</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($alertas as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Telefono'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Tipo_Tarea'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Cope'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Fecha_Liquidacion'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Fecha_Carga'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Semana'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Año'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Metraje'] ?? '') ?></td>
            <td style="color:red; font-weight:bold;"><?= htmlspecialchars($row['Alerta'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Detalle_Esci'] ?? '') ?></td>
            <td style="text-align:center;"><?= !empty($row['Bandera_Revision']) ? '✔' : '✖' ?></td>
            <td>
                <button type="button" class="btn btn-primary btn-sm btn-galeria"
                        data-folio="<?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?>"
                        data-idcontratista="<?= htmlspecialchars($idContratistas ?? '') ?>"
                        data-nombrecontratista="<?= htmlspecialchars($Contratista ?? '') ?>">
                    <i class="fas fa-images"></i>
                </button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
