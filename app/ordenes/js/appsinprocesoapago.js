$(document).ready(function(){

    // Detecta cambio de semana
    $("#semana").change(function(){
        var semana = $(this).val();
        if(semana){ 
            LoadSinProcesoaPago(semana);
        } else {
            $("#LoadSinProcesoaPago").html(''); // Limpiar tabla si no hay semana
        }
    });

});

// Función AJAX que envía la semana seleccionada
function LoadSinProcesoaPago(semana){
    $.ajax({
        type:'POST',
        url:'modal/TablaOrdenesSinProcesoaPago.php',
        data: { semana: semana }, // enviamos solo la semana
        beforeSend: function(){
            $("#LoadSinProcesoaPago").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadSinProcesoaPago").html(data);
        },
        error: function(err){
            console.log(err);
        }
    });
}

/*function LoadSinProcesoaPago(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadSinProcesoaPago").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaOrdenesSinProcesoaPago.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadSinProcesoaPago").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadSinProcesoaPago").html(data);
        }
    });
} */