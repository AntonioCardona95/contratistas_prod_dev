
function LoadSeriesTraspaso() {
    var parametros = $("#FormQueryUnidades").serialize(); // Serializa los datos del formulario
    $("#Num_Serie_T").fadeIn('slow'); // Muestra el contenedor de series
    $.ajax({
        type: 'POST',
        url: 'modal/LoadSeriesTraspaso.php', // Asegúrate de que esta ruta sea correcta
        data: parametros,
        beforeSend: function () {
            $('#Num_Serie_T').selectpicker('refresh');
        },
        success: function (data) {
            $("#Num_Serie_T").html(data); // Inserta las opciones recibidas en el select
            $('#Num_Serie_T').selectpicker('refresh');
        },
        error: function (xhr, status, error) {
            console.error("Error en LoadSeries:", error); // Muestra errores en la consola
            alert("Hubo un error al cargar las series. Inténtalo de nuevo.");
        }
    });
}

function LoadTraspasos(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadTraspasos").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/Traspasos_Tabla.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadTraspasos").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadTraspasos").html(data);
        }
    });
}
