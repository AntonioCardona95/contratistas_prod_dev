<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusión de clases necesarias
require_once("../controller/ClassOps.php");

// Inicio de sesión y obtención de variables de sesión
session_start();
$Contratista    = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

// Valores enviados desde AJAX
$emulador   = $_POST['emulador'] ?? null;
$semana     = $_POST['semana'] ?? null;
$tecnologia = $_POST['tecnologia'] ?? null;

// Validaciones básicas
if (empty($Contratista) || empty($idContratistas)) {
    echo "<p>No se encontró información del contratista.</p>";
    exit;
}

if (empty($emulador) || empty($semana) || empty($tecnologia)) {
    echo "<p>Faltan filtros para mostrar la información.</p>";
    exit;
}

// Normalizamos emulador
$emulador = strtoupper(trim($emulador));

// Instancia de la clase Ops
$modal = new Ops();

// eso solo me obtiene los datos de coordiapp - tac y produccion contratistas
$filas = $modal->ObtenerProduccionContratistaSinIncidencias(
    $idContratistas,
    $emulador,
    $semana,
    $tecnologia
);

$filas = is_array($filas) ? $filas : [];

// Mostrar en pantalla para depuración 
/*echo "<h3>Contenido de \$filas:</h3>"; 
echo "<pre>"; 
print_r($filas); 
echo "</pre>"; */

if (empty($filas)) {
    echo "<p>No se encontraron registros sin incidencias para los filtros seleccionados.</p>";
    exit;
}


// COLUMNAS EXTRA POR EMULADOR

$columnasExtra = [];

if ($emulador === 'FIBRA' || $emulador === 'MIGRACION') {
    $columnasExtra = [
        'Bajante aéreo 25m','Bajante aéreo 50m','Bajante aéreo 75m','Bajante aéreo 100m',
        'Bajante aéreo 125m','Bajante aéreo 150m','Bajante aéreo 175m','Bajante aéreo 200m',
        'Bajante aéreo 250m','Bajante aéreo 300m','ML bajantes aéreos >300m',
        'Bajante subterráneo 25m','Bajante subterráneo 50m','Bajante subterráneo 75m',
        'Bajante subterráneo 100m','Bajante subterráneo 125m','Bajante subterráneo 150m',
        'Bajante subterráneo 175m','Bajante subterráneo 200m','Bajante subterráneo 250m',
        'Bajante subterráneo 300m','ML bajantes subterráneos >300m',
        'Visita adicional','Conexión FO por fusión','Radial en banqueta','Radial en cepa libre',
        'Reparación de radial','Migración cobre a FO','Migración cobre a FO con visita'
    ];
}

if ($emulador === 'COBRE') {
    $columnasExtra = [
        'Línea básica 1 par','Plusvalía tramo adicional 50m','Bonificación 1 a 5 OS',
        'Bonificación 6 a 15 OS','Bonificación 16 a 25 OS','Bonificación >25 OS',
        'Montaje de puente','Cableado interior 1 aparato','Cableado interior adicional',
        'Instalación de poste 25','Pruebas VDSL en roseta','Cableado interior + modem',
        'Identificación red principal','Identificación red secundaria',
        'Ubicación cliente y prueba VDSL','Prueba VDSL adicional'
    ];
}

// FUNCIONES DE APOYO
function obtenerRangoMetrajeFibra($metraje) {
    if ($metraje <= 25)  return '25m';
    if ($metraje <= 50)  return '50m';
    if ($metraje <= 75)  return '75m';
    if ($metraje <= 100) return '100m';
    if ($metraje <= 125) return '125m';
    if ($metraje <= 150) return '150m';
    if ($metraje <= 175) return '175m';
    if ($metraje <= 200) return '200m';
    if ($metraje <= 250) return '250m';
    if ($metraje <= 300) return '300m';
    return '>300m';
}

function obtenerPlusvaliaCobre($metraje) {
    if ($metraje <= 50)  return 0;
    if ($metraje <= 100) return 1;
    if ($metraje <= 150) return 2;
    if ($metraje <= 200) return 3;
    if ($metraje <= 250) return 4;
    return 5;
}

?>
<link rel="stylesheet" href="css/estilosdatatable_nomina.css">
<!--  MODAL GALERÍA -->
<div class="modal fade" id="modalGaleria" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Galería</h5>
        <button type="button" data-bs-dismiss="modal"
            style="width:2rem;height:2rem;background:transparent;border:none;
                   font-size:2rem;color:red;line-height:1;">&times;</button>
      </div>

      <div class="modal-body">
        <p><strong>Nombre Contratista:</strong> <span id="modalNombreContratista"></span></p>
        <p><strong>ID Contratista:</strong> <span id="modalIdContratista"></span></p>
        <p><strong>Folio:</strong> <span id="modalFolio"></span></p>

        <div class="galeria-fotos">
          <div>
            <p><strong>Foto Casa Cliente:</strong></p>
            <a href="#" id="fotoGrande" target="_blank">
              <img id="miniaturaFoto" src="" alt="">
            </a>
          </div>

          <div>
            <p><strong>Foto Modem:</strong></p>
            <a href="#" id="fotoGrandemodem" target="_blank">
              <img id="miniaturaFotomodem" src="" alt="">
            </a>
          </div>

          <div>
            <p><strong>Foto Trasera ONT:</strong></p>
            <a href="#" id="fotoGrandemodemtrasera" target="_blank">
              <img id="miniaturaFotomodemtrasera" src="" alt="">
            </a>
          </div>

          <div>
            <p><strong>Foto OS:</strong></p>
            <a href="#" id="fotoGrandeos" target="_blank">
              <img id="miniaturaFotoos" src="" alt="">
            </a>
          </div>

          <div>
            <p><strong>Foto Terminal:</strong></p>
            <a href="#" id="fotopuerto" target="_blank">
              <img id="miniaturafotopuerto" src="" alt="">
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===========================
     TABLA
=========================== -->
<table class="table table-bordered display nowrap" id="ventas" style="width:100%">

<thead>
<tr>
    <th data-col="folio_pisa">Folio Pisa</th>
    <th data-col="galeria">Galería</th>
    <th data-col="telefono">Teléfono</th>
    <th data-col="tecnologia">Tecnología</th>
    <th data-col="tipo_tarea">Tipo de tarea</th>
    <th data-col="cope">COPE</th>
    <th data-col="tecnico">Técnico</th>
    <th data-col="expediente">Expediente</th>
    <th data-col="distrito">Distrito</th>
    <th data-col="numero_serie">Número de Serie (ONT)</th>
    <th data-col="fecha_liquidacion">Fecha de Liquidación</th>
    <th data-col="area">Área</th>
    <th data-col="division">División</th>
    <th data-col="direccion">Direccion</th>
    <th data-col="terminal">Terminal</th>
    <th data-col="puerto">Puerto</th>
    <th data-col="tipo_instalacion">Tipo Instalacion</th>
    <th data-col="latitud">Latitud</th>
    <th data-col="longitud">Longitud</th>

    <?php foreach ($columnasExtra as $col): ?>
        <th data-col="<?= htmlspecialchars($col) ?>">
            <?= htmlspecialchars($col) ?>
        </th>
    <?php endforeach; ?>
</tr>
</thead>
<tbody>
<?php foreach ($filas as $row): ?>
<tr>
    <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>

    <td>
        <button class="btn btn-link btn-galeria"
            data-nombrecontratista="<?= htmlspecialchars($Contratista) ?>"
            data-idcontratista="<?= htmlspecialchars($idContratistas) ?>"
            data-folio="<?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?>"
            data-bs-toggle="modal"
            data-bs-target="#modalGaleria">
            <i class="fa-solid fa-images" style="font-size:2.5em;color:#007bff"></i>
        </button>
    </td>

    <td><?= htmlspecialchars($row['Telefono'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Tecnologia'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Tipo_Tarea'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['COPE'] ?? '') ?></td>
    <td><?= htmlspecialchars(($row['Nombre_T'] ?? '') . ' ' . ($row['Apellidos_T'] ?? '')) ?></td>
    <td><?= htmlspecialchars($row['NExpediente'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Distrito'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Numero_Serie'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Fecha_Liquidacion'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['area'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Division'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Direccion_Cliente'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Terminal'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Puerto'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Tipo_Instalacion'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Latitud'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Longitud'] ?? '') ?></td>

<?php
    $metraje = (int)($row['Metraje'] ?? 0);
    $tipoInstalacion = strtoupper($row['Tipo_Instalacion'] ?? '');
    $rangoFibra = obtenerRangoMetrajeFibra($metraje);
    $plusvalia  = obtenerPlusvaliaCobre($metraje);

    foreach ($columnasExtra as $col) {

        $valor = '';

        /* ===== FIBRA / MIGRACION ===== */
        if ($emulador === 'FIBRA' || $emulador === 'MIGRACION') {

            if ($tipoInstalacion === 'AEREA' && str_contains($col, 'Bajante aéreo')) {
                if (str_contains($col, $rangoFibra)) {
                    $valor = '1';
                }
            }

            if ($tipoInstalacion === 'SUBTERRANEA' && str_contains($col, 'Bajante subterráneo')) {
                if (str_contains($col, $rangoFibra)) {
                    $valor = '1';
                }
            }
        }

        /* ===== COBRE ===== */
        if ($emulador === 'COBRE' && $col === 'Plusvalía tramo adicional 50m') {
            $valor = $plusvalia;
        }

        echo '<td style="text-align:center;font-weight:bold;">'.$valor.'</td>';
    }
?>
</tr>
<?php endforeach; ?>
</tbody>

<tfoot>
<tr>
    <th>Folio Pisa</th>
    <th>Galería</th>
    <th>Teléfono</th>
    <th>Tecnología</th>
    <th>Tipo de tarea</th>
    <th>COPE</th>
    <th>Técnico</th>
    <th>Expediente</th>
    <th>Distrito</th>
    <th>Número de Serie (ONT)</th>
    <th>Fecha de Liquidación</th>
    <th>Área</th>
    <th>División</th>
    <th>Direccion</th>
    <th>Terminal</th>
    <th>Puerto</th>
    <th>Tipo instalacion</th>
    <th>Latitud</th>
    <th>Longitud</th>

    <?php foreach ($columnasExtra as $col): ?>
        <th><?= htmlspecialchars($col) ?></th>
    <?php endforeach; ?>
</tr>
</tfoot>
</table>

<!-- ===========================
     SCRIPTS NECESARIOS PARA BOTONES
=========================== -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="js/galeria_fotos.js"></script>
<script>
$(document).ready(function () {

    const table = new DataTable('#ventas', {
        scrollX: true,
        scrollCollapse: true,
        autoWidth: false,
        ordering: false,
        searching: true,
        fixedColumns: { left: 1 },
        language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },

        // DOM para mostrar botones
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-success', text: '<i class="fas fa-file-excel"></i>' },
            { extend: 'pdfHtml5', className: 'btn btn-danger', text: '<i class="fas fa-file-pdf"></i>' }
        ]
    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


