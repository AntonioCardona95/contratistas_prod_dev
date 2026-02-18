//console.log("✅ insertar_foliospaco.js cargado");

//console.log("✅ insertar_foliospaco.js cargado");

$(document).on('click', '#btnInsertarFolios', function () {

    const btn = $(this);

    const emulador      = btn.data('emulador');      // FIBRA
    const idEmulador    = btn.data('idemulador');    // 1
    const semana        = btn.data('semana');
    const tecnologia    = btn.data('tecnologia');
    const idContratista = btn.data('idcontratista');

    let filas = [];

    // Usar la API de DataTables para recorrer TODAS las filas
    let table = $('#ventas').DataTable();

    table.rows().every(function () {
        let data = this.data(); // array con todas las celdas de la fila

        let fila = {
            folio_pisa: data[0].trim(),
            telefono: data[2].trim(),
            tecnologia: data[3].trim(),
            tipo_tarea: data[4].trim(),
            cope: data[5].trim(),
            tecnico: data[6].trim(),
            Expediente: data[7].trim(),
            Distrito: data[8].trim(),
            num_serie: data[9].trim(),
            fecha_liquidacion: data[10].trim(),
            Area: data[11].trim(),
            Division: data[12].trim(),
            Direccion: data[13].trim(),
            Cliente: data[14].trim(),
            terminal: data[15].trim(),
            puerto: data[16].trim(),
            tipo_instalacion: data[17].trim(),
            latitud: data[18].trim(),
            longitud: data[19].trim(),
            metraje: data[20].trim()
        };

        filas.push(fila);
    });

    if (filas.length === 0) {
        alert('No hay filas para insertar');
        return;
    }

    // Mensajes de depuración en consola 
    //console.log("Total de filas capturadas:", filas.length);
    //console.table(filas);

    $.ajax({
        url: '/ordenes/modal/insertar_foliospaco.php',
        type: 'POST',
        dataType: 'json',
        data: {
            filas: filas,
            emulador: emulador,        // ← nombre
            idEmulador: idEmulador,    // ← id
            semana: semana,
            tecnologia: tecnologia,
            idContratista: idContratista
        },
        success: function (resp) {
            if (resp.ok) {
                alert('Registros insertados correctamente');
            } else {
                alert(resp.error);
            }
        },
        error: function (err) {
            console.error(err);
            alert('Error en AJAX');
        }
    });

});


/* $(document).on('click', '#btnInsertarFolios', function () {

    const btn = $(this);

    const emulador      = btn.data('emulador');      // FIBRA
    const idEmulador    = btn.data('idemulador');    // 1
    const semana        = btn.data('semana');
    const tecnologia    = btn.data('tecnologia');
    const idContratista = btn.data('idcontratista');

    let filas = [];

    $('#ventas tbody tr').each(function () {

        let fila = {
            folio_pisa: $(this).find('td:eq(0)').text().trim(),
            telefono: $(this).find('td:eq(2)').text().trim(),
            tecnologia: $(this).find('td:eq(3)').text().trim(),
            tipo_tarea: $(this).find('td:eq(4)').text().trim(),
            cope: $(this).find('td:eq(5)').text().trim(),
            tecnico: $(this).find('td:eq(6)').text().trim(),
            Expediente: $(this).find('td:eq(7)').text().trim(),
            Distrito: $(this).find('td:eq(8)').text().trim(),
            num_serie: $(this).find('td:eq(9)').text().trim(),
            fecha_liquidacion: $(this).find('td:eq(10)').text().trim(),
            Area: $(this).find('td:eq(11)').text().trim(),
            Division: $(this).find('td:eq(12)').text().trim(),
            Direccion: $(this).find('td:eq(13)').text().trim(),
            Cliente: $(this).find('td:eq(14)').text().trim(),
            terminal: $(this).find('td:eq(15)').text().trim(),
            puerto: $(this).find('td:eq(16)').text().trim(),
            tipo_instalacion: $(this).find('td:eq(17)').text().trim(),
            latitud: $(this).find('td:eq(18)').text().trim(),
            longitud: $(this).find('td:eq(19)').text().trim(),
            metraje: $(this).find('td:eq(20)').text().trim()
        };

        filas.push(fila);
    });

    if (filas.length === 0) {
        alert('No hay filas para insertar');
        return;
    }

    // Mensajes de depuración en consola 
    console.log("Total de filas capturadas:", filas.length);
    console.table(filas);

    //console.log('Filas a enviar:', filas);

    $.ajax({
        url: '/ordenes/modal/insertar_foliospaco.php',
        type: 'POST',
        dataType: 'json',
        data: {
            filas: filas,
            emulador: emulador,        // ← nombre
            idEmulador: idEmulador,    // ← id
            semana: semana,
            tecnologia: tecnologia,
            idContratista: idContratista
        },
        success: function (resp) {
            if (resp.ok) {
                alert('Registros insertados correctamente');
            } else {
                alert(resp.error);
            }
        },
        error: function (err) {
            console.error(err);
            alert('Error en AJAX');
        }
    });

}); */


/*console.log("✅ Estoy dentro de mi archivo JS");
$.ajax({
    url: '../modal/insertar_foliospaco.php',
    type: 'POST',
    dataType: 'json',
    data: {
        filas: filas,
        emulador: '<?= $emulador ?>',
        semana: '<?= $semana ?>',
        tecnologia: '<?= $tecnologia ?>',
        idContratista: '<?= $idContratistas ?>'
    },
    success: function (resp) {
        if (resp.ok) {
            alert('Registros insertados correctamente');
        } else {
            alert(resp.error);
        }
    }
}); */
