<?php
require_once($_SERVER['DOCUMENT_ROOT']."/ordenes/controller/ClassResultados.php");

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
                    <th>ID</th>
                    <th>Orden</th>
                    <th>Teléfono</th>
                    <th>Serie</th>
                    <th>Fecha</th>
                    <!-- agrega las columnas que tengas en tu tabla -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalle as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['orden']) ?></td>
                        <td><?= htmlspecialchars($row['telefono']) ?></td>
                        <td><?= htmlspecialchars($row['serie']) ?></td>
                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
