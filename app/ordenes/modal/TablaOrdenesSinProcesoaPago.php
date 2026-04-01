<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Inclusión de clases necesarias
require_once("../controller/ClassOps.php");

// Inicio de sesión y obtención de variables de sesión
session_start();
$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

$semana = $_POST['semana'] ?? null;

// Si no hay semana seleccionada, no mostrar tabla
if (empty($semana)) {
    echo ""; // No mostrar nada
    exit;
}

if (empty($Contratista) || empty($idContratistas)) {
    echo "<p>No se encontró información del contratista.</p>";
    exit;
}

// Instancia de la clase Ops
$modal = new Ops();

// Obtener expedientes de técnicos del contratista
$expedientesData = $modal->ObtenerExpedientesPorContratista($idContratistas);
$expedientesData = is_array($expedientesData) ? $expedientesData : [];

// Convertir los expedientes a un arreglo simple
$expedientes = [];
foreach ($expedientesData as $exp) {
    $expedientes[] = $exp['NExpediente'];
}

/*echo "<pre>";
print_r($expedientes);
echo "</pre>"; */
// Si no hay expedientes, no continuar
if (empty($expedientes)) {
    echo "<p>No se encontraron expedientes de técnicos para este contratista.</p>";
    exit;
}

// Ahora pasamos los expedientes a la función de Producción
$filas = $modal->OrdenesConIncidencias($idContratistas,$expedientes,$semana);
$filas = is_array($filas) ? $filas : [];
?>
<!-- Bootstrap CSS--> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/EstilosTablaSinProcesoPago.css">
<link rel="stylesheet" type="text/css" href="css/modalprenomina.css">
<table class="table table-bordered display nowrap" style="width:100%" id="ventas">
    <thead>
        <tr>
            <th>Galería</th>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>Tipo de tarea</th>
            <th>Cope</th>
            <th>Fecha de liquidacion</th>
            <th>Semana</th>
            <th>Metraje</th>
            <th>Calificador</th>
            <th>Alerta</th>
            <th>Valor TAC</th>
            <th>Valor Coordiapp</th>
             <th>Valor ESCI</th>
            <th>Metraje Contratista</th>
            <th>Diferencia</th>
            <th>Metraje Distancia</th>
            <th>Diferencia Distancia</th>
            
        </tr>
    </thead>
    <tbody>
    <?php foreach ($filas as $row): ?>
        <tr>
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
            <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Telefono'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Tipo_Tarea'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Cope'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Fecha_Liquidacion'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Semana'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['metraje'] ?? '') ?></td>
              <td style="text-align: center; font-weight: bold;">
                <?= !empty($row['Alerta']) ? 'X' : '✓' ?>
            </td>

            <td style="color: red; font-weight: bold;">
                <?= htmlspecialchars($row['Alerta'] ?? '') ?>
            </td>



            <td>
                <?= htmlspecialchars($row['Valor_TAC'] ?? '') ?>
            </td>

            

            <td>
                <?= htmlspecialchars($row['Valor_Coordiapp'] ?? '') ?>
            </td>
            <td>
    <?= htmlspecialchars($row['Valor_ESCI'] ?? '') ?>
</td>

             <td>
                <?= htmlspecialchars($row['Valor_Contratista'] ?? '') ?>
            </td>
            <td style="<?= ($row['Diferencia_Metraje'] ?? 0) > 0 ? 'background-color: yellow; font-weight:bold;' : '' ?>">
    <?= $row['Diferencia_Metraje'] ?? 0 ?>
</td>
  <td style="<?= ($row['Metraje_Distancia'] ?? 0) > 0 ? 'background-color: #e6f3ff; font-weight:bold;' : '' ?>">
                <?= number_format($row['Metraje_Distancia'] ?? 0, 2) ?>
            </td>
<td style="<?= ($row['Diferencia_Metraje_Distancia'] ?? 0) > 0 ? 'background-color: yellow; font-weight:bold;' : '' ?>">
    <?= $row['Diferencia_Metraje_Distancia'] ?? 0 ?>
</td>

          
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!--  MODAL GALERÍA -->
<div class="modal fade" 
     id="modalGaleria" 
     tabindex="-1" 
     aria-hidden="true"
     data-bs-backdrop="true"
     data-bs-keyboard="true">

  <div class="modal-dialog modal-dialog-centered modal-lg modal-galeria-custom">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header header-galeria">
        <h5 class="modal-title fw-bold">Galería</h5>
        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"
                aria-label="Cerrar"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">

        <div class="info-galeria">
          <p><strong>Nombre Contratista:</strong> <span id="modalNombreContratista"></span></p>
          <p><strong>ID Contratista:</strong> <span id="modalIdContratista"></span></p>
          <p><strong>Folio:</strong> <span id="modalFolio"></span></p>
        </div>

        <!-- 4 FOTOS SUPERIORES -->
        <div class="fila-superior">

          <div class="foto-item">
            <p>Foto Casa Cliente</p>
            <a href="#" id="fotoGrande" target="_blank">
              <img id="miniaturaFoto" src="" alt="">
            </a>
          </div>

          <div class="foto-item">
            <p>Foto Modem</p>
            <a href="#" id="fotoGrandemodem" target="_blank">
              <img id="miniaturaFotomodem" src="" alt="">
            </a>
          </div>

          <div class="foto-item">
            <p>Foto Trasera ONT</p>
            <a href="#" id="fotoGrandemodemtrasera" target="_blank">
              <img id="miniaturaFotomodemtrasera" src="" alt="">
            </a>
          </div>

          <div class="foto-item">
            <p>Foto OS</p>
            <a href="#" id="fotoGrandeos" target="_blank">
              <img id="miniaturaFotoos" src="" alt="">
            </a>
          </div>

        </div>

        <!-- FOTO INFERIOR CENTRADA -->
        <div class="fila-inferior">
          <div class="foto-item">
            <p>Foto Terminal</p>
            <a href="#" id="fotopuerto" target="_blank">
              <img id="miniaturafotopuerto" src="" alt="">
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