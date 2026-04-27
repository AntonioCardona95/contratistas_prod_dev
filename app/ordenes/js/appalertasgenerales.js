console.log('🚀 entramos a js alertas generales');

$(document).ready(function(){

    console.log('✅ document ready ejecutado');

    // URL completa
    console.log('🌐 URL:', window.location.href);

    // Obtener parámetros
    var urlParams = new URLSearchParams(window.location.search);
    var fecha_inicio = urlParams.get('fecha_inicio');
    var fecha_fin = urlParams.get('fecha_fin');

    console.log('📅 fecha_inicio:', fecha_inicio);
    console.log('📅 fecha_fin:', fecha_fin);

    // Verificar si existe el div
    console.log('🧱 Existe #LoadAlertasGenerales:', $("#LoadAlertasGenerales").length);

    // Ejecutar si hay fechas
    if (fecha_inicio && fecha_fin) {
        console.log('🚀 Ejecutando LoadAlertasGenerales...');
        LoadAlertasGenerales(fecha_inicio, fecha_fin);
    } else {
        console.warn('⚠️ NO hay fechas en la URL, no se ejecuta AJAX');
    }

});


// ============================
// AJAX
// ============================
function LoadAlertasGenerales(fecha_inicio, fecha_fin){

    console.log('📡 Entrando a AJAX...');
    console.log('📤 Enviando datos:', {
        fecha_inicio: fecha_inicio,
        fecha_fin: fecha_fin
    });

    $.ajax({
        type: 'POST',
        url: 'modal/TablaAlertasGenerales.php',
        data: { 
            fecha_inicio: fecha_inicio, 
            fecha_fin: fecha_fin 
        },

        beforeSend: function(){
            console.log('⏳ beforeSend ejecutado');
            $("#LoadAlertasGenerales").html("<center><img src='../image/loading-46.gif'></center>");
        },

        success:function(data){
            console.log('✅ AJAX SUCCESS');

            console.log('📦 RESPUESTA CRUDA DEL PHP:');
            console.log(data);

            console.log('📏 LONGITUD RESPUESTA:', data.length);

            $("#LoadAlertasGenerales").html(data);

            console.log('🧱 HTML insertado en el div:');
            console.log($("#LoadAlertasGenerales").html());

            // Verificar si la tabla existe después de insertar
            console.log('📊 Existe tabla #ventasg:', $("#ventasg").length);

        },

        error: function(err){
            console.error('❌ AJAX ERROR');
            console.error(err);
        }
    });
}