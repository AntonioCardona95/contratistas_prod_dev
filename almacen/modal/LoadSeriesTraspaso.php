<?php

    // filepath: c:\Users\Axel Garcia OPS\Desktop\HTDOCS\contratistas_dash\modal\LoadSeries.php
    require_once('../controller/ClassOps.php');
    
    // Validar entradas
    if (empty($_POST['FK_Tecnico_Salida_Det'])) {
        echo '<option value="">NO SALE TECNICO</option>';
        exit;
    }

    $FK_Tecnico_Salida_Det = $_POST['FK_Tecnico_Salida_Det'];
    $FK_Contratista_Salida_Det = $_POST['FK_Contratista_Salida'] ?? '';

    // Instanciar la clase Ops
    $modal = new Ops();

    // Obtener las series disponibles
    $filas = $modal->LoadSeriesTraspasos($FK_Tecnico_Salida_Det, $FK_Contratista_Salida_Det);

    if (!empty($filas)) {
        foreach ($filas as $row) {
            // Filtrar por el contratista correspondiente
            echo '<option value="' . htmlspecialchars($row['Num_Serie_Salida_Det']) . '">' . htmlspecialchars($row['Num_Serie_Salida_Det']) . '</option>';
        }
    } else {
        echo '<option value="">No hay series disponibles</option>';
    }
?>