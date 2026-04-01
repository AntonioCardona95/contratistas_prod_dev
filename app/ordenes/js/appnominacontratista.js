$(document).ready(function () {

    // ===============================
    // FUNCIÓN QUE CARGA LA DATA TABLE
    // ===============================
    function enviarValoresContratista() {
        const emulador = $('#emuladorSelect').val();
        const tecnologia = $('#tecnologiaSelect').val();
        const semana = $('#semana').val();
        const nombreContratista = $('#infoContratista').data('nombre');
        const idContratista = $('#infoContratista').data('id');

        $('#LoadNomina').html('');

        if (emulador && tecnologia && semana) {
            $.ajax({
                url: 'modal/TablaOrdenesPago.php',
                type: 'POST',
                data: {
                    emulador,
                    tecnologia,
                    semana,
                    nombreContratista,
                    idContratista
                },
                success: function (response) {
                    $('#LoadNomina').html(response);
                },
                error: function () {
                    $('#LoadNomina').html('Ocurrió un error al cargar los datos.');
                }
            });
        }
    }

    // ===============================
    // EVENTOS DE LOS SELECTS
    // ===============================
    $('#emuladorSelect, #tecnologiaSelect, #semana')
        .on('change', enviarValoresContratista);

    // ===============================
    // BOTÓN LIBERAR PAGO (DEBUG)
    // ===============================
    $('#btnLiberarPago').on('click', function () {

        const emulador = $('#emuladorSelect').val();
        const tecnologia = $('#tecnologiaSelect').val();
        const semana = $('#semana').val();
        const idContratista = $('#infoContratista').data('id');

        const table = $('#ventas').DataTable();

        // ===============================
        // LEER NOMBRES DE COLUMNAS
        // ===============================
        const columnas = [];
        $('#ventas thead th').each(function () {
            columnas.push($(this).data('col'));
        });

        // ===============================
        // LEER FILAS COMO OBJETO
        // ===============================
        const filas = [];

        table.rows().every(function () {
            const data = this.data();
            const filaObj = {};

            columnas.forEach((col, i) => {
                if (col && col !== 'galeria') {
                    filaObj[col] = data[i];
                }
            });

            filas.push(filaObj);
        });

        // ===============================
        // ENVÍO A PHP (DEBUG)
        // ===============================
        $.ajax({
            url: 'modal/debug_liberar_pago.php',
            type: 'POST',
            data: {
                emulador,
                tecnologia,
                semana,
                idContratista,
                filas: JSON.stringify(filas)
            },
            dataType: 'json', // asegura que jQuery parsea la respuesta
            success: function (response) {
                console.log('💡 RESPUESTA DE DEPURACIÓN:', response);
                console.table(response.filas);
            },
            error: function (err) {
                console.error('❌ ERROR AJAX', err);
            }
        });

    });

});
