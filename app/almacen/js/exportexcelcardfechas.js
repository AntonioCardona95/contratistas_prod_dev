$(document).ready(function() {
    console.log("--> JS exportexcelcardfechas.js cargado correctamente.");

    $(document).on('click', '#btnExportarLiquidadas', function(e) {
        e.preventDefault();

        console.log("=== INICIANDO EXPORTACIÓN A EXCEL ===");

        // Extraer los valores de los filtros actuales en pantalla
        const fechaInicio   = $('#fecha_inicial').val() || $('#Fecha1').val() || '';
        const fechaFin      = $('#fecha_final').val() || $('#Fecha2').val() || '';
        const idContratista = $('#select_contratista').val() || $('#idContratistas').val() || '';
        const tipoTarea     = $('#select_tipo_tarea').val() || $('#TipoTarea').val() || '';
        const area          = $('#select_area').val() || '';
        const division      = $('#select_division').val() || '';
        const cope          = $('#select_cope').val() || '';

        // Imprimir cada variable en consola para verificar qué está capturando
        console.log("Fecha Inicio:", fechaInicio);
        console.log("Fecha Fin:", fechaFin);
        console.log("ID Contratista:", idContratista);
        console.log("Tipo Tarea:", tipoTarea);
        console.log("Área:", area);
        console.log("División:", division);
        console.log("Cope:", cope);

        // Construir los parámetros URL
        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            contratista: idContratista,
            tipo_tarea: tipoTarea,
            area: area,
            division: division,
            cope: cope
        });

        const urlFinal = 'modal/ExportarPorFecha.php?' + params.toString();
        console.log("URL de descarga generada:", urlFinal);

        // Abrir la descarga en una ventana o pestaña nueva (para no bloquear la página actual)
        window.location.href = urlFinal;
    });
});