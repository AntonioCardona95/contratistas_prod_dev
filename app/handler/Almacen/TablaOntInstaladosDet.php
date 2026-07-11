<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . '/../../controller/ClassOps.php';

    $modal = new Ops();
    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];

    $Fecha1 = $_POST['Fecha1'] ?? '';
    $Fecha2 = $_POST['Fecha2'] ?? '';

    $mes_anterior = date('Y-m-d', strtotime('-1 month'));

    if(empty($Fecha1)){
        $Fecha1 = $mes_anterior." 00:00:00";
        $Fecha2 = date('Y-m-d')." 23:59:59";
		$filas = $modal->ONTInstaladosDet($idContratistas,$Fecha1,$Fecha2);
    }elseif(!empty($Fecha1) && !empty($Fecha2)){
        $Fecha1 = $_POST['Fecha1']." 00:00:00"; 
        $Fecha2 = $_POST['Fecha2']." 23:59:59";
		$filas = $modal->ONTInstaladosDet($idContratistas,$Fecha1,$Fecha2);
    }
?>
<table class="table table-bordered display nowrap" style="width:100%" id="modalTable">			
    <thead>
        <tr>
            <th>Folio_Pisa</th>
            <th>Tecnico</th>
            <th>Material</th> 
            <th>SKU</th>
            <th>Numero de Serie</th>
            <th>Fecha Asignacion</th>
            <th>Fecha Instalacion</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Folio_Pisa</th>
            <th>Tecnico</th>
            <th>Material</th> 
            <th>SKU</th>
            <th>Numero de Serie</th>
            <th>Fecha Asignacion</th>
            <th>Fecha Instalacion</th>
        </tr>
    </tfoot>
    <?php
        if(!empty($filas)){
            
            foreach($filas as $row){

                echo "
                <tr>
                    <td>".$row['Folio_Pisa']."</td>
                    <td>".$row['Tecnico']."</td>
                    <td>".$row['Producto']."</td>
                    <td>".$row['Modelo']."</td>
                    <td>".$row['Num_Serie_Salida_Det']."</td>
                    <td>".$row['Fecha_Salida_Det']."</td>
                    <td>".$row['Fecha_Coordiapp']."</td>
                </tr>";
            }

        }
    ?>
</table>
<script>
	$(document).ready(function () {
        // Destruir DataTable anterior si existe
        if ($.fn.DataTable.isDataTable('#modalTable')) {
            $('#modalTable').DataTable().destroy();
        }
        
        let table = new DataTable('#modalTable', {
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
