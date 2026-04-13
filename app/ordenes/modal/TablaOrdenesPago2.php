<?php
// Mostrar errores en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ===============================
// INCLUSIÓN DE CLASES
// ===============================
require_once("../controller/ClassOps.php");
require_once("../controller/ClassNomina.php");

// INICIO DE SESIÓN
session_start();
$Contratista    = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

// VALIDACIÓN DE SESIÓN
if (empty($Contratista) || empty($idContratistas)) {
    echo "<p>No se encontró información del contratista.</p>";
    exit;
}

// VALORES RECIBIDOS POR POST
$emulador   = $_POST['emulador'] ?? null;
$semana     = $_POST['semana'] ?? null;
$tecnologia = $_POST['tecnologia'] ?? null;

/*echo "<h3>Contenido de \$emulador:</h3>";
echo "<pre>";
print_r($emulador);
echo "</pre>"; */

/*echo "<h3>Contenido de \$semana:</h3>";
echo "<pre>";
print_r($semana);
echo "</pre>"; */

/* echo "<h3>Contenido de \$tecnologia:</h3>";
echo "<pre>";
print_r($tecnologia);
echo "</pre>";  */



// VALIDACIÓN DE FILTROS
if (empty($emulador) || empty($semana) || empty($tecnologia)) {
    echo "<p>Faltan filtros para mostrar la información.</p>";
    exit;
}

// NORMALIZAMOS EMULADOR
$emulador = strtoupper(trim($emulador));

// OBTENER ID DEL EMULADOR POR MEDIO DEL NOMBRE DEL EMULADOR
$nomina = new Nomina();
$idEmulador = $nomina->obtenerIdEmuladorPorNombre($emulador);

if (!$idEmulador) {
    echo "<p>Emulador no válido: {$emulador}</p>";
    exit;
}

// CONSTRUCCIÓN DE FOLIO PACO
$foliopaco = $emulador . "_" . $semana;

// INSTANCIA DE OPS
$modal = new Ops();

// Obtener expedientes de técnicos del contratista
$expedientesData = $modal->ObtenerExpedientesPorContratista($idContratistas);
$expedientesData = is_array($expedientesData) ? $expedientesData : [];

// Convertir los expedientes a un arreglo simple
$expedientes = [];
foreach ($expedientesData as $exp) {
    $expedientes[] = $exp['NExpediente'];
}

echo "<pre>";
print_r($expedientes);
echo "</pre>"; 

// Si no hay expedientes, no continuar
if (empty($expedientes)) {
    echo "<p>No se encontraron expedientes de técnicos para este contratista.</p>";
    exit;
}

// OBTENER PRODUCCIÓN SIN INCIDENCIAS
$filas = $modal->ObtenerProduccionContratistaSinIncidencias(
    $idContratistas,
    $emulador,
    $semana,
    $tecnologia
);

// VALIDACIONES FINALES
$filas = is_array($filas) ? $filas : [];

// DEPURACIÓN: ver qué contiene $filas
/* echo "<h3>Contenido de \$filas:</h3>";
echo "<pre>";
print_r($filas);
echo "</pre>"; */

if (empty($filas)) {
    echo "<p>No se encontraron registros sin incidencias para los filtros seleccionados.</p>";
    exit;
}

// VARIABLES AUXILIARES
$Estatus = 'PENDIENTE';
date_default_timezone_set("America/Mexico_City");
$fecha = date("Y-m-d H:i:s");
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
<button id="btnInsertarFolios"
        class="btn btn-primary mb-3"
        data-emulador="<?= htmlspecialchars($emulador) ?>"     
        data-idemulador="<?= $idEmulador ?>"                   
        data-semana="<?= htmlspecialchars($semana) ?>"
        data-tecnologia="<?= htmlspecialchars($tecnologia) ?>"
        data-idcontratista="<?= htmlspecialchars($idContratistas) ?>">
    <i class="fa-solid fa-database"></i> Insertar Folios Paco
</button>
<!-- ===========================
     TABLA
=========================== -->
<table class="table table-bordered display nowrap" id="ventas" style="width:100%">

<thead>
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
    <th>Dirección</th>
    <th>Cliente</th>
    <th>Terminal</th>
    <th>Puerto</th>
    <th>Tipo Instalación</th>
    <th>Latitud</th>
    <th>Longitud</th>
    <th>Metraje</th>
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
    <td><?= htmlspecialchars($row['Cliente_Titular'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Terminal'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Puerto'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Tipo_Instalacion'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Latitud'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Longitud'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['Metraje_Coordiapp'] ?? '') ?></td>
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
    <th>Dirección</th>
    <th>Terminal</th>
    <th>Puerto</th>
    <th>Tipo Instalación</th>
    <th>Latitud</th>
    <th>Longitud</th>
    <th>Metraje</th>
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
<script src="js/insertar_foliospaco.js"></script>
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


