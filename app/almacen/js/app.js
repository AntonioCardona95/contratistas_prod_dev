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

    // 3. Tipo de Tarea
    var TipoTarea = $('#TipoTarea').val();
    if (TipoTarea) {
        parametros += '&TipoTarea=' + encodeURIComponent(TipoTarea);
    }

    // 4. Área (Soporta data-nombre o texto visible de la opción)
    var selectArea = $('#selectArea').val();
    var nombreArea = $('#selectArea option:selected').data('nombre') || $('#selectArea option:selected').text().trim();
    if (nombreArea === '-- Seleccionar Área --' || nombreArea === '-- Seleccione un área --') {
        nombreArea = '';
    }

    if (selectArea) {
        parametros += '&selectArea=' + encodeURIComponent(selectArea);
    }
    if (nombreArea) {
        parametros += '&nombreArea=' + encodeURIComponent(nombreArea);
    }

    // 5. División (Agregado envío de id y nombre)
    var selectDivision = $('#selectDivision').val();
    var nombreDivision = $('#selectDivision option:selected').data('nombre') || $('#selectDivision option:selected').text().trim();
    if (nombreDivision === '-- Seleccionar División --' || nombreDivision === '-- Seleccione una división --') {
        nombreDivision = '';
    }

    if (selectDivision) {
        parametros += '&selectDivision=' + encodeURIComponent(selectDivision);
    }
    if (nombreDivision) {
        parametros += '&nombreDivision=' + encodeURIComponent(nombreDivision);
    }

    // 6. Cope (Agregado envío de id y nombre)
    var selectCope = $('#selectCope').val();
    var nombreCope = $('#selectCope option:selected').data('nombre') || $('#selectCope option:selected').text().trim();
    if (nombreCope === '-- Seleccionar Cope --' || nombreCope === '-- Seleccione un cope --') {
        nombreCope = '';
    }

    if (selectCope) {
        parametros += '&selectCope=' + encodeURIComponent(selectCope);
    }
    if (nombreCope) {
        parametros += '&nombreCope=' + encodeURIComponent(nombreCope);
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
}

/**
 * Carga los KPIs filtrados por fechas, contratista, tipo de tarea y ubicación.
 */
function LoadKpisControl(ejecutar) {
    console.log("Entró a LoadKpisControl");

    var parametros = $("#FormQueryUnidades").serialize();

    // Contratista
    var selectContratista = $('#selectContratista').val();
    if (selectContratista) {
        parametros += '&selectContratista=' + encodeURIComponent(selectContratista);
    }

    // Tipo de Tarea
    var TipoTarea = $('#TipoTarea').val();
    if (TipoTarea) {
        parametros += '&TipoTarea=' + encodeURIComponent(TipoTarea);
    }

    // Área
    var selectArea = $('#selectArea').val();
    var nombreArea = $('#selectArea option:selected').data('nombre') || $('#selectArea option:selected').text();
    if (nombreArea) {
        nombreArea = $.trim(nombreArea);
    }
    if (nombreArea === '-- Seleccionar Área --' || nombreArea === '-- Seleccione un área --') {
        nombreArea = '';
    }

    if (selectArea) {
        parametros += '&selectArea=' + encodeURIComponent(selectArea);
    }
    if (nombreArea) {
        parametros += '&nombreArea=' + encodeURIComponent(nombreArea);
    }

    // División
    var selectDivision = $('#selectDivision').val();
    var nombreDivision = $('#selectDivision option:selected').data('nombre') || $('#selectDivision option:selected').text();
    if (nombreDivision) {
        nombreDivision = $.trim(nombreDivision);
    }
    if (nombreDivision === '-- Seleccionar División --' || nombreDivision === '-- Seleccione una división --') {
        nombreDivision = '';
    }

    if (selectDivision) {
        parametros += '&selectDivision=' + encodeURIComponent(selectDivision);
    }
    if (nombreDivision) {
        parametros += '&nombreDivision=' + encodeURIComponent(nombreDivision);
    }

    // Cope
    var selectCope = $('#selectCope').val();
    var nombreCope = $('#selectCope option:selected').data('nombre') || $('#selectCope option:selected').text();
    if (nombreCope) {
        nombreCope = $.trim(nombreCope);
    }
    if (nombreCope === '-- Seleccionar Cope --' || nombreCope === '-- Seleccione un cope --') {
        nombreCope = '';
    }

    if (selectCope) {
        parametros += '&selectCope=' + encodeURIComponent(selectCope);
    }
    if (nombreCope) {
        parametros += '&nombreCope=' + encodeURIComponent(nombreCope);
    }

    // Fechas
    var fechascard1 = $('#fechascard1').val();
    var fechascard2 = $('#fechascard2').val();

    if (fechascard1) {
        parametros += '&fechascard1=' + encodeURIComponent(fechascard1);
    }
    if (fechascard2) {
        parametros += '&fechascard2=' + encodeURIComponent(fechascard2);
    }

    // Bandera ejecutar
    if (ejecutar) {
        parametros += '&ejecutar=1';
    }

    $("#LoadKpisControl").fadeIn('slow');

    return $.ajax({
        type: 'POST',
        url: 'modal/KpisControlProduccion.php',
        data: parametros,
        beforeSend: function() {
            $("#LoadKpisControl").html("<center><img src='img/caleidoscopio.gif' alt='Cargando...' style='max-width: 800px; height: auto;'></center>");
        },
        success: function(data) {
            $("#LoadKpisControl").html(data);
        }
    });
}

/**
 * Carga División y COPES dependientes del Área y después refresca los KPIs.
 */
function CargarDependenciasArea() {
    var selectArea = $('#selectArea').val();

    // Limpiar si no hay Área
    if (!selectArea) {
        $('#selectDivision').html('<option value=""></option>').prop('disabled', true).val('').trigger('change.select2');
        $('#selectCope').html('<option value=""></option>').prop('disabled', true).val('').trigger('change.select2');
        LoadKpisControl();
        return;
    }

    // Petición de División y Copes en paralelo
    var reqDivision = $.ajax({
        type: 'POST',
        url: 'modal/selectores.php',
        data: { action: 'getDivisionByArea', idArea: selectArea },
        dataType: 'json'
    });

    var reqCopes = $.ajax({
        type: 'POST',
        url: 'modal/selectores.php',
        data: { action: 'getCopesByArea', idArea: selectArea },
        dataType: 'json'
    });

    // Esperar a que ambas peticiones terminen antes de llamar a LoadKpisControl
    $.when(reqDivision, reqCopes).done(function(resDiv, resCopes) {
        var dataDiv = resDiv[0];
        var dataCopes = resCopes[0];

        // Llenar División
        if (dataDiv && dataDiv.idDivision) {
            var optionDiv = new Option(dataDiv.Division, dataDiv.idDivision, true, true);
            $(optionDiv).attr('data-nombre', dataDiv.Division);
            $('#selectDivision').html(optionDiv).prop('disabled', false).trigger('change.select2');
        } else {
            $('#selectDivision').html('<option value="">No hay división</option>').prop('disabled', true).trigger('change.select2');
        }

        // Llenar Copes
        if (dataCopes && dataCopes.length > 0) {
            var options = '<option value="">-- Seleccione un cope --</option>';
            $.each(dataCopes, function(index, item) {
                options += '<option value="'+item.id+'" data-nombre="'+item.COPE+'">'+item.COPE+'</option>';
            });
            $('#selectCope').html(options).prop('disabled', false).trigger('change.select2');
        } else {
            $('#selectCope').html('<option value="">No hay copes</option>').prop('disabled', true).trigger('change.select2');
        }

        // Cargar los KPIs cuando todo esté listo
        LoadKpisControl();
    });
}

/**
 * Resetea los selectores de Área, División y Cope a su estado inicial.
 */
function LimpiarSelectoresUbicacion() {
    $('#selectArea').val('').trigger('change.select2');
    $('#selectDivision').html('<option value=""></option>').prop('disabled', true).trigger('change.select2');
    $('#selectCope').html('<option value=""></option>').prop('disabled', true).trigger('change.select2');
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