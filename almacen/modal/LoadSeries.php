<?php
    // filepath: c:\Users\Axel Garcia OPS\Desktop\HTDOCS\contratistas_dash\modal\LoadSeries.php
    require_once('../controller/ClassOps.php');

    // Debug: Mostrar qué datos se están recibiendo
    error_log("POST data: " . print_r($_POST, true));

    // Validar entradas
    if (empty($_POST['FK_Material_Salidas_Contratistas']) || empty($_POST['FK_Contratista_Salida'])) {
        echo '<option value="">Error: Datos incompletos</option>';
        error_log("Error: Datos incompletos en LoadSeries.php");
        exit();
    }

    $material = $_POST['FK_Material_Salidas_Contratistas'];
    $FK_Contratista_Salida = $_POST['FK_Contratista_Salida'];
    
    error_log("Material: $material, Contratista: $FK_Contratista_Salida");

    // Instanciar la clase Ops
    $modal = new Ops();

    // Obtener las series disponibles
    $filas = $modal->LoadSeries($material, $FK_Contratista_Salida);
    
    error_log("Filas obtenidas: " . (is_array($filas) ? count($filas) : 0));

    if (!empty($filas)) {
        $hasResults = false;
        $seriesEncontradas = array();
        foreach ($filas as $row) {
            // Filtrar por el contratista correspondiente
            if ($row['FK_Contratista_Salida'] == $FK_Contratista_Salida) {
                echo '<option value="' . htmlspecialchars($row['Num_Serie_S']) . '">' . htmlspecialchars($row['Num_Serie_S']) . '</option>';
                $hasResults = true;
                $seriesEncontradas[] = $row['Num_Serie_S'];
            }
        }
        
        error_log("Series encontradas para material $material y contratista $FK_Contratista_Salida: " . implode(', ', $seriesEncontradas));
        
        if (!$hasResults) {
            echo '<option value="">No hay series disponibles para este contratista</option>';
        }
    } else {
        echo '<option value="">No hay series disponibles</option>';
    }
?>