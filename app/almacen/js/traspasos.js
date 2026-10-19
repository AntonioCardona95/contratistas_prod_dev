function LoadSeriesTraspaso() {
    var parametros = $('#FormQueryUnidades').serialize();
    $('#Num_Serie_T').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: 'modal/LoadSeriesTraspaso.php',
        data: parametros,
        beforeSend: function () {
            $('#Num_Serie_T').selectpicker('refresh');
        },
        success: function (data) {
            $('#Num_Serie_T').html(data);
            $('#Num_Serie_T').selectpicker('refresh');
        },
        error: function (xhr, status, error) {
            console.error('Error en LoadSeries:', error);
            alert('Hubo un error al cargar las series. Inténtalo de nuevo.');
        }
    });
}

function LoadTraspasos() {
    var parametros = $('#FormQueryUnidades').serialize();
    $('#LoadTraspasos').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: 'modal/Traspasos_Tabla.php',
        data: parametros,
        beforeSend: function () {
            $('#LoadTraspasos').html("<center><img src='../image/loading-46.gif'></center>");
        },
        success: function (data) {
            $('#LoadTraspasos').html(data);
        }
    });
}
