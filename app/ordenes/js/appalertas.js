$(document).ready(function(){
    // Obtener las fechas de la URL
    var urlParams = new URLSearchParams(window.location.search);
    var fecha_inicio = urlParams.get('fecha_inicio');
    var fecha_fin = urlParams.get('fecha_fin');

    // Si las fechas están disponibles, ejecutamos la función LoadAlertas
    if (fecha_inicio && fecha_fin) {
        LoadAlertas(fecha_inicio, fecha_fin);
    }

});

// Función AJAX que envía las fechas seleccionadas
function LoadAlertas(fecha_inicio, fecha_fin){
    $.ajax({
        type: 'POST',
        url: 'modal/TablaAlertas.php', // El archivo que procesa la solicitud
        data: { 
            fecha_inicio: fecha_inicio, 
            fecha_fin: fecha_fin 
        }, // enviamos las fechas
        beforeSend: function(){
            $("#LoadAlertas").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadAlertas").html(data);
        },
        error: function(err){
            console.log(err);
        }
    });
}