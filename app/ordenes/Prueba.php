<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user = "Analytics_ED";
$pass = "Enlace053715/";
$host = "192.168.1.198";
$db   = "analisis";
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8";

try {
    $conexion = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "✅ Conexión exitosa a la base de datos '$db'<br>";

    // Prueba rápida: obtener las tablas disponibles
    $stmt = $conexion->query("SHOW TABLES");
    $tablas = $stmt->fetchAll();

    echo "Tablas encontradas:<br>";
    foreach ($tablas as $t) {
        echo "- " . implode(", ", $t) . "<br>";
    }

} catch (PDOException $e) {
    die("❌ Error de conexión a la base de datos: " . $e->getMessage());
}
