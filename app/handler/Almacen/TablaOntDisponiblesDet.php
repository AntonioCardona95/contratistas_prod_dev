<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . '/../../controller/ClassOps.php';

    $modal = new Ops();
    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];
	$filas = $modal->ONTDisponiblesAsignarDet($idContratistas);
    
?>
<table class="table table-bordered display nowrap" style="width:100%" id="modalTable">			
    <thead>
        <tr>
            <th>Folio Vale</th>
            <th>Fecha Entrega</th>
            <th>Almacen Origen</th>
            <th>Material</th> 
            <th>SKU</th>
            <th>Numero de Serie</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Folio Vale</th>
            <th>Fecha Entrega</th>
            <th>Almacen Origen</th>
            <th>Material</th> 
            <th>SKU</th>
            <th>Numero de Serie</th>
        </tr>
    </tfoot>
    <?php
        if(!empty($filas)){
            
            foreach($filas as $row){

                echo "
                <tr>
                    <td>".$row['Folio_Salida_Material_Det_S']."</td>
                    <td>".$row['Fecha_Salida_S']."</td>
                    <td>".$row['Localidad_Material_S']."</td>
                    <td>".$row['Producto']."</td>
                    <td>".$row['Modelo']."</td>
                    <td>".$row['Num_Serie_S']."</td>
                </tr>";
            }

        }
    ?>
</table>
<script>
	$(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#modalTable')) {
            $('#modalTable').DataTable().destroy();
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
        
        let table = new DataTable('#modalTable', dtConfig);
	});
</script>
