$(document).ready(function () {

    // Obtener parámetros de la URL
    var urlParams = new URLSearchParams(window.location.search);

    var fecha_inicio = urlParams.get('fecha_inicio');
    var fecha_fin    = urlParams.get('fecha_fin');

    //  Nuevos parámetros
    var categoria = urlParams.get('categoria');
    var cantidad  = urlParams.get('cantidad');

    //console.log("Fecha inicio:", fecha_inicio);
    //console.log("Fecha fin:", fecha_fin);
    //console.log("Categoría:", categoria);
    //console.log("Cantidad:", cantidad);

    // Ejecutar AJAX si existen los parámetros necesarios
    if (fecha_inicio && fecha_fin && categoria) {
        LoadDetalleAlertas(fecha_inicio, fecha_fin, categoria, cantidad);
    }

});


// Función AJAX
function LoadDetalleAlertas(fecha_inicio, fecha_fin, categoria, cantidad) {

    $.ajax({
        type: 'POST',
        url: 'modal/TablaAlertas.php',

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