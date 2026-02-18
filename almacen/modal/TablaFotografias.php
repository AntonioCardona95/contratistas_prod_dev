<?php
    header("Access-Control-Allow-Origin: *");

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');

    require_once("../controller/ClassOps.php");

    session_start();
    $folio_pisa = isset($_POST['folio_pisa']) ? $_POST['folio_pisa'] : '';
    if (empty($folio_pisa)) {
        echo "<h3>Folio no encontrado</h3>";
        exit;
    }else{
        echo "<h3>$folio_pisa</h3>";
    }

    $modal = new Ops();
    $Fotos = $modal->GetFotos($folio_pisa);
    
    if (!is_array($Fotos)) {
        $Fotos = [];
    }

    $Foto_Ont = '';
    $Foto_Casa_Cliente = '';
    $No_Serie_ONT = '';
    $Foto_Puerto = '';
    
    foreach ($Fotos as $row) {
        $Foto_Ont = !empty($row['Foto_Ont']) ? substr($row['Foto_Ont'], 2) : '';
        $Foto_Casa_Cliente = !empty($row['Foto_Casa_Cliente']) ? substr($row['Foto_Casa_Cliente'], 2) : '';
        $No_Serie_ONT = !empty($row['No_Serie_ONT']) ? substr($row['No_Serie_ONT'], 2) : '';
        $Foto_Puerto = !empty($row['Foto_Puerto']) ? substr($row['Foto_Puerto'], 2) : '';
        $Foto_INE = !empty($row['Foto_INE']) ? substr($row['Foto_INE'], 2) : '';
    }
    
    function url_exists($url) {
        $headers = @get_headers($url);
        return is_array($headers) && strpos($headers[0], '200') !== false;
    }

    $ruta = "https://vps.ed-intra.com/API/";
    $ruta2 = "https://api.ed-intra.com/";

    // Priorizar $ruta2, si no existe usar $ruta
    $url_foto_ont = url_exists($ruta2 . $Foto_Ont) ? $ruta2 . $Foto_Ont : $ruta . $Foto_Ont;
    $url_foto_casa_cliente = url_exists($ruta2 . $Foto_Casa_Cliente) ? $ruta2 . $Foto_Casa_Cliente : $ruta . $Foto_Casa_Cliente;
    $url_no_serie_ont = url_exists($ruta2 . $No_Serie_ONT) ? $ruta2 . $No_Serie_ONT : $ruta . $No_Serie_ONT;
    $url_foto_puerto = url_exists($ruta2 . $Foto_Puerto) ? $ruta2 . $Foto_Puerto : $ruta . $Foto_Puerto;
    $url_foto_ine = url_exists($ruta2 . $Foto_INE) ? $ruta2 . $Foto_INE : $ruta . $Foto_INE;

?>
<!-- Incluye Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<div class="container mt-4">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Foto ONT</th>
                <th>Foto Casa Cliente</th>
                <th>No. Serie ONT</th>
                <th>Foto Terminal</th>
                <th>Foto OS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($Fotos as $row): ?>
                <tr>
                    <!-- Foto ONT -->
                    <td>
                        <?php
                        $fotoOnt = !empty($row['Foto_Ont']) ? substr($row['Foto_Ont'], 2) : '';
                        if ($fotoOnt):
                        ?>
                            <center>
                            <img src="<?= htmlspecialchars($url_foto_ont) . '?v=' . rand() ?>" alt="Foto ONT" width="100">
                            <br>
                            <button class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="fotoONT">
                                Remplazar Fotografía
                            </button>
                        <?php else: ?>
                            <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="fotoONT">
                                Cargar Fotografía
                            </button>
                            </center>
                        <?php endif; ?>
                    </td>
                    <!-- Foto Casa Cliente -->
                    <td>
                        <?php
                        $fotoCasa = !empty($row['Foto_Casa_Cliente']) ? substr($row['Foto_Casa_Cliente'], 2) : '';
                        if ($fotoCasa):
                        ?>
                            <center>
                            <img src="<?= htmlspecialchars($url_foto_casa_cliente) . '?v=' . rand() ?>" alt="Foto Casa Cliente" width="100">
                            <br>
                            <button class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="foto_casa_cliente">
                                Remplazar Fotografía
                            </button>
                        <?php else: ?>
                            <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="foto_casa_cliente">
                                Cargar Fotografía
                            </button>
                            </center>
                        <?php endif; ?>
                    </td>
                    <!-- No. Serie ONT -->
                    <td>
                        <?php
                        $No_Serie_ONT = !empty($row['No_Serie_ONT']) ? substr($row['No_Serie_ONT'], 2) : '';
                        if ($No_Serie_ONT):
                        ?>
                            <center>
                            <img src="<?= htmlspecialchars($url_no_serie_ont) . '?v=' . rand() ?>" alt="Foto No. Serie ONT" width="100">
                            <br>
                            <button class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="fotoSerie">
                                Remplazar Fotografía
                            </button>
                        <?php else: ?>
                            <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="fotoSerie">
                                Cargar Fotografía
                            </button>
                            </center>
                        <?php endif; ?>
                    </td>
                    <!-- Foto Puerto -->
                    <td>
                        <?php
                        $fotoPuerto = !empty($row['Foto_Puerto']) ? substr($row['Foto_Puerto'], 2) : '';
                        if ($fotoPuerto):
                        ?>
                            <center>
                            <img src="<?= htmlspecialchars($url_foto_puerto) . '?v=' . rand() ?>" alt="Foto Puerto" width="100">
                            <br>
                            <button class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="foto_puerto">
                                Remplazar Fotografía
                            </button>
                        <?php else: ?>
                            <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="foto_puerto">
                                Cargar Fotografía
                            </button>
                            </center>
                        <?php endif; ?>
                    </td>
                    <!-- Foto INE -->
                    <td>
                        <?php
                        $fotoIne = !empty($row['Foto_INE']) ? substr($row['Foto_INE'], 2) : '';
                        if ($fotoIne):
                        ?>
                            <center>
                            <img src="<?= htmlspecialchars($url_foto_ine) . '?v=' . rand() ?>" alt="Foto INE" width="100">
                            <br>
                            <button class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="foto_INE">
                                Remplazar Fotografía
                            </button>
                        <?php else: ?>
                            <button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCargarFoto"
                                data-folio="<?= htmlspecialchars($row['Folio_Pisa']) ?>"
                                data-tipo="foto_INE">
                                Cargar Fotografía
                            </button>
                            </center>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS y dependencias -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>