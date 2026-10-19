function LoadEntradas(){
    var parametros = $('#FormQueryUnidades').serialize();
    $('#LoadEntradas').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: 'modal/Entradas_Tabla.php',
        data: parametros,
        beforeSend: function () {
            $('#LoadEntradas').html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>');
        },
        success: function (data) {
            $('#LoadEntradas').html(data);
        },
        error: function () {
            $('#LoadEntradas').html('<div class="alert alert-danger">No fue posible cargar la información.</div>');
        }
    });
}

