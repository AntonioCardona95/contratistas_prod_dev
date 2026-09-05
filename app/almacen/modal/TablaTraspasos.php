<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassOps.php");
    require_once(__DIR__ . '/../../config/ContratistaContexto.php');

    $modal = new Ops();
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
<table class="table table-bordered display nowrap" style="width:100%" id="ventas">			
    <thead>
        <tr>
            <th>Folio Traspaso</th> 
            <th>Contratista</th>
            <th>Tecnico Origen</th>
            <th>Tecnico Destino</th>
            <th>Cantidad</th>
            <th>Fecha</th>
            <th>Comentarios</th>
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
            <th>Comentarios</th>
            <th>Accion</th>
        </tr>
    </tfoot>
    <?php
        if(!empty($filas)){
            foreach($filas as $row){
                echo "
                    <tr>
                        <td>".$row['Folio_Traspaso_T']."</td>
                        <td>".$row['Contratista']."</td>
                        <td>".$row['TecnicoOrigen']."</td>
                        <td>".$row['TecnicoDestino']."</td>
                        <td>".$row['Cantidad_Traspaso']."</td>
                        <td>".$row['Fecha_Traspaso']."</td>
                        <td>".$row['Comentarios_Traspaso']."</td>
                        <td>
                            <a href='modal/TraspasoDet.php?id=".$row['Folio_Traspaso_T']."'>
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