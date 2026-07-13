<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . '/../../controller/ClassOps.php';

    $modal = new Ops();
    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];

    $fechaInicio = $_POST['fechaInicio'] ?? $_POST['Fecha1'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? $_POST['Fecha2'] ?? '';

    $fechaInicioDefault = date('Y-m-d', strtotime('-2 months'));
    $fechaFinDefault = date('Y-m-d');

    if(empty($fechaInicio)){
        $fechaInicio = $fechaInicioDefault;
        $fechaFin = $fechaFinDefault;
        $filas = $modal->Salidas($idContratistas, $fechaInicio . " 00:00:00", $fechaFin . " 23:59:59");
    }elseif(!empty($fechaInicio) && !empty($fechaFin)){
        $fechaInicio = $fechaInicio . " 00:00:00"; 
        $fechaFin = $fechaFin . " 23:59:59";
        $filas = $modal->Salidas($idContratistas, $fechaInicio, $fechaFin);
    }
?>
<div class="table-responsive">
    <table class="table table-bordered display nowrap" style="width:100%" id="ventas">
        <thead>
            <tr>
                <th>Folio Salida</th> 
                <th>Contratista</th>
                <th>Tecnico</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Folio Salida</th> 
                <th>Contratista</th>
                <th>Tecnico</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Accion</th>
            </tr>
        </tfoot>
        <?php
            if(!empty($filas)){
                foreach($filas as $row){
                    echo "
                        <tr>
                            <td>".$row['Folio_Salida_T']."</td>
                            <td>".$row['Contratista']."</td>
                            <td>".$row['Tecnico']."</td>
                            <td>".$row['Cantidad_T']."</td>
                            <td>".$row['Fecha_T']."</td>
                            <td>
                                <a href='modal/SalidasDet.php?id=".$row['Folio_Salida_T']."'>
                                    <button type='button' class='btn btn-danger' data-toggle='modal' data-target='#modalForm'>
                                        <i class='fa-solid fa-file-lines'></i>
                                    </button>
                                </a>
                            </td>
                        </tr>";
                }
            }
        ?>
    </table>
</div>
<script>
    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#ventas')) {
            $('#ventas').DataTable().destroy();
        }

        var dtConfig = {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: true,
            searching: true,
            paging: true,
            pageLength: 10,
            scrollY: true,
            dom: '<"d-flex justify-content-between align-items-center" Bf>' +
                 'rt' +
                 '<"row"<"col-md-12 text-right"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    titleAttr: 'Exportar a Excel',
                    className: 'btn btn-success'
                }
            ],
            initComplete: function() {
                $('#detallesFilter').append($('.dataTables_filter'));
                $('#detallesPaginationBottom').append($('.dataTables_paginate'));
            }
        };

        new DataTable('#ventas', dtConfig);
    });
</script>