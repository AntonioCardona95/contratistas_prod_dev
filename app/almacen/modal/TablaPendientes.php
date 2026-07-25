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

    $filas = $modal->ObtenerPendientes($idContratistas, $Fecha1, $Fecha2);
?>

<style>
    .modal-header-gradient {
        background: linear-gradient(135deg, #4462e7 0%, #764ba2 100%) !important;
    }
    td { align-content: center; }
</style>

<table class="table table-bordered display nowrap" style="width:100%" id="pendientes">
    <thead>
        <tr>
            <th>Folio</th>
            <th>Almacén</th>
            <th>Unidad de Negocio</th>
            <th>Solicitante</th>
            <th>Cantidad</th>
            <th>Fecha</th>
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
            <th>Fecha</th>
            <th>Acción</th>
        </tr>
    </tfoot>
    <tbody>
        <?php if (!empty($filas)): foreach ($filas as $row): ?>
            <tr>
                <td><span class="badge badge-warning">SD-A-<?= $row['Folio_Salida_ST'] ?></span></td>
                <td><?= strtoupper($row['Almacen'] ?? '') ?></td>
                <td><?= strtoupper($row['Unidad_Negocio'] ?? '') ?></td>
                <td><?= strtoupper($row['Solicitante_Salida_T'] ?? '') ?></td>
                <td><?= number_format($row['Cantidad_ST']) ?></td>
                <td><?= strtoupper($row['Fecha_Salida_ST'] ?? '') ?></td>
                <td class="text-center">
                    <button type="button" class="btn btn-info btn-sm ver-detalle-pendiente"
                        data-folio="<?= $row['Folio_Salida_ST'] ?>"
                        title="VER DETALLES">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm aceptar-pendiente"
                        data-folio="<?= $row['Folio_Salida_ST'] ?>"
                        title="ACEPTAR SALIDA">
                        <i class="fas fa-check"></i>
                    </button>
                </td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>

<!-- MODAL DE DETALLES + ACEPTAR/RECHAZAR -->
<div class="modal fade" id="detallePendienteModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient text-white border-0 align-items-center">
                <h5 class="modal-title"><i class="fas fa-box-open"></i> Detalle de Salida <span id="detalle-pendiente-folio"></span></h5>
                <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalle-pendiente-contenido">
                <div class="text-center"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger" id="btn-rechazar-pendiente">
                    <i class="fas fa-times-circle"></i> Rechazar
                </button>
                <button type="button" class="btn btn-success" id="btn-aceptar-pendiente">
                    <i class="fas fa-check-circle"></i> Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var table = new DataTable('#pendientes', {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: false,
            searching: true,
            scrollY: true,
            scrollX: true
        });

        // AUTOFILTRAR SI SE LLEGO DESDE UNA NOTIFICACION
        const urlParams = new URLSearchParams(window.location.search);
        const folioBuscar = urlParams.get('folio');
        if (folioBuscar) {
            table.search(folioBuscar).draw();
        }
    });

    var folioActivoPendiente = null;

    // ABRIR EL MODAL Y CARGAR EL DETALLE
    $(document).off('click', '.ver-detalle-pendiente').on('click', '.ver-detalle-pendiente', function(){
        var folio = $(this).data('folio');
        folioActivoPendiente = folio;

        $('#detalle-pendiente-folio').text('SD-A-' + folio);
        $('#detalle-pendiente-contenido').html('<div class="text-center"><div class="spinner-border text-primary"></div></div>');
        $('#detallePendienteModal').modal('show');

        $.get('modal/DetalleSalidaTemp.php', { folio: folio }, function(html){
            $('#detalle-pendiente-contenido').html(html);
        }).fail(function(){
            $('#detalle-pendiente-contenido').html('<div class="text-danger text-center">Error al cargar el detalle.</div>');
        });
    });

    // ACEPTAR
    $(document).off('click', '#btn-aceptar-pendiente').on('click', '#btn-aceptar-pendiente', function(){
        if (!folioActivoPendiente) return;

        Swal.fire({
            position: 'top',
            icon: 'question',
            title: '¿Aceptar esta salida?',
            html: 'Confirmas que recibiste correctamente el material de la salida <strong>SD-A-' + folioActivoPendiente + '</strong>.',
            showCancelButton: true,
            confirmButtonText: 'Sí, aceptar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#1cc88a'
        }).then(function(result){
            if (!result.isConfirmed) return;

            $.post('modal/AceptarSalida.php', { folio: folioActivoPendiente }, function(response){
                if (response.success) {
                    Swal.fire({
                        position: 'top',
                        icon: 'success',
                        title: 'Salida aceptada',
                        text: 'El material fue marcado como aceptado correctamente.',
                        showConfirmButton: true
                    });
                    $('#detallePendienteModal').modal('hide');
                    LoadPendientes();
                } else {
                    Swal.fire({
                        position: 'top',
                        icon: 'error',
                        title: 'Error al aceptar',
                        text: response.message || 'Ocurrió un error.',
                        showConfirmButton: true
                    });
                }
            }, 'json');
        });
    });

    // RECHAZAR
    $(document).off('click', '#btn-rechazar-pendiente').on('click', '#btn-rechazar-pendiente', function(){
        if (!folioActivoPendiente) return;

        Swal.fire({
            position: 'top',
            icon: 'warning',
            title: '¿Rechazar esta salida?',
            html: 'Vas a rechazar el material de la salida <strong>SD-A-' + folioActivoPendiente + '</strong>. Esta acción notificará al almacén.',
            showCancelButton: true,
            confirmButtonText: 'Sí, rechazar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#e74a3b'
        }).then(function(result){
            if (!result.isConfirmed) return;

            $.post('modal/RechazarSalida.php', { folio: folioActivoPendiente }, function(response){
                if (response.success) {
                    Swal.fire({
                        position: 'top',
                        icon: 'success',
                        title: 'Salida rechazada',
                        text: 'El almacén fue notificado del rechazo.',
                        showConfirmButton: true
                    });
                    $('#detallePendienteModal').modal('hide');
                    LoadPendientes();
                } else {
                    Swal.fire({
                        position: 'top',
                        icon: 'error',
                        title: 'Error al rechazar',
                        text: response.message || 'Ocurrió un error.',
                        showConfirmButton: true
                    });
                }
            }, 'json');
        });
    });
</script>