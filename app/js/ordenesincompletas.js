function LoadOrdenesIncompletas(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOrdenesIncompletas").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'../../handler/Operaciones/TablaIncompletas.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadOrdenesIncompletas").html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></div>');
        },
        success:function(data){
            $("#LoadOrdenesIncompletas").html(data);
        },
        error: function () {
            $('#LoadOrdenesIncompletas').html('<div class="alert alert-danger">No fue posible cargar la información.</div>');
        }
    });
}