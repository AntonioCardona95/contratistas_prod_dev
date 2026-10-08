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
    console.log('================================');
    console.log('🚀 Entró a LoadKpisControl');
    console.trace('QUIÉN LLAMÓ A LoadKpisControl');
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

// NUEVA FUNCIÓN: Solo recarga División y Cope cuando el usuario CAMBIA de Área
function CargarDependenciasArea() {

    console.log('================================');
    console.log('🚀 Entró a LoadKpisControl');
    console.trace('QUIÉN LLAMÓ A LoadKpisControl');

    console.log('========================================');
    console.log('🚀 ENTRÓ A CargarDependenciasArea');
    console.log('🕒 Hora:', new Date().toLocaleTimeString());

    var selectArea = $('#selectArea').val();

    console.log('📍 Área seleccionada ID:', selectArea);
    console.log(
        '📍 Área seleccionada Nombre:',
        $('#selectArea option:selected').text()
    );

    // Limpiar y resetear División y Cope si no hay Área
    if (!selectArea) {

        console.warn('⚠ selectArea viene vacío');

        $('#selectDivision')
            .html('<option value=""></option>')
            .prop('disabled', true)
            .trigger('change.select2');

        $('#selectCope')
            .html('<option value=""></option>')
            .prop('disabled', true)
            .trigger('change.select2');

        console.log('⛔ Se canceló la carga porque no hay Área');
        console.log('========================================');

        return;
    }

    console.log('📡 Iniciando carga de División');

    // Cargar División por Área
    $.ajax({
        type: 'POST',
        url: 'modal/selectores.php',
        data: {
            action: 'getDivisionByArea',
            idArea: selectArea
        },
        dataType: 'json',

        beforeSend: function () {

            console.log(
                '📤 Enviando petición División',
                {
                    action: 'getDivisionByArea',
                    idArea: selectArea
                }
            );

        },

        success: function(data) {

            console.log('✅ Respuesta División recibida');

            console.log(data);

            if (data && data.idDivision) {

                console.log('🏢 División encontrada');
                console.log('ID División:', data.idDivision);
                console.log('Nombre División:', data.Division);

                $('#selectDivision').html(
                    '<option value="' +
                    data.idDivision +
                    '" data-nombre="' +
                    data.Division +
                    '">' +
                    data.Division +
                    '</option>'
                );

                $('#selectDivision')
                    .prop('disabled', false)
                    .trigger('change.select2');

                console.log(
                    '✅ selectDivision actualizado'
                );

            } else {

                console.warn(
                    '⚠ No se encontró División para el área'
                );

                $('#selectDivision')
                    .html(
                        '<option value="">No hay división para esta área</option>'
                    )
                    .prop('disabled', true)
                    .trigger('change.select2');
            }
        },

        error: function(xhr,status,error){

            console.error('❌ ERROR DIVISIÓN');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);

        }
    });

    console.log('📡 Iniciando carga de COPES');

    // Cargar Copes por Área
    $.ajax({

        type: 'POST',
        url: 'modal/selectores.php',

        data: {
            action: 'getCopesByArea',
            idArea: selectArea
        },

        dataType: 'json',

        beforeSend: function(){

            console.log(
                '📤 Enviando petición COPES',
                {
                    action: 'getCopesByArea',
                    idArea: selectArea
                }
            );

        },

        success: function(data) {

            console.log('✅ Respuesta COPES recibida');

            console.log(data);

            if (data && data.length > 0) {

                console.log(
                    '📋 Total COPES encontrados:',
                    data.length
                );

                var options =
                    '<option value="">-- Seleccione un cope --</option>';

                $.each(data, function(index, item) {

                    console.log(
                        'COPE #' + index,
                        item
                    );

                    options +=
                        '<option value="' +
                        item.id +
                        '" data-nombre="' +
                        item.COPE +
                        '">' +
                        item.COPE +
                        '</option>';
                });

                $('#selectCope')
                    .html(options)
                    .prop('disabled', false)
                    .trigger('change.select2');

                console.log(
                    '✅ selectCope actualizado'
                );

            } else {

                console.warn(
                    '⚠ No se encontraron COPES para esta área'
                );

                $('#selectCope')
                    .html(
                        '<option value="">No hay copes en esta área</option>'
                    )
                    .prop('disabled', true)
                    .trigger('change.select2');
            }
        },

        error: function(xhr,status,error){

            console.error('❌ ERROR COPES');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);

        },

        complete:function(){

            console.log('🏁 Finalizó petición COPES');
            console.log('========================================');

        }
    });
}


// MONITOREAR CAMBIOS DE ÁREA
$('#selectArea').on('change', function(){

    console.log('');
    console.log('🔄 CAMBIÓ EL ÁREA');

    console.log(
        'ID:',
        $(this).val()
    );

    console.log(
        'Nombre:',
        $('#selectArea option:selected').text()
    );

});

// MONITOREAR CAMBIOS DE DIVISIÓN
$('#selectDivision').on('change', function(){

    console.log('');
    console.log('🔄 CAMBIÓ LA DIVISIÓN');

    console.log(
        'ID:',
        $(this).val()
    );

    console.log(
        'Nombre:',
        $('#selectDivision option:selected').text()
    );

});

// MONITOREAR CAMBIOS DE COPE
$('#selectCope').on('change', function(){

    console.log('');
    console.log('🔄 CAMBIÓ EL COPE');

    console.log(
        'ID:',
        $(this).val()
    );

    console.log(
        'Nombre:',
        $('#selectCope option:selected').text()
    );

});

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