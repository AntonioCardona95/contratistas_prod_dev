<?php
require_once("../controller/ClassOps.php");
require_once("../controller/ClassResultados.php");
require_once("../controller/ClassProduccion.php");

session_start();

$Contratista    = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

// =========================
// PARAMETROS RECIBIDOS
// =========================
$fecha_inicio = $_POST['fecha_inicio'] ?? "";
$fecha_fin    = $_POST['fecha_fin'] ?? "";

// NUEVOS PARAMETROS
$categoria    = $_POST['categoria'] ?? "";
$cantidad     = $_POST['cantidad'] ?? "";

// =========================
// VALIDACIONES
// =========================
if (empty($categoria)) {
    echo "<p>No se recibieron parámetros válidos.</p>";
    exit;
}

if (empty($Contratista) || empty($idContratistas)) {
    echo "<p>No se encontró información del contratista.</p>";
    exit;
}

$modal = new Ops();
$resultados = new Resultados();

$alertas = [];

// Si solo depende de categoría (como lo tienes ahora)
//$alertas = $resultados->getDetalleAlertasPorTipo($categoria);


$alertas = $resultados->getDetalleAlertasPorTipo(
    $categoria,
    $fecha_inicio,
    $fecha_fin
); 


$alertas = is_array($alertas) ? $alertas : [];

if (empty($alertas)) {
    echo "<p>No se encontraron alertas para la categoría seleccionada.</p>";
    exit;
}
?>

<!-- Bootstrap CSS--> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="css/EstilosTablaSinProcesoPago.css">
<link rel="stylesheet" type="text/css" href="css/modalprenomina.css">


<!-- =========================
     INFORMACION SUPERIOR
========================= -->
<div class="alert alert-primary mb-3">

    <div class="row">

        <div class="col-md-4">
            <strong>Categoría:</strong>
            <?= htmlspecialchars($categoria ?? '') ?>
        </div>

        <div class="col-md-4">
            <strong>Cantidad:</strong>
            <?= htmlspecialchars($cantidad ?? '') ?>
        </div>

        <div class="col-md-4">
            <strong>Rango:</strong>
            <?= htmlspecialchars($fecha_inicio ?? '') ?>
            -
            <?= htmlspecialchars($fecha_fin ?? '') ?>
        </div>

    </div>

</div>


<table class="table table-bordered display nowrap" style="width:100%" id="ventas">

    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Tipo Tarea</th>
            <th>Cope</th>
            <th>Fecha Liquidación</th>
            <th>Alerta</th>
            <th>Detalle Esci</th>
            <th>Contratista</th>
            <th>Comparativa Tip Tarea</th>
            <th>Comparativa Tecnologia</th>
            <th>Comparativa Numeros de serie</th>
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
            

          <td style="color:red; font-weight:bold;">
    <?= htmlspecialchars($row['Tipo_Alerta'] ?? '') ?>
</td>

            <td>
                <?= htmlspecialchars($row['Detalle_Esci'] ?? '') ?>
            </td>

          

            <td>
                <?= htmlspecialchars($row['nombre_completo'] ?? '') ?>
            </td>

            <td>
                <?= htmlspecialchars($row['Detalle_Tipo_Tarea'] ?? '') ?>
            </td>

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


<!-- ============================
     MODAL GALERÍA
============================ -->
<div class="modal fade" 
     id="modalGaleria" 
     tabindex="-1" 
     aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-lg modal-galeria-custom">

    <div class="modal-content">

      <div class="modal-header header-galeria">

        <h5 class="modal-title fw-bold">
            Galería
        </h5>

        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"></button>

      </div>

      <div class="modal-body">

        <div class="info-galeria">

          <p>
            <strong>Nombre Contratista:</strong>
            <span id="modalNombreContratista"></span>
          </p>

          <p>
            <strong>ID Contratista:</strong>
            <span id="modalIdContratista"></span>
          </p>

          <p>
            <strong>Folio:</strong>
            <span id="modalFolio"></span>
          </p>

        </div>

        <div class="fila-superior">

          <div class="foto-item">

            <p>Foto Casa Cliente</p>

            <a href="#" id="fotoGrande" target="_blank">
              <img id="miniaturaFoto" src="">
            </a>

          </div>

          <div class="foto-item">

            <p>Foto Modem</p>

            <a href="#" id="fotoGrandemodem" target="_blank">
              <img id="miniaturaFotomodem" src="">
            </a>

          </div>

          <div class="foto-item">

            <p>Foto Trasera ONT</p>

            <a href="#" id="fotoGrandemodemtrasera" target="_blank">
              <img id="miniaturaFotomodemtrasera" src="">
            </a>

          </div>

          <div class="foto-item">

            <p>Foto OS</p>

            <a href="#" id="fotoGrandeos" target="_blank">
              <img id="miniaturaFotoos" src="">
            </a>

          </div>

        </div>

        <div class="fila-inferior">

          <div class="foto-item">

            <p>Foto Terminal</p>

            <a href="#" id="fotopuerto" target="_blank">
              <img id="miniaturafotopuerto" src="">
            </a>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/galeria_fotos.js"></script>


<!-- =========================
     DATATABLE
========================= -->
<script>

$(document).ready(function () {

    new DataTable('#ventas', {

        fixedColumns: {
            start: 1,
            end: 1
        },

        responsive: true,

        language: {
            URL:'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
        },

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