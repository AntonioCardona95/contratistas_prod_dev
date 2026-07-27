<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once("../controller/ClassOps.php");

    $modal = new Ops();
    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = $_SESSION['idContratistas'];

    $Fecha1 = $_POST['Fecha1'];
    $Fecha2 = $_POST['Fecha2'];

    $mes_anterior = date('Y-m-d', strtotime('-1 month'));

    if(empty($Fecha1)){
        $Fecha1 = $mes_anterior." 00:00:00";
        $Fecha2 = date('Y-m-d')." 23:59:59";
		$filas = $modal->Entradas($idContratistas,$Fecha1,$Fecha2);
    }elseif(!empty($Fecha1) && !empty($Fecha2)){
        $Fecha1 = $_POST['Fecha1']." 00:00:00"; 
        $Fecha2 = $_POST['Fecha2']." 23:59:59";
		$filas = $modal->Entradas($idContratistas,$Fecha1,$Fecha2);
    }
?>
<div id="entradas-loading" style="text-align: center; padding: 20px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Cargando datos, por favor espere...</p>
</div>

<div id="contenedor-tabla-entradas" style="opacity: 0; transition: opacity 0.3s ease;">
    <table class="table table-bordered display nowrap" style="width:100%" id="ventas">            
        <thead>
            <tr>
                <th>Folio Entrada</th> 
                <th>Almacen</th>
                <th>Contratista</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Accion</th>
            </tr>
        </thead>
    <tfoot>
        <tr>
            <th>Folio Entrada</th> 
            <th>Almacen</th>
            <th>Contratista</th>
            <th>Tipo</th>
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
                    <td>".$row['Folio_Salida_ST']."</td>
                    <td>".$row['Almacen']."</td>
                    <td>".$row['Contratista']."</td>
                    <td>".$row['Tipo_MovS_T']."</td>
                    <td>".$row['Cantidad_ST']."</td>
                    <td>".$row['Fecha_Salida_ST']."</td>
                    <td>
                        <a href='modal/EntradasDet.php?id=".$row['Folio_Salida_ST']."'>
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
        let table = new DataTable('#ventas', {
            responsive: true,
            language: { url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json' },
            ordering: false,
            searching: true,
            scrollY: true,
            scrollX: true,
            initComplete: function(settings, json) {
                $('#entradas-loading').hide();
                $('#contenedor-tabla-entradas').css('opacity', '1');
            }
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