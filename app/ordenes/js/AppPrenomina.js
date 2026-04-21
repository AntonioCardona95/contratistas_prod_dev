$(document).ready(function(){

    // Detecta cambio de semana
    $("#semana").change(function(){
        var semana = $(this).val();

        if(semana){ 
            LoadAlertas(semana);
        } else {
            $("#LoadAlertas").html('');
        }
    });

});


// ================================
// AJAX
// ================================
function LoadAlertas(semana){
    $.ajax({
        type: 'POST',
        url: 'modal/TablaNuevaAlertas.php',
        data: { semana: semana },

        beforeSend: function(){
            $("#LoadAlertas").html("<center><img src='../image/loading-46.gif'></center>");
        },

        success: function(data){

            // Insertar tabla
            $("#LoadAlertas").html(data);

            // 🔥 IMPORTANTE: esperar a que el DOM pinte la tabla
            setTimeout(function(){
                initDataTable();
            }, 50);
        },

        error: function(err){
            console.log("Error AJAX:", err);
        }
    });
}


// ================================
// DATA TABLE INIT
// ================================
function initDataTable(){

    console.log("Inicializando DataTable...");

    // evitar duplicados si se vuelve a cargar
    if ($.fn.DataTable.isDataTable('#ventas')) {
        $('#ventas').DataTable().destroy();
    }

    $('#ventas').DataTable({
        responsive: {
            details: {
                type: 'column',   // muestra detalles en columna
                target: 'tr'      // despliega debajo de la fila
            }
        },
        ordering: false,
        searching: true,
        paging: true,
        dom: 'Bfrtip',

        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },

        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger'
            }
        ],

        columnDefs: [
            { responsivePriority: 1, targets: 0 }, // Folio Pisa siempre visible
            { responsivePriority: 2, targets: 1 }, // Teléfono siempre visible
            { responsivePriority: 3, targets: -1 } // Botón Galería siempre visible
        ]
    });

    console.log("DataTable cargado correctamente");
}

/*$(document).ready(function(){

    // Detecta cambio de semana
    $("#semana").change(function(){
        var semana = $(this).val();
        if(semana){ 
            LoadAlertas(semana);
        } else {
            $("#LoadAlertas").html(''); // Limpiar tabla si no hay semana
        }
    });

});

// Función AJAX que envía la semana seleccionada
function LoadAlertas(semana){
    $.ajax({
        type:'POST',
        url:'modal/TablaNuevaAlertas.php',
        data: { semana: semana }, // enviamos solo la semana
        beforeSend: function(){
            $("#LoadAlertas").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadAlertas").html(data);
        },
        error: function(err){
            console.log(err);
        }
    });
} */

/*$(document).ready(function(){

    console.log(" JS cargado y listo"); // Confirmar que entró al ready

    // Detecta cambio de semana
    $("#semana").change(function(){
        var semana = $(this).val();
        console.log("📌 Semana seleccionada:", semana); // Ver qué valor tomó
        if(semana){ 
            LoadAlertas(semana);
        } else {
            $("#LoadAlertas").html(''); // Limpiar tabla si no hay semana
        }
    });

});

// Función AJAX que envía la semana seleccionada
function LoadAlertas(semana){
    console.log(" Entrando a LoadAlertas con semana:", semana); // Confirmar que llega el valor

    $.ajax({
        type:'POST',
        url:'modal/TablaNuevaAlertas.php',
        data: { semana: semana }, // enviamos solo la semana
        beforeSend: function(){
            console.log(" Enviando petición AJAX con semana:", semana);
            $("#LoadAlertas").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            console.log(" Respuesta recibida del servidor");
            $("#LoadAlertas").html(data);
        },
        error: function(err){
            console.log(" Error en AJAX:", err);
        }
    });
} */


