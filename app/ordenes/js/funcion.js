console.log('bienvenido');

function cargarDashboard() {
    console.log('hola');

    $.ajax({
        url: 'ajax/get_dashboard.php',
        method: 'GET',
        dataType: 'json',
        data: {
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#fecha_fin').val()
        },
        success: function(res) {
            console.log('respuesta:', res); // 👈 para debug
            $('#total_tac').text(res.total_tac);
        },
        error: function(xhr, status, error) {
            console.error("Error AJAX:", error);
        }
    });
}

// 👇 ESTA PARTE ES LA CLAVE
$(document).ready(function() {
    console.log('DOM listo');
    cargarDashboard();
});