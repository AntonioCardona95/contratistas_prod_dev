<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassResultados.php");

session_start();

$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

$resultados = new Resultados();

// Recibe la categoría desde el GET
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$cantidad  = isset($_GET['cantidad']) ? (int)$_GET['cantidad'] : 0;

// Nuevo método en tu clase que filtra por categoría
$detalle = [];
if ($categoria !== '') {
    $detalle = $resultados->getDetalleAlertasPorTipo($categoria);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de <?= htmlspecialchars($categoria) ?></title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #007bff; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #007bff; color: white; }
    </style>
</head>
<body>
    <h1>Resultados de <?= htmlspecialchars($categoria) ?> (<?= $cantidad ?>)</h1>

    <?php if (empty($detalle)): ?>
        <p>No se encontraron registros para esta categoría.</p>
    <?php else: ?>
        <table>
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
            <th>Contratista</th>
            <th>Comparativa Tip Tarea</th>
            <th>Comparativa Tecnologia</th>
            <th>Comparativa Numeros de serie</th>
            <th>Galería</th>
        </tr>
            </thead>
            <tbody>
                <?php foreach ($detalle as $row): ?>
        <tr>

            <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Telefono'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Tipo_Tarea'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Cope'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Fecha_Liquidacion'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Fecha_Carga'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Semana'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['anio'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['metraje'] ?? '') ?></td>

            <td style="color:red; font-weight:bold;">
                <?= htmlspecialchars($row['Alerta'] ?? '') ?>
            </td>

            <td>
                <?= htmlspecialchars($row['Detalle_Esci'] ?? '') ?>
            </td>

            <td style="text-align:center;">
                <?= !empty($row['Bandera_Revision']) ? '✔' : '✖' ?>
            </td>
            <td><?= htmlspecialchars($row['nombre_completo'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Detalle_Tipo_Tarea'] ?? '') ?></td>
            
            <td>
    <?= nl2br(htmlspecialchars($row['Detalle_Tecnologia'] ?? '')) ?>
</td>

<td>
    <?= nl2br(htmlspecialchars($row['Detalle_Series'] ?? '')) ?>
</td>


            <td>
                <button 
                    type="button"
                    class="btn btn-primary btn-sm btn-galeria"
                    data-folio="<?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?>"
                    data-idcontratista="<?= htmlspecialchars($idContratistas ?? '') ?>"
                    data-nombrecontratista="<?= htmlspecialchars($Contratista ?? '') ?>"
                >
                    <i class="fas fa-images"></i>
                </button>
            </td>

        </tr>
    <?php endforeach; ?>

            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
