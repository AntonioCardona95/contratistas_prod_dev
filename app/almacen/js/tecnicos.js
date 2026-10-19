
function LoadTecnicos(){
    var parametros = $('#FormQueryUnidades').serialize();
    $('#LoadEntradas').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: 'modal/Tecnicos_Tabla.php',
        data: parametros,
        beforeSend: function () {
            $('#LoadTecnicos').html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>');
        },
        success: function (data) {
            $('#LoadTecnicos').html(data);
        },
        error: function () {
            $('#LoadTecnicos').html('<div class="alert alert-danger">No fue posible cargar la información.</div>');
        }
    });
}