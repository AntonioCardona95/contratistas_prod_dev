$(document).ready(function() {
    $('#btnExportarLiquidadas').on('click', function(e) {
        e.preventDefault();

        // Extraer los valores de los filtros actuales en pantalla
        const fechaInicio   = $('#fecha_inicial').val() || $('#Fecha1').val() || '';
        const fechaFin      = $('#fecha_final').val() || $('#Fecha2').val() || '';
        const idContratista = $('#select_contratista').val() || $('#idContratistas').val() || '';
        const tipoTarea     = $('#select_tipo_tarea').val() || $('#TipoTarea').val() || '';
        const area          = $('#select_area').val() || '';
        const division      = $('#select_division').val() || '';
        const cope          = $('#select_cope').val() || '';

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

        // Detonar la descarga direccionando al archivo en la carpeta modal/
        window.location.href = 'modal/exportar_liquidadas.php?' + params.toString();
    });
});