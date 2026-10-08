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
    // Identificador único para distinguir cada llamada en la consola
    var idLlamada = Math.floor(Math.random() * 1000);
    
    console.group("🚀 [LoadKpisControl #" + idLlamada + "] Inicio de ejecución");
    console.log("--> Parámetro 'ejecutar':", ejecutar);
    console.trace("--> Traza de llamada (¿Quién llamó a esta función?):");

    // 1. Convertimos los datos del formulario de fechas a un String/Objeto
    var parametros = $("#FormQueryUnidades").serialize();

    // 2. Contratista
    var selectContratista = $('#selectContratista').val();
    if (selectContratista) {
        parametros += '&selectContratista=' + encodeURIComponent(selectContratista);
    }

    // 3. Tipo de Tarea
    var TipoTarea = $('#TipoTarea').val();
    if (TipoTarea) {
        parametros += '&TipoTarea=' + encodeURIComponent(TipoTarea);
    }

    // 4. Área (Obtenemos ID y Nombre del Option seleccionado)
    var selectArea = $('#selectArea').val();
    var nombreArea = $('#selectArea option:selected').data('nombre') || $('#selectArea option:selected').text();
    nombreArea = $.trim(nombreArea);

    if (selectArea) {
        parametros += '&selectArea=' + encodeURIComponent(selectArea);
    }
    if (nombreArea && selectArea) {
        parametros += '&nombreArea=' + encodeURIComponent(nombreArea);
    }

    // 5. División (Obtenemos ID y Nombre del Option seleccionado)
    var selectDivision = $('#selectDivision').val();
    var nombreDivision = $('#selectDivision option:selected').data('nombre') || $('#selectDivision option:selected').text();
    nombreDivision = $.trim(nombreDivision);

    if (selectDivision) {
        parametros += '&selectDivision=' + encodeURIComponent(selectDivision);
    }
    if (nombreDivision && selectDivision) {
        parametros += '&nombreDivision=' + encodeURIComponent(nombreDivision);
    }

    // 6. Cope
    var selectCope = $('#selectCope').val();
    if (selectCope) {
        parametros += '&selectCope=' + encodeURIComponent(selectCope);
    }

    // 7. Fechas
    var fechascard1 = $('#fechascard1').val();
    var fechascard2 = $('#fechascard2').val();

    if (fechascard1) {
        parametros += '&fechascard1=' + encodeURIComponent(fechascard1);
    }
    if (fechascard2) {
        parametros += '&fechascard2=' + encodeURIComponent(fechascard2);
    }

    // 8. Bandera ejecutar
    if (ejecutar) {
        parametros += '&ejecutar=1';
    }

    // Imprimir en consola los datos extraídos antes de enviar
    console.log("--> Datos recolectados:", {
        selectArea: selectArea,
        nombreArea: nombreArea,
        selectDivision: selectDivision,
        selectCope: selectCope,
        selectContratista: selectContratista,
        TipoTarea: TipoTarea,
        fechas: [fechascard1, fechascard2]
    });
    console.log("--> Query String enviada:", parametros);

    $("#LoadKpisControl").fadeIn('slow');

    // 9. Llamada principal para refrescar KPIs
    return $.ajax({
        type: 'POST',
        url: 'modal/KpisControlProduccion.php',
        data: parametros,
        beforeSend: function(objeto) {
            console.log("⏳ [LoadKpisControl #" + idLlamada + "] Enviando petición a modal/KpisControlProduccion.php...");
            $("#LoadKpisControl").html("<center><img src='img/caleidoscopio.gif' alt='Cargando...' style='max-width: 800px; height: auto;'></center>");
        },
        success: function(data) {
            console.log("✅ [LoadKpisControl #" + idLlamada + "] Respuesta recibida con éxito de KpisControlProduccion.php");
            $("#LoadKpisControl").html(data);
            console.groupEnd();
        },
        error: function(xhr, status, error) {
            console.error("❌ [LoadKpisControl #" + idLlamada + "] Error en la petición AJAX:", status, error);
            console.groupEnd();
        }
    });
}

/*function LoadKpisControl(ejecutar) {
    console.log("Entró a LoadKpisControl");

    // 1. Convertimos los datos del formulario de fechas a un String/Objeto
    var parametros = $("#FormQueryUnidades").serialize();

    // 2. Contratista
    var selectContratista = $('#selectContratista').val();
    if (selectContratista) {
        parametros += '&selectContratista=' + encodeURIComponent(selectContratista);
    }

    // 3. Tipo de Tarea (NUEVO)
    var TipoTarea = $('#TipoTarea').val();
    if (TipoTarea) {
        parametros += '&TipoTarea=' + encodeURIComponent(TipoTarea);
    }

    // 4. Área (Obtenemos ID y Nombre del Option seleccionado)
    var selectArea = $('#selectArea').val();
    var nombreArea = $('#selectArea option:selected').data('nombre') || $('#selectArea option:selected').text();
    nombreArea = $.trim(nombreArea);

    if (selectArea) {
        parametros += '&selectArea=' + encodeURIComponent(selectArea);
    }
    if (nombreArea && selectArea) {
        parametros += '&nombreArea=' + encodeURIComponent(nombreArea);
    }

    // 5. División
    var selectDivision = $('#selectDivision').val();
    if (selectDivision) {
        parametros += '&selectDivision=' + encodeURIComponent(selectDivision);
    }

    // 6. Cope
    var selectCope = $('#selectCope').val();
    if (selectCope) {
        parametros += '&selectCope=' + encodeURIComponent(selectCope);
    }

    // 7. Fechas
    var fechascard1 = $('#fechascard1').val();
    var fechascard2 = $('#fechascard2').val();

    if (fechascard1) {
        parametros += '&fechascard1=' + encodeURIComponent(fechascard1);
    }
    if (fechascard2) {
        parametros += '&fechascard2=' + encodeURIComponent(fechascard2);
    }

    // 8. Bandera ejecutar
    if (ejecutar) {
        parametros += '&ejecutar=1';
    }

    $("#LoadKpisControl").fadeIn('slow');

    // 9. Llamada principal para refrescar KPIs
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
} */

// FUNCIÓN CORREGIDA
function CargarDependenciasArea() {
    var selectArea = $('#selectArea').val();

    if (!selectArea) {
        $('#selectDivision').html('<option value="">-- Seleccionar División --</option>').prop('disabled', true).trigger('change.select2');
        $('#selectCope').html('<option value="">-- Seleccionar Cope --</option>').prop('disabled', true).trigger('change.select2');
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'modal/selectores.php',
        data: { action: 'getDivisionByArea', idArea: selectArea },
        dataType: 'json',
        success: function(data) {
            if (data && data.length > 0) {
                // 1. Iniciamos el select con la opción neutra vacía
                var options = '<option value="">-- Seleccionar División --</option>';

                // 2. Si data es un array de divisiones asociadas al área
                $.each(data, function(index, item) {
                    options += '<option value="' + item.idDivision + '" data-nombre="' + item.Division + '">' + item.Division + '</option>';
                });

                // 3. Cargamos las opciones SIN la propiedad 'selected'
                $('#selectDivision').html(options).prop('disabled', false).val('').trigger('change.select2');
            } else if (data && data.idDivision) {
                // Si tu PHP te devuelve un solo objeto en lugar de un array
                var options = '<option value="">-- Seleccionar División --</option>';
                options += '<option value="' + data.idDivision + '" data-nombre="' + data.Division + '">' + data.Division + '</option>';
                
                $('#selectDivision').html(options).prop('disabled', false).val('').trigger('change.select2');
            } else {
                $('#selectDivision').html('<option value="">-- Sin divisiones --</option>').prop('disabled', true).trigger('change.select2');
            }
        }
    });


    var reqCopes = $.ajax({
        type: 'POST',
        url: 'modal/selectores.php',
        data: { action: 'getCopesByArea', idArea: selectArea },
        dataType: 'json',
        success: function(data) {
            if (data && data.length > 0) {
                var options = '<option value="">-- Seleccione un cope --</option>';
                $.each(data, function(index, item) {
                    options += '<option value="'+item.id+'" data-nombre="'+item.COPE+'">'+item.COPE+'</option>';
                });
                $('#selectCope').html(options).prop('disabled', false).trigger('change.select2');
            } else {
                $('#selectCope').html('<option value="">No hay copes en esta área</option>').prop('disabled', true).trigger('change.select2');
            }
        }
    });

    // Una vez que ambas dependencias fueron cargadas en el DOM, se ejecuta la actualización de KPIs una sola vez
    $.when(reqDivision, reqCopes).always(function() {
        LoadKpisControl();
    });


    // Cargar Copes por Área
    $.ajax({
        type: 'POST',
        url: 'modal/selectores.php',
        data: { action: 'getCopesByArea', idArea: selectArea },
        dataType: 'json',
        success: function(data) {
            if (data && data.length > 0) {
                var options = '<option value="">-- Seleccione un cope --</option>';
                $.each(data, function(index, item) {
                    options += '<option value="'+item.id+'">'+item.COPE+'</option>';
                });
                $('#selectCope').html(options).prop('disabled', false).trigger('change.select2');
            } else {
                $('#selectCope').html('<option value="">No hay copes en esta área</option>').prop('disabled', true).trigger('change.select2');
            }
        }
    });
}


// Resetea los selectores de Área, División y Cope a su estado inicial
function LimpiarSelectoresUbicacion() {
    // 1. Resetear Área a su valor por defecto (vacío)
    $('#selectArea').val('').trigger('change.select2');

    // 2. Limpiar y deshabilitar División
    $('#selectDivision')
        .html('<option value=""></option>')
        .prop('disabled', true)
        .trigger('change.select2');

    // 3. Limpiar y deshabilitar Cope
    $('#selectCope')
        .html('<option value=""></option>')
        .prop('disabled', true)
        .trigger('change.select2');
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