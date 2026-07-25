let ultimoIdVisto = 0;

$(document).ready(function() {
    // 1. CARGA INICIAL: SOLO POBLAR EL DROPDOWN, SIN TOAST
    $.getJSON('Ajax_Notificaciones.php', { solo_dropdown: 1 }, function(data) {
        $('#alertsCount').text(data.count_total);

        if (data.recientes && data.recientes.length > 0) {
            data.recientes.forEach(agregarNotificacionAlDropdown);
            ultimoIdVisto = Math.max(...data.recientes.map(n => parseInt(n.id, 10)));
        }
    });

    // 2. A PARTIR DE AQUI, EL POLLING SI DISPARA TOAST PARA LO REALMENTE NUEVO
    setInterval(revisarNotificaciones, 20000);
});

function revisarNotificaciones() {
    $.getJSON('Ajax_Notificaciones.php', { desde_id: ultimoIdVisto }, function(data) {
        $('#alertsCount').text(data.count_total);

        data.nuevas.forEach(function(n) {
            mostrarToast(n);
            agregarNotificacionAlDropdown(n);
            if (parseInt(n.id, 10) > ultimoIdVisto) ultimoIdVisto = parseInt(n.id, 10);
        });
    });
}

function agregarNotificacionAlDropdown(n) {
    if ($('#alertsList .text-gray-500').length) {
        $('#alertsList').empty();
    }

    var item = `
        <div class="dropdown-item d-flex justify-content-between align-items-center" data-id="${n.id}">
            <span class="small" style="max-width: 200px;">${n.mensaje}</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary btn-sm" onclick="marcarComoVisto(${n.id})" title="Marcar como visto">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="irANotificacion(${n.id}, '${n.url_destino}')" title="Ir">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>`;
    $('#alertsList').prepend(item);
}

function marcarComoVisto(id) {
    $.post('Ajax_Notificaciones.php', { accion: 'marcar_leido', id: id }, function() {
        $(`#alertsList div[data-id="${id}"]`).fadeOut(200, function() { $(this).remove(); });
        var actual = parseInt($('#alertsCount').text(), 10);
        $('#alertsCount').text(Math.max(0, actual - 1));

        if ($('#alertsList div').length === 0) {
            $('#alertsList').html('<a class="dropdown-item text-center small text-gray-500">No hay notificaciones</a>');
        }
    });
}

function irANotificacion(id, url) {
    $.post('Ajax_Notificaciones.php', { accion: 'marcar_leido', id: id }, function() {
        window.location.href = url;
    });
}

function mostrarToast(n) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'info',
        title: n.mensaje,
        showConfirmButton: false,
        timer: 6000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('click', () => irANotificacion(n.id, n.url_destino));
        }
    });
}