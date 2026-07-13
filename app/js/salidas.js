function LoadSalidas() {
    var parametros = $('#FormQueryUnidades').serialize();

    $('#LoadSalidas').fadeIn('slow');

    $.ajax({
        type: 'POST',
        url:'../../handler/Almacen/TablaSalidas.php',
        data: parametros,
        beforeSend: function () {
            $('#LoadSalidas').html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></div>');
        },
        success: function (data) {
            $('#LoadSalidas').html(data);
        },
        error: function () {
            $('#LoadSalidas').html('<div class="alert alert-danger">No fue posible cargar la información.</div>');
        }
    });
}