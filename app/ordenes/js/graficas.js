// Función para actualizar la gráfica de ONTs
function actualizarCards() {
    const fecha1 = $('#Fecha1').val();
    const fecha2 = $('#Fecha2').val();

    //  Depuración: imprime las fechas seleccionadas
    console.log('fechas grafica');
    console.log('📅 Fecha1:', fecha1);
    console.log('📅 Fecha2:', fecha2);

    if (!fecha1 || !fecha2) {
        alert('Selecciona ambas fechas.');
        return;
    }

    $.ajax({
        url: 'alertasgrafica.php',
        method: 'GET',
        dataType: 'json',
        data: { Fecha1: fecha1, Fecha2: fecha2 },
        success: function(valores) {
            Highcharts.chart('container', {
                chart: { type: 'column', height: 700 },
                title: { text: 'Cantidad de Resultados' },
                xAxis: {
                    categories: [
                        'NO EN COORDIAPP','SIN NAVEGACION','ONT_DUPLICADA_EN_TAC','ONT_DUPLICADA_EN_ESCI',
                        'TECNOLOGIA_DIFERENTE','TIPO_TAREA_DIFERENTE','NUMERO_SERIE_DIFERENTE',
                        'ONT_NO_ENCONTRADA_EN_ALMACEN','ONT_ASIGNADA_A_OTRO_CONTRATISTA',
                        'ONT SIN ASIGNAR','ORDEN A REVISION POR 30 DIAS','SERVICIO INACTIVO','ONT_NO_ASIGNADA_AL_CONTRATISTA'
                    ],
                    labels: { rotation: -45, style: { fontSize: '12px' } }
                },
                yAxis: {
                    min: 0,
                    title: { text: 'Cantidad' }
                },
                tooltip: { pointFormat: 'Cantidad: <b>{point.y}</b>' },
                plotOptions: {
                    column: { 
                        minPointLength: 10,
                        dataLabels: { enabled: true, style: { fontSize: '13px', fontWeight: 'bold', color: 'white' } },
                        color: '#007bff'
                    },
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    window.location.href = 'DetalleNuevoAlertas.php?categoria=' + encodeURIComponent(this.category) + '&cantidad=' + this.y;
                                }
                            }
                        }
                    }
                },
                series: [{ name: 'Resultados', data: valores || [] }]
            });
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar datos ONTs:", error);
        }
    });
}

// Función para actualizar la gráfica de fotos
function actualizarFotos() {
    const fecha1 = $('#Fecha1').val();
    const fecha2 = $('#Fecha2').val();

    console.log('fechas grafica fotos');
    console.log('📅 Fecha1:', fecha1);
    console.log('📅 Fecha2:', fecha2);

    if (!fecha1 || !fecha2) return;

    $.ajax({
        url: 'alertasgraficafotos.php',
        method: 'GET',
        dataType: 'json',
        data: { Fecha1: fecha1, Fecha2: fecha2 },
        success: function(valores) {
            Highcharts.chart('containerf', {
                chart: { type: 'column', height: 700 },
                title: { text: 'Cantidad de Resultados' },
                xAxis: {
                    categories: [
                        'FOTO_ONT_FRONTAL_FALLIDA',
                        'FOTO ONT TRASERA FALLIDA',
                        'FOTO_CASA_CLIENTE_FALLIDA',
                        'FOTO OS FALLIDA',
                        'FOTO TERMINAL FALLIDA',
                        'FALTA_EVIDENCIA_FOTO_PUERTO',
                        'FOTO DOMICILIO FALLIDA MAL TOMADA',
                        'FOTO TERMINAL FALLIDA MAL TOMADA',
                        'FOTO LEDS FALLIDA MAL TOMADA'
                    ],
                    labels: { rotation: -45, style: { fontSize: '12px' } }
                },
                yAxis: { min: 0, title: { text: 'Cantidad' } },
                tooltip: { pointFormat: 'Cantidad: <b>{point.y}</b>' },
                plotOptions: {
                    column: { 
                        minPointLength: 10,
                        dataLabels: { enabled: true, style: { fontSize: '13px', fontWeight: 'bold', color: 'white' } },
                        color: '#28a745'
                    },
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    window.location.href = 'DetalleAlertasFotos.php?categoria=' + encodeURIComponent(this.category) + '&cantidad=' + this.y;
                                }
                            }
                        }
                    }
                },
                series: [{ name: 'Resultados', data: valores || [] }]
            });
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar datos fotos:", error);
        }
    });
}

// Inicializamos ambas gráficas vacías al cargar la página
$(document).ready(function() {
    Highcharts.chart('container', {
        chart: { type: 'column', height: 700 },
        title: { text: 'Cantidad de Resultados' },
        xAxis: {
            categories: [
                'NO EN COORDIAPP','SIN NAVEGACION','ONT_DUPLICADA_EN_TAC','ONT_DUPLICADA_EN_ESCI',
                'TECNOLOGIA_DIFERENTE','TIPO_TAREA_DIFERENTE','NUMERO_SERIE_DIFERENTE',
                'ONT_NO_ENCONTRADA_EN_ALMACEN','ONT_ASIGNADA_A_OTRO_CONTRATISTA',
                'ONT SIN ASIGNAR','ORDEN A REVISION POR 30 DIAS','SERVICIO INACTIVO','ONT_NO_ASIGNADA_AL_CONTRATISTA'
            ]
        },
        yAxis: { min: 0 },
        series: [{ name: 'Resultados', data: [] }]
    });

    Highcharts.chart('containerf', {
        chart: { type: 'column', height: 700 },
        title: { text: 'Cantidad de Resultados' },
        xAxis: {
            categories: [
                'FOTO_ONT_FRONTAL_FALLIDA',
                'FOTO ONT TRASERA FALLIDA',
                'FOTO_CASA_CLIENTE_FALLIDA',
                'FOTO OS FALLIDA',
                'FOTO TERMINAL FALLIDA',
                'FALTA_EVIDENCIA_FOTO_PUERTO',
                'FOTO DOMICILIO FALLIDA MAL TOMADA',
                'FOTO TERMINAL FALLIDA MAL TOMADA',
                'FOTO LEDS FALLIDA MAL TOMADA'
            ]
        },
        yAxis: { min: 0 },
        series: [{ name: 'Resultados', data: [] }]
    });
});