let tecnicosLoadInProgress = false;

function LoadTecnicos() {
    if (tecnicosLoadInProgress) {
        return;
    }

    tecnicosLoadInProgress = true;

    $.ajax({
        url: 'modal/TablaTecnicos.php',
        type: 'GET',
        success: function (html) {
            $('#LoadTecnicos').html(html);
        },
        error: function (xhr, status) {
            console.error('Error al cargar técnicos:', status, xhr);
            $('#LoadTecnicos').html('<div class="alert alert-danger">No se pudo cargar la vista de técnicos.</div>');
        },
        complete: function () {
            tecnicosLoadInProgress = false;
        }
    });
}

$(document).ready(function () {
    LoadTecnicos();
});
