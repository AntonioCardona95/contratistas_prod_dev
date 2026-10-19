function LoadSalidas() {
    var parametros = $('#FormQueryUnidades').serialize();
    $('#LoadSalidas').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: 'modal/Salidas_Tabla.php',
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

function LoadDetalles(folio) {
    $('#modalSalidaDetalleContenido').html(
        '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>'
    );
    $('#modalSalidaDetalle').modal('show');

    $.ajax({
        url: 'modal/Salidas_Detalles.php',
        type: 'POST',
        data: { folio: folio },
        success: function (contenido) {
            $('#modalSalidaDetalleContenido').html(contenido);
            $('#modalSalidaDetalleTitulo').text('Detalle de salida: Folio ' + folio);
        },
        error: function () {
            $('#modalSalidaDetalleContenido').html(
                '<div class="alert alert-danger mb-0">No fue posible cargar el detalle.</div>'
            );
        }
    });
}

$(document).on('click', '.btn-detalle-salida', function () {
    LoadDetalles($(this).data('folio'));
});
