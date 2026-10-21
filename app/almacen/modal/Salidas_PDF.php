<?php

ini_set('display_errors', '0');
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../controller/ClassSalidas.php';
require_once __DIR__ . '/../../config/ContratistaContexto.php';

$baseDir = realpath(__DIR__ . '/..'); 
$logo_path = $baseDir . '/img/enlace.png';

$main_color = '#0f172a';

$logo_html = '';
if (file_exists($logo_path)) {
    $logo_data = base64_encode(file_get_contents($logo_path));
    $logo_extension = pathinfo($logo_path, PATHINFO_EXTENSION);
    $logo_mime = ($logo_extension === 'png') ? 'image/png' : 'image/jpeg';
    $logo_base64 = 'data:' . $logo_mime . ';base64,' . $logo_data;
    
    $logo_html = '<img src="' . $logo_base64 . '" style="height: 60px; width: auto; margin-bottom: 8px;" alt="Logotipo">';
} else {
    $logo_html = '<div class="logo-text">FOLIO DE SALIDA</div>';
}


try {
    require __DIR__ . '/../../vendor/autoload.php';
    requerirSesionAlmacen();

    ini_set("pcre.backtrack_limit", "10000000");
    ob_start();

    $folio = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$folio || $folio < 1) {
        throw new InvalidArgumentException('El folio de salida no es valido.');
    }

    $modal = new Salidas();
    $idContratista = obtenerIdContratistaContexto();
    if (esAdministradorContratista() && $idContratista === 0) {
        $idContratista = filter_input(INPUT_GET, 'contratista', FILTER_VALIDATE_INT);
    }
    if (!$idContratista || $idContratista < 1) {
        throw new InvalidArgumentException('Debe especificar el contratista de la salida.');
    }

    $encabezados = $modal->ObtenerEncabezadoSalidaPDF($folio, $idContratista);
    if (empty($encabezados)) {
        http_response_code(404);
        throw new RuntimeException('No se encontro la salida solicitada.');
    }

    $datosSalida = $encabezados[0];
    $materialesSalida = $modal->ObtenerMaterialesSalidaPDF($folio, $idContratista);
    $seriesSalida = $modal->ObtenerSeriesSalidaPDF($folio, $idContratista);
    $escape = static function ($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    };
    $fechaTimestamp = !empty($datosSalida['Fecha_T']) ? strtotime($datosSalida['Fecha_T']) : false;
    $fechaSalidaTexto = ($fechaTimestamp !== false) ? date('d/m/Y H:i', $fechaTimestamp) : 'Sin fecha';
    $folioSalidaTexto = $escape($datosSalida['Folio_Salida_T'] ?? $folio);

    $transferenciasAgrupadas = [];
    $devolucionesAgrupadas = [];

    // =========================================================
    // 1. ESTRUCTURA HTML CON CSS EMBEBIDO
    // =========================================================
    $PDF = '<!DOCTYPE html>
        <html lang="es">
        <head>
         <meta charset="utf-8">
        <title>Folio de Salida</title>
        <style>
             body { 
                font-family: Arial, sans-serif; 
                font-size: 10pt; 
                margin: 0; 
                padding: 0;
                line-height: 1.3;
                color: #2c3e50;
            }
            .header-block {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-bottom: 3px solid ' . $main_color . ';
                padding: 12px 10px;
                margin-bottom: 18px;
            }
            .logo-text {
                font-size: 18pt;
                font-weight: bold;
                color: ' . $main_color . ';
                margin-bottom: 8px;
                letter-spacing: 2px;
            }
            .logo-box {
                flex: 0 0 140px;
                text-align: left;
            }
            .title-box h4 {
                font-size: 16pt;
                color: ' . $main_color . '; 
                margin: 0;
                font-weight: bold;
            }
            .title-box {
                flex: 1 1 auto;
                text-align: center;
                padding: 0 10px;
            }
            .date-box {
                flex: 0 0 180px;
                text-align: right;
                font-size: 10pt;
                color: #333;
            }
            .date-box .date-label { font-weight: bold; color: #555; display: block; font-size: 8pt; text-transform: uppercase; }
            .date-box .date-value { font-size: 10pt; }
            .metadata-block, .location-block {
                display: block;
                margin-bottom: 10px;
            }
            .data-group {
                display: block;
                width: 100%;
                margin-bottom: 5px;
                padding: 3px 0;
            }
            .data-group.large-data {
                display: block;
                width: 100%;
                margin-right: 0;
            }
            .data-group .label {
                font-weight: bold;
                color: #555;
                display: block; 
                font-size: 8pt;
                text-transform: uppercase;
            }
            .data-group .value {
                font-size: 10pt;
                border-bottom: 1px dashed #ccc; 
                padding-bottom: 1px;
                white-space: normal;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }
            .primary-value {
                color: ' . $main_color . '; 
                font-size: 14pt;
                font-weight: bold;
            }
            .section-section-title, .section-title {
                font-size: 14pt;
                color: ' . $main_color . '; 
                border-bottom: 2px solid ' . $main_color . ';
                padding-bottom: 3px;
                margin-top: 25px;
                margin-bottom: 10px;
                font-weight: normal;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }
            .data-table th {
                background-color: ' . $main_color . '; 
                color: white;
                font-weight: bold;
                padding: 8px 10px;
                border: 1px solid #e0e0e0;
                text-align: left;
                text-transform: uppercase;
                font-size: 9pt;
            }
            .data-table td {
                padding: 6px 10px;
                border: 1px solid #e0e0e0;
                vertical-align: top;
                font-size: 9pt;
            }
            .series-table th:first-child,
            .series-table td:first-child {
                padding-left: 4px;
                padding-right: 4px;
                text-align: center;
                white-space: nowrap;
            }
            /* Efecto Cebra */
            .data-table tbody tr:nth-child(even) {
                background-color: #f9f9f9; 
            }
            .align-right {
                text-align: right;
                font-weight: bold;
            }
            .align-center {
                text-align: center;
            }
             .location-block .data-group {
                width: 32%; /* 3 columnas */
                float: left;
                margin-right: 1%;
            }
            .footer-sign {
                display: table;
                width: 100%;
                margin-top: 30px;
                table-layout: fixed;
            }
            .signature {
                display: table-cell;
                vertical-align: bottom;
                width: 50%;
                padding-top: 0;
            }
            .signature.left { text-align: left; padding-left: 30px; }
            .signature.right { text-align: right; padding-right: 30px; }
            .signature .line {
                border-bottom: 2px solid #333;
                width: 80%;
                margin: 8px auto 0;
                display: block;
                height: 14px;
            }
            .signature .label-sign {
                font-weight: bold;
                font-size: 8pt;
                color: #555;
                display: block;
                text-align: center;
            }
            .small-note {
                font-size: 7pt;
                color: #888;
                margin-top: 50px;
                text-align: center;
            }
            .no-data-note {
                font-style: italic;
                color: #888;
                margin-top: 15px;
                text-align: center;
                padding: 10px;
                border: 1px dashed #ccc;
            }
        </style>
    </head>
    <body>';

    // 1. CABECERA: LOGO (izq), TÍTULO (centro), FECHA (der)
    $PDF .= '
    <div class="header-block">
        <div class="title-box">
            '. $logo_html .'
            <h4>COMPROBANTE DE SALIDA DE MATERIAL: ' . $folioSalidaTexto . '</h4>
        </div>
        <div class="date-box">
            <span class="date-label">Fecha:</span>
            <span class="date-value">' . $fechaSalidaTexto . '</span>
        </div>
    </div>';

    // 2. METADATOS: DATOS GENERALES
    $PDF .= '
        <div class="metadata-block">
            <div class="data-group">
                <span class="label">CONTRATISTA:</span>
                <span class="value">' . htmlspecialchars(strtoupper((string)($datosSalida['Contratista'] ?? 'N/D')), ENT_QUOTES, 'UTF-8') . '</span>
            </div>
            <div class="data-group large-data">
                <span class="label">TÉCNICO:</span>
                <span class="value">' . htmlspecialchars(strtoupper((string)($datosSalida['Tecnico'] ?? 'N/D')), ENT_QUOTES, 'UTF-8') . '</span>
            </div>
            <div class="data-group">
                <span class="label">FOLIO DE SALIDA:</span>
                <span class="value">' . $folioSalidaTexto . '</span>
            </div>
            <div class="data-group large-data">
                <span class="label">CANTIDAD REGISTRADA:</span>
                <span class="value">' . htmlspecialchars((string)($datosSalida['Cantidad_T'] ?? 'N/D'), ENT_QUOTES, 'UTF-8') . '</span>
            </div>
        </div>
        ';
    
    // 3. TABLA DE MATERIALES: DETALLE 
    $PDF .= '
    <h2 class="section-section-title">MATERIALES ENTREGADOS</h2>
    <table width="100%" class="data-table materials-table">
        <thead>
            <tr>
                <th width="35%">MATERIAL</th>
                <th width="20%">MODELO</th>
                <th width="25%">TECNOLOGÍA</th>
                <th width="20%" class="align-center">CANTIDAD</th>
            </tr>
        </thead>
        <tbody>';

    if (!empty($materialesSalida)) {
        foreach ($materialesSalida as $material) {
            $PDF .= "
            <tr>
                <td>".htmlspecialchars(strtoupper((string)($material['Producto'] ?? '')), ENT_QUOTES, 'UTF-8')."</td>
                <td>".htmlspecialchars((string)($material['Modelo'] ?? ''), ENT_QUOTES, 'UTF-8')."</td>
                <td>".htmlspecialchars((string)($material['Tecnologia'] ?? ''), ENT_QUOTES, 'UTF-8')."</td>
                <td class='align-center'>".htmlspecialchars((string)($material['Cantidad'] ?? ''), ENT_QUOTES, 'UTF-8')."</td>
            </tr>";
        }
    } else {
        $PDF .= '<tr><td colspan="4" class="no-data-note">No hay materiales registrados para este folio.</td></tr>';
    }

    $PDF .= '</tbody></table>';

    // 4. TABLA DE SERIES
    if (!empty($seriesSalida)) {
        $PDF .= '
        <br>
        <h2 class="section-title">NÚMEROS DE SERIE ENTREGADOS</h2>
        <table width="100%" class="data-table series-table">
            <thead>
                <tr>
                    <th width="7%">#</th>
                    <th width="28%">NÚMERO DE SERIE</th>
                    <th width="40%">MATERIAL</th>
                    <th width="25%">MODELO</th>
                </tr>
            </thead>
            <tbody>';

        $contadorSeries = 1;

        foreach ($seriesSalida as $filaSerie) {
            $PDF .= "
            <tr>
                    <td>".$contadorSeries."</td>
                    <td>".htmlspecialchars(strtoupper((string)($filaSerie['Num_Serie_Salida_Det'] ?? '')), ENT_QUOTES, 'UTF-8')."</td>
                    <td>".htmlspecialchars(strtoupper((string)($filaSerie['Producto'] ?? '')), ENT_QUOTES, 'UTF-8')."</td>
                    <td>".htmlspecialchars((string)($filaSerie['Modelo'] ?? ''), ENT_QUOTES, 'UTF-8')."</td>
            </tr>";
            $contadorSeries++;
        }

        $PDF .= '</tbody></table>';
    } else {
        $PDF .= '<p class="no-data-note">Este folio no tiene números de serie asociados.</p>';
    }

    // 5. SECCIÓN DE TRANSFERENCIAS REALIZADAS
    if (!empty($transferenciasAgrupadas)) {
        foreach ($transferenciasAgrupadas as $transferencia) {
            $fechaTransferenciaTexto = !empty($transferencia['fecha']) ? htmlspecialchars($transferencia['fecha'], ENT_QUOTES, 'UTF-8') : 'N/A';
            $folioTransferenciaTexto = htmlspecialchars($transferencia['folio'], ENT_QUOTES, 'UTF-8');
            $contratistaDestinoTexto = htmlspecialchars(strtoupper($transferencia['contratista_destino']), ENT_QUOTES, 'UTF-8');
            $totalPiezasTransferidas = count($transferencia['series']);

            $PDF .= '
            <div style="page-break-before: always; margin-top: 20px;">
                <div class="header-block">
                    <div class="title-box">
                        '. $logo_html .'
                        <h4>VALE DE TRANSFERENCIA: ' . $folioTransferenciaTexto . '</h4>
                    </div>
                    <div class="date-box">
                        <span class="date-label">Fecha:</span>
                        <span class="date-value">' . $fechaTransferenciaTexto . '</span>
                    </div>
                </div>

                <div class="metadata-block">';

            if (!empty($transferencia['tipo_transferencia'])) {
                $PDF .= '
                    <div class="data-group large-data">
                        <span class="label">TIPO DE TRANSFERENCIA:</span>
                        <span class="value">' . htmlspecialchars(strtoupper($transferencia['tipo_transferencia']), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>';
            }

            if (!empty($transferencia['motivo'])) {
                $PDF .= '
                    <div class="data-group large-data">
                        <span class="label">MOTIVO:</span>
                        <span class="value">' . htmlspecialchars(strtoupper($transferencia['motivo']), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>';
            }

            // Determinar la etiqueta del destino según el tipo de transferencia
            $esContraCope = (strtolower(trim($transferencia['tipo_transferencia'])) === 'contratista-cope');
            $etiquetaDestino = $esContraCope ? 'COPE DESTINO' : 'CONTRATISTA DESTINO';

            $PDF .= '
                    <div class="data-group">
                        <span class="label">UNIDAD NEGOCIO:</span>
                        <span class="value">' . htmlspecialchars(strtoupper(obtenerValorSalida($datosSalida, 'Unidad_Negocio')), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="data-group">
                        <span class="label">ALMACÉN ORIGEN:</span>
                        <span class="value">' . htmlspecialchars(strtoupper(obtenerValorSalida($datosSalida, 'Almacen')), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="data-group large-data">
                        <span class="label">CONTRATISTA ORIGEN:</span>
                        <span class="value">' . htmlspecialchars(strtoupper(obtenerValorSalida($datosSalida, 'Contratista')), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="data-group large-data">
                        <span class="label">' . $etiquetaDestino . ':</span>
                        <span class="value">' . $contratistaDestinoTexto . '</span>
                    </div>
                    <div class="data-group">
                        <span class="label">TOTAL PIEZAS:</span>
                        <span class="value">' . $totalPiezasTransferidas . '</span>
                    </div>
                    <div class="data-group">
                        <span class="label">TOTAL METROS:</span>
                        <span class="value">0</span>
                    </div>
                </div>

                <h2 class="section-section-title">DETALLE DE MATERIAL</h2>
                <table width="100%" class="data-table materials-table">
                    <thead>
                        <tr>
                            <th width="39%">MATERIAL</th>
                            <th width="12%">MODELO</th>
                            <th width="13%" class="align-center">CANTIDAD CERRADA</th>
                            <th width="12%" class="align-center">PIEZAS SUELTAS</th>
                            <th width="12%" class="align-center">TOTAL PIEZAS</th>
                            <th width="12%" class="align-center">TOTAL METROS</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($transferencia['materiales'] as $materialTransferido) {
                $PDF .= "
                        <tr>
                            <td>" . htmlspecialchars(strtoupper($materialTransferido['Producto']), ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($materialTransferido['Modelo'], ENT_QUOTES, 'UTF-8') . "</td>
                            <td class='align-center'>" . number_format($materialTransferido['Total_Piezas']) . "</td>
                            <td class='align-center'>0</td>
                            <td class='align-center'>" . number_format($materialTransferido['Total_Piezas']) . "</td>
                            <td class='align-center'>0</td>
                        </tr>";
            }

            $PDF .= '
                    </tbody>
                </table>

                <br>
                <h2 class="section-title">ONTS TRANSFERIDAS</h2>
                <table width="100%" class="data-table series-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="35%">NÚMERO DE SERIE</th>
                            <th width="35%">MATERIAL</th>
                            <th width="25%">SKU</th>
                        </tr>
                    </thead>
                    <tbody>';

            $contadorTransferidas = 1;
            foreach ($transferencia['series'] as $serieTransferida) {
                $PDF .= "
                        <tr>
                            <td>" . $contadorTransferidas . "</td>
                            <td>" . htmlspecialchars(strtoupper($serieTransferida['Num_Serie_S']), ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars(strtoupper($serieTransferida['Producto']), ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($serieTransferida['Modelo'], ENT_QUOTES, 'UTF-8') . "</td>
                        </tr>";
                $contadorTransferidas++;
            }

            $PDF .= '
                    </tbody>
                </table>
            </div>';
        }
    }

    // ANEXOS DE LOS COMPROBANTES DE DEVOLUCION
    if (!empty($devolucionesAgrupadas)) {
        foreach ($devolucionesAgrupadas as $folioDevolucion => $devolucion) {
            $datosDevolucion = $devolucion['datos'];
            $fechaDevolucionTexto = htmlspecialchars((string)($datosDevolucion['Fecha_Dev_T'] ?? 'N/A'), ENT_QUOTES, 'UTF-8');
            $folioDevolucionTexto = htmlspecialchars((string)$folioDevolucion, ENT_QUOTES, 'UTF-8');
            $totalSeriesDevueltas = count($devolucion['series']);

            $PDF .= '
            <div style="page-break-before: always; margin-top: 20px;">
                <div class="header-block">
                    <div class="title-box">
                        '. $logo_html .'
                        <h4>COMPROBANTE DE DEVOLUCIÓN: ' . $folioDevolucionTexto . '</h4>
                    </div>
                    <div class="date-box">
                        <span class="date-label">Fecha:</span>
                        <span class="date-value">' . $fechaDevolucionTexto . '</span>
                    </div>
                </div>

                <div class="metadata-block">
                    <div class="data-group">
                        <span class="label">TIPO DE MOVIMIENTO:</span>
                        <span class="value">' . htmlspecialchars(strtoupper((string)($datosDevolucion['Tipo_MovD_T'] ?? 'N/A')), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="data-group large-data">
                        <span class="label">CONTRATISTA ORIGEN:</span>
                        <span class="value">' . htmlspecialchars(strtoupper((string)($datosDevolucion['Contratista'] ?? 'N/A')), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="data-group">
                        <span class="label">ALMACÉN DESTINO:</span>
                        <span class="value">' . htmlspecialchars(strtoupper((string)($datosDevolucion['Almacen'] ?? 'N/A')), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="data-group large-data">
                        <span class="label">USUARIO REGISTRO:</span>
                        <span class="value">' . htmlspecialchars(strtoupper((string)($datosDevolucion['Nombre'] ?? 'N/A')), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="data-group">
                        <span class="label">SOLICITANTE:</span>
                        <span class="value">' . htmlspecialchars(strtoupper((string)($datosDevolucion['Solicitante_Dev'] ?? 'N/A')), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="data-group large-data">
                        <span class="label">MOTIVO:</span>
                        <span class="value">' . htmlspecialchars(strtoupper((string)($datosDevolucion['Motivo_Dev'] ?? 'N/A')), ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="data-group">
                        <span class="label">TOTAL ONTS DEVUELTAS:</span>
                        <span class="value">' . number_format($totalSeriesDevueltas) . '</span>
                    </div>
                </div>

                <h2 class="section-section-title">DETALLE DE MATERIAL DEVUELTO</h2>
                <table width="100%" class="data-table materials-table">
                    <thead>
                        <tr>
                            <th width="39%">MATERIAL</th>
                            <th width="12%">MODELO</th>
                            <th width="13%" class="align-center">CANTIDAD DEVUELTA</th>
                            <th width="12%" class="align-center">PIEZAS SUELTAS</th>
                            <th width="12%" class="align-center">TOTAL PIEZAS</th>
                            <th width="12%" class="align-center">TOTAL METROS</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($devolucion['materiales'] as $materialDevuelto) {
                $PDF .= "
                        <tr>
                            <td>" . htmlspecialchars(strtoupper((string)($materialDevuelto['Producto'] ?? '')), ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars((string)($materialDevuelto['Modelo'] ?? ''), ENT_QUOTES, 'UTF-8') . "</td>
                            <td class='align-center'>" . number_format((float)($materialDevuelto['Cantidad_Dev_Det'] ?? 0)) . "</td>
                            <td class='align-center'>" . number_format((float)($materialDevuelto['Piezas_Sueltas'] ?? 0)) . "</td>
                            <td class='align-center'>" . number_format((float)($materialDevuelto['Total_Piezas'] ?? 0)) . "</td>
                            <td class='align-center'>" . number_format((float)($materialDevuelto['Total_Metros'] ?? 0)) . "</td>
                        </tr>";
            }

            $PDF .= '
                    </tbody>
                </table>

                <br>
                <h2 class="section-title">ONTS DEVUELTAS</h2>
                <table width="100%" class="data-table series-table">
                    <thead>
                        <tr>
                            <th width="7%">#</th>
                            <th width="25%">NÚMERO DE SERIE</th>
                            <th width="43%">MATERIAL</th>
                            <th width="25%">SKU</th>
                        </tr>
                    </thead>
                    <tbody>';

            $contadorDevolucion = 1;
            foreach ($devolucion['series'] as $serieDevuelta) {
                $PDF .= "
                        <tr>
                            <td>" . $contadorDevolucion . "</td>
                            <td>" . htmlspecialchars(strtoupper((string)($serieDevuelta['Num_SerieDev'] ?? '')), ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars(strtoupper((string)($serieDevuelta['Producto'] ?? '')), ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars((string)($serieDevuelta['Modelo'] ?? ''), ENT_QUOTES, 'UTF-8') . "</td>
                        </tr>";
                $contadorDevolucion++;
            }

            $PDF .= '
                    </tbody>
                </table>
            </div>';
        }
    }

    $PDF .= '</body></html>';

    // ----------------------------------------------------
    // 5. INICIALIZACIÓN DE Mpdf Y SALIDA
    // ----------------------------------------------------
    $mpdf = new \Mpdf\Mpdf([
        'tempDir' => sys_get_temp_dir(),
        // No necesitamos basepath ya que la imagen está en base64 y el CSS está en línea.
    ]);
    
    // Pie de página de mPDF (Usaremos el HTML original de firma)

    $mpdf->setFooter("<H6>ENTREGADO POR: _______________________________________________________ RECIBIDO POR: _______________________________________________________ Fecha: __________________________</H6>");
    
    $mpdf->showImageErrors = true;
    
    // Ya no cargamos CSS externo
    
    $mpdf->writeHTML($PDF);
    
    if (ob_get_length() > 0) {
        ob_end_clean();
    }

    // 'D' => force download. Use Output with filename to set download name.
    $mpdf->Output();
    exit;
} catch (\Throwable $errorExcepcion) {
    // Limpiar buffers para evitar salida parcial HTML
    if (ob_get_length() > 0) {
        @ob_end_clean();
    }

    // Responder con código 500 y mostrar detalles completos (útil para depuración)
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');

    $mensajeError = "Se produjo un error al generar el PDF:\n";
    $mensajeError .= "Mensaje: " . $errorExcepcion->getMessage() . "\n";
    $mensajeError .= "Archivo: " . $errorExcepcion->getFile() . "\n";
    $mensajeError .= "Línea: " . $errorExcepcion->getLine() . "\n\n";
    $mensajeError .= "Traza:\n" . $errorExcepcion->getTraceAsString() . "\n";

    // Si existe excepción previa, mostrarla también
    $excepcionPrevia = $errorExcepcion->getPrevious();
    while ($excepcionPrevia) {
        $mensajeError .= "\n-> Excepción previa: " . $excepcionPrevia->getMessage() . " en " . $excepcionPrevia->getFile() . ":" . $excepcionPrevia->getLine() . "\n";
        $mensajeError .= $excepcionPrevia->getTraceAsString() . "\n";
        $excepcionPrevia = $excepcionPrevia->getPrevious();
    }

    echo $mensajeError;
    // En entornos de producción quitar la línea anterior y usar un mensaje genérico
}
?>