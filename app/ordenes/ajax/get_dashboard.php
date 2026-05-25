<?php

// DEPURACION A FONDO 
require_once("../controller/ClassProduccion.php");

header('Content-Type: application/json; charset=utf-8');

try {
    $prod = new Produccion();

    // Fechas que llegan del front
    $fechaInicio = $_GET['fecha_inicio'] ?? null;
    $fechaFin    = $_GET['fecha_fin'] ?? null;

    $debug = [
        "fecha_inicio_recibida" => $fechaInicio,
        "fecha_fin_recibida"    => $fechaFin,
    ];

    // Conexión
    try {
        $conexion = $prod->get_conexion_analisis_erp();
        $info = $conexion->query("SELECT DATABASE() as db, USER() as usuario")->fetch();
        $debug["conexion_ok"] = true;
        $debug["conexion_info"] = $info;

        // Listado de tablas
        $tablas = $conexion->query("SHOW TABLES")->fetchAll();
        $debug["tablas_disponibles"] = $tablas;
    } catch (Exception $e) {
        echo json_encode([
            "error" => true,
            "mensaje" => "Error de conexión: " . $e->getMessage(),
            "debug" => $debug
        ]);
        exit;
    }

    // SQL de prueba sin filtros
    $sqlTest = "SELECT COUNT(1) AS total FROM seg_ns_prod_eval";
    try {
        $resTest = $conexion->query($sqlTest)->fetchAll();
        $debug["conteo_total_tabla"] = $resTest;
    } catch (Exception $e) {
        $debug["conteo_total_tabla_error"] = $e->getMessage();
    }

    // SQL con filtros
    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $sql = "SELECT COUNT(1) AS total
                FROM seg_ns_prod_eval
                WHERE FECHA_LIQD BETWEEN '$fechaInicio 00:00:00' AND '$fechaFin 23:59:59'";
    } else {
        $sql = $sqlTest;
    }

    try {
        $resFiltro = $conexion->query($sql)->fetchAll();
        $debug["sql_usado"] = $sql;
        $debug["resultado_sql"] = $resFiltro;
    } catch (Exception $e) {
        $debug["sql_usado"] = $sql;
        $debug["resultado_sql_error"] = $e->getMessage();
    }

    // Funciones normales
    $totalTac       = $prod->getTotalTac($fechaInicio, $fechaFin);
    $totalAlertas   = $prod->getTotalAlertas($fechaInicio, $fechaFin);
    $totalCorrectas = $prod->getTotalCorrectas($fechaInicio, $fechaFin);

    echo json_encode([
        "total_tac"   => $totalTac,
        "alertas"     => $totalAlertas,
        "correctas"   => $totalCorrectas,
        "debug"       => $debug
    ]);

} catch (Exception $e) {
    echo json_encode([
        "error" => true,
        "mensaje" => $e->getMessage()
    ]);
}

/*require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

// Usa los mismos nombres que manda el JS
$fechaInicio = $_GET['fecha_inicio'] ?? null;
$fechaFin    = $_GET['fecha_fin'] ?? null;

$totalTac     = $prod->getTotalTac($fechaInicio, $fechaFin);
$totalAlertas = $prod->getTotalAlertas($fechaInicio, $fechaFin);
$totalCorrectas = $prod->getTotalCorrectas($fechaInicio, $fechaFin);

// Asegúrate de que SOLO se imprima JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    "total_tac" => $totalTac,
    "alertas"   => $totalAlertas,
    "correctas" => $totalCorrectas
]); */




/*require_once("../controller/ClassProduccion.php");

$prod = new Produccion();

$inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fin    = $_GET['fecha_fin'] ?? date('Y-m-d');

$totalTac = $prod->getTotalTac($inicio, $fin);

echo json_encode([
    "total_tac" => $totalTac
]); */