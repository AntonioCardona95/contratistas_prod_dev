<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassTraspasos.php");
    require_once(__DIR__ . '/../../config/ContratistaContexto.php');

    $modal = new Traspasos();
    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = obtenerIdContratistaContexto();

    $Fecha1 = $_POST['Fecha1'];
    $Fecha2 = $_POST['Fecha2'];

    $mes_anterior = date('Y-m-d', strtotime('-1 month'));

    if(empty($Fecha1)){
        $Fecha1 = "2025-04-14 00:00:00";
        $Fecha2 = date('Y-m-d')." 23:59:59";
        $filas = $modal->Traspasos($idContratistas,$Fecha1,$Fecha2);
    }elseif(!empty($Fecha1) && !empty($Fecha2)){
        $Fecha1 = $_POST['Fecha1']." 00:00:00"; 
        $Fecha2 = $_POST['Fecha2']." 23:59:59";
        $filas = $modal->Traspasos($idContratistas,$Fecha1,$Fecha2);
    }
?>
<style>
    .comentario-btn {
        position: relative;
    }

    .acciones-traspaso .btn + .btn {
        margin-left: .25rem;
    }

    .tiene-comentario::after {
        content: '';
        position: absolute;
        top: -2px;
        right: -2px;
        width: 10px;
        height: 10px;
        background-color: red;
        border-radius: 50%;
        border: 1px solid white;
    }

    /* CENTRADO DE DATOS */
    #traspasos_wrapper .dt-scroll-head th,
    #traspasos_wrapper .dt-scroll-foot th,
    #traspasos_wrapper .dt-scroll-body td,
    #traspasos thead th,
    #traspasos tfoot th,
    #traspasos tbody td {
        text-align: center !important;
        vertical-align: middle !important;
    }

</style>

<div id="traspasos-loading" style="text-align: center; padding: 20px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Cargando datos, por favor espere...</p>
</div>

<div id="contenedor-tabla-traspasos" style="opacity: 0; transition: opacity 0.3s ease;">

    <table class="table table-bordered display nowrap" style="width:100%" id="traspasos">			
        <thead>
            <tr>
                <th>Folio Traspaso</th> 
                <th>Contratista</th>
                <th>Tecnico Origen</th>
                <th>Tecnico Destino</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Folio Traspaso</th> 
                <th>Contratista</th>
                <th>Tecnico Origen</th>
                <th>Tecnico Destino</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Accion</th>
            </tr>
        </tfoot>
        <tbody>
            <?php foreach ($filas ?? [] as $row): ?>
                <?php
                    $comentario = trim((string)($row['Comentarios_Traspaso'] ?? ''));
                    $tieneComentario = $comentario !== '';
                    $claseNotificacion = $tieneComentario ? 'tiene-comentario' : '';
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['Folio_Traspaso_T'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Contratista'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['TecnicoOrigen'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['TecnicoDestino'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Cantidad_Traspaso'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['Fecha_Traspaso'] ?? '') ?></td>
                    <td class="acciones-traspaso">
                        <button type="button"
                            class="btn btn-info comentario-btn <?= $claseNotificacion ?>"
                            data-comentario="<?= htmlspecialchars($comentario, ENT_QUOTES, 'UTF-8') ?>"
                            aria-label="Ver comentario"
                            title="<?= $tieneComentario ? 'Ver comentario' : 'Sin comentario' ?>">
                            <i class="fas fa-comment-alt"></i>
                        </button>
                        <a href="modal/Traspasos_Detalles.php?id=<?= (int)$row['Folio_Traspaso_T'] ?>">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalForm">
                                <i class="fa-solid fa-file-lines"></i>
                            </button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- MODAL DE COMENTARIOS -->
<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient text-white border-0 align-items-center">
                <h5 class="modal-title" id="comentarioModalLabel">
                    <i class="fas fa-comment-alt"></i> Comentarios
                </h5>
                <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Cerrar" id="cerrarModalComentario">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="comentarioTexto"></div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function () {
        let table = new DataTable('#traspasos', {
            fixedColumns: {
                start: 1,
                end: 1
            },
            responsive:true,
            language:{ url:'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'},
            ordering: true,
            searching: true,
		    scrollY: true,
			scrollX: true,
            initComplete: function(settings, json) {
                $('#traspasos-loading').hide();
                $('#contenedor-tabla-traspasos').css('opacity', '1');
            }
        });
    
        new DataTable.Buttons(table, {
                buttons: [
                    {
                        extend:    'excelHtml5',
                        text:      '<i class="fas fa-file-excel"></i> Exportar a Excel',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-success'
                    }
                ],
        });
        
        table
            .buttons(0, null)
            .container()
            .prependTo(table.table().container());

	});

    $(document).off('click.traspasosComentarios', '.comentario-btn')
        .on('click.traspasosComentarios', '.comentario-btn', function () {
            const comentario = String($(this).data('comentario') || '').trim();
            $('#comentarioTexto').text(comentario || 'Sin comentarios registrados');
            $('#modalComentario').modal('show');
        });

    $(document).off('click.cerrarComentario', '#cerrarModalComentario')
        .on('click.cerrarComentario', '#cerrarModalComentario', function () {
            $('#modalComentario').modal('hide');
        });
    
</script>