$(document).ready(function() {
    $.ajax({
        url: 'alertasgraficafotos.php',
        method: 'GET',
        dataType: 'json',
        success: function(valores) {
            Highcharts.chart('containerf', {
                chart: { 
                    type: 'column',
                    height: 700 // altura más grande
                },
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
                yAxis: {
                    min: 0,
                    title: { text: 'Cantidad' },
                    labels: {
                        formatter: function () {
                            return Highcharts.numberFormat(this.value, 0, '', ',');
                        },
                        style: { fontSize: '12px' }
                    },
                    plotLines: [{
                        value: 1000,
                        color: 'red',
                        width: 2,
                        dashStyle: 'ShortDash',
                        label: {
                            text: 'Umbral 1000',
                            align: 'right',
                            style: { color: 'red', fontWeight: 'bold' }
                        }
                    }]
                },
                tooltip: { pointFormat: 'Cantidad: <b>{point.y}</b>' },
                plotOptions: {
                    column: {
                        minPointLength: 10, // barras pequeñas siempre visibles
                        dataLabels: {
                            enabled: true,
                            inside: true,
                            style: { fontSize: '13px', fontWeight: 'bold', color: 'white' }
                        },
                        color: '#007bff'
                    },
                    series: {
                        cursor: 'pointer', // cursor tipo mano
                        point: {
                            events: {
                                click: function () {
                                    // Redirige al detalle con parámetros GET
                                    window.location.href = 'detalleAlertas.php?categoria=' 
                                        + encodeURIComponent(this.category) 
                                        + '&cantidad=' + this.y;
                                }
                            }
                        }
                    }
                },
                series: [{
                    name: 'Resultados',
                    data: valores
                }]
            });
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar datos:", error);
        }
    });
});


/*$(document).ready(function() {
    $.ajax({
        url: 'alertasgraficafotos.php',
        method: 'GET',
        dataType: 'json',
        success: function(valores) {
            Highcharts.chart('containerf', {
                chart: { 
                    type: 'column',
                    height: 700 //  altura más grande
                },
                title: { text: 'Cantidad de Resultados' },
                xAxis: {
                    categories: [
                        'FOTO_ONT_FRONTAL_FALLIDA','FOTO ONT TRASERA FALLIDA','FOTO_CASA_CLIENTE_FALLIDA',
                        'FOTO OS FALLIDA','FOTO TERMINAL FALLIDA','FALTA_EVIDENCIA_FOTO_PUERTO'
                        
                    ],
                    labels: { rotation: -45, style: { fontSize: '12px' } }
                },
                yAxis: {
                    min: 0,
                    title: { text: 'Cantidad' },
                    labels: {
                        formatter: function () {
                            return Highcharts.numberFormat(this.value, 0, '', ',');
                        },
                        style: { fontSize: '12px' }
                    },
                    plotLines: [{
                        value: 1000,
                        color: 'red',
                        width: 2,
                        dashStyle: 'ShortDash',
                        label: {
                            text: 'Umbral 1000',
                            align: 'right',
                            style: { color: 'red', fontWeight: 'bold' }
                        }
                    }]
                },
                tooltip: { pointFormat: 'Cantidad: <b>{point.y}</b>' },
                plotOptions: {
                    column: {
                        minPointLength: 10, // 👈 barras pequeñas siempre visibles
                        dataLabels: {
                            enabled: true,
                            inside: true,
                            style: { fontSize: '13px', fontWeight: 'bold', color: 'white' }
                        },
                        color: '#007bff'
                    },
                    series: {
                        cursor: 'pointer', // 👈 cursor tipo mano
                        point: {
                            events: {
                                click: function () {
                                    alert('Categoría: ' + this.category + '\nCantidad: ' + this.y);
                                    // aquí puedes redirigir o cargar detalle según la categoría
                                }
                            }
                        }
                    }
                },
                series: [{
                    name: 'Resultados',
                    data: valores
                }]
            });
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar datos:", error);
        }
    });
}); */