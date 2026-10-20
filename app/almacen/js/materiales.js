function LoadPendientes() {
    var parametros = $('#FormQueryUnidades').serialize();
    var folioBuscar = new URLSearchParams(window.location.search).get('folio');

    if (folioBuscar) {
        var folioNumerico = folioBuscar.replace(/\D/g, '');
        if (folioNumerico.length) {
            parametros += (parametros.length ? '&' : '') + 'folio=' + encodeURIComponent(folioNumerico);
        }
    }

    $('#LoadPendientes').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: 'modal/AceptarMaterial_Tabla.php',
        data: parametros,
        success: function(data) {
            $('#LoadPendientes').html(data);
        }
    });
}

function LoadRechazados() {
    var parametros = $('#FormQueryUnidades').serialize();
    $('#LoadRechazados').fadeIn('slow');
    $.ajax({
        type: 'POST',
        url: 'modal/MaterialRechazado_Tabla.php',
        data: parametros,
        success: function(data) {
            $('#LoadRechazados').html(data);
        }
    });
}


