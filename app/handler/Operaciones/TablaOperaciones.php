
<?php
// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclusión de clases necesarias
    require_once __DIR__ . '/../../controller/ClassOps.php';

// Inicio de sesión y obtención de variables de sesión
session_start();
$Contratista = $_SESSION['Contratista'] ?? null;
$idContratistas = $_SESSION['idContratistas'] ?? null;

// Recepción y validación de parámetros de fechas
$Fecha1 = $_POST['Fecha1'] ?? '';
$Fecha2 = $_POST['Fecha2'] ?? '';
$mes_anterior = date('Y-m-d', strtotime('-1 month'));

if (empty($Fecha1)) {
    $Fecha1 = $mes_anterior . " 00:00:00";
    $Fecha2 = date('Y-m-d') . " 23:59:59";
} else {
    $Fecha1 = $Fecha1 . " 00:00:00";
    $Fecha2 = $Fecha2 . " 23:59:59";
}

// Consulta de órdenes
$modal = new Ops();
$filas = $modal->OrdenesCoordiApp($idContratistas, $Fecha1, $Fecha2);
$filas = is_array($filas) ? $filas : [];

/*Calcula la distancia entre dos coordenadas geográficas usando la fórmula de Haversine. */
function distanciaEuclidiana($lat1, $lon1, $lat2, $lon2)
{
    $radioTierra = 6371; // km
    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);

    $dLat = $lat2Rad - $lat1Rad;
    $dLon = $lon2Rad - $lon1Rad;

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos($lat1Rad) * cos($lat2Rad) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distancia = $radioTierra * $c * 1000; // metros
    return $distancia;
}
?>
<table class="table table-bordered display nowrap" style="width:100%" id="ventas">
    <thead>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>ONT</th>
            <th>Contratista</th>
            <th>Tecnico</th>
            <th>Expediente</th>
            <th>Cope</th>
            <th>Area</th>
            <th>Division</th>
            <th>Distrito</th>
            <th>Tecnologia</th>
            <th>Tipo Tarea</th>
            <th>Tipo Instalacion</th>
            <th>Metraje</th>
            <th>Fecha</th>
            <th>Estatus</th>
            <th>Terminal</th>
            <th>Latitud Terminal</th>
            <th>Longitud Terminal</th>
            <th>Puerto</th>
            <th>Nombre Cliente</th>
            <th>Direccion Cliente</th>
            <th>Latitud</th>
            <th>Longitud</th>
            <th>Ultimo Paso</th>
            <th>Accion</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Folio Pisa</th>
            <th>Telefono</th>
            <th>ONT</th>
            <th>Contratista</th>
            <th>Tecnico</th>
            <th>Expediente</th>
            <th>Cope</th>
            <th>Area</th>
            <th>Division</th>
            <th>Distrito</th>
            <th>Tecnologia</th>
            <th>Tipo Tarea</th>
            <th>Tipo Instalacion</th>
            <th>Metraje</th>
            <th>Fecha</th>
            <th>Estatus</th>
            <th>Terminal</th>
            <th>Latitud Terminal</th>
            <th>Longitud Terminal</th>
            <th>Puerto</th>
            <th>Nombre Cliente</th>
            <th>Direccion Cliente</th>
            <th>Latitud</th>
            <th>Longitud</th>
            <th>Ultimo Paso</th>
            <th>Accion</th>
        </tr>
    </tfoot>
    <tbody>
    <?php foreach ($filas as $row):?>
        <tr>
            <td><?= htmlspecialchars($row['Folio_Pisa'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Telefono'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Ont'] ?? '') ?></td>
            <td><?= htmlspecialchars(($row['Contratista'] ?? '') . ' ' . strtoupper($row['apellido_paterno'] ?? '') . ' ' . strtoupper($row['apellido_materno'] ?? '')) ?></td>
            <td><?= htmlspecialchars(($row['Nombre_T'] ?? '') . ' ' . ($row['Apellidos_T'] ?? '')) ?></td>
            <td><?= htmlspecialchars($row['NExpediente'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['COPE'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['area'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Division'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Distrito'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Tecnologia'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Tipo_Tarea'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Tipo_Instalacion'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Metraje'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Fecha_Coordiapp'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Estatus_Orden'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Terminal'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Latitud_Terminal'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Longitud_Terminal'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Puerto'] ?? '') ?></td>
            <td><?= strtoupper(($row['Cliente_Titular'] ?? '') . ' ' . ($row['Apellido_Paterno_Titular'] ?? '') . ' ' . ($row['Apellido_Materno_Titular'] ?? '')) ?></td>
            <td><?= strtoupper($row['Direccion_Cliente'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Latitud'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Longitud'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['Step_Registro'] ?? '') ?></td>
            <td>
                <a href="https://www.google.com/maps/dir/<?= urlencode($row['Latitud']) ?>,<?= urlencode($row['Longitud']) ?>/<?= urlencode($row['Latitud_Terminal']) ?>,<?= urlencode($row['Longitud_Terminal']) ?>?zoom=20" target="_blank">
                    <button type="button" class="btn btn-danger" title="Ver ruta en mapa">
                        <i class="fas fa-map-marker"></i>
                    </button>
                </a>
                
                <a href="modal/Galeria.php?Folio_Pisa=<?= urlencode($row['Folio_Pisa']) ?>" target="_blank">
                    <button type="button" class="btn btn-success" title="Ver galería">
                        <i class="fas fa-images"></i>
                    </button>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
    $(document).ready(function () {
        let table = new DataTable('#ventas', {
            fixedColumns: {
                start: 1,
                end: 1
            },
            responsive:true,
            'language':{
                'URL':'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
            },
            "ordering": false,
            "searching": true,
                "scrollY": true,
                "scrollX": true
        });
    
        new DataTable.Buttons(table, {
                buttons: [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fas fa-file-excel"></i> ',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend:    'pdfHtml5',
                        text:      '<i class="fas fa-file-pdf"></i> ',
                        titleAttr: 'Exportar a PDF',
                        className: 'btn btn-danger'
                    }
                ],
        });
        
        table
            .buttons(0, null)
            .container()
            .prependTo(table.table().container());
    });
</script>