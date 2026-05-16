$(document).ready(function() {
    $.ajax({
        url: 'alertasgrafica.php',
        method: 'GET',
        dataType: 'json',
        success: function(valores) {
            Highcharts.chart('container', {
                chart: { type: 'column' },
                title: { text: 'Cantidad de Resultados' },
                xAxis: {
                    categories: [
                        'NO EN COORDIAPP','SIN NAVEGACION','ONT DUPLICADA TAC','ONT_DUPLICADA_EN_ESCI',
                        'TECNOLOGIA DIFERENTE','TIPO TAREA DIFERENTE','NUMERO DE SERIE DIFERENTE',
                        'ONT NO EN ALMACEN','ONT ASIGNADA A OTRO CONTRATISTA',
                        'ONT SIN ASIGNAR','ORDEN A REVISION POR 30 DIAS'
                    ],
                    labels: { rotation: -45, style: { fontSize: '11px' } }
                },
                yAxis: { min: 0, title: { text: 'Cantidad' } },
                tooltip: { pointFormat: 'Cantidad: <b>{point.y}</b>' },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true,
                            inside: true,
                            style: { fontSize: '12px', fontWeight: 'bold', color: 'white' }
                        },
                        color: '#007bff'
                    }
                },
                series: [{
                    name: 'Resultados',
                    data: valores // 👈 aquí entran los números en el orden fijo
                }]
            });
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar datos:", error);
        }
    });
});
