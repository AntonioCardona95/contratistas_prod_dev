console.log('entra a rechazadas');
function LoadOrdenesRechazadas(semana, anio){

    console.log("Semana recibida:", semana);
    console.log("Año recibido:", anio);

    $("#LoadOrdenesRechazadas").fadeIn('slow');

    $.ajax({
        type:'POST',
        url:'modal/TablaOrdenesSinPago.php',
        data:{
            semana: semana,
            anio: anio
        },
        beforeSend: function(){
            $("#LoadOrdenesRechazadas").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            console.log("Respuesta AJAX recibida");
            $("#LoadOrdenesRechazadas").html(data);
        }
    });
}


/*function LoadOrdenesRechazadas(semana, anio){

    $("#LoadOrdenesRechazadas").fadeIn('slow');

    $.ajax({
        type:'POST',
        url:'modal/TablaOrdenesSinPago.php',
        data:{
            semana: semana,
            anio: anio
        },
        beforeSend: function(){
            $("#LoadOrdenesRechazadas").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOrdenesRechazadas").html(data);
        }
    });
} */
