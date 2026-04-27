$(document).ready(function(){

    // Obtener las fechas de la URL
    var urlParams = new URLSearchParams(window.location.search);
    var fecha_inicio = urlParams.get('fecha_inicio');
    var fecha_fin = urlParams.get('fecha_fin');

    // 🔥 SIEMPRE ejecutar (con o sin fechas)
    LoadAlertasGenerales(fecha_inicio, fecha_fin);

});

// Función AJAX que envía las fechas seleccionadas
function LoadAlertasGenerales(fecha_inicio, fecha_fin){
    $.ajax({
        type: 'POST',
        url: 'modal/TablaAlertasGenerales.php',
        data: { 
            fecha_inicio: fecha_inicio, 
            fecha_fin: fecha_fin 
        },
        beforeSend: function(){
            $("#LoadAlertasGenerales").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadAlertasGenerales").html(data);
        },
        error: function(err){
            console.log(err);
        }
    });
}