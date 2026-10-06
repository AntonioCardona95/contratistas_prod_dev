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
    // 1. Convertimos los datos del formulario de fechas a un String/Objeto
    var parametros = $("#FormQueryUnidades").serialize();

    // 2. Le concatenamos el valor del select de contratistas
    var selectContratista = $('#selectContratista').val();
    if (selectContratista) {
        parametros += '&selectContratista=' + encodeURIComponent(selectContratista);
    }

    // 3. Si viene la bandera opcional de 'ejecutar'
    if (ejecutar) {
        parametros += '&ejecutar=1';
    }

    $("#LoadKpisControl").fadeIn('slow');

    // Retornamos la promesa de AJAX para que ejecute la animación correctamente en ejecutarFiltradoProduccion()
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