<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . '/../controller/ClassIndex.php';
    require_once __DIR__ . '/../../config/ContratistaContexto.php';

    $index = new Index();
    session_start();
    $Contratista = $_SESSION['Contratista'];
    $idContratistas = obtenerIdContratistaContexto();

    $Fecha1 = $_POST['Fecha1'] ?? '';
    $Fecha2 = $_POST['Fecha2'] ?? '';

    $mes_anterior = date('Y-m-d', strtotime('-3 month'));

    if (empty($Fecha1) || empty($Fecha2)) {
        $Fecha1 = $mes_anterior . ' 00:00:00';
        $Fecha2 = date('Y-m-d') . ' 23:59:59';
    } else {
        $Fecha1 = $Fecha1 . ' 00:00:00';
        $Fecha2 = $Fecha2 . ' 23:59:59';
    }

    $filas = $index->ONTInstaladosDet($idContratistas, $Fecha1, $Fecha2);
?>

<style>
    /* ESTILOS RESPONSIVOS PARA TARJETAS EN MÓVIL */
    #ont-instaladas-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    #ont-instaladas-cards-container {
        display: none;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .ont-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .ont-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    .ont-card-header {
        padding: 16px 16px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .ont-card-body {
        padding: 0 16px 16px;
    }

    .ont-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f3f5;
        gap: 10px;
    }

    .ont-field:last-child {
        border-bottom: none;
    }

    .ont-field span {
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        flex: 1;
    }

    .ont-field strong {
        color: #1f2937;
        font-size: 0.95rem;
        font-weight: 600;
        text-align: right;
        min-width: 120px;
    }

    /* BUSCADOR MÓVIL */
    #ont-instaladas-mobile-search {
        display: none;
    }

    @media (max-width: 1200px) {
        #ont-instaladas-cards-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        #ont-instaladas-table-wrapper {
            display: none;
        }

        #ont-instaladas-cards-container {
            display: grid;
            grid-template-columns: 1fr;
        }

        #ont-instaladas-mobile-search {
            display: block;
            margin-bottom: 14px;
        }
    }
</style>

<!-- CONTENEDOR PRINCIPAL CON TABLA Y TARJETAS -->
<div id="ont-instaladas-wrapper">
    <div id="ont-instaladas-buttons-container"></div>
    <!-- TABLA PARA DESKTOP -->
    <div id="ont-instaladas-table-wrapper">
        <table class="table table-bordered display nowrap" style="width:100%" id="ont-instaladas">			
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
            <tbody>
                <?php
                    if(!empty($filas)){
                        foreach($filas as $row){
                            echo "
                            <tr 
                                data-folio='".$row['Folio_Pisa']."'
                                data-tecnico='".$row['Tecnico']."'
                                data-material='".$row['Producto']."'
                                data-sku='".$row['Modelo']."'
                                data-serie='".$row['Num_Serie_Salida_Det']."'
                                data-fecha-asignacion='".$row['Fecha_Salida_Det']."'
                                data-fecha-instalacion='".$row['Fecha_Coordiapp']."'>
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
            </tbody>
        </table>
    </div>

    <!-- BUSCADOR MÓVIL -->
    <div id="ont-instaladas-mobile-search">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="ont-instaladas-search-input" class="form-control" placeholder="Buscar ONT...">
        </div>
    </div>

    <!-- CONTENEDOR DE TARJETAS PARA MÓVIL -->
    <div id="ont-instaladas-cards-container"></div>
</div>

<script>
	$(document).ready(function () {
        let table = new DataTable('#ont-instaladas', {
            fixedColumns: {
                start: 1,
                end: 1
            },
            responsive:true,
            'language':{
                url:'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
            },
            "ordering": false,
            "searching": true,
			"scrollY": true,
			"scrollX": true,
            "initComplete": function(settings, json) {
                renderOntInstaladosCards();
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
        
        table.buttons(0, null).container().appendTo('#ont-instaladas-buttons-container');

        // FUNCIÓN PARA GENERAR TARJETAS DESDE DATOS DE LA TABLA
        function renderOntInstaladosCards() {
            const container = $('#ont-instaladas-cards-container');
            container.empty();

            const rows = table.rows({ search: 'applied' }).nodes();

            if (rows.length === 0) {
                container.html('<div class="text-center text-muted">No se encontraron registros.</div>');
                return;
            }

            rows.each(function(node) {
                const row = $(node);
                const folio = row.data('folio');
                const tecnico = row.data('tecnico');
                const material = row.data('material');
                const sku = row.data('sku');
                const serie = row.data('serie');
                const fechaAsignacion = row.data('fecha-asignacion');
                const fechaInstalacion = row.data('fecha-instalacion');

                const card = `
                    <div class="ont-card">
                        <div class="ont-card-header">
                            <div>
                                <div class="font-weight-bold">${folio}</div>
                                <div class="text-muted small">${tecnico}</div>
                            </div>
                        </div>
                        <div class="ont-card-body">
                            <div class="ont-field">
                                <span>Material</span>
                                <strong>${material}</strong>
                            </div>
                            <div class="ont-field">
                                <span>SKU</span>
                                <strong>${sku}</strong>
                            </div>
                            <div class="ont-field">
                                <span>Serie</span>
                                <strong>${serie}</strong>
                            </div>
                            <div class="ont-field">
                                <span>Asignación</span>
                                <strong>${fechaAsignacion}</strong>
                            </div>
                            <div class="ont-field">
                                <span>Instalación</span>
                                <strong>${fechaInstalacion}</strong>
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });
        }

        // REDIBUJAR TARJETAS CUANDO HAY CAMBIOS EN LA TABLA
        table.on('search.dt page.dt draw.dt', function() {
            renderOntInstaladosCards();
        });

        // BUSCADOR MÓVIL
        $('#ont-instaladas-search-input').on('input', function() {
            table.search($(this).val()).draw();
        });
    });
</script>