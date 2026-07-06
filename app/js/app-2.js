function LoadOntDisponiblesDet(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOntDisponiblesDet").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaOntDisponiblesDet.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadOntDisponiblesDet").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOntDisponiblesDet").html(data);
        }
    });
}

function LoadOntAsignadosDet(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOntAsignadosDet").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaOntAsignadosDet.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadOntAsignadosDet").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOntAsignadosDet").html(data);
        }
    });
}

function LoadOntSinInstalarDet(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOntSinInstalarDet").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaOntSinInstalarDet.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadOntSinInstalarDet").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOntSinInstalarDet").html(data);
        }
    });
}

function LoadOntInstaladosDet(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOntInstaladosDet").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaOntInstaladosDet.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadOntInstaladosDet").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOntInstaladosDet").html(data);
        }
    });
}

function LoadSeries() {
    var materialSeleccionado = $("#FK_Material_Salidas_Contratistas").val();
    var contratista = $("#FK_Contratista_Salida").val();
    
    console.log("Material seleccionado:", materialSeleccionado);
    console.log("Contratista:", contratista);
    
    // Destruir y limpiar completamente el Bootstrap Select
    try {
        $('#Num_Serie_T').selectpicker('destroy');
    } catch(e) {
        console.log("SelectPicker no estaba inicializado");
    }
    
    $("#Num_Serie_T").empty();
    $("#Num_Serie_T").removeClass('selectpicker');
    
    if (!materialSeleccionado || !contratista) {
        console.log("Faltan datos requeridos");
        $("#Num_Serie_T").html('<option value="">Selecciona primero un material</option>');
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
            
            // Reinicializar Bootstrap Select
            $("#Num_Serie_T").addClass('selectpicker');
            $('#Num_Serie_T').selectpicker({
                liveSearch: true,
                title: 'Selecciona series'
            });
        },
        error: function (xhr, status, error) {
            console.error("Error en LoadSeries:", error);
            console.error("Status:", status);
            console.error("XHR:", xhr.responseText);
            $("#Num_Serie_T").html('<option value="">Error al cargar series</option>');
            $("#Num_Serie_T").addClass('selectpicker');
            $('#Num_Serie_T').selectpicker({
                liveSearch: true,
                title: 'Selecciona series'
            });
            alert("Hubo un error al cargar las series. Inténtalo de nuevo.");
        }
    });
}
