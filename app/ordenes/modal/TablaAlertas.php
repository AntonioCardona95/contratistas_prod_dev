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
            <th>Contratista</th>
            <th>Comparativa Tip Tarea</th>
            <th>Comparativa Tecnologia</th>
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
            td><?= htmlspecialchars($row['Detalle_Tipo_Tarea'] ?? '') ?></td>
            td><?= htmlspecialchars($row['Detalle_Tecnologia	'] ?? '') ?></td>

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
        <h5 class="modal-title fw-bold">Galería</h5>
        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="info-galeria">
          <p><strong>Nombre Contratista:</strong> <span id="modalNombreContratista"></span></p>
          <p><strong>ID Contratista:</strong> <span id="modalIdContratista"></span></p>
          <p><strong>Folio:</strong> <span id="modalFolio"></span></p>
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
<!-- Bootstrap JS  cierre del modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/galeria_fotos.js"></script>
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