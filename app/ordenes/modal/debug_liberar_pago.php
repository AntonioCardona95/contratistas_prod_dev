<?php
/*ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// 🔹 Solo depuración, no se importa ClassNomina ni se hace inserción

$idContratista = $_SESSION['idContratistas'] ?? null;
$emulador      = $_POST['emulador'] ?? null;
$semana        = $_POST['semana'] ?? null;
$tecnologia    = $_POST['tecnologia'] ?? null;

// Decodificar las filas enviadas desde JS
$filas = json_decode($_POST['filas'], true);

// 🔹 Guardar en log del servidor por si quieres revisar
error_log('DEBUG $filas: ' . print_r($filas, true));

// 🔹 Devolver JSON incluyendo todo lo recibido
echo json_encode([
    'debug' => true,
    'emulador'      => $emulador,
    'semana'        => $semana,
    'tecnologia'    => $tecnologia,
    'idContratista' => $idContratista,
    'filas'         => $filas
]); */


/*ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once("../controller/ClassNomina.php");

$nomina = new Nomina();

$idContratista = $_SESSION['idContratistas'];
$emulador      = $_POST['emulador']; // "FIBRA"
$semana        = $_POST['semana'];   // "51"
$tecnologia    = $_POST['tecnologia'] ?? null;

$filas = json_decode($_POST['filas'], true);



$resultado = $nomina->insertarNomina(
    $emulador,       // nombre del emulador
    $semana,
    $tecnologia,
    $idContratista,
    $filas
);

echo json_encode($resultado); */

/*ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once("../controller/ClassNomina.php");

$idContratista = $_SESSION['idContratistas'];
$emulador      = $_POST['emulador'];
$semana        = $_POST['semana'];
$tecnologia = $_POST['tecnologia'] ?? null;

$filas = json_decode($_POST['filas'], true);

// 1️⃣ Crear folioPaco
$folioPaco = $emulador . "_" . $semana;

// 2️⃣ Obtener idEmulador (si ya lo tienes directo, usa ese)
$idEmulador = 1; // AJUSTA según tu tabla emulador

// 3️⃣ Insertar
$nomina = new Nomina();
$folioPaco = $emulador . '_' . $semana;

$resultado = $nomina->insertarNomina(
    $folioPaco,
    $semana,
    $tecnologia,
    $idContratista,
    $filas
);
if ($resultado === true) {
    echo json_encode([
        'status' => 'ok',
        'folioPaco' => $folioPaco,
        'total' => count($filas)
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'mensaje' => $resultado
    ]);
} */





/*ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

echo "<pre style='font-size:13px;'>";

// ===============================
// SESSION
// ===============================
echo "================ SESSION ================\n";
var_dump($_SESSION);

// ===============================
// POST COMPLETO
// ===============================
echo "\n================ POST RAW ================\n";
var_dump($_POST);

// ===============================
// VARIABLES PRINCIPALES
// ===============================
$idContratistas = $_SESSION['idContratistas'] ?? null;
$emulador   = $_POST['emulador'] ?? null;
$semana     = $_POST['semana'] ?? null;
$tecnologia = $_POST['tecnologia'] ?? null;

echo "\n=========== VARIABLES FINALES ===========\n";
var_dump(compact('idContratistas','emulador','semana','tecnologia'));

// ===============================
// FILAS DE LA TABLA
// ===============================
echo "\n=========== FILAS DE DATATABLE ===========\n";

if (!empty($_POST['filas'])) {

    $filas = json_decode($_POST['filas'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ ERROR JSON: " . json_last_error_msg();
        exit;
    }

    echo "TOTAL FILAS RECIBIDAS: " . count($filas) . "\n\n";

    foreach ($filas as $index => $fila) {

        echo "----------- FILA #" . ($index + 1) . " -----------\n";

        foreach ($fila as $colIndex => $valor) {
            echo "[$colIndex] => ";
            var_dump($valor);
        }

        echo "\n";
    }

} else {
    echo "❌ NO LLEGÓ EL ARRAY 'filas'\n";
}

echo "</pre>"; */
/*ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

echo "<pre>";

echo "SESSION:\n";
var_dump($_SESSION);

echo "\nPOST:\n";
var_dump($_POST);

$idContratistas = $_SESSION['idContratistas'] ?? null;
$emulador   = $_POST['emulador'] ?? null;
$semana     = $_POST['semana'] ?? null;
$tecnologia = $_POST['tecnologia'] ?? null;

echo "\nVARIABLES FINALES:\n";
var_dump(compact('idContratistas','emulador','semana','tecnologia'));

echo "</pre>"; */
