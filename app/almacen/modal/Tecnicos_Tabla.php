<?php
    session_start();

    require_once __DIR__ . '/../controller/ClassIndex.php';
    require_once __DIR__ . '/../../config/ContratistaContexto.php';

    requerirSesionAlmacen();

    $operaciones = new Index();
    $tecnicos = $operaciones->Tecnicos(obtenerIdContratistaContexto()) ?: [];
    $totalTecnicos = count($tecnicos);
?>

<?php if ($totalTecnicos === 0) : ?>
    <div class="alert alert-empty" role="alert">
        <i class="fas fa-users"></i>
        No se encontraron técnicos activos para este contratista.
    </div>
<?php else : ?>
    <!-- BOTON DE EXCEL -->
    <div id="tecnicos-buttons-container" class="mb-3"></div>
    <div id="tecnicos-mobile-search">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search" aria-hidden="true"></i></span>
            </div>
            <input type="text" id="tecnicos-search-input" class="form-control" placeholder="Buscar técnicos...">
        </div>
    </div>

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
                        $cope = $tecnico['COPE'] ?? 'SIN COPE';
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

    <div id="tecnicos-grid-container"></div>
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
        pageLength: 10,
        initComplete: function () {
            const api = this.api();
            $('#TecnicosTable').hide();
        
            // Mueve el contenedor de tarjetas al centro de la estructura de DataTables
            $('#TecnicosTable').after($('#tecnicos-grid-container'));

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

        function escapeHtml(value) {
            return $('<div>').text(value == null ? '' : value).html();
        }

        $(visibleRows).each(function () {
            const row = $(this);
            const nombre = escapeHtml(row.data('nombre') || 'Sin nombre');
            const expediente = escapeHtml(row.data('expediente') || 'N/A');
            const telefono = escapeHtml(row.data('telefono') || 'N/A');
            const cope = escapeHtml(row.data('cope') || 'SIN COPE');

            container.append(`
                <article class="tecnico-card">
                    <div class="tecnico-card-cover"></div>
                    <span class="tecnico-status">ACTIVO</span>
                    <div class="tecnico-avatar" aria-hidden="true">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <div class="tecnico-card-body">
                        <h3 class="tecnico-nombre">${nombre}</h3>
                        <div class="tecnico-expediente">
                            <i class="fas fa-id-card" aria-hidden="true"></i>
                            <span>EXPEDIENTE: #${expediente}</span>
                        </div>
                        <hr class="tecnico-card-divider">
                        <div class="tecnico-info-item">
                            <span class="tecnico-info-icon" aria-hidden="true"><i class="fas fa-phone"></i></span>
                            <span class="tecnico-info-copy">
                                <span class="tecnico-info-label">Teléfono</span>
                                <span class="tecnico-info-value">${telefono}</span>
                            </span>
                        </div>
                        <div class="tecnico-info-item">
                            <span class="tecnico-info-icon" aria-hidden="true"><i class="fas fa-building"></i></span>
                            <span class="tecnico-info-copy">
                                <span class="tecnico-info-label">Centro de trabajo</span>
                                <span class="tecnico-info-value">${cope}</span>
                            </span>
                        </div>
                        <div class="tecnico-inventario">
                            <span class="tecnico-inventario-icon" aria-hidden="true"><i class="fas fa-boxes"></i></span>
                            <span class="tecnico-inventario-copy">
                                <span class="tecnico-inventario-label">Fibra restante</span>
                                <span class="tecnico-inventario-description">Inventario asignado en turno</span>
                            </span>
                            <span class="tecnico-inventario-cantidad">0 m</span>
                        </div>
                    </div>
                    <footer class="tecnico-card-footer">Identificación oficial del técnico</footer>
                </article>
            `);
        });
    }

    table.on('search.dt page.dt order.dt', function () {
        renderCards(table);
    });

    $('#tecnicos-search-input').on('input', function () {
        table.search($(this).val()).draw();
    });
}());
</script>
<?php endif; ?>
