// ALMACENA EL ULTIMO ID DE NOTIFICACION QUE YA SE HA PROCESADO.
let ultimoIdVisto = 0;
// CONTEXTO DE AUDIO REUTILIZABLE PARA REPRODUCIR EL SONIDO DE LAS ALERTAS.
let audioContext = null;
// INDICA SI EL AUDIO YA FUE ACTIVADO Y PUEDE USARSE.
let audioDisponible = false;

$(document).ready(function() {
    // DESBLOQUEA EL AUDIO AL PRIMER CLICK O TECLA DEL USUARIO, QUE ES LO QUE NECESITAN LA MAYORIA DE NAVEGADORES.
    document.addEventListener('click', activarAudioNotificacion, { once: true });
    document.addEventListener('keydown', activarAudioNotificacion, { once: true });
    // 1. CARGA INICIAL: OBTIENE LAS NOTIFICACIONES RECIENTES Y LAS MUESTRA EN EL DROPDOWN SIN MOSTRAR TOAST.
    $.getJSON('Ajax_Notificaciones.php', { solo_dropdown: 1 }, function(data) {
        $('#alertsCount').text(data.count_total);

        if (data.recientes && data.recientes.length > 0) {
            data.recientes.forEach(agregarNotificacionAlDropdown);
            ultimoIdVisto = Math.max(...data.recientes.map(n => parseInt(n.id, 10)));
        }
    });

    // 2. A PARTIR DE AQUI, EL POLLING REVISA SI HAY NOTIFICACIONES NUEVAS Y LAS MUESTRA COMO TOAST.
    setInterval(revisarNotificaciones, 20000);
});

// CONSULTA PERIODICAMENTE SI LLEGARON NUEVAS NOTIFICACIONES DESDE EL ULTIMO ID VISTO.
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

// INSERTA UNA NOTIFICACION EN EL DROPDOWN DEL BELL Y LE ASIGNA ICONO Y COLOR SEGUN SU TIPO.
function agregarNotificacionAlDropdown(n) {
    if ($('#alertsList .text-gray-500').closest('.notif-item, div').length && $('#alertsList').find('.notif-item').length === 0) {
        $('#alertsList').empty();
    }

    var iconoClase = 'fa-bell';
    var colorClase = 'default';

    if (n.tipo_notificacion === 'salida_pendiente') {
        iconoClase = 'fa-box-open';
        colorClase = 'pendiente';
    } else if (n.tipo_notificacion === 'salida_aceptada') {
        iconoClase = 'fa-check';
        colorClase = 'aceptada';
    } else if (n.tipo_notificacion === 'salida_rechazada') {
        iconoClase = 'fa-times';
        colorClase = 'rechazada';
    }

    var fecha = n.fecha_creacion ? formatearFechaRelativa(n.fecha_creacion) : '';

    var item = `
        <div class="notif-item" data-id="${n.id}">
            <div class="notif-icon ${colorClase}">
                <i class="fas ${iconoClase}"></i>
            </div>
            <div class="notif-texto">
                <span class="notif-mensaje">${n.mensaje}</span>
                <span class="notif-fecha">${fecha}</span>
            </div>
            <div class="notif-acciones">
                <button class="btn btn-outline-secondary" onclick="marcarComoVisto(${n.id})" title="Marcar como visto">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-outline-primary" onclick="irANotificacion(${n.id}, '${n.url_destino}')" title="Ir">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>`;
    $('#alertsList').prepend(item);
}

// FORMATEA LA FECHA PARA MOSTRARLA COMO "HACE X MINUTOS", "HACE X HORAS" O "HACE X DIAS".
function formatearFechaRelativa(fechaStr) {
    var fecha = new Date(fechaStr.replace(' ', 'T'));
    var ahora = new Date();
    var diffMs = ahora - fecha;
    var diffMin = Math.floor(diffMs / 60000);

    if (diffMin < 1) return 'Justo ahora';
    if (diffMin < 60) return 'Hace ' + diffMin + ' min';
    var diffHoras = Math.floor(diffMin / 60);
    if (diffHoras < 24) return 'Hace ' + diffHoras + ' h';
    var diffDias = Math.floor(diffHoras / 24);
    return 'Hace ' + diffDias + ' d';
}

// MARCA UNA NOTIFICACION COMO LEIDA EN EL SERVIDOR Y LA ELIMINA DEL DROPDOWN VISUALMENTE.
function marcarComoVisto(id) {
    $.post('Ajax_Notificaciones.php', { accion: 'marcar_leido', id: id }, function() {
        $(`#alertsList .notif-item[data-id="${id}"]`).fadeOut(200, function() { $(this).remove(); });
        var actual = parseInt($('#alertsCount').text(), 10);
        $('#alertsCount').text(Math.max(0, actual - 1));

        if ($('#alertsList .notif-item').length === 0) {
            $('#alertsList').html(`
                <div class="text-center small text-gray-500 py-4">
                    <i class="fas fa-inbox fa-2x mb-2 d-block text-gray-300"></i>
                    No hay notificaciones
                </div>`);
        }
    });
}

// REDIRIGE A LA URL DE LA NOTIFICACION Y LA MARCA COMO LEIDA ANTES DE NAVEGAR.
function irANotificacion(id, url) {
    $.post('Ajax_Notificaciones.php', { accion: 'marcar_leido', id: id }, function() {
        window.location.href = url;
    });
}

// DESBLOQUEA EL AUDIO AL PRIMER INTERACTUAR DEL USUARIO PARA QUE LOS NAVEGADORES LO PERMITAN.
function activarAudioNotificacion() {
    if (audioDisponible) return;

    try {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) return;

        audioContext = new AudioContextClass();
        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }
        audioDisponible = true;
    } catch (e) {
        console.log('No se pudo inicializar el audio de notificaciones', e);
    }
}

// REPRODUCE UN SONIDO CORTO CUANDO LLEGA UNA NUEVA NOTIFICACION.
function reproducirSonidoNotificacion() {
    if (!audioDisponible) {
        activarAudioNotificacion();
    }

    if (!audioContext) return;

    try {
        const playTone = (freq, start, duration, gainValue) => {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(freq, start);
            gainNode.gain.setValueAtTime(gainValue, start);
            gainNode.gain.exponentialRampToValueAtTime(0.0001, start + duration);
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            oscillator.start(start);
            oscillator.stop(start + duration);
        };

        playTone(880, 0, 0.15, 0.08);
        playTone(1180, 0.14, 0.16, 0.06);
    } catch (e) {
        console.log('No se pudo reproducir el sonido de la notificación', e);
    }
}

// MUESTRA EL TOAST DE SWEETALERT CON EL MENSAJE Y EL SONIDO ASOCIADO.
function mostrarToast(n) {
    reproducirSonidoNotificacion();

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