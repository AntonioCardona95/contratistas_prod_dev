function LoadOrdenes(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOrdenes").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaProduccion.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadOrdenes").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOrdenes").html(data);
        }
    });
}