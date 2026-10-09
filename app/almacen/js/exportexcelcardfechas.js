$(document).ready(function() {
    // Desvincular eventos previos para evitar ejecuciones duplicadas
    $(document).off('click', '#btnExportarLiquidadas').on('click', '#btnExportarLiquidadas', function(e) {
        e.preventDefault();

        // 1. Capturar los valores de cada filtro
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

        // 2. Estado visual temporal del botón
        const $btn = $(this);
        const htmlOriginal = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');

        // 3. Crear formulario dinámico POST
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

        // 4. Inyectar en DOM y submit
        $('body').append($form);
        $form.submit();
        $form.remove();

        // 5. Limpieza general de filtros (excepto Tipo de Tarea)
        setTimeout(function() {
            // Blanquear entradas de fecha
            $('#fechascard1, #fechascard2, #fecha_inicial, #fecha_final').val('');

            // Restablecer selectores de Contratista, Área, División y Cope (compatibilidad con select2 y select nativo)
            const $selectsALimpiar = $('#selectContratista, #select_contratista, #selectArea, #select_area, #selectDivision, #select_division, #selectCope, #select_cope');
            
            $selectsALimpiar.val('');
            if ($.fn.select2) {
                $selectsALimpiar.trigger('change');
            }

            // Restaurar el botón de exportación
            $btn.prop('disabled', false).html(htmlOriginal);
        }, 1000);
    });
});
/*$(document).ready(function() {
    // .off('click') evita que el evento se duplique al recargar la vista AJAX
    $(document).off('click', '#btnExportarLiquidadas').on('click', '#btnExportarLiquidadas', function(e) {
        e.preventDefault();

        // Capturar filtros
        const fechaInicio   = $('#fechascard1').val() || $('#fecha_inicial').val() || $('#Fecha1').val() || '';
        const fechaFin      = $('#fechascard2').val() || $('#fecha_final').val() || $('#Fecha2').val() || '';
        // console.log('fechaInicio:', fechaInicio);
        // console.log('fechaFin:', fechaFin);
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

        // Estado visual del botón
        const $btn = $(this);
        const htmlOriginal = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');

        // Crear formulario POST dinámico
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

        $('body').append($form);
        $form.submit();
        $form.remove();

        // Restaurar el botón tras enviar
        setTimeout(function() {
            $btn.prop('disabled', false).html(htmlOriginal);
        }, 1000);
    });
}); */