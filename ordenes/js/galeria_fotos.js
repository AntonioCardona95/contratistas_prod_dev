$(document).ready(function() {
    $('.btn-galeria').on('click', function() {
        var folio = $(this).data('folio');
        var idContratista = $(this).data('idcontratista');
        var nombreContratista = $(this).data('nombrecontratista');

        $.ajax({
            url: 'modal/obtenerfotos.php',
            type: 'POST',
            data: { folio: folio },
            dataType: 'json',
            success: function(res) {
                if (res.error) return;

                $('#modalNombreContratista').text(nombreContratista);
                $('#modalIdContratista').text(idContratista);
                $('#modalFolio').text(folio);

                var img = function(v){ return v && v.trim() !== '' ? v : 'noimagen.jpg'; };

                $('#miniaturaFoto').attr('src', img(res.Foto_Casa_Cliente));
                $('#miniaturaFotomodem').attr('src', img(res.Foto_Ont));
                $('#miniaturaFotomodemtrasera').attr('src', img(res.No_Serie_ONT));
                $('#miniaturaFotoos').attr('src', img(res.Foto_INE));
                $('#miniaturafotopuerto').attr('src', img(res.Foto_Puerto));

                $('#fotoGrande').attr('href', 'verImagen.php?src=' + encodeURIComponent(img(res.Foto_Casa_Cliente)));
                $('#fotoGrandemodem').attr('href', 'verImagen.php?src=' + encodeURIComponent(img(res.Foto_Ont)));
                $('#fotoGrandemodemtrasera').attr('href', 'verImagen.php?src=' + encodeURIComponent(img(res.No_Serie_ONT)));
                $('#fotoGrandeos').attr('href', 'verImagen.php?src=' + encodeURIComponent(img(res.Foto_INE)));
                $('#fotopuerto').attr('href', 'verImagen.php?src=' + encodeURIComponent(img(res.Foto_Puerto)));

                $('#modalGaleria').modal('show');
            }
        });
    });
});