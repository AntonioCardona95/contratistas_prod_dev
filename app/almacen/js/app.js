function LoadEntradas(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadEntradas").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/Entradas_Tabla.php',
        data: parametros,
        success:function(data){
            $("#LoadEntradas").html(data);
        }
    });
}

function LoadAreas(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadAreas").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/LoadAreas.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadAreas").html("");
        },
        success:function(data){
            $("#LoadAreas").html(data);
        }
    });
}

function LoadCopes(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadCopes").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/LoadCopes.php',
        data: parametros,
         beforeSend: function(objeto){
            $("#LoadCopes").html("");
        },
        success:function(data){
            $("#LoadCopes").html(data);
        }
    });
}

function LoadSalidas(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadSalidas").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/Salidas_Tabla.php',
        data: parametros,
        success:function(data){
            $("#LoadSalidas").html(data);
        }
    });
}

function LoadKPIS(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadKPIS").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/KPIS.php',
        data: parametros,
         beforeSend: function(objeto){
                $("#LoadKPIS").html("<center><img src='img/caleidoscopio.gif' alt='Cargando...' style='max-width: 800px; height: auto;'></center>");
        },
        success:function(data){
            $("#LoadKPIS").html(data);
        }
    });
}

function LoadKpisControl(ejecutar) {
    console.log("Entró a LoadKpisControl");

    // 1. Convertimos los datos del formulario de fechas a un String/Objeto
    var parametros = $("#FormQueryUnidades").serialize();

    // 2. Contratista
    var selectContratista = $('#selectContratista').val();
    if (selectContratista) {
        parametros += '&selectContratista=' + encodeURIComponent(selectContratista);
    }

    // 3. Área
    var selectArea = $('#selectArea').val();
    if (selectArea) {
        parametros += '&selectArea=' + encodeURIComponent(selectArea);

        // --- Cargar división y copes en base al área seleccionada ---
        $.ajax({
            type: 'POST',
            url: 'modal/selectores.php',
            data: { action: 'getDivisionByArea', idArea: selectArea },
            dataType: 'json',
            success: function(data) {
                console.log("Respuesta divisiones:", data);
                if (data) {
                    $('#selectDivision').html('<option value="'+data.idDivision+'">'+data.Division+'</option>');
                    $('#selectDivision').prop('disabled', false);
                } else {
                    $('#selectDivision').html('<option value="">No hay división para esta área</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error AJAX divisiones:", status, error);
            }
        });

        $.ajax({
            type: 'POST',
            url: 'modal/selectores.php',
            data: { action: 'getCopesByArea', idArea: selectArea },
            dataType: 'json',
            success: function(data) {
                console.log("Respuesta copes:", data);
                if (data.length > 0) {
                    $('#selectCope').html('<option value="">-- Seleccione un cope --</option>');
                    $.each(data, function(index, item) {
                        $('#selectCope').append('<option value="'+item.id+'">'+item.COPE+'</option>');
                    });
                    $('#selectCope').prop('disabled', false);
                } else {
                    $('#selectCope').html('<option value="">No hay copes en esta área</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error AJAX copes:", status, error);
            }
        });
    }

    // 4. División
    var selectDivision = $('#selectDivision').val();
    if (selectDivision) {
        parametros += '&selectDivision=' + encodeURIComponent(selectDivision);
    }

    // 5. Cope
    var selectCope = $('#selectCope').val();
    if (selectCope) {
        parametros += '&selectCope=' + encodeURIComponent(selectCope);
    }

    // 6. Fechas
    var fechascard1 = $('#fechascard1').val();
    var fechascard2 = $('#fechascard2').val();

    if (fechascard1) {
        parametros += '&fechascard1=' + encodeURIComponent(fechascard1);
    }
    if (fechascard2) {
        parametros += '&fechascard2=' + encodeURIComponent(fechascard2);
    }

    // 7. Bandera ejecutar
    if (ejecutar) {
        parametros += '&ejecutar=1';
    }

    $("#LoadKpisControl").fadeIn('slow');

    // 8. Llamada principal para refrescar KPIs
    return $.ajax({
        type: 'POST',
        url: 'modal/KpisControlProduccion.php',
        data: parametros,
        beforeSend: function(objeto) {
            $("#LoadKpisControl").html("<center><img src='img/caleidoscopio.gif' alt='Cargando...' style='max-width: 800px; height: auto;'></center>");
        },
        success: function(data) {
            $("#LoadKpisControl").html(data);
        }
    });
}




/*function LoadKpisControl(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadKpisControl").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/KpisControlProduccion.php',
        data: parametros,
         beforeSend: function(objeto){
                $("#LoadKpisControl").html("<center><img src='img/caleidoscopio.gif' alt='Cargando...' style='max-width: 800px; height: auto;'></center>");
        },
        success:function(data){
            $("#LoadKpisControl").html(data);
        }
    });
}  */



function LoadProduccion(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadProduccion").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/KpisProduccion.php',
        data: parametros,
         beforeSend: function(objeto){
                $("#LoadProduccion").html("<center><img src='img/caleidoscopio.gif' alt='Cargando...' style='max-width: 800px; height: auto;'></center>");
        },
        success:function(data){
            $("#LoadProduccion").html(data);
        }
    });
}

function LoadReporte() {
    // Busca los valores en los inputs de la Topbar
    var f1 = $("#Fecha1").val() || $("input[name='Fecha1']").val() || "";
    var f2 = $("#Fecha2").val() || $("input[name='Fecha2']").val() || "";
    var tipoTarea = $("#TipoTarea").val() || "";

    // Si al cargar vienen vacíos, asignar el mes actual por defecto
    if (!f1) {
        var d = new Date();
        d.setMonth(d.getMonth() - 1);
        f1 = d.toISOString().split('T')[0];
    }
    if (!f2) {
        f2 = new Date().toISOString().split('T')[0];
    }

    var parametros = {
        Fecha1: f1,
        Fecha2: f2,
        TipoTarea: tipoTarea
    };

    $("#LoadReporte").fadeIn('slow');

    $.ajax({
        type: 'POST',
        url: 'modal/TablaReporte.php',
        data: parametros,
        beforeSend: function() {
            $("#LoadReporte").html("<center><img src='img/caleidoscopio.gif' alt='Cargando...' style='max-width: 800px; height: auto;'></center>");
        },
        success: function(data) {
            $("#LoadReporte").html(data);
        },
        error: function(xhr, status, error) {
            // Si quieres quitar también este log, puedes dejar vacío el bloque
            console.error("Error al cargar la tabla:", error);
        }
    });
}

$(document).ready(function() {
    LoadReporte();
});




function LoadPendientes(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadPendientes").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaPendientes.php',
        data: parametros,
        success:function(data){
            $("#LoadPendientes").html(data);
        }
    });
}

function LoadRechazados(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadRechazados").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaRechazados.php',
        data: parametros,
        success:function(data){
            $("#LoadRechazados").html(data);
        }
    });
}