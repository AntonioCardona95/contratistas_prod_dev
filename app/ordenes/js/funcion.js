function procesarAlertas() {
    console.log('Procesando alertas...');

    $('#btnLoadKPIS').prop('disabled', true);
    $('#estadoProceso').text('⏳ Procesando alertas, espera...');

    $.ajax({
        url: 'ajax/procesar_alertas.php',
        method: 'GET',
        data: {
            fecha_inicio: $('#Fecha1').val(),
            fecha_fin: $('#Fecha2').val()
        },
        success: function(res) {
            console.log('Proceso terminado:', res);

            $('#estadoProceso').text('✅ Alertas procesadas correctamente');
            $('#btnLoadKPIS').prop('disabled', false);

            // Ahora refrescamos las cards
            actualizarCards();
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            $('#estadoProceso').text('❌ Error al procesar alertas');
            $('#btnLoadKPIS').prop('disabled', false);
        }
    });
}

function actualizarCards() {
    console.log('cargando dashboard...');

    // 🔎 Depuración: imprime las fechas que se envían
    var fechaInicio = $('#Fecha1').val();
    var fechaFin = $('#Fecha2').val();
    console.log('📅 Fecha inicio seleccionada:', fechaInicio);
    console.log('📅 Fecha fin seleccionada:', fechaFin);

    $.ajax({
        url: 'ajax/get_dashboard.php',
        method: 'GET',
        dataType: 'json',
        data: {
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        },
        success: function(res) {
            console.log('✅ RESPUESTA AJAX:', res);

            $('#total_tac').text(res.total_tac);
            $('#alertas').text(res.alertas);
        },
        error: function(xhr, status, error) {
            console.error("❌ ERROR AJAX:", error);
            console.log("response:", xhr.responseText);
        }
    });
}




/*function cargarDashboard() {
    console.log('cargando dashboard...');

    $.ajax({
        url: 'ajax/get_dashboard.php',
        method: 'GET',
        dataType: 'json',
        data: {
            fecha_inicio: $('#Fecha1').val(),
            fecha_fin: $('#Fecha2').val()
        },
        success: function(res) {
            console.log('✅ RESPUESTA AJAX:', res);

            $('#total_tac').text(res.total_tac);
            $('#alertas').text(res.alertas);
        },
        error: function(xhr, status, error) {
            console.error("❌ ERROR AJAX:", error);
            console.log("response:", xhr.responseText);
        }
    });
} */