<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once("../controller/ClassOps.php");

session_start();

$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

$semana = $_POST['semana'] ?? null;

// Si no hay semana seleccionada
if (empty($semana)) {
    echo "";
    exit;
}

if (empty($Contratista) || empty($idContratistas)) {
    echo "<p>No se encontró información del contratista.</p>";
    exit;
}

$modal = new Ops();

/* ============================
   🔹 EXPEDIENTES (SE CONSERVA)
============================ */
$expedientesData = $modal->ObtenerExpedientesPorContratista($idContratistas);
$expedientesData = is_array($expedientesData) ? $expedientesData : [];

$expedientes = [];
foreach ($expedientesData as $exp) {
    $expedientes[] = $exp['NExpediente'];
}

if (empty($expedientes)) {
    echo "<p>No se encontraron expedientes de técnicos para este contratista.</p>";
    exit;
}

/* ============================
   🔥 NUEVA FUENTE DE DATOS
   Produccion_alertas
============================ */

// 🔴 AQUÍ ES DONDE CAMBIA TODO
// Debes tener este método en tu clase Ops:
// ObtenerProduccionAlertas($idContratistas, $semana)

$alertas = $modal->ObtenerProduccionAlertas($idContratistas, $semana);
$alertas = is_array($alertas) ? $alertas : [];

?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/modalprenomina.css">

<!-- ============================
     TABLA NUEVA (ALERTAS)
============================ -->
<table class="table table-bordered table-striped" style="width:100%">

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

            <!-- ============================
                 BOTÓN GALERÍA (SE CONSERVA)
            ============================ -->
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
     MODAL GALERÍA (SE CONSERVA TAL CUAL)
============================ -->
<div class="modal fade" 
     id="modalGaleria" 
     tabindex="-1" 
     aria-hidden="true"
     data-bs-backdrop="true"
     data-bs-keyboard="true">

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

        <!-- FOTOS -->
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

<!-- JS galería (SE CONSERVA) -->
<script src="js/galeria_fotos.js"></script>