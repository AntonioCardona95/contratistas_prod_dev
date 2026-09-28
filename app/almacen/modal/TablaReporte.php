<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Se cambió la clase a ClassConsultasProduccion
    require_once("../controller/ClassConsultasProduccion.php");
    require_once(__DIR__ . '/../../config/ContratistaContexto.php');

    $modal = new ClassConsultasProduccion();
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $Contratista = $_SESSION['Contratista'] ?? null;
    $idContratistas = obtenerIdContratistaContexto();

    $Fecha1 = $_POST['Fecha1'] ?? '';
    $Fecha2 = $_POST['Fecha2'] ?? '';

    $mes_anterior = date('Y-m-d', strtotime('-1 month'));

    if (empty($Fecha1)) {
        $Fecha1 = $mes_anterior . " 00:00:00";
        $Fecha2 = date('Y-m-d') . " 23:59:59";
    } else {
        $Fecha1 = $Fecha1 . " 00:00:00"; 
        $Fecha2 = (!empty($Fecha2) ? $Fecha2 : date('Y-m-d')) . " 23:59:59";
    }

    // Llamada al método de la clase de producción
    $filas = $modal->ConsultarProduccionDiaria($idContratistas, $Fecha1, $Fecha2);
?>

<style>
    @media (max-width: 768px) {
        .dt-buttons {
            margin-bottom: 15px;
        }
    }

    #entradas-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    #entradas_wrapper .dt-scroll-head th,
    #entradas_wrapper .dt-scroll-foot th,
    #entradas_wrapper .dt-scroll-body td,
    #entradas thead th,
    #entradas tfoot th,
    #entradas tbody td {
        text-align: center !important;
        vertical-align: middle !important;
    }

    #entradas-cards-container {
        display: none;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .entrada-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.04);
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .entrada-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }

    .entrada-card-header {
        padding: 18px 18px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .entrada-card-body {
        padding: 0 18px 18px;
    }

    .entrada-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f3f5;
        gap: 10px;
    }

    .entrada-field:last-child {
        border-bottom: none;
    }

    .entrada-field span {
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        flex: 1;
    }

    .entrada-field strong {
        color: #1f2937;
        font-size: 0.95rem;
        font-weight: 600;
        text-align: right;
        min-width: 120px;
    }

    @media (max-width: 1200px) {
        #entradas-cards-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* ── BUSCADOR MOVIL ── */
    #entradas-mobile-search {
        display: none;
    }

    @media (max-width: 768px) {
        #entradas-table-wrapper {
            display: none;
        }

        #entradas-cards-container {
            display: grid;
            grid-template-columns: 1fr;
        }

        #entradas-mobile-search {
            display: block;
            margin-bottom: 14px;
        }

        .modal-title {
            font-size: 15px !important;
        }

        h2 {
            font-size: 15px !important;
        }
    }
</style>

<div id="reporte-loading" style="text-align: center; padding: 20px;">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
    <p>Cargando datos del reporte, por favor espere...</p>
</div>

<div id="contenedor-tabla-reporte" style="opacity: 0; transition: opacity 0.3s ease;">
    <div id="reporte-buttons-container"></div>
    <div id="reporte-table-wrapper">
        <table class="table table-bordered display nowrap" style="width:100%" id="reporte-produccion">
            <thead>
                <tr>
                    <th>Folio Pisa</th>
                    <th>Teléfono</th>
                    <th>Tipo Tarea</th>
                    <th>Contratista</th>
                    <th>Técnico</th>
                    <th>Fecha Liq</th>
                    <th>Señalamiento</th>
                    <th>Navegación</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Folio Pisa</th>
                    <th>Teléfono</th>
                    <th>Tipo Tarea</th>
                    <th>Contratista</th>
                    <th>Técnico</th>
                    <th>Fecha Liq</th>
                    <th>Señalamiento</th>
                    <th>Navegación</th>
                    <th>Acción</th>
                </tr>
            </tfoot>
            <tbody>
                <?php if (!empty($filas)): foreach ($filas as $row): ?>
                    <tr data-folio="<?= htmlspecialchars($row['Folio_Pisa'], ENT_QUOTES, 'UTF-8') ?>"
                        data-telefono="<?= htmlspecialchars($row['TELEFONO'], ENT_QUOTES, 'UTF-8') ?>"
                        data-contratista="<?= htmlspecialchars(strtoupper($row['Contratista'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-tecnico="<?= htmlspecialchars(strtoupper($row['Tecnico'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                        data-fecha="<?= htmlspecialchars($row['FECHA_LIQ'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        
                        <td><?= htmlspecialchars($row['Folio_Pisa'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['TELEFONO'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Tipo_de_Tarea'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Contratista'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['Tecnico'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['FECHA_LIQ'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if (!empty($row['Tiene_Senalamiento'])): ?>
                                <span class="badge bg-warning text-dark">
                                    <?= htmlspecialchars($row['Tipo_Senalamiento'] ?? 'Sí', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Sin señalamiento</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= ($row['Flag_Navega'] ?? 0) == 1 ? 'bg-success' : 'bg-danger' ?>">
                                <?= htmlspecialchars($row['Estatus_Navegacion'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm btn-detalle-reporte" 
                                    data-folio="<?= htmlspecialchars($row['Folio_Pisa'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-telefono="<?= htmlspecialchars($row['TELEFONO'], ENT_QUOTES, 'UTF-8') ?>" 
                                    data-fecha="<?= htmlspecialchars($row['FECHA_LIQ'], ENT_QUOTES, 'UTF-8') ?>" 
                                    title="Ver detalle del reporte">
                                <i class="fa-solid fa-file-lines"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div id="reporte-mobile-search">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="reporte-search-input" class="form-control" placeholder="Buscar en el reporte...">
        </div>
    </div>

    <div id="reporte-cards-container"></div>
</div>