$(document).ready(function() {
    console.log("--> JS exportexcelcardfechas.js cargado correctamente.");

    $(document).on('click', '#btnExportarLiquidadas', function(e) {
        e.preventDefault();

        console.log("=== INICIANDO EXPORTACIÓN A EXCEL (POST SEGURO) ===");

        // 1. Extraer fechas probando los IDs reales descubiertos en la traza (fechascard1, fechascard2)
        const fechaInicio   = $('#fechascard1').val() || $('#fecha_inicial').val() || $('#Fecha1').val() || '';
        const fechaFin      = $('#fechascard2').val() || $('#fecha_final').val() || $('#Fecha2').val() || '';
        const idContratista = $('#select_contratista').val() || $('#idContratistas').val() || '';
        const tipoTarea     = $('#select_tipo_tarea').val() || $('#TipoTarea').val() || '';
        const area          = $('#select_area').val() || '';
        const division      = $('#select_division').val() || '';
        const cope          = $('#select_cope').val() || '';

        const datosFiltro = {
            fecha_inicio: fechaInicio,
            fecha_fin:    fechaFin,
            contratista:  idContratista,
            tipo_tarea:   tipoTarea,
            area:         area,
            division:     division,
            cope:         cope
        };

        console.log("Parámetros capturados para envío:", datosFiltro);

        if (!fechaInicio || !fechaFin) {
            console.warn("Atención: Rango de fechas no detectado o vacío.");
        }

        // 2. Feedback temporal en el botón
        const $btn = $(this);
        const htmlOriginal = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');

        // 3. Crear iframe invisible para canalizar la descarga sin recargar la página ni usar blobs propensos a bloqueos HTTP
        let $iframe = $('#iframeDescargaExcel');
        if ($iframe.length === 0) {
            $iframe = $('<iframe>', {
                id: 'iframeDescargaExcel',
                name: 'iframeDescargaExcel',
                style: 'display:none;'
            }).appendTo('body');
        }

        // 4. Crear formulario dinámico POST dirigido al iframe
        const $form = $('<form>', {
            action: 'modal/ExportarPorFecha.php',
            method: 'POST',
            target: 'iframeDescargaExcel',
            style: 'display:none;'
        });

        // 5. Inyectar campos hidden
        $.each(datosFiltro, function(clave, valor) {
            $form.append($('<input>', {
                type: 'hidden',
                name: clave,
                value: valor
            }));
        });

        // 6. Enviar formulario y restaurar el botón
        $('body').append($form);
        $form.submit();
        $form.remove();

        // Restaurar botón tras un breve retraso
        setTimeout(function() {
            $btn.prop('disabled', false).html(htmlOriginal);
            console.log("--> Formulario enviado. Descarga iniciada en iframe oculto.");
        }, 1500);
    });
});