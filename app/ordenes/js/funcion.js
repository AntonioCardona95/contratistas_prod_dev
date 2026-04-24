function cargarDashboard() {
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
}