<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Mexico_City');
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

/* =========================
   NORMALIZAR FECHA
========================= */
function normalizarFechaExcel($valorFecha)
{
    if ($valorFecha === null || $valorFecha === '') return null;

    if (is_numeric($valorFecha)) {
        try {
            return Date::excelToDateTimeObject($valorFecha)->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return null;
        }
    }

    $fecha = trim($valorFecha);

    $fecha = str_ireplace(
        ['a. m.', 'p. m.', 'a.m.', 'p.m.', 'a m', 'p m'],
        ['AM', 'PM', 'AM', 'PM', 'AM', 'PM'],
        $fecha
    );

    $formatos = [
        'Y-m-d H:i:s','Y-m-d H:i','Y-m-d',
        'd/m/Y H:i:s A','d/m/Y H:i A',
        'd/m/Y H:i:s','d/m/Y H:i','d/m/Y',
        'd-m-Y H:i:s','d-m-Y H:i','d-m-Y'
    ];

    foreach ($formatos as $formato) {
        $dt = DateTime::createFromFormat($formato, $fecha);
        if ($dt !== false) return $dt->format('Y-m-d H:i:s');
    }

    $timestamp = strtotime($fecha);
    return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
}

if (isset($_POST['submit'])) {

    $semana = $_POST['semana'] ?? '';
    $anio   = $_POST['anio'] ?? date('Y');

    $file = $_FILES['excelFile']['tmp_name'] ?? '';
    if ($file === '') die("No se seleccionó archivo.");

    $spreadsheet = IOFactory::load($file);
    $worksheet   = $spreadsheet->getActiveSheet();
    $highestRow  = $worksheet->getHighestRow();

    $modal = new Ops();

    $insertados = 0;

    $foliosExcel = [];
    $telefonosExcel = [];

    $foliosDuplicados = [];
    $telefonosDuplicados = [];
    $foliosRevision = [];

    // 🔥 NUEVO: ARRAY PARA ALERTAS MASIVAS
    $registrosParaAlertas = [];

    for ($i = 2; $i <= $highestRow; $i++) {

        $folio      = trim((string)$worksheet->getCell('A'.$i)->getFormattedValue());
        $telefono   = trim((string)$worksheet->getCell('B'.$i)->getFormattedValue());
        $tipoTarea  = trim((string)$worksheet->getCell('C'.$i)->getFormattedValue());
        $cope       = trim((string)$worksheet->getCell('D'.$i)->getFormattedValue());
        $fechaExcel = $worksheet->getCell('E'.$i)->getValue();
        $metraje    = trim((string)$worksheet->getCell('F'.$i)->getFormattedValue());

        if ($folio === '' || $telefono === '' || $fechaExcel === null || $fechaExcel === '') {
            continue;
        }

        // =========================
        // DUPLICADOS EN EXCEL
        // =========================
        if (isset($foliosExcel[$folio])) {
            $foliosDuplicados[] = $folio;
            continue;
        }

        if (isset($telefonosExcel[$telefono])) {
            $telefonosDuplicados[] = $telefono;
            continue;
        }

        $foliosExcel[$folio] = true;
        $telefonosExcel[$telefono] = true;

        $fechaLiquidacion = normalizarFechaExcel($fechaExcel);
        if ($fechaLiquidacion === null) continue;

        // =========================
        // CALCULAR SEMANA
        // =========================
        $dt = new DateTime($fechaLiquidacion);

        if ($semana === '') {
            $semanaCalculada = (int)$dt->format('W');
            $anioCalculado   = (int)$dt->format('o');
        } else {
            $semanaCalculada = (int)$semana;
            $anioCalculado   = (int)$anio;
        }

        // =========================
        // DUPLICADOS EN BD
        // =========================
        if ($modal->ExisteFolioProduccion($folio, $telefono, $idContratistas)) {
            $foliosDuplicados[] = $folio;
            $telefonosDuplicados[] = $telefono;
            continue;
        }

        // =========================
        // INSERTAR PRODUCCION
        // =========================
        $resultado = $modal->InsertarProduccion(
            $folio,
            $telefono,
            $tipoTarea,
            $cope,
            $fechaLiquidacion,
            $idContratistas,
            $semanaCalculada,
            $anioCalculado,
            $metraje
        );

        if ($resultado['insertado']) {

            $insertados++;

            // 🔥 GUARDAMOS PARA ALERTAS MASIVAS
            $registrosParaAlertas[] = [
                'folio' => $folio,
                'telefono' => $telefono,
                'tipoTarea' => $tipoTarea,
                'cope' => $cope,
                'fechaLiquidacion' => $fechaLiquidacion,
                'idContratista' => $idContratistas,
                'semana' => $semanaCalculada,
                'anio' => $anioCalculado,
                'metraje' => $metraje,
                'fechaCarga' => $resultado['fechaCarga']
            ];
        }
    }

    // AQUÍ OCURRE LA MAGIA (UNA SOLA VEZ)
    if (!empty($registrosParaAlertas)) {
        $modal->InsertarAlertasMasivo($registrosParaAlertas);
    }

    $foliosDuplicados   = array_unique($foliosDuplicados);
    $telefonosDuplicados = array_unique($telefonosDuplicados);
    $foliosRevision     = array_unique($foliosRevision);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>

<?php if (!empty($foliosDuplicados) || !empty($telefonosDuplicados) || !empty($foliosRevision)) { ?>

Swal.fire({
    icon: 'warning',
    title: 'Resultado de la importación',
    html: `
        <?php if (!empty($foliosDuplicados)) { ?>
            <p><b>Folios duplicados (NO insertados):</b></p>
            <div style="max-height:150px;overflow:auto;text-align:left">
            <?= implode("<br>", $foliosDuplicados) ?>
            </div>
            <br>
        <?php } ?>

        <?php if (!empty($telefonosDuplicados)) { ?>
            <p><b>Teléfonos duplicados (NO insertados):</b></p>
            <div style="max-height:150px;overflow:auto;text-align:left">
            <?= implode("<br>", $telefonosDuplicados) ?>
            </div>
            <br>
        <?php } ?>

        <?php if (!empty($foliosRevision)) { ?>
            <p style="color:red;"><b>Folios enviados a revisión (+30 días):</b></p>
            <div style="max-height:150px;overflow:auto;text-align:left">
            <?= implode("<br>", $foliosRevision) ?>
            </div>
            <br>
        <?php } ?>

        <b>Registros insertados:</b> <?= $insertados ?>
    `,
    confirmButtonText: 'Aceptar'
}).then(() => {
    window.location.href = "CargaExcel.php?cargado=1&semana=<?= $semana ?>&anio=<?= $anio ?>";
});

<?php } else { ?>

Swal.fire({
    icon: 'success',
    title: 'Importación completada',
    text: 'Se insertaron <?= $insertados ?> registros correctamente.',
    confirmButtonText: 'Aceptar'
}).then(() => {
    
    window.location.href = "CargaExcel.php?cargado=1&semana=<?= $semana ?>&anio=<?= $anio ?>";
});

<?php } ?>

</script>
</body>
</html>