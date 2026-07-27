<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassOps.php");
    $modal = new Ops();

    session_start();
    $idContratistas = $_SESSION['idContratistas'];

    $Fecha1 = $_POST['Fecha1'] ?? null;
    $Fecha2 = $_POST['Fecha2'] ?? null;
    $mes_anterior = date('Y-m-d', strtotime('-2 month'));

    if (empty($Fecha1)) {
        $Fecha1 = $mes_anterior . " 00:00:00";
        $Fecha2 = date('Y-m-d') . " 23:59:59";
    } else {
        $Fecha1 = $Fecha1 . " 00:00:00";
        $Fecha2 = $Fecha2 . " 23:59:59";
    }

    $filas = $modal->ObtenerRechazados($idContratistas, $Fecha1, $Fecha2);
?>

<!-- CONTENEDOR PRINCIPAL DE LA TABLA, CON OPACIDAD 0 DE INICIO PARA EL EFECTO DE CARGA -->
<div id="rechazados-loading" style="text-align: center; padding: 20px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Cargando datos, por favor espere...</p>
</div>

<div id="contenedor-tabla-rechazados" style="opacity: 0; transition: opacity 0.3s ease;">
    <table class="table table-bordered display nowrap datatable-init" style="width:100%" id="rechazados">
        <thead>
            <tr>
                <th>Folio</th>
                <th>Almacén</th>
                <th>Unidad de Negocio</th>
                <th>Solicitante</th>
                <th>Cantidad</th>
                <th>Fecha Salida</th>
                <th>Fecha Rechazo</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Folio</th>
                <th>Almacén</th>
                <th>Unidad de Negocio</th>
                <th>Solicitante</th>
                <th>Cantidad</th>
                <th>Fecha Salida</th>
                <th>Fecha Rechazo</th>
                <th>Acción</th>
            </tr>
        </tfoot>
        <tbody>
            <?php if (!empty($filas)): foreach ($filas as $row): ?>
                <tr>
                    <td><span class="badge badge-danger">SD-A-<?= $row['Folio_Salida_ST'] ?></span></td>
                    <td><?= strtoupper($row['Almacen'] ?? '') ?></td>
                    <td><?= strtoupper($row['Unidad_Negocio'] ?? '') ?></td>
                    <td><?= strtoupper($row['Solicitante_Salida_T'] ?? '') ?></td>
                    <td><?= number_format($row['Cantidad_ST']) ?></td>
                    <td><?= strtoupper($row['Fecha_Salida_ST'] ?? '') ?></td>
                    <td><?= strtoupper($row['Fecha_Respuesta'] ?? '') ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-dark btn-sm ver-detalle-rechazado"
                            data-folio="<?= $row['Folio_Salida_ST'] ?>"
                            title="VER DETALLES">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>


<!-- MODAL DE DETALLES - SOLO LECTURA -->
<div class="modal fade" id="detalleRechazadoModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient text-white border-0 align-items-center" style="background: linear-gradient(135deg, #e74a3b 0%, #c0392b 100%) !important;">
                <h5 class="modal-title"><i class="fas fa-circle-xmark"></i> Detalle de Salida Rechazada <span id="detalle-rechazado-folio"></span></h5>
                <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalle-rechazado-contenido">
                <div class="text-center"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var table = new DataTable('#rechazados', {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: false,
            searching: true,
            scrollY: true,
            scrollX: true,
            initComplete: function(settings, json) {
                $('#rechazados-loading').hide();
                $('#contenedor-tabla-rechazados').css('opacity', '1');
            }
        });

        const urlParams = new URLSearchParams(window.location.search);
        const folioBuscar = urlParams.get('folio');
        if (folioBuscar) {
            table.search(folioBuscar).draw();
        }
    });

    $(document).off('click', '.ver-detalle-rechazado').on('click', '.ver-detalle-rechazado', function(){
        var folio = $(this).data('folio');

        $('#detalle-rechazado-folio').text('SD-A-' + folio);
        $('#detalle-rechazado-contenido').html('<div class="text-center"><div class="spinner-border text-primary"></div></div>');
        $('#detalleRechazadoModal').modal('show');

        $.get('modal/DetalleSalidaTemp.php', { folio: folio }, function(html){
            $('#detalle-rechazado-contenido').html(html);
        }).fail(function(){
            $('#detalle-rechazado-contenido').html('<div class="text-danger text-center">Error al cargar el detalle.</div>');
        });
    });
</script>