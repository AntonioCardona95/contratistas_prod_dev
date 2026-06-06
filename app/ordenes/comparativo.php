<?php
require_once __DIR__ . '/../cnx/cnx.php';

try {
    $cnx = new Conexion();
    $dbRemoto = $cnx->get_conexion();             // puede ser PDO o string
    $dbCoordiapp = $cnx->get_conexioncoordiapp(); // puede ser PDO o string

    // Validar que sean objetos PDO
    if (!($dbRemoto instanceof PDO)) {
        throw new Exception("Error en conexión remota: $dbRemoto");
    }
    if (!($dbCoordiapp instanceof PDO)) {
        throw new Exception("Error en conexión local: $dbCoordiapp");
    }

    // Solo registros de 2026
    $sql = "SELECT * 
            FROM tecnico_instalaciones_coordiapp
            WHERE YEAR(Fecha_Coordiapp) = 2026";

    $stmt1 = $dbRemoto->query($sql);
    $stmt2 = $dbCoordiapp->query($sql);

    $data1 = [];
    foreach ($stmt1->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $data1[$row['Folio_Pisa']] = $row;
    }

    $data2 = [];
    foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $data2[$row['Folio_Pisa']] = $row;
    }

    // Comparación
    $diferencias = [];
    foreach ($data1 as $folio => $registro1) {
        if (!isset($data2[$folio])) {
            $diferencias[$folio][] = [
                "Campo" => "Folio_Pisa",
                "Remoto" => $folio,
                "Local" => "NO EXISTE"
            ];
            continue;
        }
        $registro2 = $data2[$folio];
        foreach ($registro1 as $campo => $valor1) {
            $valor2 = $registro2[$campo];
            if ($valor1 != $valor2) {
                $diferencias[$folio][] = [
                    "Campo" => $campo,
                    "Remoto" => $valor1,
                    "Local" => $valor2
                ];
            }
        }
    }

    // HTML
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Diferencias</title>";
    echo "<style>
            table { border-collapse: collapse; width: 100%; margin-bottom:20px; }
            th, td { border: 1px solid #ccc; padding: 6px; }
            th { background: #eee; }
            h2 { margin-top: 30px; }
          </style></head><body>";

    if (empty($diferencias)) {
        echo "<h2>No se encontraron diferencias en registros de 2026</h2>";
    } else {
        foreach ($diferencias as $folio => $diffs) {
            echo "<h2>Folio: $folio (".count($diffs)." diferencias)</h2>";
            echo "<table><tr><th>Campo</th><th>Remoto</th><th>Local</th></tr>";
            foreach ($diffs as $d) {
                echo "<tr>
                        <td>{$d['Campo']}</td>
                        <td>{$d['Remoto']}</td>
                        <td>{$d['Local']}</td>
                      </tr>";
            }
            echo "</table>";
        }
    }

    echo "</body></html>";

} catch (Exception $e) {
    echo "Error: ".$e->getMessage();
}
?>
