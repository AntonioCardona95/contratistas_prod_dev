$(document).ready(function() {
    // Evitar que el evento click se duplique al recargar el DOM vía AJAX
    $(document).off('click', '#btnExportarLiquidadas').on('click', '#btnExportarLiquidadas', function(e) {
        e.preventDefault();

        // 1. Capturar los filtros desde el DOM
        const fechaInicio   = $('#fechascard1').val() || $('#fecha_inicial').val() || $('#Fecha1').val() || '';
        const fechaFin      = $('#fechascard2').val() || $('#fecha_final').val() || $('#Fecha2').val() || '';
        const idContratista = $('#selectContratista').val() || $('#select_contratista').val() || '';
        const tipoTarea     = $('#TipoTarea').val() || $('#select_tipo_tarea').val() || '';
        const area          = $('#selectArea').val() || $('#select_area').val() || '';
        const division      = $('#selectDivision').val() || $('#select_division').val() || '';
        const cope          = $('#selectCope').val() || $('#select_cope').val() || '';

        const datosFiltro = {
            fecha_inicio: fechaInicio,
            fecha_fin:    fechaFin,
            contratista:  idContratista,
            tipo_tarea:   tipoTarea,
            area:         area,
            division:     division,
            cope:         cope
        };

        // 2. Feedback visual temporal en el botón
        const $btn = $(this);
        const htmlOriginal = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');

        // 3. Crear formulario dinámico POST en segundo plano
        const $form = $('<form>', {
            action: 'modal/ExportarPorFecha.php',
            method: 'POST',
            style: 'display:none;'
        });

        $.each(datosFiltro, function(clave, valor) {
            $form.append($('<input>', {
                type: 'hidden',
                name: clave,
                value: valor
            }));
        });

        // 4. Enviar formulario
        $('body').append($form);
        $form.submit();
        $form.remove();

        // 5. Restaurar el botón inmediatamente después del submit
        setTimeout(function() {
            $btn.prop('disabled', false).html(htmlOriginal);
        }, 1000);
    });
});