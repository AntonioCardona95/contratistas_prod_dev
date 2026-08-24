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
    .dt-container .row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 0;
    }

    #botones-detalles,
    #search-detalles {
        display: flex;
        align-items: center;
        min-height: 44px;
    }

    #botones-detalles {
        justify-content: flex-start;
    }

    #search-detalles {
        justify-content: flex-end;
        margin-left: auto;
        width: 100%;
    }

    .dt-search {
        margin-bottom: 0;
        margin-top: 0;
        display: flex;
        justify-content: flex-end;
        width: 100%;
    }

    .dt-search input {
        width: 320px !important;
        max-width: 100% !important;
        padding-left: 35px;
        border-radius: 8px;
    }

    .dt-paging {
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
    }

    .dt-paging .pagination {
        margin: 0;
        gap: 5px;
    }

    .dt-paging .pagination .page-item .page-link {
        border-radius: 6px;
        color: #2f3f52;
        border: 1px solid #dfe3e8;
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

    #tecnicos-grid-container {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin: 20px 0;
    }

    @media (max-width: 1200px) {
        #tecnicos-grid-container {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .dt-container .row {
            display: block;
        }

        #botones-detalles,
        #search-detalles {
            justify-content: flex-start;
            width: 100%;
            margin-top: 10px;
        }

        #search-detalles {
            justify-content: stretch;
        }

        #tecnicos-grid-container {
            grid-template-columns: 1fr;
        }
    }

    .tecnico-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        transition: all 0.25s ease;
        border: 1px solid rgba(59, 130, 246, 0.35);
        position: relative;
    }

    .tecnico-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 5px;
        background: linear-gradient(90deg, #1d82f5 0%, #2ec4b6 100%);
    }

    .tecnico-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 35px rgba(37, 99, 235, 0.12);
        border-color: rgba(29, 130, 245, 0.7);
    }

    .tecnico-card .card-body {
        padding: 22px 20px 18px;
    }

    .tecnico-nombre {
        font-size: 1.05rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        line-height: 1.3;
    }

    .tecnico-nombre::before {
        content: '\f007';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        color: #1d82f5;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(29, 130, 245, 0.1);
    }

    .tecnico-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        padding: 10px 12px;
        background: linear-gradient(180deg, #f8fafc 0%, #eef3f8 100%);
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        gap: 10px;
    }

    .tecnico-info-item:nth-child(2n) {
        border-left: 3px solid #18a957;
    }

    .tecnico-info-item:nth-child(3n) {
        border-left: 3px solid #f59e0b;
    }

    .tecnico-info-item:first-of-type {
        border-left: 3px solid #1d82f5;
    }

    .tecnico-info-label {
        font-weight: 700;
        color: #475569;
        min-width: 102px;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .tecnico-info-value {
        color: #1e293b;
        font-weight: 600;
        flex: 1;
        margin-left: 6px;
        font-size: 0.92rem;
        word-break: break-word;
        text-align: right;
    }
</style>

<?php if ($totalTecnicos === 0) : ?>
    <div class="alert alert-empty" role="alert">
        <i class="fas fa-users"></i>
        No se encontraron técnicos activos para este contratista.
    </div>
<?php else : ?>
    <!-- BOTON DE EXCEL -->
    <div id="tecnicos-buttons-container" class="mb-3"></div>

    <div id="tecnicos-table-wrapper">
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
    </div>

    <div id="tecnicos-grid-container" class="row g-4"></div>
<?php endif; ?>

<?php if ($totalTecnicos > 0) : ?>
<script>
(function () {
    const table = new DataTable('#TecnicosTable', {
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json'
        },
        ordering: false,
        searching: true,
        pageLength: 12,
        initComplete: function () {
            const api = this.api();
            $('#TecnicosTable').hide();
            $('.dt-search input').attr('placeholder', 'Buscar por nombre, expediente o telefono');
            renderCards(api);
        }
    });

    const botones = new DataTable.Buttons(table, {
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
            titleAttr: 'Exportar a Excel',
            className: 'btn btn-success'
        }]
    });

    botones.container().appendTo('#tecnicos-buttons-container');

    function renderCards(dataTable) {
        const container = $('#tecnicos-grid-container');
        container.empty();

        const visibleRows = dataTable.rows({ page: 'current' }).nodes();
        if (visibleRows.length === 0) {
            container.html('<div class="col-12 text-center"><p>No se encontraron técnicos.</p></div>');
            return;
        }

        $(visibleRows).each(function () {
            const row = $(this);
            container.append(`
                <div class="tecnico-card">
                    <div class="card-body">
                        <div class="tecnico-nombre">${row.data('nombre') || 'Sin nombre'}</div>
                        <div class="tecnico-info-item">
                            <span class="tecnico-info-label">Expediente</span>
                            <span class="tecnico-info-value">${row.data('expediente') || 'N/A'}</span>
                        </div>
                        <div class="tecnico-info-item">
                            <span class="tecnico-info-label">Teléfono</span>
                            <span class="tecnico-info-value">${row.data('telefono') || 'N/A'}</span>
                        </div>
                        <div class="tecnico-info-item">
                            <span class="tecnico-info-label">COPE</span>
                            <span class="tecnico-info-value">${row.data('cope') || 'SIN COPE'}</span>
                        </div>
                    </div>
                </div>
            `);
        });
    }

    table.on('search.dt page.dt order.dt', function () {
        renderCards(table);
    });
}());
</script>
<?php endif; ?>

