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
    var parametros = $("#FormQueryUnidades").serialize() || '';

    // 2. Contratista
    var selectContratista = $('#selectContratista').val();
    if (selectContratista) {
        parametros += (parametros ? '&' : '') + 'selectContratista=' + encodeURIComponent(selectContratista);
    }

    // 3. Tipo de Tarea
    var TipoTarea = $('#TipoTarea').val();
    if (TipoTarea) {
        parametros += (parametros ? '&' : '') + 'TipoTarea=' + encodeURIComponent(TipoTarea);
    }

    // 4. Área
    var selectArea = $('#selectArea').val();
    if (selectArea) {
        parametros += (parametros ? '&' : '') + 'selectArea=' + encodeURIComponent(selectArea);
    }

    // 5. División
    var selectDivision = $('#selectDivision').val();
    if (selectDivision) {
        parametros += (parametros ? '&' : '') + 'selectDivision=' + encodeURIComponent(selectDivision);
    }

    // 6. Cope
    var selectCope = $('#selectCope').val();
    if (selectCope) {
        parametros += (parametros ? '&' : '') + 'selectCope=' + encodeURIComponent(selectCope);
    }

    // 7. Fechas
    var fechascard1 = $('#fechascard1').val();
    var fechascard2 = $('#fechascard2').val();

    if (fechascard1) {
        parametros += (parametros ? '&' : '') + 'fechascard1=' + encodeURIComponent(fechascard1);
    }
    if (fechascard2) {
        parametros += (parametros ? '&' : '') + 'fechascard2=' + encodeURIComponent(fechascard2);
    }

    // 8. Bandera ejecutar
    if (ejecutar) {
        parametros += (parametros ? '&' : '') + 'ejecutar=1';
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

// 1. Al cambiar ÁREA: Carga División y resetea Cope
function CargarDependenciasArea() {

    console.log('entro a cargad Dependencias Area');
    var selectArea = $('#selectArea').val();
    
    // Si no hay área seleccionada, reseteamos y deshabilitamos
    if (!selectArea) {
        $('#selectDivision').html('<option value=""></option>').prop('disabled', true).val('').trigger('change.select2');
        $('#selectCope').html('<option value=""></option>').prop('disabled', true).val('').trigger('change.select2');
        LoadKpisControl();
        return;
    }

    // Cargar División por Área
    $.ajax({
        type: 'POST',
        url: 'modal/selectores.php',
        data: { action: 'getDivisionByArea', idArea: selectArea },
        dataType: 'json',
        beforeSend: function() {
            $('#selectDivision').prop('disabled', true);
            $('#selectCope').prop('disabled', true);
        },
        success: function(data) {
            var options = '<option value=""></option>';

            if (Array.isArray(data) && data.length > 0) {
                $.each(data, function(index, item) {
                    var idDiv = item.idDivision || item.id;
                    var nombreDiv = item.Division || item.nombre || item.division;
                    options += '<option value="' + idDiv + '">' + nombreDiv + '</option>';
                });
                $('#selectDivision').html(options).prop('disabled', false);
            } else if (data && (data.idDivision || data.id)) {
                var idDiv = data.idDivision || data.id;
                var nombreDiv = data.Division || data.nombre || data.division;
                options += '<option value="' + idDiv + '">' + nombreDiv + '</option>';
                $('#selectDivision').html(options).prop('disabled', false);
            } else {
                $('#selectDivision').html('<option value="">No hay división para esta área</option>').prop('disabled', true);
            }

            // Reseteamos Cope limpia sin disparar eventos extra
            $('#selectCope').html('<option value=""></option>').prop('disabled', true);

            // Refrescamos visualmente los select2
            $('#selectDivision').trigger('change.select2');
            $('#selectCope').trigger('change.select2');

            // Cargamos los KPIs filtrados
            LoadKpisControl();
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener divisiones:", error);
            $('#selectDivision').html('<option value=""></option>').prop('disabled', true).trigger('change.select2');
            $('#selectCope').html('<option value=""></option>').prop('disabled', true).trigger('change.select2');
            LoadKpisControl();
        }
    });
}

// 2. Al cambiar DIVISIÓN: Carga los Copes de la División seleccionada
function CargarCopesPorDivision() {

    console.log('entro a CargarCopesPorDivision');
    var selectDivision = $('#selectDivision').val();

    if (!selectDivision) {
        $('#selectCope').html('<option value=""></option>').prop('disabled', true).val('').trigger('change.select2');
        LoadKpisControl();
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'modal/selectores.php',
        data: { action: 'getCopesByDivision', idDivision: selectDivision },
        dataType: 'json',
        beforeSend: function() {
            $('#selectCope').prop('disabled', true);
        },
        success: function(data) {
            if (data && data.length > 0) {
                var options = '<option value=""></option>';
                $.each(data, function(index, item) {
                    options += '<option value="' + item.id + '">' + (item.COPE || item.cope || item.nombre) + '</option>';
                });
                $('#selectCope').html(options).prop('disabled', false);
            } else {
                $('#selectCope').html('<option value="">No hay copes en esta división</option>').prop('disabled', true);
            }

            $('#selectCope').trigger('change.select2');
            LoadKpisControl();
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener copes:", error);
            $('#selectCope').html('<option value=""></option>').prop('disabled', true).trigger('change.select2');
            LoadKpisControl();
        }
    });
}

// 3. Resetea los selectores de Ubicación
function LimpiarSelectoresUbicacion() {
    console.log('Limpiando selectores de ubicación');
    $('#selectArea').val('').trigger('change.select2');
    $('#selectDivision').html('<option value=""></option>').prop('disabled', true).val('').trigger('change.select2');
    $('#selectCope').html('<option value=""></option>').prop('disabled', true).val('').trigger('change.select2');
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