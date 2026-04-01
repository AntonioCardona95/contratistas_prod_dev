
function LoadNoCoordiapp(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadNoCoordiapp").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaOrdenesNoCargadasCoordiapp.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadNoCoordiapp").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadNoCoordiapp").html(data);
        }
    });
}