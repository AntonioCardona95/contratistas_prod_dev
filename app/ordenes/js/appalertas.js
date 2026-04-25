$(document).ready(function(){

    // Detecta cambio de semana
    $("#semana").change(function(){
        var semana = $(this).val();
        if(semana){ 
            LoadAlertas(semana);
        } else {
            $("#LoadAlertas").html(''); // Limpiar tabla si no hay semana
        }
    });

});

// Función AJAX que envía la semana seleccionada
function LoadAlertas(semana){
    $.ajax({
        type:'POST',
        url:'modal/TablaAlertas.php',
        data: { semana: semana }, // enviamos solo la semana
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