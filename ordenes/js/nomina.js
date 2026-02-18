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

});
