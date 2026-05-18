$(document).ready(function() {
    $.ajax({
        url: 'alertasgrafica.php',
        method: 'GET',
        dataType: 'json',
        success: function(valores) {
            Highcharts.chart('container', {
                chart: { 
                    type: 'column',
                    height: 700 // altura más grande
                },
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
                                    // Redirige al archivo PHP con parámetros GET
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

