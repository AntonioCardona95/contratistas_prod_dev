console.log("✅ Estamos en detalle.js");
$(document).ready(function () {

    var urlParams = new URLSearchParams(window.location.search);

    var fecha_inicio = urlParams.get('fecha_inicio');
    var fecha_fin    = urlParams.get('fecha_fin');
    // Mostrar en consola los valores recibidos
    console.log("📅 Fecha inicio:", fecha_inicio);
    console.log("📅 Fecha fin:", fecha_fin);
    var categoria    = urlParams.get('categoria');
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