$(document).ready(function () {

    var urlParams = new URLSearchParams(window.location.search);

    var fecha_inicio = urlParams.get('fecha_inicio');
    var fecha_fin    = urlParams.get('fecha_fin');
    var categoria    = urlParams.get('categoria');
    var cantidad     = urlParams.get('cantidad');

    // Asegurar valores vacíos en lugar de null (opcional pero recomendable)
    fecha_inicio = fecha_inicio || "";
    fecha_fin    = fecha_fin || "";
    cantidad     = cantidad || "";

    // SOLO validas categoría
    if (categoria) {
        LoadDetalleAlertasFotos(fecha_inicio, fecha_fin, categoria, cantidad);
    }

});

function LoadDetalleAlertasFotos(fecha_inicio, fecha_fin, categoria, cantidad) {

    $.ajax({
        type: 'POST',
        url: 'modal/TablaAlertasDetalleFotos.php',

        data: {
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            categoria: categoria,
            cantidad: cantidad
        },

        beforeSend: function () {
            $("#LoadDetalleAlertasFotos").html(
                "<center><img src='../image/loading-46.gif'></center>"
            );
        },

        success: function (data) {
            $("#LoadDetalleAlertasFotos").html(data);
        },

        error: function (err) {
            console.log(err);
        }
    });

}