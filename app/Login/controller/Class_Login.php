<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');

    /**
     * DESARROLLADO ING.ANTONIO CARDONA
     */

    require_once(__DIR__ . '/../../cnx/cnx.php');

    class Login extends Conexion
    {
        public function get_user($User_Contratista)
        {
            try {
                $rows = [];
                $conexion = $this->get_conexion();
                
                // 🔍 PRUEBA 1: Verificar si la conexión se estableció bien
                if (!$conexion) {
                    echo "<div style='background:red; color:white; padding:15px; font-family:sans-serif;'>";
                    echo "<h3>ERROR CRÍTICO:</h3> No hay conexión a la base de datos.";
                    echo "</div>";
                    exit;
                }

                $sql = "SELECT * FROM contratistas WHERE User_Contratista = :User_Contratista";
                $statement = $conexion->prepare($sql);
                $statement->bindParam(":User_Contratista", $User_Contratista, PDO::PARAM_STR);
                $statement->execute();
                
                while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
                    $rows[] = $result;
                }

                // 🔍 PRUEBA 2: Imprimir en pantalla qué está pasando exactamente con la búsqueda
                echo "<div style='background:#f4f4f4; border:2px solid #333; padding:20px; margin:20px; font-family:monospace; color:#000;'>";
                echo "<h2 style='color:#d9534f; margin-top:0;'>🔍 DEPURACIÓN DE LOGIN (MODO PRUEBA):</h2>";
                echo "<strong>Usuario que intentaste buscar:</strong> " . htmlspecialchars($User_Contratista) . "<br>";
                echo "<strong>Total de registros encontrados en la BD:</strong> " . count($rows) . "<br><br>";
                echo "<strong>Resultado crudo que devuelve la consulta:</strong><br>";
                echo "<pre style='background:#fff; padding:10px; border:1px solid #ccc;'>";
                print_r($rows);
                echo "</pre>";
                echo "<p style='color:blue; font-weight:bold;'>👉 La ejecución se detuvo aquí para que puedas ver esta información. Comenta la línea 'exit;' en el código cuando termines de probar.</p>";
                echo "</div>";
                
                exit; // <-- Detiene el flujo para que puedas ver el resultado en pantalla

                return $rows;

            } catch (Exception $e) {
                // 🔍 PRUEBA 3: Si ocurre un error en la consulta SQL o PDO, lo imprimirá en pantalla
                echo "<div style='background:darkred; color:white; padding:20px; font-family:sans-serif;'>";
                echo "<h3>❌ ERROR EN LA CONSULTA SQL:</h3>";
                echo $e->getMessage();
                echo "</div>";
                exit;
                
                return [];
            }
        }   
    }
?>