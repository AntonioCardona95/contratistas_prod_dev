
function LoadKPIS(){
    // TOMA TODOS LOS VALORES DE LOS FILTROS CON EL ID FORMQUERYUNIDADES
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadKPIS").show();
    // NUESTRA PETICION AJAX MANDA TODOS LOS DATOS A LA TABLAKPIS.PHP
    $.ajax({
        type:'POST',
        url:'../../handler/Almacen/Estadisticas.php',
        data: parametros,
        // CUANDO LA PETICION SEA EXITOSA, CARGA LOS DATOS EN EL DIV loadKPIS
        success:function(data){
            $("#LoadKPIS").html(data);
        }
    });
}
