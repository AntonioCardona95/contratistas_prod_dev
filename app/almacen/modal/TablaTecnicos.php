<?php
    session_start();

    require_once __DIR__ . '/../controller/ClassOps.php';

    if (empty($_SESSION['idContratistas'])) {
        header('location: ../../Login/modal/poweroff.php');
        exit;
    }

    $operaciones = new Ops();
    $tecnicos = $operaciones->Tecnicos($_SESSION['idContratistas']) ?: [];
    $totalTecnicos = count($tecnicos);
?>

<style>
    .prod-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 45%, #0f766e 100%);
        color: #fff;
        border-radius: 12px;
        padding: 1rem 1.75rem;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        margin: 15px 0;
    }

    .prod-hero h1 {
        margin: 0;
    }

    .badge-total {
        background: linear-gradient(135deg, #007BFF, #17A2B8);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
    }

    .dt-search {
        margin-bottom: 0px;
        margin-top: 0px;
        width: 100%;
    }

    .dt-search label {
        width: 100%;
        margin: 0;
    }

    .dt-search input {
        width: 100% !important;
        max-width: 520px !important;
        padding: 10px 15px !important;
        border: 1px solid #d6dbe1;
        border-radius: 8px;
        font-size: 14px !important;
        background: #fff;
        color: #2a2f36;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
    }

    .dt-search input:focus {
        outline: none;
        border-color: #1d82f5;
        box-shadow: 0 0 0 3px rgba(29, 130, 245, 0.1);
    }

    .dt-buttons {
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .dt-buttons .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.55rem 1rem;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
    }

    .dt-buttons .btn-success {
        background: linear-gradient(135deg, #1abf73, #2cae5a);
        border: none;
    }

    .dt-buttons .btn-success:hover {
        background: linear-gradient(135deg, #15995f, #1f8e4e);
    }

    .dt-paging {
        margin-top: 0px;
        display: flex;
        justify-content: flex-end;
    }

    .dt-paging .pagination {
        margin: 0;
        gap: 5px;
    }

    .dt-paging .pagination .page-item .page-link {
        color: #2f3f52;
        border-radius: 6px;
        border: 1px solid #dfe3e8;
        padding: 0.45rem 0.8rem;
        background: #fff;
    }

    .dt-paging .pagination .page-item.active .page-link {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .dt-info {
        padding-top: 0.4rem;
        color: #495057;
        font-size: 14px;
        margin: 0;
    }

    #tecnicos-grid-container {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 25px;
        margin: 20px 0 20px 0;
    }

    @media (max-width: 1200px) {
        #tecnicos-grid-container {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        #tecnicos-grid-container {
            grid-template-columns: 1fr;
        }
    }

    .tecnico-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 2px solid rgba(29, 130, 245, 0.9);
        border-top: 4px solid rgba(29, 130, 245, 0.9);
    }

    .tecnico-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .tecnico-card .card-body {
        padding: 25px;
    }

    .tecnico-nombre {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .tecnico-nombre::before {
        content: '\f007';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        color: #1d82f5;
    }

    .tecnico-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 14px;
        padding: 10px 10px 10px 12px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #17a2b8;
    }

    .tecnico-info-item:nth-child(2n) {
        border-left-color: #28a745;
    }

    .tecnico-info-item:nth-child(3n) {
        border-left-color: #ffc107;
    }

    .tecnico-info-label {
        font-weight: 600;
        color: #495057;
        min-width: 110px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tecnico-info-value {
        color: #2c3e50;
        font-weight: 500;
        flex: 1;
        margin-left: 10px;
        font-size: 0.95rem;
        word-break: break-word;
    }
</style>

<?php if ($totalTecnicos === 0) : ?>
    <div class="alert alert-empty" role="alert">
        <i class="fas fa-users"></i>
        No se encontraron técnicos activos para este contratista.
    </div>
<?php else : ?>
    <div class="card shadow mb-4" style="border: none; border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-6" id="search-container"></div>
                <div class="col-md-6 d-flex justify-content-end" id="buttons-container"></div>
            </div>
        </div>
    </div>

    <div id="tecnicos-grid-container"></div>

    <div class="card shadow mt-4" style="border: none; border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-6" id="pagination-info"></div>
                <div class="col-md-6 d-flex justify-content-end" id="pagination-container"></div>
            </div>
        </div>
    </div>

    <table class="table table-bordered display nowrap datatable-init" style="width:100%;" id="TecnicosTable">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Expediente</th>
                <th>Teléfono</th>
                <th>COPE</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tecnicos as $tecnico) : ?>
                <?php
                    $nombreCompleto = trim(($tecnico['Nombre_T'] ?? '') . ' ' . ($tecnico['Apellidos_T'] ?? ''));
                    $telefono = $tecnico['Celular_T'] ?? $tecnico['Telefono'] ?? 'N/A';
                    $expediente = $tecnico['NExpediente'] ?? 'N/A';
                    $cope = $tecnico['Cope'] ?? 'SIN COPE';
                ?>
                <tr data-nombre="<?= htmlspecialchars($nombreCompleto, ENT_QUOTES, 'UTF-8') ?>"
                    data-expediente="<?= htmlspecialchars($expediente, ENT_QUOTES, 'UTF-8') ?>"
                    data-telefono="<?= htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') ?>"
                    data-cope="<?= htmlspecialchars($cope, ENT_QUOTES, 'UTF-8') ?>">
                    <td><?= htmlspecialchars($nombreCompleto) ?></td>
                    <td><?= htmlspecialchars($expediente) ?></td>
                    <td><?= htmlspecialchars($telefono) ?></td>
                    <td><?= htmlspecialchars($cope) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

