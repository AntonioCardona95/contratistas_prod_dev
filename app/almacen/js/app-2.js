function LoadSeries() {
    var materialSeleccionado = $("#FK_Material_Salidas_Contratistas").val();
    var contratista = $("#FK_Contratista_Salida").val();
    
    console.log("Material seleccionado:", materialSeleccionado);
    console.log("Contratista:", contratista);
    
    // Select2 se inicializa una sola vez; solo se reemplazan sus opciones.
    $("#Num_Serie_T").empty();
    if (!materialSeleccionado || !contratista) {
        console.log("Faltan datos requeridos");
        $("#Num_Serie_T").html('<option value="">Selecciona primero un material</option>');
        $('#Num_Serie_T').val(null).trigger('change');
        return;
    }
    
    var parametros = {
        FK_Material_Salidas_Contratistas: materialSeleccionado,
        FK_Contratista_Salida: contratista
    };
    
    console.log("Enviando parámetros:", parametros);
    
    $.ajax({
        type: 'POST',
        url: 'modal/LoadSeries.php',
        data: parametros,
        beforeSend: function () {
            $("#Num_Serie_T").html('<option value="">Cargando series...</option>');
        },
        success: function (data) {
            console.log("Respuesta del servidor:", data);
            
            // Limpiar completamente y agregar las nuevas opciones
            $("#Num_Serie_T").html('');
            
            if (data.trim() === '' || data.includes('No hay series disponibles')) {
                $("#Num_Serie_T").html('<option value="">No hay series disponibles</option>');
            } else {
                $("#Num_Serie_T").html(data);
            }
            
            $('#Num_Serie_T').val(null).trigger('change');
        },
        error: function (xhr, status, error) {
            console.error("Error en LoadSeries:", error);
            console.error("Status:", status);
            console.error("XHR:", xhr.responseText);
            $("#Num_Serie_T").html('<option value="">Error al cargar series</option>');
            $('#Num_Serie_T').val(null).trigger('change');
            alert("Hubo un error al cargar las series. Inténtalo de nuevo.");
        }
    });
}
