$(document).ready(function(){

    console.log("✅ JS cargado y listo"); // Confirmar que entró al ready

    // Detecta cambio de semana
    $("#semana").change(function(){
        var semana = $(this).val();
        console.log("📌 Semana seleccionada:", semana); // Ver qué valor tomó
        if(semana){ 
            LoadAlertas(semana);
        } else {
            $("#LoadAlertas").html(''); // Limpiar tabla si no hay semana
        }
    });

});

// Función AJAX que envía la semana seleccionada
function LoadAlertas(semana){
    console.log("➡️ Entrando a LoadAlertas con semana:", semana); // Confirmar que llega el valor

    $.ajax({
        type:'POST',
        url:'modal/TablaNuevaAlertas.php',
        data: { semana: semana }, // enviamos solo la semana
        beforeSend: function(){
            console.log("⏳ Enviando petición AJAX con semana:", semana);
            $("#LoadAlertas").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            console.log("✅ Respuesta recibida del servidor");
            $("#LoadAlertas").html(data);
        },
        error: function(err){
            console.log("❌ Error en AJAX:", err);
        }
    });
}



/*$(document).ready(function(){

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
        url:'modal/TablaNuevaAlertas.php',
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
} */