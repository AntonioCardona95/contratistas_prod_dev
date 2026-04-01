<?php
$src = isset($_GET['src']) ? $_GET['src'] : '';
if (!$src) {
    echo "No se especificó ninguna imagen.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Visor de Imagen</title>
<style>
    body {
        margin: 0;
        background-color: #000;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    img {
        max-width: 100%;
        max-height: 100%;
        cursor: zoom-in;
        transition: transform 0.25s ease;
    }
    img.zoom {
        transform: scale(2);
        cursor: zoom-out;
    }
</style>
</head>
<body>
    <img src="<?php echo htmlspecialchars($src); ?>" id="imagenZoom" alt="Imagen">
    <script>
        const img = document.getElementById('imagenZoom');
        img.addEventListener('click', () => {
            img.classList.toggle('zoom');
        });
    </script>
</body>
</html>