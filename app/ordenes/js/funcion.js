console.log('bienvenido');

function cargarDashboard() {
    console.log('cargando dashboard...');

    $.ajax({
        url: 'ajax/get_dashboard.php',
        method: 'GET',
        dataType: 'json',
        data: {
            fecha_inicio: $('#Fecha1').val(), // ✅ CORREGIDO
            fecha_fin: $('#Fecha2').val()     // ✅ CORREGIDO
        },
        success: function(res) {
            console.log('respuesta:', res);

            $('#total_tac').text(res.total_tac);
            $('#alertas').text(res.alertas); // ✅ NUEVO
        },
        error: function(xhr, status, error) {
            console.error("Error AJAX:", error);
        }
    });
}

$(document).ready(function() {
    console.log('DOM listo');
    cargarDashboard();
});