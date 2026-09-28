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