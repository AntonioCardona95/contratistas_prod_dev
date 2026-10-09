$(document).ready(function() {
    console.log("--> JS exportexcelcardfechas.js (AJAX) cargado correctamente.");

    $(document).on('click', '#btnExportarLiquidadas', function(e) {
        e.preventDefault();

        console.log("=== INICIANDO EXPORTACIÓN VÍA AJAX ===");

        // 1. Extraer los valores de los filtros dinámicamente desde el DOM
        const datosFiltro = {
            fecha_inicio: $('#fecha_inicial').val() || $('#Fecha1').val() || '',
            fecha_fin:    $('#fecha_final').val() || $('#Fecha2').val() || '',
            contratista:  $('#select_contratista').val() || $('#idContratistas').val() || '',
            tipo_tarea:   $('#select_tipo_tarea').val() || $('#TipoTarea').val() || '',
            area:         $('#select_area').val() || '',
            division:     $('#select_division').val() || '',
            cope:         $('#select_cope').val() || ''
        };

        // Imprimir datos en consola para depuración
        console.log("Parámetros capturados para la petición POST:", datosFiltro);

        // Feedback visual en el botón durante la carga
        const $btn = $(this);
        const htmlOriginal = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generando...');

        // 2. Realizar petición AJAX enviando datos por POST
        $.ajax({
            url: 'modal/ExportarPorFecha.php',
            type: 'POST',
            data: datosFiltro,
            xhrFields: {
                responseType: 'blob' // Esencial para recibir archivos binarios
            },
            success: function(response, status, xhr) {
                console.log("--> Respuesta recibida con éxito del servidor.");

                // Crear un objeto Blob con el contenido de la respuesta (Excel)
                const blob = new Blob([response], { type: 'application/vnd.ms-excel;charset=utf-8' });
                
                // Generar un enlace temporal en memoria para forzar la descarga
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                
                // Generar nombre de archivo dinámico
                const fechaHoy = new Date().toISOString().slice(0, 10);
                a.href = downloadUrl;
                a.download = 'Reporte_Liquidadas_' + fechaHoy + '.xls';
                
                // Inyectar enlace temporal, hacer clic automático y removerlo
                document.body.appendChild(a);
                a.click();
                
                // Limpiar la memoria
                window.URL.revokeObjectURL(downloadUrl);
                $(a).remove();

                // Restaurar botón
                $btn.prop('disabled', false).html(htmlOriginal);
            },
            error: function(xhr, status, error) {
                console.error("Error en la descarga AJAX:", error);
                alert("Ocurrió un error al intentar generar el reporte Excel.");
                $btn.prop('disabled', false).html(htmlOriginal);
            }
        });
    });
});