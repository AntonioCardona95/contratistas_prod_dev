console.log('estamos en detalle js alertas');
$(document).ready(function () {

    var urlParams = new URLSearchParams(window.location.search);

    var fecha_inicio = urlParams.get('fecha_inicio');
    console.log(fecha_inicio);
    var fecha_fin    = urlParams.get('fecha_fin');
    console.log(fecha_fin);
    var categoria    = urlParamsget('categoria');
    var cantidad     = urlParams.get('cantidad');

    // Asegurar valores vacíos en lugar de null (opcional pero recomendable)
    fecha_inicio = fecha_inicio || "";
    fecha_fin    = fecha_fin || "";
    cantidad     = cantidad || "";

    // SOLO validas categoría
    if (categoria) {
        LoadDetalleAlertas(fecha_inicio, fecha_fin, categoria, cantidad);
    }

});

function LoadDetalleAlertas(fecha_inicio, fecha_fin, categoria, cantidad) {

    $.ajax({
        type: 'POST',
        url: 'modal/TablaAlertasDetalle.php',

        data: {
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin,
            categoria: categoria,
            cantidad: cantidad
        },

        beforeSend: function () {
            $("#LoadDetalleAlertas").html(
                "<center><img src='../image/loading-46.gif'></center>"
            );
        },

        success: function (data) {
            $("#LoadDetalleAlertas").html(data);
        },

        error: function (err) {
            console.log(err);
        }
    });

}