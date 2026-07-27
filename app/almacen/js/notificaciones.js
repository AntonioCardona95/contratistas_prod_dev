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
    // ICONO Y COLOR SEGUN EL TIPO DE NOTIFICACION
    var iconoTipo = 'info';
    var colorBorde = '#4462e7';

    if (n.tipo_notificacion === 'salida_aceptada') {
        iconoTipo = 'success';
        colorBorde = '#1cc88a';
    } else if (n.tipo_notificacion === 'salida_rechazada') {
        iconoTipo = 'error';
        colorBorde = '#e74a3b';
    } else if (n.tipo_notificacion === 'salida_pendiente') {
        iconoTipo = 'warning';
        colorBorde = '#f6c23e';
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: false,
        timer: 6000,
        timerProgressBar: true,
        showCloseButton: true,
        customClass: {
            popup: 'toast-notificacion-custom'
        },
        didOpen: (toast) => {
            toast.style.borderLeft = '4px solid ' + colorBorde;
            toast.addEventListener('click', (e) => {
                // EVITAR QUE EL CLICK EN LA X DE CERRAR TAMBIEN DISPARE LA NAVEGACION
                if (!e.target.closest('.swal2-close')) {
                    irANotificacion(n.id, n.url_destino);
                }
            });
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: iconoTipo,
        title: n.mensaje
    });
}