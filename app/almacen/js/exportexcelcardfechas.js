$(document).ready(function() {
    console.log("--> [exportexcelcardfechas.js] Script cargado y listo.");

    $(document).on('click', '#btnExportarLiquidadas', function(e) {
        e.preventDefault();

        console.group("=== [EXPORTACIÓN A EXCEL INICIADA] ===");
        console.log("--> Evento click detectado en #btnExportarLiquidadas");

        // 1. Extraer los valores probando las IDs detectadas
        const fechaInicio   = $('#fechascard1').val() || $('#fecha_inicial').val() || $('#Fecha1').val() || '';
        const fechaFin      = $('#fechascard2').val() || $('#fecha_final').val() || $('#Fecha2').val() || '';
        const idContratista = $('#select_contratista').val() || $('#idContratistas').val() || '';
        const tipoTarea     = $('#select_tipo_tarea').val() || $('#TipoTarea').val() || '';
        const area          = $('#select_area').val() || '';
        const division      = $('#select_division').val() || '';
        const cope          = $('#select_cope').val() || '';

        // 2. Mostrar cada variable individualmente en la consola
        console.group("--> [VALORES CAPTURADOS DEL DOM]");
        console.log("Fecha Inicio (#fechascard1 / #Fecha1):", fechaInicio);
        console.log("Fecha Fin (#fechascard2 / #Fecha2):", fechaFin);
        console.log("Contratista:", idContratista);
        console.log("Tipo Tarea:", tipoTarea);
        console.log("Área:", area);
        console.log("División:", division);
        console.log("Cope:", cope);
        console.groupEnd();

        // Validar si las fechas tienen datos
        if (!fechaInicio || !fechaFin) {
            console.warn("⚠️ ALERTA: No se detectaron fechas seleccionadas o están vacías.");
        } else {
            console.log("✅ Rango de fechas detectado correctamente.");
        }

        const datosFiltro = {
            fecha_inicio: fechaInicio,
            fecha_fin:    fechaFin,
            contratista:  idContratista,
            tipo_tarea:   tipoTarea,
            area:         area,
            division:     division,
            cope:         cope
        };

        // 3. Feedback visual en el botón
        const $btn = $(this);
        const htmlOriginal = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');
        console.log("--> Estado del botón cambiado a 'Generando...'");

        // 4. Crear iframe transparente en el DOM para canalizar la descarga POST sin bloqueo HTTP
        let $iframe = $('#iframeDescargaExcel');
        if ($iframe.length === 0) {
            console.log("--> Creando iframe oculta '#iframeDescargaExcel'");
            $iframe = $('<iframe>', {
                id: 'iframeDescargaExcel',
                name: 'iframeDescargaExcel',
                style: 'display:none;'
            }).appendTo('body');
        }

        // 5. Construir y enviar el formulario dinámico
        console.log("--> Generando formulario dinámico POST dirigido a modal/ExportarPorFecha.php");
        const $form = $('<form>', {
            action: 'modal/ExportarPorFecha.php',
            method: 'POST',
            target: 'iframeDescargaExcel',
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
        console.log("--> Formulario inyectado en el DOM. Enviando (submit)...");
        $form.submit();
        $form.remove();

        // 6. Restablecer el botón
        setTimeout(function() {
            $btn.prop('disabled', false).html(htmlOriginal);
            console.log("--> Proceso finalizado exitosamente. Botón restaurado.");
            console.groupEnd();
        }, 1500);
    });
});