<?php
// Errores solo en log (no en pantalla)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

require_once(__DIR__ . '/controller/ClassOps.php');

$Contratista    = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

if (empty($Contratista) || empty($idContratistas)) {
    die("Sesión no válida.");
}

if (isset($_POST['submit'])) {

    $semana = $_POST['semana'] ?? '';
    if ($semana === '') {
        die("Debes seleccionar una semana.");
    }

    $file = $_FILES['excelFile']['tmp_name'] ?? '';
    if ($file === '') {
        die("No se seleccionó archivo.");
    }

    $spreadsheet = IOFactory::load($file);
    $worksheet   = $spreadsheet->getActiveSheet();
    $rows        = $worksheet->toArray();

    $modal = new Ops();
    $insertados = 0;

    // 🔹 Duplicados
    $foliosExcel      = [];
    $foliosDuplicados = [];

    for ($i = 1; $i < count($rows); $i++) {

        $folio      = trim((string)($rows[$i][0] ?? ''));
        $telefono   = trim((string)($rows[$i][1] ?? ''));
        $tipoTarea  = trim((string)($rows[$i][2] ?? ''));
        $cope       = trim((string)($rows[$i][3] ?? ''));
        $fechaExcel = $rows[$i][4] ?? null;

        if ($folio === '' || $fechaExcel === null || $fechaExcel === '') {
            continue;
        }

        // 🔹 Duplicado en el mismo Excel
        if (isset($foliosExcel[$folio])) {
            $foliosDuplicados[] = $folio;
            continue;
        }
        $foliosExcel[$folio] = true;

        // 🔹 Ya existe en la BD
        if ($modal->ExisteFolioProduccion($folio, $idContratistas)) {
            $foliosDuplicados[] = $folio;
            continue;
        }

        $fechaLiquidacion = null;
        $fechaExcel = trim((string)$fechaExcel);

        if (is_numeric($fechaExcel)) {
            $fechaLiquidacion = Date::excelToDateTimeObject($fechaExcel)
                ->format('Y-m-d H:i:s');
        }

        if ($fechaLiquidacion === null) {
            $dt = DateTime::createFromFormat('Y-m-d H:i:s', $fechaExcel);
            if ($dt !== false) {
                $fechaLiquidacion = $dt->format('Y-m-d H:i:s');
            }
        }

        if ($fechaLiquidacion === null) {
            $dt = DateTime::createFromFormat('Y-m-d', $fechaExcel);
            if ($dt !== false) {
                $fechaLiquidacion = $dt->format('Y-m-d 00:00:00');
            }
        }

        if ($fechaLiquidacion === null) {
            $dt = DateTime::createFromFormat('d/m/Y', $fechaExcel);
            if ($dt !== false) {
                $fechaLiquidacion = $dt->format('Y-m-d 00:00:00');
            }
        }

        if ($fechaLiquidacion === null) {
            $dt = DateTime::createFromFormat('d-m-Y', $fechaExcel);
            if ($dt !== false) {
                $fechaLiquidacion = $dt->format('Y-m-d 00:00:00');
            }
        }

        if ($fechaLiquidacion === null) {
            continue;
        }

        $modal->InsertarProduccion(
            $folio,
            $telefono,
            $tipoTarea,
            $cope,
            $fechaLiquidacion,
            $idContratistas,
            $semana
        );

        $insertados++;
    }

    // 🔹 MODAL SI HAY DUPLICADOS
    if (!empty($foliosDuplicados)) {
        $foliosDuplicados = array_unique($foliosDuplicados);
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Folios duplicados</title>

            <!-- CSS EXTERNO DEL MODAL -->
            <link rel="stylesheet" href="css/estilos-modal-repetidos.css">
        </head>
        <body>
            <div class="overlay">
                <div class="modal">
                    <div class="modal-header">Folios duplicados detectados</div>
                    <div class="modal-body">
                        <p>Los siguientes folios <strong>NO se insertaron</strong>:</p>
                        <ul>
                            <?php foreach ($foliosDuplicados as $f): ?>
                                <li><?= htmlspecialchars($f) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p><strong>Registros insertados:</strong> <?= $insertados ?></p>
                    </div>
                    <div class="modal-footer">
                        <button onclick="window.location.href='https://contratistas.ed-intra.com/ordenes/CargaExcel.php'">
                            Aceptar
                        </button>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    echo "✔ Se insertaron correctamente {$insertados} registros.";
    exit;
}
?>