
function LoadProduccion(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadProduccion").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaProduccionContratistas.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadProduccion").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadProduccion").html(data);
        }
    });
}