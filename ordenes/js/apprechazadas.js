function LoadOrdenesRechazadas(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOrdenesRechazadas").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaOrdenesSinPago.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadOrdenesRechazadas").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOrdenesRechazadas").html(data);
        }
    });
}