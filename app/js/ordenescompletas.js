function LoadOrdenes(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOrdenes").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'../../handler/Operaciones/TablaOperaciones.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadOrdenes").html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></div>');
        },
        success:function(data){
            $("#LoadOrdenes").html(data);
        },
        error: function () {
            $('#LoadOrdenes').html('<div class="alert alert-danger">No fue posible cargar la información.</div>');
        }
    });
}