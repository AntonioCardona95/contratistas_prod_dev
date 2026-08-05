// ALMACENA EL ULTIMO ID DE NOTIFICACION QUE YA SE HA PROCESADO.
let ultimoIdVisto = 0;
// CONTEXTO DE AUDIO REUTILIZABLE PARA REPRODUCIR EL SONIDO DE LAS ALERTAS.
let audioContext = null;
// INDICA SI EL AUDIO YA FUE ACTIVADO Y PUEDE USARSE.
let audioDisponible = false;
// ELEMENTO DE AUDIO QUE REPRODUCE EL SONIDO REAL DE LA NOTIFICACION.
let audioElemento = null;
let audioUrl = null;

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
        if (AudioContextClass) {
            audioContext = new AudioContextClass();
            if (audioContext.state === 'suspended') {
                audioContext.resume();
            }
        }

        if (!audioElemento) {
            const src = crearSonidoNotificacionUrl();
            audioElemento = new Audio(src);
            audioElemento.preload = 'auto';
            audioElemento.volume = 0.6;
        }

        audioDisponible = true;
    } catch (e) {
        console.log('No se pudo inicializar el audio de notificaciones', e);
    }
}

function crearSonidoNotificacionUrl() {
    if (audioUrl) return audioUrl;

    const sampleRate = 22050;
    const duration = 0.28;
    const totalSamples = Math.floor(sampleRate * duration);
    const buffer = new ArrayBuffer(44 + totalSamples * 2);
    const view = new DataView(buffer);

    const writeString = (offset, value) => {
        for (let i = 0; i < value.length; i++) {
            view.setUint8(offset + i, value.charCodeAt(i));
        }
    };

    const writeInt16 = (offset, value) => {
        view.setInt16(offset, value, true);
    };

    const writeInt32 = (offset, value) => {
        view.setInt32(offset, value, true);
    };

    writeString(0, 'RIFF');
    writeInt32(4, 36 + totalSamples * 2);
    writeString(8, 'WAVE');
    writeString(12, 'fmt ');
    writeInt32(16, 16);
    writeInt16(20, 1);
    writeInt16(22, 1);
    writeInt32(24, sampleRate);
    writeInt32(28, sampleRate * 2);
    writeInt16(32, 2);
    writeInt16(34, 16);
    writeString(36, 'data');
    writeInt32(40, totalSamples * 2);

    let offset = 44;
    for (let i = 0; i < totalSamples; i++) {
        const t = i / sampleRate;
        const envelope = Math.exp(-4 * (i / totalSamples));
        const tone = Math.sin(2 * Math.PI * 880 * t) * 0.6 + Math.sin(2 * Math.PI * 1180 * t) * 0.3;
        const sample = Math.max(-1, Math.min(1, tone * envelope));
        writeInt16(offset, Math.floor(sample * 0x7fff));
        offset += 2;
    }

    const blob = new Blob([buffer], { type: 'audio/wav' });
    audioUrl = URL.createObjectURL(blob);
    return audioUrl;
}

// REPRODUCE UN SONIDO CORTO CUANDO LLEGA UNA NUEVA NOTIFICACION.
function reproducirSonidoNotificacion() {
    if (!audioDisponible) {
        activarAudioNotificacion();
    }

    if (!audioElemento) return;

    try {
        audioElemento.pause();
        audioElemento.currentTime = 0;
        audioElemento.play().catch(function(err) {
            console.log('No se pudo reproducir el sonido de la notificación', err);
        });
    } catch (e) {
        console.log('No se pudo reproducir el sonido de la notificación', e);
    }
}

// MUESTRA EL TOAST DE SWEETALERT CON EL MENSAJE Y EL SONIDO ASOCIADO.
function mostrarToast(n) {
    setTimeout(function() {
        reproducirSonidoNotificacion();
    }, 120);

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