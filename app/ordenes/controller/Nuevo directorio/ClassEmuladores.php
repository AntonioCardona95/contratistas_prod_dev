<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    /**
     * DESARROLLADO ING. ANTONIO CARDONA
     */
    require_once($_SERVER['DOCUMENT_ROOT']."/cnx/cnx.php");

    class Contratistas extends Conexion

    {
        public function getNombreEmulador()
        {
            $rows = [];
            $conexion = $this->get_conexion();
            $sql = "SELECT DISTINCT emulador FROM emuladores_nueva";
            $statement = $conexion->prepare($sql);
            $statement->execute();
            while ($result = $statement->fetch(PDO::FETCH_ASSOC)) 
        {
            $rows[] = $result;
        }

        return $rows;

        }

        public function getIdEmulador($emulador, $tecnologia)
{
    $rows = [];
    $conexion = $this->get_conexion();

    // Consulta con WHERE que incluye ambas condiciones
    $sql = "SELECT tipoemulador FROM emuladores_nueva WHERE emulador = :emulador AND tecnologia = :tecnologia";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':emulador', $emulador, PDO::PARAM_STR);
    $statement->bindParam(':tecnologia', $tecnologia, PDO::PARAM_STR);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}

        

        public function getTecnologiaEmulador()
        {
            $rows = [];
            $conexion = $this->get_conexion();
            $sql = "SELECT DISTINCT tecnologia FROM emuladores_nueva";
            $statement = $conexion->prepare($sql);
            $statement->execute();
            while ($result = $statement->fetch(PDO::FETCH_ASSOC)) 
        {
            $rows[] = $result;
        }

        return $rows;

        }

        public function getTecnicosYExpedientesPorContratista($nombreContratista)
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "SELECT DISTINCT 
                t.NExpediente,
                t.Nombre_T,
                t.Apellidos_T
            FROM tecnicos t
            INNER JOIN contratistas c ON c.idContratistas = t.FK_Contratista_Tecnico
            WHERE CONCAT_WS(' ', c.Contratista, c.apellido_paterno, c.apellido_materno) = ?
              AND t.NExpediente IS NOT NULL
            ORDER BY t.NExpediente ASC";

    $statement = $conexion->prepare($sql);
    $statement->execute([$nombreContratista]);

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = [
            'NExpediente' => $result['NExpediente'],
            'Nombre_Tecnico' => $result['Nombre_T'],
            'Apellidos_Tecnico' => $result['Apellidos_T']
        ];
    }

    return $rows;
}

/* public function getDatosEmuladoresFiltrados($tecnologia, $semana, $nombreContratista, $filtros = [])
{
    $conexion = $this->get_conexion();
    $conexionAnalytics = $this->get_conexion_analytics();

    $tecnicosExpedientesData = [];
    if (!empty($nombreContratista)) {
        $tecnicosExpedientesData = $this->getTecnicosYExpedientesPorContratista($nombreContratista);
    }

    $nExpedientes = array_column($tecnicosExpedientesData, 'NExpediente');

    $sql = "SELECT 
        tinst.Folio_Pisa, 
        tinst.Telefono, 
        tinst.Tipo_Tarea,
        cp.COPE,
        tinst.Metraje,
        tinst.Tipo_Instalacion,
        tinst.Direccion_Cliente,
        tinst.terminal,
        tinst.puerto,
        UPPER(CONCAT_WS(' ', tinst.Cliente_Titular, tinst.Apellido_Paterno_Titular, tinst.Apellido_Materno_Titular)) AS Nombre_Cliente,
        UPPER(CONCAT_WS(' ', t.Nombre_T, t.Apellidos_T)) AS Nombre_Tecnico,
        tinst.distrito AS Distrito_CoordiApp,
        tinst.Ont AS Num_Serie_CoordiApp,
        tinst.Latitud AS Latitud_CoordiApp,
        tinst.Longitud AS Longitud_CoordiApp,
        tinst.Fecha_Coordiapp
    FROM tecnico_instalaciones_coordiapp tinst
    INNER JOIN tecnicos t ON t.idTecnico = tinst.FK_Tecnico_apps
    INNER JOIN contratistas c ON c.idContratistas = t.FK_Contratista_Tecnico
    LEFT JOIN copes cp ON cp.id = tinst.FK_Cope
    WHERE 1=1";

    $params = [];
    $conditions = [];

    if (!empty($tecnologia)) {
        $conditions[] = "tinst.Tecnologia = ?";
        $params[] = $tecnologia;
    }

    if (!empty($semana)) {
        $conditions[] = "WEEK(tinst.Fecha_Coordiapp, 1) = ?";
        $params[] = $semana;
    }

    if (!empty($nombreContratista)) {
        $conditions[] = "CONCAT_WS(' ', c.Contratista, c.apellido_paterno, c.apellido_materno) = ?";
        $params[] = $nombreContratista;
    }

    if (!empty($filtros['prefijos'])) {
        $likeClauses = [];
        foreach ($filtros['prefijos'] as $prefijo) {
            $likeClauses[] = "tinst.Tipo_Tarea LIKE ?";
            $params[] = $prefijo . '%';
        }
        $conditions[] = "(" . implode(" OR ", $likeClauses) . ")";
    }

    if (!empty($filtros['excluir_prefijos'])) {
        foreach ($filtros['excluir_prefijos'] as $excluido) {
            $conditions[] = "tinst.Tipo_Tarea NOT LIKE ?";
            $params[] = $excluido . '%';
        }
    }

    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    $statement = $conexion->prepare($sql);
    $statement->execute($params);

    $datos = [];

    while ($fila = $statement->fetch(PDO::FETCH_ASSOC)) {
        // DEBUG: Mostrar fila de CoordiApp (incluyendo números de serie)
        echo "<pre>DEBUG COORDIAPP:\n";
        var_dump($fila);
        echo "</pre>";

        $folio = $fila['Folio_Pisa'];
        $telefono = $fila['Telefono'];

        $fila['Origen_CoordiApp'] = true;
        $fila['Origen_TAC'] = false;

        $fila['Telefono_CoordiApp'] = !empty($telefono);
        $fila['Telefono_TAC'] = false;

        $fila['Telefono_CoordiApp_Valor'] = $telefono;
        $fila['Telefono_TAC_Valor'] = null;
        $fila['Tipo_tarea_TAC_Valor'] = null;

        $fila['COPE_CoordiApp_Valor'] = $fila['COPE'];
        $fila['COPE_TAC_Valor'] = null;

        $fila['Num_Serie_CoordiApp_Valor'] = $fila['Num_Serie_CoordiApp'];

        $fila['Nombre_Tecnico_CoordiApp'] = $fila['Nombre_Tecnico'];

        $fila['Latitud_CoordiApp_Valor'] = $fila['Latitud_CoordiApp'];
        $fila['Longitud_CoordiApp_Valor'] = $fila['Longitud_CoordiApp'];

        $fila['Latitud_TAC_Valor'] = null;
        $fila['Longitud_TAC_Valor'] = null;

        $fila['Fecha_Coordiapp_Valor'] = $fila['Fecha_Coordiapp'] ?? null;
        $fila['Fecha_LIQ_TAC_Valor'] = null;

        $fila['Alertas'] = [];
        $fila['Calificador'] = true;

        $fila['Expediente'] = null;

        $datos[$folio] = $fila;
    }

    $tacData = $this->obtenerDatosTacBolsa($conexionAnalytics, $nExpedientes, $semana, $tecnologia, $filtros);

    // DEBUG: Mostrar datos TAC completos
    echo "<pre>DEBUG DATOS TAC OBTENIDOS:\n";
    var_dump($tacData);
    echo "</pre>";

    foreach ($tacData as $tacRow) {
        // DEBUG: Mostrar fila TAC individual
        echo "<pre>DEBUG FILA TAC:\n";
        var_dump($tacRow);
        echo "</pre>";

        $folio = $tacRow['Folio_Pisa'];
        $telefonoTac = $tacRow['Telefono'] ?? null;
        $tipoTareaTac = $tacRow['Tipo_tarea'] ?? null;
        $copeTac = $tacRow['NOM_CT'] ?? null;
        $numSerieTac = $tacRow['Num__Serie'] ?? null;
        $nombreTecnicoTac = $tacRow['Tecnico'] ?? null;

        $latTac = $tacRow['Latitud'] ?? null;
        $longTac = $tacRow['Longitud'] ?? null;

        $expedienteTac = $tacRow['Expediente'] ?? null;

        if (isset($datos[$folio])) {
            $datos[$folio]['Origen_TAC'] = true;
            $datos[$folio]['Telefono_TAC'] = !empty($telefonoTac);
            $datos[$folio]['Telefono_TAC_Valor'] = $telefonoTac;
            $datos[$folio]['Tipo_tarea_TAC_Valor'] = $tipoTareaTac;
            $datos[$folio]['COPE_TAC_Valor'] = $copeTac;

            $datos[$folio]['Num_Serie_TAC_Valor'] = $numSerieTac;
            $datos[$folio]['Nombre_Tecnico_TAC'] = $nombreTecnicoTac;

            $datos[$folio]['Latitud_TAC_Valor'] = $latTac;
            $datos[$folio]['Longitud_TAC_Valor'] = $longTac;

            $datos[$folio]['Expediente'] = $expedienteTac;
            $datos[$folio]['Fecha_LIQ_TAC_Valor'] = $tacRow['FECHA_LIQ'] ?? null;
        } else {
            $tacRow += [
                'Telefono' => '',
                'Tipo_Tarea' => '',
                'COPE' => '',
                'Metraje' => '',
                'Tipo_Instalacion' => '',
                'Direccion_Cliente' => '',
                'Nombre_Cliente' => '',
                'Nombre_Tecnico' => '',
            ];
            $tacRow['Origen_CoordiApp'] = false;
            $tacRow['Origen_TAC'] = true;
            $tacRow['Telefono_CoordiApp'] = false;
            $tacRow['Telefono_TAC'] = !empty($telefonoTac);

            $tacRow['Telefono_CoordiApp_Valor'] = null;
            $tacRow['Telefono_TAC_Valor'] = $telefonoTac;
            $tacRow['Tipo_tarea_TAC_Valor'] = $tipoTareaTac;

            $tacRow['COPE_CoordiApp_Valor'] = null;
            $tacRow['COPE_TAC_Valor'] = $copeTac;

            $tacRow['Num_Serie_CoordiApp_Valor'] = null;
            $tacRow['Num_Serie_TAC_Valor'] = $numSerieTac;

            $tacRow['Nombre_Tecnico_CoordiApp'] = null;
            $tacRow['Nombre_Tecnico_TAC'] = $nombreTecnicoTac;

            $tacRow['Latitud_TAC_Valor'] = $latTac;
            $tacRow['Longitud_TAC_Valor'] = $longTac;

            $tacRow['Fecha_Coordiapp_Valor'] = null;
            $tacRow['Fecha_LIQ_TAC_Valor'] = $tacRow['FECHA_LIQ'] ?? null;

            $tacRow['Expediente'] = $expedienteTac;

            $tacRow['Alertas'] = [];
            $tacRow['Calificador'] = true;

            $datos[$folio] = $tacRow;
        }
    }

    foreach ($datos as &$fila) {
        if (empty($fila['Origen_TAC'])) {
            $fila['Alertas'][] = 'ORDEN_NO_EN_TAC';
            $fila['Calificador'] = false;
            continue;
        }

        $copeCoordi = trim($fila['COPE_CoordiApp_Valor'] ?? '');
        $copeTac = trim($fila['COPE_TAC_Valor'] ?? '');

        if ($copeCoordi === '') {
            $fila['Alertas'][] = 'COPE_NO_CAPTURADO';
            $fila['Calificador'] = false;
        }

        $telCoordi = preg_replace('/\D/', '', $fila['Telefono_CoordiApp_Valor'] ?? '');
        $telTac = preg_replace('/\D/', '', $fila['Telefono_TAC_Valor'] ?? '');

        $fila['TelefonosDiferentes'] = (!empty($telCoordi) && !empty($telTac) && $telCoordi !== $telTac);

        if ($fila['TelefonosDiferentes']) {
            $fila['Alertas'][] = 'TEL_DIFERENTE';
            $fila['Calificador'] = false;
        }

        $tipoCoordi = trim($fila['Tipo_Tarea'] ?? '');
        $tipoTac = trim($fila['Tipo_tarea_TAC_Valor'] ?? '');

        $fila['TipoTareaDiferente'] = (!empty($tipoCoordi) && !empty($tipoTac) && strcasecmp($tipoCoordi, $tipoTac) !== 0);

        if ($fila['TipoTareaDiferente']) {
            $fila['Alertas'][] = 'TIPO_TAREA_NO_COINCIDE';
            $fila['Calificador'] = false;
        }

        $fila['CopesDiferentes'] = (!empty($copeCoordi) && !empty($copeTac) && strcasecmp($copeCoordi, $copeTac) !== 0);

        if ($fila['CopesDiferentes']) {
            $fila['Alertas'][] = 'COPE_NO_COINCIDE';
            $fila['Calificador'] = false;
        }

        // Aquí el debug para números de serie
        echo "<pre>DEBUG SERIE:\n";
        var_dump('CoordiApp (Ont):', $fila['Num_Serie_CoordiApp_Valor'] ?? '');
        var_dump('TAC (Num__Serie):', $fila['Num_Serie_TAC_Valor'] ?? '');
        echo "--------------------------\n";

        $numSerieCoordi = trim($fila['Num_Serie_CoordiApp_Valor'] ?? '');
        $numSerieTac = trim($fila['Num_Serie_TAC_Valor'] ?? '');

        $fila['NumSerieDiferente'] = (!empty($numSerieCoordi) && !empty($numSerieTac) && strcasecmp($numSerieCoordi, $numSerieTac) !== 0);

        if ($fila['NumSerieDiferente']) {
            $fila['Alertas'][] = 'NUM_SERIE_NO_COINCIDE';
            $fila['Calificador'] = false;
        }

        $nombreTecCoordi = trim($fila['Nombre_Tecnico_CoordiApp'] ?? '');
        $nombreTecTac = trim($fila['Nombre_Tecnico_TAC'] ?? '');

        $fila['NombreTecnicoDiferente'] = (!empty($nombreTecCoordi) && !empty($nombreTecTac) && strcasecmp($nombreTecCoordi, $nombreTecTac) !== 0);

        if ($fila['NombreTecnicoDiferente']) {
            $fila['Alertas'][] = 'NOMBRE_TECNICO_NO_COINCIDE';
            $fila['Calificador'] = false;
        }
    }
    unset($fila);

    return array_values($datos);
} */

public function getDatosEmuladoresFiltrados($tecnologia, $semana, $nombreContratista, $filtros = [])
{
   /*echo "Filtros:\n";
    var_dump($filtros); */

    


    $conexion = $this->get_conexion();
    $conexionAnalytics = $this->get_conexion_analytics();
    $conexionAnalisis = $this->get_conexion_analisis();


    $tecnicosExpedientesData = [];
    if (!empty($nombreContratista)) {
        $tecnicosExpedientesData = $this->getTecnicosYExpedientesPorContratista($nombreContratista);
    }

    //var_dump($nExpedientes);

    $nExpedientes = array_column($tecnicosExpedientesData, 'NExpediente');

    //var_dump($nExpedientes);

    $sql = "SELECT 
    tinst.Folio_Pisa, 
    tinst.Telefono, 
    tinst.Tipo_Tarea,
    cp.COPE,
    tinst.Metraje,
    tinst.Tipo_Instalacion,
    tinst.Direccion_Cliente,
    tinst.terminal,
    tinst.puerto,
    UPPER(CONCAT_WS(' ', tinst.Cliente_Titular, tinst.Apellido_Paterno_Titular, tinst.Apellido_Materno_Titular)) AS Nombre_Cliente,
    UPPER(CONCAT_WS(' ', t.Nombre_T, t.Apellidos_T)) AS Nombre_Tecnico,
    tinst.distrito AS Distrito_CoordiApp,
    tinst.Ont AS Num_Serie_CoordiApp,
    tinst.Latitud AS Latitud_CoordiApp,
    tinst.Longitud AS Longitud_CoordiApp,
    tinst.Latitud_Terminal,
    tinst.Longitud_Terminal,
    tinst.Fecha_Coordiapp,
    t.NExpediente AS Expediente
FROM tecnico_instalaciones_coordiapp tinst
INNER JOIN tecnicos t ON t.idTecnico = tinst.FK_Tecnico_apps
INNER JOIN contratistas c ON c.idContratistas = t.FK_Contratista_Tecnico
LEFT JOIN copes cp ON cp.id = tinst.FK_Cope
WHERE 1=1
  AND YEAR(tinst.Fecha_Coordiapp) = 2025";

    $params = [];
    $conditions = [];

    if (!empty($tecnologia)) {
        $conditions[] = "tinst.Tecnologia = ?";
        $params[] = $tecnologia;
    }

    if (!empty($semana)) {
        $conditions[] = "WEEK(tinst.Fecha_Coordiapp, 1) = ?";
        $params[] = $semana;
    }

    if (!empty($nombreContratista)) {
        $conditions[] = "CONCAT_WS(' ', c.Contratista, c.apellido_paterno, c.apellido_materno) = ?";
        $params[] = $nombreContratista;
    }

    if (!empty($filtros['prefijos'])) {
        $likeClauses = [];
        foreach ($filtros['prefijos'] as $prefijo) {
            $likeClauses[] = "tinst.Tipo_Tarea LIKE ?";
            $params[] = $prefijo . '%';
        }
        $conditions[] = "(" . implode(" OR ", $likeClauses) . ")";
    }

    if (!empty($filtros['excluir_prefijos'])) {
        foreach ($filtros['excluir_prefijos'] as $excluido) {
            $conditions[] = "tinst.Tipo_Tarea NOT LIKE ?";
            $params[] = $excluido . '%';
        }
    }

     // NUEVO: filtro por los expedientes del contratista
    if (!empty($nExpedientes)) {
        $placeholders = implode(',', array_fill(0, count($nExpedientes), '?'));
        $conditions[] = "t.NExpediente IN ($placeholders)";
        $params = array_merge($params, $nExpedientes);
    }




    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    // 🔎 Depuración: ver el SQL y los parámetros
/*echo "<pre>";
echo "SQL Final:\n$sql\n";
echo "Parametros:\n";
print_r($params);
echo "</pre>"; */



    $statement = $conexion->prepare($sql);
    $statement->execute($params);

    $datos = [];

    while ($fila = $statement->fetch(PDO::FETCH_ASSOC)) 
    {  
        // aqui ya no sale ese folio


        $folio = $fila['Folio_Pisa'];
        $telefono = $fila['Telefono'];
        $tipotareaca = $fila['Tipo_Tarea'];

        /*echo "Folio: $folio<br>";
        echo "Teléfono: $telefono<br>";
        echo "Tipo de Tarea: $tipotareaca<br>"; */
        //$fila['Tipo_Tarea_Original'] = $fila['Tipo_Tarea'] ?? '';

        $fila['Origen_CoordiApp'] = true;
        $fila['Origen_TAC'] = false;

        $fila['Telefono_CoordiApp'] = !empty($telefono);
        $fila['Telefono_TAC'] = false;

        $fila['Telefono_CoordiApp_Valor'] = $telefono;
        $fila['Telefono_TAC_Valor'] = null;
        $fila['Tipo_tarea_TAC_Valor'] = null;

        $fila['COPE_CoordiApp_Valor'] = $fila['COPE'];
        $fila['COPE_TAC_Valor'] = null;

        $fila['Num_Serie_CoordiApp_Valor'] = $fila['Num_Serie_CoordiApp'];
        $fila['Nombre_Tecnico_CoordiApp'] = $fila['Nombre_Tecnico'];
        //$fila['Tecnico_CoordiApp'] = $fila['Nombre_Tecnico'];

        $fila['Latitud_CoordiApp_Valor'] = $fila['Latitud_CoordiApp'];
        $fila['Longitud_CoordiApp_Valor'] = $fila['Longitud_CoordiApp'];

        $fila['Latitud_TAC_Valor'] = null;
        $fila['Longitud_TAC_Valor'] = null;

        $fila['Fecha_Coordiapp_Valor'] = $fila['Fecha_Coordiapp'] ?? null;
        $fila['Fecha_LIQ_TAC_Valor'] = null;

        $fila['Alertas'] = [];
        $fila['Calificador'] = true;

        $fila['Expediente_CoordiApp'] = $fila['Expediente'] ?? null;
        $fila['Expediente_TAC'] = null;

       

        //echo "Expediente_CoordiApp: " . ($fila['Expediente_CoordiApp'] ?? 'null') . "\n";
        //echo "Expediente_TAC: " . ($fila['Expediente_TAC'] ?? 'null') . "\n";

        // ** Aquí calculamos la distancia entre domicilio y terminal **
        $fila['Distancia_Coordiapp_Terminal'] = $this->calcularDistanciaMetros(
            $fila['Latitud_CoordiApp'],
            $fila['Longitud_CoordiApp'],
            $fila['Latitud_Terminal'],
            $fila['Longitud_Terminal']
        );

        $datos[$folio] = $fila;
    }

    // 🔍 Imprimir los resultados obtenidos
/*echo "<pre>";
print_r($datos);
echo "</pre>"; /

 /*   echo "Valor de \$nExpedientes:\n";
print_r($nExpedientes); */


/*echo "Valor de \$nExpedientes:\n";
print_r($nExpedientes);

echo "Valor de \$semana:\n";
var_dump($semana);

echo "Valor de \$tecnologia:\n";
var_dump($tecnologia);

echo "Valor de \$filtros:\n";
print_r($filtros);
echo "</pre>"; */

/*$folioPrueba = ['68460279'];
$semana = null;
$tecnologia = null;
$filtros = []; */
    
    //var_dump($nExpedientes);
    $tacData = $this->obtenerDatosTacBolsa($conexionAnalytics,$conexionAnalisis,$nExpedientes, $semana, $tecnologia,$nombreContratista,$filtros);

   



    //var_dump($tacData);

    foreach ($tacData as $tacRow) {

    $folio = $tacRow['Folio_Pisa'];
    /*if ($folio == 68460279) {
        echo "<script>alert('ENTRA AL FOLIO 68460279');</script>";
        echo "Folio: ";
        var_dump($folio);
        echo "Telefono: ";
        var_dump($telefonoTac);
        echo "Tipo de tarea: ";
        var_dump($tipoTareaTac);
    } */
    //echo "Folio actual: $folio <br>";
    //var_dump($folio, array_key_exists($folio, $datos));
    $telefonoTac = $tacRow['TELEFONO'] ?? null;
    $tipoTareaTac = $tacRow['Tipo_de_Tarea'] ?? null; // llega vacio

    /*if ($folio == 68460279) {
        echo "Folio: $folio\n";
        echo "Telefono: " . var_export($telefonoTac, true) . "\n";
        echo "Tipo de tarea: " . var_export($tipoTareaTac, true) . "\n";
    } */

   /* if ($folio == 68460279) {
        echo "<script>alert('ENTRA AL FOLIO 68460279');</script>";
    echo "Folio: ";
    var_dump($folio);

    echo "Telefono: ";
    var_dump($telefonoTac);

    echo "Tipo de tarea: ";
    var_dump($tipoTareaTac);
} */
    

    $copeTac = $tacRow['NOM_CT'] ?? null;
    $numSerieTac = $tacRow['Num__Serie'] ?? null;
    $nombreTecnicoTac = $tacRow['Tecnico'] ?? null;

    $latTac = $tacRow['Latitud'] ?? null;
    $longTac = $tacRow['Longitud'] ?? null;

    $expedienteTac = $tacRow['Expediente'] ?? null;

    // Imprimir para debug
  /* echo "<pre>";
    echo "Folio: $folio\n";
    
    echo "</pre>";

    echo "<pre>";
    echo "tecnico: $nombreTecnicoTac\n";
    
    echo "</pre>"; */

    if (isset($datos[$folio])) {
        //Aquí es donde se asigna el valor real de TAC al campo Tipo_tarea_TAC_Valor.
        //Si no hay registro TAC para ese folio, Tipo_tarea_TAC_Valor permanece null
        $datos[$folio]['Origen_TAC'] = true;
        $datos[$folio]['Telefono_TAC'] = !empty($telefonoTac);
        $datos[$folio]['Telefono_TAC_Valor'] = $telefonoTac;
        $datos[$folio]['Tipo_tarea_TAC_Valor'] = $tipoTareaTac;
        $datos[$folio]['COPE_TAC_Valor'] = $copeTac;

        $datos[$folio]['Num_Serie_TAC_Valor'] = $numSerieTac;
        $datos[$folio]['Nombre_Tecnico_TAC'] = $nombreTecnicoTac;
       // $datos[$folio]['Tecnico_TAC'] = $nombreTecnicoTac;

        $datos[$folio]['Latitud_TAC_Valor'] = $latTac;
        $datos[$folio]['Longitud_TAC_Valor'] = $longTac;

        $datos[$folio]['Expediente_TAC'] = $expedienteTac;
        $datos[$folio]['Fecha_LIQ_TAC_Valor'] = $tacRow['FECHA_LIQ'] ?? null;

        // Opcional: calcular distancia
        // $datos[$folio]['Distancia_TAC_Terminal'] = $this->calcularDistanciaMetros(...);
    } else {
        $tacRow += [
            'TELEFONO' => '',
            'Tipo_de_Tarea' => '',
            'COPE' => '',
            'Metraje' => '',
            'Tipo_Instalacion' => '',
            'Direccion_Cliente' => '',
            'Nombre_Cliente' => '',
            //'Tecnico' => '',
            'Nombre_Tecnico' => '',
            'Expediente' => '',
        ];
        $tacRow['Origen_CoordiApp'] = false;
        $tacRow['Origen_TAC'] = true;
        $tacRow['Telefono_CoordiApp'] = false;
        $tacRow['Telefono_TAC'] = !empty($telefonoTac);

        $tacRow['Telefono_CoordiApp_Valor'] = null;
        $tacRow['Telefono_TAC_Valor'] = $telefonoTac;
        $tacRow['Tipo_tarea_TAC_Valor'] = $tipoTareaTac;

        $tacRow['COPE_CoordiApp_Valor'] = null;
        $tacRow['COPE_TAC_Valor'] = $copeTac;

        $tacRow['Num_Serie_CoordiApp_Valor'] = null;
        $tacRow['Num_Serie_TAC_Valor'] = $numSerieTac;

        $tacRow['Nombre_Tecnico_CoordiApp'] = null;
        $tacRow['Nombre_Tecnico_TAC'] = $nombreTecnicoTac;

        //$tacRow['Tecnico_CoordiApp'] = null;
        //$tacRow['Tecnico_TAC'] = $nombreTecnicoTac;

        $tacRow['Latitud_TAC_Valor'] = $latTac;
        $tacRow['Longitud_TAC_Valor'] = $longTac;

        $tacRow['Fecha_Coordiapp_Valor'] = null;
        $tacRow['Fecha_LIQ_TAC_Valor'] = $tacRow['FECHA_LIQ'] ?? null;

        $tacRow['Expediente_CoordiApp'] = null;
            $tacRow['Expediente_TAC'] = $expedienteTac;

        $tacRow['Alertas'] = [];
        $tacRow['Calificador'] = true;

        // Opcional: calcular distancia
        $tacRow['Distancia_Coordiapp_Terminal'] = null;

        $datos[$folio] = $tacRow;


    }
}

// -------------------------------
    //  NUEVO: agregar razones de la comparación TAC vs CoordiApp
    // -------------------------------
    $comparaciones = $this->compararTacVsCoordiApp(
        $conexionAnalytics,
        $conexionAnalisis,
        $nExpedientes,
        $semana,
        $tecnologia,
        $nombreContratista,
        $filtros
    );

    foreach ($comparaciones as $comp) {
        $folio = $comp['folio'];
        if (isset($datos[$folio])) {
            $datos[$folio]['razones'] = $comp['razones'] ?? '-';
        }
    }

    // Para filas que no tengan Razones
    foreach ($datos as &$fila) {
        if (!isset($fila['razones'])) {
            $fila['razones'] = '-';
        }
    }
    unset($fila);
    //Aquí $fila ya es una referencia a $datos[$folio].
    foreach ($datos as &$fila) {
        
        $this->aplicarValidaciones($fila,$filtros);
    } 
    
    unset($fila);

    return array_values($datos);
}

// Toda la lógica de alertas se aplica folio por folio, porque $fila es la referencia a un folio concreto
//private function aplicarValidaciones(array &$fila): void
private function aplicarValidaciones(array &$fila, array $filtros = []): void
{
    // $fila contiene todos los valores de mi consulta a coordiapp


    // Inicializar alertas y calificador si no existen
    if (!isset($fila['Alertas'])) {
        $fila['Alertas'] = [];
    }
    if (!isset($fila['Calificador'])) {
        $fila['Calificador'] = true;
    }

    if (!isset($fila['Advertencias'])) {
        $fila['Advertencias'] = [];
    }

    // --- FOLIO ---
    // fila no es el folio en sí, es un array que contiene todos los datos de un folio específico.
    //Cada $fila dentro del foreach ($datos as &$fila) representa una fila de datos asociada a un folio, y dentro de esa fila están todos los campos de CoordiApp y TAC, incluyendo el folio ($fila['Folio_Pisa']), el teléfono, tipo de tarea
    
    $existeCoordi = !empty($fila['Origen_CoordiApp']);
    
    //verificando si ese folio particular (el de $fila) existe en CoordiApp, usando la información ya almacenada en la fila.
    $existeTac = !empty($fila['Origen_TAC']);

    if (!$existeCoordi && $existeTac) {
        $fila['Alertas'][] = 'Folio no existe en CoordiApp';
        $fila['Calificador'] = false;
        return; // No evaluamos más alertas
    }

    if ($existeCoordi && !$existeTac) {
        $fila['Alertas'][] = 'Folio no existe en TAC';
        $fila['Calificador'] = false;
        return; // No evaluamos más alertas
    }

    



    // Nueva información: la razón de la alerta
    /*if (!empty($tecnologia) && ($fila['Tecnologia'] ?? '') !== $tecnologia) {
        $fila['Razon_Alerta'] = "No cumple la condición de tecnología: esperado '$tecnologia', encontrado '" . ($fila['Tecnologia'] ?? 'null') . "'";
    } else {
        $fila['Razon_Alerta'] = "Folio no encontrado en la tabla CoordiApp";
    } */



    // --- TELÉFONO ---
    $telCoordi = preg_replace('/\D/', '', $fila['Telefono_CoordiApp_Valor'] ?? '');
    $telTac = preg_replace('/\D/', '', $fila['Telefono_TAC_Valor'] ?? '');

    if (empty($telCoordi) && !empty($telTac)) {
        $fila['Alertas'][] = 'Teléfono no existe en CoordiApp';
        $fila['Calificador'] = false;
    }
    if (!empty($telCoordi) && empty($telTac)) {
        $fila['Alertas'][] = 'Teléfono no existe en TAC';
        $fila['Calificador'] = false;
    }
    if (!empty($telCoordi) && !empty($telTac) && $telCoordi !== $telTac) {
        $fila['Alertas'][] = 'Teléfonos diferentes';
        $fila['Calificador'] = false;
    }

    // --- COPE ---
    $copeCoordi = trim($fila['COPE_CoordiApp_Valor'] ?? '');
    $copeTac = trim($fila['COPE_TAC_Valor'] ?? '');

    if (empty($copeCoordi) && !empty($copeTac)) {
        $fila['Alertas'][] = 'COPE no existe en CoordiApp';
        $fila['Calificador'] = false;
    }
    if (!empty($copeCoordi) && empty($copeTac)) {
        $fila['Alertas'][] = 'COPE no existe en TAC';
        $fila['Calificador'] = false;
    }
    // Comparación insensible a mayúsculas
    if (!empty($copeCoordi) && !empty($copeTac) && strcasecmp($copeCoordi, $copeTac) !== 0) {
        $fila['Alertas'][] = 'COPE diferente';
        $fila['Calificador'] = false;
    }

        // --- TIPO DE TAREA --- 
     // --- TIPO DE TAREA ---
    $tipoCoordi = trim($fila['Tipo_Tarea_Original'] ?? $fila['Tipo_Tarea'] ?? '');
    $tipoTac = trim($fila['Tipo_tarea_TAC_Valor'] ?? '');

    if (empty($tipoCoordi) && !empty($tipoTac)) {
        $fila['Alertas'][] = 'Tipo de tarea no existe en CoordiApp';
        $fila['Calificador'] = false;
    } elseif (!empty($tipoCoordi) && empty($tipoTac)) {
        $fila['Alertas'][] = 'Tipo de tarea no existe en TAC';
        $fila['Calificador'] = false;
    } elseif (!empty($tipoCoordi) && !empty($tipoTac) && strcasecmp($tipoCoordi, $tipoTac) !== 0) {
        $fila['Alertas'][] = 'Tipo de tarea diferente';
        $fila['Calificador'] = false;
    }
    




    // --- NOMBRE DEL TÉCNICO ---
    $nombreTecCoordi = trim($fila['Nombre_Tecnico_CoordiApp'] ?? '');
    $nombreTecTac = trim($fila['Nombre_Tecnico_TAC'] ?? '');

    if (empty($nombreTecCoordi) && !empty($nombreTecTac)) {
        $fila['Alertas'][] = 'Nombre del técnico no existe en CoordiApp';
        $fila['Calificador'] = false;
    }
    if (!empty($nombreTecCoordi) && empty($nombreTecTac)) {
        $fila['Alertas'][] = 'Nombre del técnico no existe en TAC';
        $fila['Calificador'] = false;
    }
    // Comparación insensible a mayúsculas
    if (!empty($nombreTecCoordi) && !empty($nombreTecTac) && strcasecmp($nombreTecCoordi, $nombreTecTac) !== 0) {
        $fila['Alertas'][] = 'Nombre del técnico diferente';
        $fila['Calificador'] = false;
    }

    // --- NÚMERO DE SERIE ---
    $numSerieCoordi = trim($fila['Num_Serie_CoordiApp_Valor'] ?? '');
    $numSerieTac = trim($fila['Num_Serie_TAC_Valor'] ?? '');

    if (empty($numSerieCoordi) && !empty($numSerieTac)) {
        $fila['Alertas'][] = 'Número de serie no existe en CoordiApp';
        $fila['Calificador'] = false;
    }
    if (!empty($numSerieCoordi) && empty($numSerieTac)) {
        $fila['Alertas'][] = 'Número de serie no existe en TAC';
        $fila['Calificador'] = false;
    }
    // Comparación insensible a mayúsculas/minúsculas
    if (!empty($numSerieCoordi) && !empty($numSerieTac) && strcasecmp($numSerieCoordi, $numSerieTac) !== 0) {
        $fila['Alertas'][] = 'Número de serie diferente';
        $fila['Calificador'] = false;
    }

    $expCoordi = trim($fila['Expediente_CoordiApp'] ?? '');
    $expTac = trim($fila['Expediente_TAC'] ?? '');
    if (empty($expCoordi) && !empty($expTac)) { $fila['Alertas'][] = 'Expediente no existe en CoordiApp'; $fila['Calificador'] = false; }
    if (!empty($expCoordi) && empty($expTac)) { $fila['Alertas'][] = 'Expediente no existe en TAC'; $fila['Calificador'] = false; }
    if (!empty($expCoordi) && !empty($expTac) && strcasecmp($expCoordi, $expTac) !== 0) { $fila['Alertas'][] = 'Expediente diferente'; $fila['Calificador'] = false; 
   }
}

private function obtenerDatosTacBolsa($conexionAnalytics, $conexionAnalisis,
    $nExpedientes,
    $semana,
    $tecnologia,
    $nombreContratista,
    $filtros = []
) {
    $rows = [];

    if (!empty($nExpedientes)) {
        $placeholders = implode(',', array_fill(0, count($nExpedientes), '?'));

        $condicionesAdicionales = '';
        $parametrosAdicionales = [];

        if (!empty($semana)) {
            $condicionesAdicionales .= " AND WEEK(FECHA_LIQ, 1) = ?";
            $parametrosAdicionales[] = $semana;
        }

        if (!empty($tecnologia)) {
            if (strtoupper($tecnologia) === 'FIBRA') {
                $condicionesAdicionales .= " AND (Tecnologia = ? OR Tecnologia = 'CAMBIOFIBRA')";
            } else {
                $condicionesAdicionales .= " AND Tecnologia = ?";
            }
            $parametrosAdicionales[] = $tecnologia;
        }

        // Lista de prefijos que corresponden a quejas
        $prefijosQuejas = ['Q', 'R', 'C', 'EB', 'EI', 'EU', 'GD', 'MG', 'F597', 'F841'];

        // Bandera para decidir si usar qm_tac_quejas
        $usarQuejas = false;

        if (!empty($filtros['prefijos'])) {
            $prefijoPlaceholders = [];
            foreach ($filtros['prefijos'] as $prefijo) {
                $prefijoPlaceholders[] = "Tipo_de_Tarea LIKE ?";
                $parametrosAdicionales[] = $prefijo . '%';

                if (in_array($prefijo, $prefijosQuejas)) {
                    $usarQuejas = true;
                }
            }
            $condicionesAdicionales .= " AND (" . implode(' OR ', $prefijoPlaceholders) . ")";
        }

        if (!empty($filtros['excluir_prefijos'])) {
            foreach ($filtros['excluir_prefijos'] as $excluido) {
                $condicionesAdicionales .= " AND Tipo_de_Tarea NOT LIKE ?";
                $parametrosAdicionales[] = $excluido . '%';

                if (in_array($excluido, $prefijosQuejas)) {
                    $usarQuejas = true;
                }
            }
        }

        // Selección de tabla y conexión
        if ($usarQuejas) {
            $tablaBase = "qm_tac_quejas";
            $conexionUsar = $conexionAnalisis;
            $condicionesAdicionales .= " AND a.Estatus_Queja = 'A TRABAJO'";
        } else {
            $tablaBase = "qm_tac";
            $conexionUsar = $conexionAnalytics;
        }

        $sql = "SELECT DISTINCT
            a.Folio_Pisa, 
            a.TELEFONO, 
            a.Tipo_de_Tarea, 
            a.NOM_CT, 
            a.Distrito, 
            a.Num__Serie, 
            a.Tecnico, 
            a.Expediente, 
            a.FECHA_LIQ, 
            b.Lat, 
            b.Lng
        FROM {$tablaBase} AS a
        LEFT JOIN (
            SELECT Folio_Pisa, TELEFONO, Dom_X AS Lng, Dom_y AS Lat
            FROM qm_bolsa_diaria_tac
            GROUP BY Folio_Pisa, Telefono
        ) AS b 
        ON a.Folio_Pisa = b.Folio_Pisa AND a.TELEFONO = b.TELEFONO
        WHERE 
            a.Expediente IN ($placeholders)
            AND a.ORIGEN = 'TAC'
            AND a.Calificador_Edo = 'COMPLETADA'
            $condicionesAdicionales";

        $stmt = $conexionUsar->prepare($sql);
        $stmt->execute(array_merge($nExpedientes, $parametrosAdicionales));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        $resultadosComparacion = $this->compararTacVsCoordiApp(
            $conexionAnalytics,
            $conexionAnalisis,
            $nExpedientes,
            $semana,
            $tecnologia,
            $nombreContratista,
            $filtros
        );
    }

    return $rows;
}

public function compararTacVsCoordiApp(
    $conexionAnalytics,
    $conexionAnalisis,
    $nExpedientes,
    $semana,
    $tecnologia,
    $nombreContratista,
    $filtros = []
) {
    $conexion = $this->get_conexion();
    $params = [];

    // -------------------------------
    // Consulta a CoordiApp
    $sqlCoordi = "SELECT 
        tinst.Folio_Pisa,
        tinst.Tecnologia,
        tinst.Tipo_Tarea,
        tinst.Fecha_Coordiapp
    FROM tecnico_instalaciones_coordiapp tinst
    INNER JOIN tecnicos t ON t.idTecnico = tinst.FK_Tecnico_apps
    INNER JOIN contratistas c ON c.idContratistas = t.FK_Contratista_Tecnico
    WHERE YEAR(tinst.Fecha_Coordiapp) = 2025";

    if (!empty($nombreContratista)) {
        $sqlCoordi .= " AND CONCAT_WS(' ', c.Contratista, c.apellido_paterno, c.apellido_materno) = ?";
        $params[] = $nombreContratista;
    }

    if (!empty($nExpedientes)) {
        if (!is_array($nExpedientes)) $nExpedientes = [$nExpedientes];
        $placeholders = implode(',', array_fill(0, count($nExpedientes), '?'));
        $sqlCoordi .= " AND t.NExpediente IN ($placeholders)";
        $params = array_merge($params, $nExpedientes);
    }

    $stmtCoordi = $conexion->prepare($sqlCoordi);
    $stmtCoordi->execute($params);
    $coordiData = $stmtCoordi->fetchAll(PDO::FETCH_ASSOC);

    // -------------------------------
    // Consulta a TAC (dependiendo de prefijos)

    $tacData = [];
    if (!empty($coordiData)) {
        $folios = array_column($coordiData, 'Folio_Pisa');
        $placeholdersTac = implode(',', array_fill(0, count($folios), '?'));

        // Lista de prefijos de quejas
        $prefijosQuejas = ['Q', 'R', 'C', 'EB', 'EI', 'EU', 'GD', 'MG', 'F597', 'F841'];

        // Verificar si alguno de los prefijos en filtros activa quejas
        $usarQuejas = false;
        if (!empty($filtros['prefijos'])) {
            foreach ($filtros['prefijos'] as $prefijo) {
                if (in_array($prefijo, $prefijosQuejas)) {
                    $usarQuejas = true;
                    break;
                }
            }
        }

        if (!empty($filtros['excluir_prefijos'])) {
            foreach ($filtros['excluir_prefijos'] as $excluido) {
                if (in_array($excluido, $prefijosQuejas)) {
                    $usarQuejas = true;
                    break;
                }
            }
        }

        // Selección de tabla y conexión
        if ($usarQuejas) {
            $tablaBase = "qm_tac_quejas";
            $conexionUsar = $conexionAnalisis;
            $extraCond = " AND a.Estatus_Queja = 'A TRABAJO'";
        } else {
            $tablaBase = "qm_tac";
            $conexionUsar = $conexionAnalytics;
            $extraCond = "";
        }

        $sqlTac = "SELECT 
            a.Folio_Pisa, 
            a.Tecnologia,
            a.Tipo_de_Tarea,
            a.FECHA_LIQ
        FROM {$tablaBase} a
        WHERE a.Folio_Pisa IN ($placeholdersTac)
          AND a.ORIGEN = 'TAC'
          AND a.Calificador_Edo = 'COMPLETADA'
          $extraCond";

        $stmtTac = $conexionUsar->prepare($sqlTac);
        $stmtTac->execute($folios);
        $tacData = $stmtTac->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------
    // Comparación CoordiApp vs TAC
    $comparacion = [];
    foreach ($coordiData as $c) {
        $folio = $c['Folio_Pisa'];
        $filaTac = array_filter($tacData, fn($t) => $t['Folio_Pisa'] == $folio);

        if (!empty($filaTac)) {
            $t = reset($filaTac);
            $fila = [
                'folio'            => $folio,
                'tecnologia_coord' => $c['Tecnologia'],
                'tarea_coord'      => $c['Tipo_Tarea'],
                'fecha_coord'      => $c['Fecha_Coordiapp'],
                'tecnologia_tac'   => $t['Tecnologia'],
                'tarea_tac'        => $t['Tipo_de_Tarea'],
                'fecha_tac'        => $t['FECHA_LIQ']
            ];
        } else {
            $fila = [
                'folio'            => $folio,
                'tecnologia_coord' => $c['Tecnologia'],
                'tarea_coord'      => $c['Tipo_Tarea'],
                'fecha_coord'      => $c['Fecha_Coordiapp'],
                'tecnologia_tac'   => null,
                'tarea_tac'        => null,
                'fecha_tac'        => null
            ];
        }

        // Generar 'razones'
        $razones = [];

        if (!empty($fila['tecnologia_coord']) && !empty($fila['tecnologia_tac']) &&
            $fila['tecnologia_coord'] !== $fila['tecnologia_tac']) {
            $razones[] = "Tecnología diferente:<br>CoordiApp = {$fila['tecnologia_coord']},<br>TAC = {$fila['tecnologia_tac']}";
        } elseif (empty($fila['tecnologia_coord']) && !empty($fila['tecnologia_tac'])) {
            $razones[] = "Campo Tecnología vacío en CoordiApp:<br>TAC = {$fila['tecnologia_tac']}";
        }

        if (!empty($fila['tarea_coord']) && !empty($fila['tarea_tac']) &&
            $fila['tarea_coord'] !== $fila['tarea_tac']) {
            $razones[] = "Tipo de tarea diferente:<br>CoordiApp = {$fila['tarea_coord']}<br>TAC = {$fila['tarea_tac']}";
        } elseif (empty($fila['tarea_coord']) && !empty($fila['tarea_tac'])) {
            $razones[] = "Campo Tipo de tarea vacío en CoordiApp:<br>TAC = {$fila['tarea_tac']}";
        }

        if (!empty($fila['fecha_coord']) && !empty($fila['fecha_tac'])) {
            $semanaCoordi = date('W', strtotime($fila['fecha_coord']));
            $semanaTAC   = date('W', strtotime($fila['fecha_tac']));
            if ($semanaCoordi !== $semanaTAC) {
                $razones[] = "Diferencia en la semana:<br>CoordiApp semana = {$semanaCoordi}<br>TAC semana = {$semanaTAC}";
            }
        } elseif (empty($fila['fecha_coord']) && !empty($fila['fecha_tac'])) {
            $razones[] = "Campo Fecha vacío en CoordiApp:<br>TAC semana = " . date('W', strtotime($fila['fecha_tac']));
        }

        $fila['razones'] = !empty($razones) ? implode('<br><br>', $razones) : '-';

        $comparacion[] = $fila;
    }

    return $comparacion;
}
/*public function compararTacVsCoordiApp($conexionAnalytics,
    $nExpedientes,
    $semana,
    $tecnologia,
    $nombreContratista,
    $filtros = [])
{
    // Conexión para CoordiApp
    $conexion = $this->get_conexion();

    $resultados = [];
    $params = []; // ✅ Inicializar array de parámetros

    // -------------------------------
    // 1️⃣ Consulta a CoordiApp
    // -------------------------------
    $sqlCoordi = "SELECT 
        tinst.Folio_Pisa,
        tinst.Tecnologia,
        tinst.Tipo_Tarea,
        cp.COPE,
        t.NExpediente,
        UPPER(CONCAT_WS(' ', t.Nombre_T, t.Apellidos_T)) AS Nombre_Tecnico
    FROM tecnico_instalaciones_coordiapp tinst
    INNER JOIN tecnicos t ON t.idTecnico = tinst.FK_Tecnico_apps
    INNER JOIN contratistas c ON c.idContratistas = t.FK_Contratista_Tecnico
    LEFT JOIN copes cp ON cp.id = tinst.FK_Cope
    WHERE YEAR(tinst.Fecha_Coordiapp) = 2025";

    // Filtro por contratista
    if (!empty($nombreContratista)) {
        $sqlCoordi .= " AND CONCAT_WS(' ', c.Contratista, c.apellido_paterno, c.apellido_materno) = ?";
        $params[] = $nombreContratista;
    }

    // Filtro por expedientes
    if (!empty($nExpedientes)) {
        $placeholders = implode(',', array_fill(0, count($nExpedientes), '?'));
        $sqlCoordi .= " AND t.NExpediente IN ($placeholders)";
        $params = array_merge($params, $nExpedientes);
    }

    $statement = $conexion->prepare($sqlCoordi);
    $statement->execute($params);
    $datosCoordi = $statement->fetchAll(PDO::FETCH_ASSOC);

    // -------------------------------
    // 2️⃣ Consulta a TAC
    // -------------------------------
    $datosTac = [];
    if (!empty($nExpedientes)) {
        $placeholdersTac = implode(',', array_fill(0, count($nExpedientes), '?'));

        $sqlTac = "SELECT DISTINCT
            a.Folio_Pisa, 
            a.TELEFONO, 
            a.Tipo_de_Tarea, 
            a.NOM_CT, 
            a.Distrito, 
            a.Num__Serie, 
            a.Tecnico, 
            a.Expediente, 
            a.FECHA_LIQ, 
            b.Lat, 
            b.Lng
        FROM qm_tac AS a
        LEFT JOIN (
            SELECT Folio_Pisa, TELEFONO, Dom_X AS Lng, Dom_y AS Lat
            FROM qm_bolsa_diaria_tac
            GROUP BY Folio_Pisa, Telefono
        ) AS b 
        ON a.Folio_Pisa = b.Folio_Pisa AND a.TELEFONO = b.TELEFONO
        WHERE a.Expediente IN ($placeholdersTac)
          AND a.ORIGEN = 'TAC'
          AND a.Calificador_Edo = 'COMPLETADA'";

        $stmtTac = $conexionAnalytics->prepare($sqlTac);
        $stmtTac->execute($nExpedientes);
        $datosTac = $stmtTac->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------
    // Resultado combinado
    // -------------------------------
    return [
        'coordiapp' => $datosCoordi,
        'tac'       => $datosTac
    ];
} */

/* public function compararTacVsCoordiApp($conexionAnalytics, $nExpedientes, $semana, $tecnologia, $filtros = [])
{
    echo "Parámetro \$nExpedientes:\n";
    var_dump($nExpedientes);

    echo "Parámetro \$semana:\n";
    var_dump($semana);

    echo "Parámetro \$tecnologia:\n";
    var_dump($tecnologia);

    echo "Parámetro \$filtros:\n";
    var_dump($filtros); 

    // Conexión para CoordiApp
    $conexion = $this->get_conexion();

    $resultados = [];

    // 1. Consulta CoordiApp
    $sqlCoordi = "SELECT 
            tinst.Folio_Pisa,
            tinst.Tecnologia,
            tinst.Tipo_Tarea,
            cp.COPE,
            t.NExpediente,
            UPPER(CONCAT_WS(' ', t.Nombre_T, t.Apellidos_T)) AS Nombre_Tecnico
        FROM tecnico_instalaciones_coordiapp tinst
        INNER JOIN tecnicos t ON t.idTecnico = tinst.FK_Tecnico_apps
        INNER JOIN contratistas c ON c.idContratistas = t.FK_Contratista_Tecnico
        LEFT JOIN copes cp ON cp.id = tinst.FK_Cope
        WHERE YEAR(tinst.Fecha_Coordiapp) = 2025";

    // Agregar filtro por semana si existe
    $paramsCoordi = [];
    if (!empty($semana)) {
        $sqlCoordi .= " AND WEEK(tinst.Fecha_Coordiapp, 1) = ?";
        $paramsCoordi[] = $semana;
    }

    // Agregar filtro por tecnología si existe
    if (!empty($tecnologia)) {
        $sqlCoordi .= " AND tinst.Tecnologia = ?";
        $paramsCoordi[] = $tecnologia;
    }

    $stmt = $conexion->prepare($sqlCoordi);
    $stmt->execute($paramsCoordi);
    $coordiData = $stmt->fetchAll(PDO::FETCH_ASSOC);

   echo '<pre>'; // Para que se vea más legible en el navegador
print_r($coordiData);
echo '</pre>'; 

    // 2. Obtener datos de TAC
    if (!empty($nExpedientes)) {
        $placeholders = implode(',', array_fill(0, count($nExpedientes), '?'));
        $sqlTac = "SELECT DISTINCT
                a.Folio_Pisa,
                a.Tecnologia,
                a.Tipo_de_Tarea,
                a.NOM_CT AS COPE,
                a.Expediente,
                a.Tecnico
            FROM qm_tac a
            WHERE a.Expediente IN ($placeholders)
              AND a.ORIGEN = 'TAC'
              AND a.Calificador_Edo = 'COMPLETADA'
              AND WEEK(a.FECHA_LIQ, 1) = ?";
        
        $stmt = $conexionAnalytics->prepare($sqlTac);
        $stmt->execute(array_merge($nExpedientes, [$semana]));
        $tacData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $tacData = [];
    }

    // Indexar por Folio
    $mapCoordi = [];
    foreach ($coordiData as $c) {
        $mapCoordi[$c['Folio_Pisa']] = $c;
    }
    $mapTac = [];
    foreach ($tacData as $t) {
        $mapTac[$t['Folio_Pisa']] = $t;
    }

    $folios = array_unique(array_merge(array_keys($mapCoordi), array_keys($mapTac)));

    foreach ($folios as $folio) {
        $c = $mapCoordi[$folio] ?? null;
        $t = $mapTac[$folio] ?? null;

        $dif = [
            'Folio_Pisa' => $folio,
            'Diferencias' => []
        ];

        // Solo comparar si existe en ambos
        if ($c && $t) {
            if (strcasecmp($c['Tecnologia'] ?? '', $t['Tecnologia'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'Tecnología no coincide';
            }
            if (strcasecmp($c['Tipo_Tarea'] ?? '', $t['Tipo_de_Tarea'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'Tipo de tarea no coincide';
            }
            if (strcasecmp($c['COPE'] ?? '', $t['COPE'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'COPE no coincide';
            }
            if (strcasecmp($c['NExpediente'] ?? '', $t['Expediente'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'Expediente no coincide';
            }
            if (strcasecmp($c['Nombre_Tecnico'] ?? '', $t['Tecnico'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'Técnico no coincide';
            }
        }

        if (!empty($dif['Diferencias'])) {
            $resultados[] = $dif;
        }
    }

    return $resultados;
} */

/*public function compararTacVsCoordiApp($conexionAnalytics, $nExpedientes, $semana, $tecnologia, $filtros = [])
{
    echo "=== INICIO DE comparacion ===\n";

    // Conexión para CoordiApp
    $conexion = $this->get_conexion();
    echo "Conexión CoordiApp establecida\n";

    $resultados = [];

    // 1. Obtener datos de CoordiApp
    $sqlCoordi = "SELECT 
            tinst.Folio_Pisa,
            tinst.Tecnologia,
            tinst.Tipo_Tarea,
            cp.COPE,
            t.NExpediente,
            UPPER(CONCAT_WS(' ', t.Nombre_T, t.Apellidos_T)) AS Nombre_Tecnico
        FROM tecnico_instalaciones_coordiapp tinst
        INNER JOIN tecnicos t ON t.idTecnico = tinst.FK_Tecnico_apps
        INNER JOIN contratistas c ON c.idContratistas = t.FK_Contratista_Tecnico
        LEFT JOIN copes cp ON cp.id = tinst.FK_Cope
        WHERE YEAR(tinst.Fecha_Coordiapp) = 2025
          AND WEEK(tinst.Fecha_Coordiapp, 1) = ?
          AND CONCAT_WS(' ', c.Contratista, c.apellido_paterno, c.apellido_materno) = ?";
    
    echo "SQL CoordiApp:\n$sqlCoordi\n";

    $stmt = $conexion->prepare($sqlCoordi);
    $stmt->execute([$semana, $filtros['nombreContratista'] ?? '']);
    $coordiData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Datos obtenidos de CoordiApp:\n";
    print_r($coordiData);

    // 2. Obtener datos de TAC
    if (!empty($nExpedientes)) {
        $placeholders = implode(',', array_fill(0, count($nExpedientes), '?'));
        $sqlTac = "SELECT DISTINCT
                a.Folio_Pisa,
                a.Tecnologia,
                a.Tipo_de_Tarea,
                a.NOM_CT AS COPE,
                a.Expediente,
                a.Tecnico
            FROM qm_tac a
            WHERE a.Expediente IN ($placeholders)
              AND a.ORIGEN = 'TAC'
              AND a.Calificador_Edo = 'COMPLETADA'
              AND WEEK(a.FECHA_LIQ, 1) = ?";

        echo "SQL TAC:\n$sqlTac\n";
        echo "Expedientes TAC:\n";
        print_r($nExpedientes);
        echo "Semana: $semana\n";

        $stmt = $conexionAnalytics->prepare($sqlTac);
        $stmt->execute(array_merge($nExpedientes, [$semana]));
        $tacData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Datos obtenidos de TAC:\n";
        print_r($tacData);
    } else {
        $tacData = [];
        echo "No hay expedientes para TAC\n";
    }

    // Indexar por Folio
    $mapCoordi = [];
    foreach ($coordiData as $c) {
        $mapCoordi[$c['Folio_Pisa']] = $c;
    }
    echo "Mapa CoordiApp por Folio:\n";
    print_r($mapCoordi);

    $mapTac = [];
    foreach ($tacData as $t) {
        $mapTac[$t['Folio_Pisa']] = $t;
    }
    echo "Mapa TAC por Folio:\n";
    print_r($mapTac);

    $folios = array_unique(array_merge(array_keys($mapCoordi), array_keys($mapTac)));
    echo "Folios a comparar:\n";
    print_r($folios);

    foreach ($folios as $folio) {
        echo "\n--- Comparando Folio: $folio ---\n";
        $c = $mapCoordi[$folio] ?? null;
        $t = $mapTac[$folio] ?? null;

        echo "Datos CoordiApp:\n";
        print_r($c);
        echo "Datos TAC:\n";
        print_r($t);

        $dif = [
            'Folio_Pisa' => $folio,
            'Diferencias' => []
        ];

        // Solo comparar si existe en ambos
        if ($c && $t) {
            if (strcasecmp($c['Tecnologia'] ?? '', $t['Tecnologia'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'Tecnología no coincide';
            }
            if (strcasecmp($c['Tipo_Tarea'] ?? '', $t['Tipo_de_Tarea'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'Tipo de tarea no coincide';
            }
            if (strcasecmp($c['COPE'] ?? '', $t['COPE'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'COPE no coincide';
            }
            if (strcasecmp($c['NExpediente'] ?? '', $t['Expediente'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'Expediente no coincide';
            }
            if (strcasecmp($c['Nombre_Tecnico'] ?? '', $t['Tecnico'] ?? '') !== 0) {
                $dif['Diferencias'][] = 'Técnico no coincide';
            }
        }

        echo "Diferencias encontradas:\n";
        print_r($dif['Diferencias']);

        if (!empty($dif['Diferencias'])) {
            $resultados[] = $dif;
        }
    }

    echo "=== RESULTADOS FINALES ===\n";
    print_r($resultados);

    return $resultados;
} */



/*private function obtenerDatosTacBolsa($conexionAnalytics, $nExpedientes, $semana, $tecnologia, $filtros = [])
{
    $rows = [];

    if (!empty($nExpedientes)) {
        $placeholders = implode(',', array_fill(0, count($nExpedientes), '?'));

        $condicionesAdicionales = '';
        $parametrosAdicionales = [];

        if (!empty($semana)) {
            $condicionesAdicionales .= " AND WEEK(FECHA_LIQ, 1) = ?";
            $parametrosAdicionales[] = $semana;
        }

       

        if (!empty($tecnologia)) {
    $condicionesAdicionales .= " AND (Tecnologia = ? OR Tecnologia = 'CAMBIOFIBRA')";
    $parametrosAdicionales[] = $tecnologia;
}

        if (!empty($filtros['prefijos'])) {
            $prefijoPlaceholders = [];
            foreach ($filtros['prefijos'] as $prefijo) {
                $prefijoPlaceholders[] = "Tipo_de_Tarea LIKE ?";
                $parametrosAdicionales[] = $prefijo . '%';
            }
            $condicionesAdicionales .= " AND (" . implode(' OR ', $prefijoPlaceholders) . ")";
        }

        if (!empty($filtros['excluir_prefijos'])) {
            foreach ($filtros['excluir_prefijos'] as $excluido) {
                $condicionesAdicionales .= " AND Tipo_de_Tarea NOT LIKE ?";
                $parametrosAdicionales[] = $excluido . '%';
            }
        }
$sql = "SELECT DISTINCT
    a.Folio_Pisa, 
    a.TELEFONO, 
    a.Tipo_de_Tarea, 
    a.NOM_CT, 
    a.Distrito, 
    a.Num__Serie, 
    a.Tecnico, 
    a.Expediente, 
    a.FECHA_LIQ, 
    b.Lat, 
    b.Lng
FROM qm_tac AS a
LEFT JOIN (
    SELECT Folio_Pisa, TELEFONO, Dom_X AS Lng, Dom_y AS Lat
    FROM qm_bolsa_diaria_tac
    GROUP BY Folio_Pisa, Telefono
) AS b 
ON a.Folio_Pisa = b.Folio_Pisa AND a.TELEFONO = b.TELEFONO
WHERE 
    a.Expediente IN ($placeholders)
    AND a.ORIGEN = 'TAC'
    AND a.Calificador_Edo = 'COMPLETADA'
    $condicionesAdicionales";


        $stmt = $conexionAnalytics->prepare($sql);
        $stmt->execute(array_merge($nExpedientes, $parametrosAdicionales));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
    }

    return $rows;
} */

public function ListPacosFoliosPaco($id_contratista)
{
    $rows = null;
    $conexion = $this->get_conexion();

    $sql = "SELECT id_folio_paco, folio_paco, tecnologia 
            FROM Folios_Paco 
            WHERE idContratistas = :id_contratista 
              AND tecnologia = 'FIBRA'
            ORDER BY folio_paco ASC";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':id_contratista', $id_contratista, PDO::PARAM_INT);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}

public function getFoliosPacoPorEmuladorYSemana($emulador, $semana)
{
    var_dump($emulador, $semana);
    exit;
    $conexion = $this->get_conexion();

    // Obtener tipoemulador con el nombre del emulador
    $sqlEmulador = "SELECT tipoemulador FROM emuladores_nueva WHERE emulador = :emulador LIMIT 1";
    $stmtEmulador = $conexion->prepare($sqlEmulador);
    $stmtEmulador->execute([':emulador' => $emulador]);
    $tipoemulador = $stmtEmulador->fetchColumn();

    var_dump("tipoemulador obtenido:", $tipoemulador); // <-- Agrega esto para ver qué devuelve
    exit;

    

    if (!$tipoemulador) {
        return [];
    }

    // Obtener folios Paco únicos
    $sqlFolios = "
        SELECT MIN(id_folio_paco) AS id_folio_paco, folio_paco 
        FROM Folios_Paco 
        WHERE tipoemulador = :tipoemulador AND semana = :semana 
        GROUP BY folio_paco 
        ORDER BY folio_paco ASC
    ";
    $stmtFolios = $conexion->prepare($sqlFolios);
    $stmtFolios->execute([
        ':tipoemulador' => $tipoemulador,
        ':semana' => $semana
    ]);

    return $stmtFolios->fetchAll(PDO::FETCH_ASSOC);
}


public function getRegistrosPorEmuladorSemanaFolioPacoA4($emulador, $semana, $folio_paco)
{
    $conexion = $this->get_conexion();

    // Primero obtener el tipoemulador con el nombre del emulador y tecnología COBRE
    $sqlTipo = "SELECT tipoemulador 
                FROM emuladores_nueva 
                WHERE emulador = :emulador 
                  AND tecnologia = 'COBRE'
                LIMIT 1";
    $stmtTipo = $conexion->prepare($sqlTipo);
    $stmtTipo->execute([':emulador' => $emulador]);
    $tipoemulador = $stmtTipo->fetchColumn();

    if (!$tipoemulador) {
        // Debug opcional
        //echo "<pre>ATENCIÓN: No se encontró tipoemulador para el emulador '{$emulador}' con tecnología COBRE.</pre>";
        return []; // No se encontró el tipoemulador
    }

    // Consulta para traer los registros que coincidan con tipoemulador, semana, folio_paco
    // y cuyo estatus_orden sea 'aceptada'
    $sql = "
        SELECT * FROM Folios_Paco
        WHERE tipoemulador = :tipoemulador
          AND semana = :semana
          AND folio_paco = :folio_paco
          AND estatus_orden = 'aceptada'
        ORDER BY id_folio_paco ASC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':tipoemulador' => $tipoemulador,
        ':semana' => $semana,
        ':folio_paco' => $folio_paco
    ]);

    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug opcional
    //echo "<pre>Tipo de emulador encontrado: " . $tipoemulador . "\n";
    //echo "Cantidad de registros obtenidos: " . count($registros) . "</pre>";

    return $registros;
}

public function getRegistrosPorEmuladorSemanaFolioPacoQueja($emulador, $semana, $folio_paco)
{
    $conexion = $this->get_conexion();

    // Obtener el tipoemulador con el nombre del emulador y tecnología COBRE o FIBRA
    $sqlTipo = "SELECT tipoemulador 
                FROM emuladores_nueva 
                WHERE emulador = :emulador 
                  AND tecnologia IN ('COBRE', 'FIBRA')
                LIMIT 1";
    $stmtTipo = $conexion->prepare($sqlTipo);
    $stmtTipo->execute([':emulador' => $emulador]);
    $tipoemulador = $stmtTipo->fetchColumn();

    if (!$tipoemulador) {
        return []; // No se encontró el tipoemulador
    }

    // Consulta para traer los registros que coincidan con tipoemulador, semana, folio_paco
    // y cuyo estatus_orden sea 'aceptada'
    $sql = "
        SELECT * FROM Folios_Paco
        WHERE tipoemulador = :tipoemulador
          AND semana = :semana
          AND folio_paco = :folio_paco
          AND estatus_orden = 'aceptada'
        ORDER BY id_folio_paco ASC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':tipoemulador' => $tipoemulador,
        ':semana' => $semana,
        ':folio_paco' => $folio_paco
    ]);

    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $registros;
}



public function getRegistrosPorEmuladorSemanaFolioPaco($emulador, $semana, $folio_paco)
{
    $conexion = $this->get_conexion();

    // Primero obtener el tipoemulador con el nombre del emulador
    $sqlTipo = "SELECT tipoemulador FROM emuladores_nueva WHERE emulador = :emulador LIMIT 1";
    $stmtTipo = $conexion->prepare($sqlTipo);
    $stmtTipo->execute([':emulador' => $emulador]);
    $tipoemulador = $stmtTipo->fetchColumn();

    if (!$tipoemulador) {
        return []; // No se encontró el tipoemulador
    }

    // Consulta para traer los registros que coincidan con tipoemulador, semana, folio_paco
    // y cuyo estatus_orden sea 'aceptada'
    $sql = "
        SELECT * FROM Folios_Paco
        WHERE tipoemulador = :tipoemulador
          AND semana = :semana
          AND folio_paco = :folio_paco
          AND estatus_orden = 'aceptada'
        ORDER BY id_folio_paco ASC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':tipoemulador' => $tipoemulador,
        ':semana' => $semana,
        ':folio_paco' => $folio_paco
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    private function calcularDistanciaMetros($lat1, $lon1, $lat2, $lon2)
{
    if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
        return null;
    }

    $radioTierra = 6371000; // metros

    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);

    $dlat = $lat2Rad - $lat1Rad;
    $dlon = $lon2Rad - $lon1Rad;

    $a = sin($dlat / 2) * sin($dlat / 2) +
         cos($lat1Rad) * cos($lat2Rad) *
         sin($dlon / 2) * sin($dlon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distancia = $radioTierra * $c;

    return round($distancia, 2); // metros con 2 decimales
}


public function getConceptosPorMetraje()
    {
        $rows = [];
        $conexion = $this->get_conexion();
        $sql = "SELECT id_campos_emulador, nombre FROM tipos_campo_emulador
                WHERE nombre LIKE 'Bajante aereo de %' OR nombre LIKE 'Bajante subterraneo de %'";
        $statement = $conexion->prepare($sql);
        $statement->execute();
        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $result;
        }
        return $rows;
    }


public function getPreciosPorContratista($idContratista) {
    $conn = $this->get_conexion(); 
    $stmt = $conn->prepare("SELECT fk_idconcepto, precio FROM precios WHERE fk_idcontratista = ?");
    $stmt->execute([$idContratista]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function insertarPagos(array $data)
{
    $rows = [];
    $conexion = $this->get_conexion();

    try {
        $conexion->beginTransaction();

        $sql = "INSERT INTO pagos (
                    tipoemulador, semana, folio_paco_texto, tecnologia, nombre_contratista, id_contratista,
                    folio_pisa, telefono, tipo_tarea, cope, empresa,
                    metraje_aereo, metraje_subterraneo, precio, subtotal
                ) VALUES (
                    :tipoemulador, :semana, :folio_paco_texto, :tecnologia, :nombre_contratista, :id_contratista,
                    :folio_pisa, :telefono, :tipo_tarea, :cope, :empresa,
                    :metraje_aereo, :metraje_subterraneo, :precio, :subtotal
                )";

        $stmt = $conexion->prepare($sql);

        foreach ($data['registros'] as $registro) {
            $stmt->execute([
                ':tipoemulador' => $data['tipoemulador'],
                ':semana' => $data['semana'],
                ':folio_paco_texto' => $data['foliopaco'],
                ':tecnologia' => $data['tecnologia'],
                ':nombre_contratista' => $data['nombreContratista'],
                ':id_contratista' => $data['id_contratista'],
                ':folio_pisa' => $registro['folio_pisa'],
                ':telefono' => $registro['telefono'],
                ':tipo_tarea' => $registro['tipo_tarea'],
                ':cope' => $registro['cope'],
                ':empresa' => $registro['empresa'],
                ':metraje_aereo' => $registro['metraje_aereo'],
                ':metraje_subterraneo' => $registro['metraje_subterraneo'],
                ':precio' => $registro['precio'],
                ':subtotal' => $data['total'],  // <--- Cambiado de subtotal a total
            ]);
        }

        $conexion->commit();

        $rows = ['status' => 'success', 'message' => 'Pagos insertados correctamente'];

    } catch (PDOException $e) {
        $conexion->rollBack();
        $rows = ['status' => 'error', 'message' => $e->getMessage()];
    }

    return $rows;
}

public function insertarPagoTotal($data)
{
    try {
        $conexion = $this->get_conexion();

        // Buscar id_folio_paco
        $sqlBuscar = "SELECT id_folio_paco FROM Folios_Paco WHERE folio_paco = :folio_paco LIMIT 1";
        $stmtBuscar = $conexion->prepare($sqlBuscar);
        $stmtBuscar->bindParam(':folio_paco', $data['foliopaco']); // coincidir con el JS
        $stmtBuscar->execute();

        $resultadoBuscar = $stmtBuscar->fetch(PDO::FETCH_ASSOC);

        if (!$resultadoBuscar || empty($resultadoBuscar['id_folio_paco'])) {
            return ['status' => 'error', 'message' => 'No se encontró el id_folio_paco para el folio_paco: ' . $data['foliopaco']];
        }

        $id_folio_paco = $resultadoBuscar['id_folio_paco'];

        // Validar total (antes usabas subtotal, ahora total)
        if (!isset($data['total']) || !is_numeric($data['total']) || $data['total'] <= 0) {
            return ['status' => 'error', 'message' => 'Error: total inválido o vacío'];
        }

        // Insertar en pago_total
        $sql = "INSERT INTO pago_total (id_folio_paco, semana, id_contratista, total)
                VALUES (:id_folio_paco, :semana, :id_contratista, :total)";

        $statement = $conexion->prepare($sql);

        $statement->bindParam(':id_folio_paco', $id_folio_paco);
        $statement->bindParam(':semana', $data['semana']);
        $statement->bindParam(':id_contratista', $data['id_contratista']);
        $statement->bindParam(':total', $data['total']);

        if ($statement->execute()) {
            return ['status' => 'success', 'message' => 'Total insertado correctamente'];
        } else {
            $errorInfo = $statement->errorInfo();
            return ['status' => 'error', 'message' => 'No se pudo insertar el total: ' . $errorInfo[2]];
        }

    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()];
    }
}

// FUNCIONES PARA MIGRACIONES //

public function obtenertecnologiafoliopaco($folioPaco)
    {
        $conexion = $this->get_conexion();
        $sql = "SELECT tecnologia FROM Folios_Paco WHERE folio_paco = :folioPaco LIMIT 1";
        $statement = $conexion->prepare($sql);
        $statement->bindParam(':folioPaco', $folioPaco, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['tecnologia'] : null;
    }

public function obtenerMetrajesPorFolioPaco($folioPacoTexto, $idContratista)
{
    $conexion = $this->get_conexion();
    $sql = "SELECT 
                folio_pisa,
                bajante_aereo_25m, bajante_aereo_50m, bajante_aereo_75m, bajante_aereo_100m,
                bajante_aereo_125m, bajante_aereo_150m, bajante_aereo_175m, bajante_aereo_200m,
                bajante_aereo_250m, bajante_aereo_300m, ml_por_bajantes_aereos_mayor_300,
                bajante_subterraneo_25m, bajante_subterraneo_50m, bajante_subterraneo_75m, bajante_subterraneo_100m,
                bajante_subterraneo_125m, bajante_subterraneo_150m, bajante_subterraneo_175m, bajante_subterraneo_200m,
                bajante_subterraneo_250m, bajante_subterraneo_300m, ml_por_bajantes_subterraneos_mayor_300
            FROM Folios_Paco
            WHERE folio_paco = :folio 
              AND idContratistas = :idContratista
              AND estatus_orden = 'aceptada'";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':folio', $folioPacoTexto, PDO::PARAM_STR);
    $statement->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $statement->execute();
    
    $resultado = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Reorganizamos los metrajes por folio_pisa para acceder fácilmente en la tabla
    $metrajesPorFolioPisa = [];
    foreach ($resultado as $fila) {
        $folioPisa = $fila['folio_pisa'];
        $metrajesPorFolioPisa[$folioPisa] = $fila;
    }

    return $metrajesPorFolioPisa;
}

public function getNominaMigraciones($emulador, $semana, $folio_paco, $idContratista)
{
    $conexion = $this->get_conexion();

    // Primero obtener el tipoemulador con el nombre del emulador
    $sqlTipo = "SELECT tipoemulador FROM emuladores_nueva WHERE emulador = :emulador LIMIT 1";
    $stmtTipo = $conexion->prepare($sqlTipo);
    $stmtTipo->execute([':emulador' => $emulador]);
    $tipoemulador = $stmtTipo->fetchColumn();

    if (!$tipoemulador) {
        return []; // No se encontró el tipoemulador
    }

    // Consulta para traer los registros que coincidan con tipoemulador, semana, folio_paco e id_contratista
    $sql = "
        SELECT * FROM Folios_Paco
        WHERE tipoemulador = :tipoemulador
          AND semana = :semana
          AND folio_paco = :folio_paco
          AND idContratistas = :idContratista
          AND estatus_orden = 'aceptada'
        ORDER BY id_folio_paco ASC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':tipoemulador' => $tipoemulador,
        ':semana' => $semana,
        ':folio_paco' => $folio_paco,
        ':idContratista' => $idContratista
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// OBTENER LOS CONCEPTOS DE MIGRACIONES AEREAS Y MIGRACIONES SUBTERRANEAS

public function obtenerConceptosMigracion()
{
    $rows = [];
    $conexion = $this->get_conexion();
    $sql = "SELECT id_campos_emulador, nombre
            FROM tipos_campo_emulador
            WHERE nombre LIKE :busqueda";

    $busqueda = '%migracion%';

    $statement = $conexion->prepare($sql);
    $statement->bindValue(':busqueda', $busqueda, PDO::PARAM_STR);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    //var_dump($rows);

    return $rows;
}

public function obtenerPrecioPorContratistaMigraciones($idContratista, $idConcepto)
{
    // Si no hay concepto, devolvemos null
    if (empty($idConcepto)) return null;

    $conexion = $this->get_conexion();

    // Consulta del precio para el contratista y concepto específico
    $sql = "SELECT precio 
            FROM precios 
            WHERE fk_idcontratista = :idContratista 
              AND fk_idconcepto = :idConcepto 
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':idConcepto', $idConcepto, PDO::PARAM_INT);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retornamos el precio si existe, si no null
    return $resultado ? $resultado['precio'] : null;
}

/*public function obtenerPrecioPorContratistaMigraciones($idContratista, $idConcepto)
{
    if (empty($idConcepto)) return null;

    $conexion = $this->get_conexion();
    $sql = "SELECT precio FROM precios WHERE fk_idcontratista = :idContratista AND fk_idconcepto = :idConcepto LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':idConcepto', $idConcepto, PDO::PARAM_INT);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    //var_dump($resultado);

    return $resultado ? $resultado['precio'] : null;
} */

public function InsertarPagosMigraciones($pagos)
{
    $conexion = $this->get_conexion();
    $sql = "INSERT INTO pagos 
    (semana, folio_paco_texto, tecnologia, nombre_contratista, id_contratista, folio_pisa, telefono, tipo_tarea, cope, empresa, metraje_aereo, metraje_subterraneo, precio, subtotal, tipoemulador) 
    VALUES 
    (:semana, :folio_paco_texto, :tecnologia, :nombre_contratista, :id_contratista, :folio_pisa, :telefono, :tipo_tarea, :cope, :empresa, :metraje_aereo, :metraje_subterraneo, :precio, :subtotal, :tipoemulador)";

    $stmt = $conexion->prepare($sql);

    try {
        $conexion->beginTransaction();
        foreach ($pagos as $pago) {
            $stmt->bindValue(':semana', $pago['semana'], PDO::PARAM_STR);
            $stmt->bindValue(':folio_paco_texto', $pago['folio_paco_texto'], PDO::PARAM_STR);
            $stmt->bindValue(':tecnologia', $pago['tecnologia'], PDO::PARAM_STR);
            $stmt->bindValue(':nombre_contratista', $pago['nombre_contratista'], PDO::PARAM_STR);
            $stmt->bindValue(':id_contratista', $pago['id_contratista'], PDO::PARAM_INT);
            $stmt->bindValue(':folio_pisa', $pago['folio_pisa'], PDO::PARAM_STR);
            $stmt->bindValue(':telefono', $pago['telefono'], PDO::PARAM_STR);
            $stmt->bindValue(':tipo_tarea', $pago['tipo_tarea'], PDO::PARAM_STR);
            $stmt->bindValue(':cope', $pago['cope'], PDO::PARAM_STR);
            $stmt->bindValue(':empresa', $pago['empresa'], PDO::PARAM_STR);
            $stmt->bindValue(':metraje_aereo', $pago['metraje_aereo'], PDO::PARAM_STR);
            $stmt->bindValue(':metraje_subterraneo', $pago['metraje_subterraneo'], PDO::PARAM_STR);
            $stmt->bindValue(':precio', $pago['precio'], PDO::PARAM_STR);
            $stmt->bindValue(':subtotal', $pago['subtotal'], PDO::PARAM_STR);
            $stmt->bindValue(':tipoemulador', $pago['tipoemulador'], PDO::PARAM_INT);

            $stmt->execute();
        }
        $conexion->commit();
        return "Inserción exitosa.";
    } catch (PDOException $e) {
        $conexion->rollBack();
        return "Error al insertar: " . $e->getMessage();
    }
}

public function obteneridemulador($emulador)
{
    $conexion = $this->get_conexion();
    $sql = "SELECT tipoemulador FROM emuladores_nueva WHERE emulador = :emulador LIMIT 1";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(':emulador', $emulador, PDO::PARAM_STR); // ← aquí estaba el error
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['tipoemulador'] : null; 
}

public function obtenertecnologiaemuladorm($emulador)
{
    $conexion = $this->get_conexion();
    $sql = "SELECT tecnologia FROM emuladores_nueva WHERE emulador = :emulador LIMIT 1";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(':emulador', $emulador, PDO::PARAM_STR); // ← aquí estaba el error
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['tecnologia'] : null; 
}

public function getFoliosPacofibra($idContratista, $tipoemulador, $semana)
{
    $rows = [];
    $conexion = $this->get_conexion();

    // Obtener id numérico del emulador filtrando por tecnologia = 'FIBRA'
    $sqlEmulador = "SELECT tipoemulador 
                    FROM emuladores_nueva 
                    WHERE emulador = :emulador 
                      AND tecnologia = 'FIBRA'
                    LIMIT 1";
    $stmtEmu = $conexion->prepare($sqlEmulador);
    $stmtEmu->bindParam(':emulador', $tipoemulador, PDO::PARAM_STR);
    $stmtEmu->execute();
    $tipoEmuladorId = $stmtEmu->fetchColumn();

    if (!$tipoEmuladorId) {
        return []; // No existe ese emulador para FIBRA
    }

    // Consulta para traer solo folio_paco únicos
    $sql = "SELECT DISTINCT folio_paco 
            FROM Folios_Paco 
            WHERE idContratistas = :idContratista 
              AND tipoemulador = :tipoemulador 
              AND semana = :semana 
              AND tecnologia = 'FIBRA'
            ORDER BY folio_paco";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':tipoemulador', $tipoEmuladorId, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
} 

public function getFoliosPacoa4($idContratista, $tipoemulador, $semana)
{
    $rows = [];
    $conexion = $this->get_conexion();

    // Obtener id numérico del emulador filtrando por tecnologia = 'FIBRA'
    $sqlEmulador = "SELECT tipoemulador 
                    FROM emuladores_nueva 
                    WHERE emulador = :emulador 
                      AND tecnologia = 'COBRE'
                    LIMIT 1";
    $stmtEmu = $conexion->prepare($sqlEmulador);
    $stmtEmu->bindParam(':emulador', $tipoemulador, PDO::PARAM_STR);
    $stmtEmu->execute();
    $tipoEmuladorId = $stmtEmu->fetchColumn();

    if (!$tipoEmuladorId) {
        return []; // No existe ese emulador para FIBRA
    }

    // Consulta para traer solo folio_paco únicos
    $sql = "SELECT DISTINCT folio_paco 
            FROM Folios_Paco 
            WHERE idContratistas = :idContratista 
              AND tipoemulador = :tipoemulador 
              AND semana = :semana 
              AND tecnologia = 'COBRE'
            ORDER BY folio_paco";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':tipoemulador', $tipoEmuladorId, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}


public function getFoliosPacoquejas($idContratista, $tipoemulador, $semana)
{
    $rows = [];
    $conexion = $this->get_conexion();

    // Obtener id numérico del emulador sin filtrar por tecnología
    $sqlEmulador = "SELECT tipoemulador 
                    FROM emuladores_nueva 
                    WHERE emulador = :emulador 
                    LIMIT 1";
    $stmtEmu = $conexion->prepare($sqlEmulador);
    $stmtEmu->bindParam(':emulador', $tipoemulador, PDO::PARAM_STR);
    $stmtEmu->execute();
    $tipoEmuladorId = $stmtEmu->fetchColumn();

    if (!$tipoEmuladorId) {
        return []; // No existe ese emulador
    }

    // Consulta para traer folio_paco únicos con tecnología FIBRA o COBRE
    $sql = "SELECT DISTINCT folio_paco 
            FROM Folios_Paco 
            WHERE idContratistas = :idContratista 
              AND tipoemulador = :tipoemulador 
              AND semana = :semana 
              AND tecnologia IN ('FIBRA', 'COBRE')
            ORDER BY folio_paco";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':tipoemulador', $tipoEmuladorId, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}




public function getFoliosPacocobre($idContratista, $tipoemulador, $semana)
{
    $rows = [];
    $conexion = $this->get_conexion();

    // Obtener id numérico del emulador filtrando también por tecnologia = 'COBRE'
    $sqlEmulador = "SELECT tipoemulador 
                    FROM emuladores_nueva 
                    WHERE emulador = :emulador 
                      AND tecnologia = 'COBRE'
                    LIMIT 1";
    $stmtEmu = $conexion->prepare($sqlEmulador);
    $stmtEmu->bindParam(':emulador', $tipoemulador, PDO::PARAM_STR);
    $stmtEmu->execute();
    $tipoEmuladorId = $stmtEmu->fetchColumn();

    if (!$tipoEmuladorId) {
        return []; // No existe ese emulador para COBRE
    }

    // Consulta para traer solo folio_paco únicos
    $sql = "SELECT DISTINCT folio_paco 
            FROM Folios_Paco 
            WHERE idContratistas = :idContratista 
              AND tipoemulador = :tipoemulador 
              AND semana = :semana 
              AND tecnologia = 'COBRE'
            ORDER BY folio_paco";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':tipoemulador', $tipoEmuladorId, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}




public function getFoliosmigraciones($idContratista, $tipoemulador, $semana)
{
    $rows = [];
    $conexion = $this->get_conexion();

    // Obtener id numérico del emulador
    $sqlEmulador = "SELECT tipoemulador FROM emuladores_nueva WHERE emulador = :emulador LIMIT 1";
    $stmtEmu = $conexion->prepare($sqlEmulador);
    $stmtEmu->bindParam(':emulador', $tipoemulador, PDO::PARAM_STR);
    $stmtEmu->execute();
    $tipoEmuladorId = $stmtEmu->fetchColumn();

    if (!$tipoEmuladorId) {
        return []; // No existe ese emulador
    }

    // Cambiar la consulta para que solo traiga folio_paco únicos
    $sql = "SELECT DISTINCT folio_paco 
        FROM Folios_Paco 
        WHERE idContratistas = :idContratista 
          AND tipoemulador = :tipoemulador 
          AND semana = :semana 
          AND tecnologia = 'FIBRA'
        ORDER BY folio_paco";

$stmt = $conexion->prepare($sql);
$stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
$stmt->bindParam(':tipoemulador', $tipoEmuladorId, PDO::PARAM_INT);
$stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
$stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $rows;
}

public function obteneremuladorcobre($folioPacoTexto)
    {
        $rows = [];
        $conexion = $this->get_conexion();

        $sql = "SELECT 
            folio_pisa, telefono, tipo_tarea, cope, empresa,
            bajante_aereo_25m, bajante_aereo_50m, bajante_aereo_75m, bajante_aereo_100m, bajante_aereo_125m,
            bajante_aereo_150m, bajante_aereo_175m, bajante_aereo_200m, bajante_aereo_250m, bajante_aereo_300m,
            ml_por_bajantes_aereos_mayor_300, bajante_subterraneo_25m, bajante_subterraneo_50m, bajante_subterraneo_75m,
            bajante_subterraneo_100m, bajante_subterraneo_125m, bajante_subterraneo_150m, bajante_subterraneo_175m,
            bajante_subterraneo_200m, bajante_subterraneo_250m, bajante_subterraneo_300m, ml_por_bajantes_subterraneos_mayor_300,
            desmontaje_en_migracion, visitas_adicionales, conexion_bajante_fo_fusion, radial_banqueta, radial_cepa_libre,
            reparacion_tropezon_radial, vsi, migra_exit_cobre_voz_fo_con_visita, inst_poste_25ft, direccion, distrito,
            facilidades_terminal_puerto, tipo_instalacion, nombre_cliente, numero_serie_modem, cuenta_clarovideo, latitud,
            longitud, expediente, nombre_tecnico, pic_no_pic
            FROM Folios_Paco
            WHERE folio_paco = :folio_paco";
        
        $statement = $conexion->prepare($sql);
        $statement->bindParam(':folio_paco', $folioPacoTexto, PDO::PARAM_STR);
        $statement->execute();

        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $result;
        }
        return $rows;
    }

    public function obtenernominacobre($folioPacoTexto)
{
    $rows = [];
    $conexion = $this->get_conexion();
    $sql = "SELECT 
                folio_pisa,
                telefono,
                tipo_tarea,
                cope,
                empresa,
                bajante_aereo_25m,
                bajante_aereo_50m,
                bajante_aereo_75m,
                bajante_aereo_100m,
                bajante_aereo_125m,
                bajante_aereo_150m,
                bajante_aereo_175m,
                bajante_aereo_200m,
                bajante_aereo_250m,
                bajante_aereo_300m,
                ml_por_bajantes_aereos_mayor_300,
                bajante_subterraneo_25m,
                bajante_subterraneo_50m,
                bajante_subterraneo_75m,
                bajante_subterraneo_100m,
                bajante_subterraneo_125m,
                bajante_subterraneo_150m,
                bajante_subterraneo_175m,
                bajante_subterraneo_200m,
                bajante_subterraneo_250m,
                bajante_subterraneo_300m,
                ml_por_bajantes_subterraneos_mayor_300
            FROM Folios_Paco
            WHERE folio_paco = :folio_paco
              AND estatus_orden = 'aceptada'";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':folio_paco', $folioPacoTexto, PDO::PARAM_STR);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}


public function obtenerIdConceptoPorString($stringConcepto)
    {
        $conexion = $this->get_conexion();
        $sql = "SELECT id_campos_emulador FROM tipos_campo_emulador WHERE string = :stringConcepto LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':stringConcepto' => $stringConcepto]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['id_campos_emulador'] ?? null;
    }

 public function obtenerPrecioCobre($idContratista, $idConcepto, $tecnologia = '', $semana = '')
    {
        if (!$idConcepto) return 0;
        $conexion = $this->get_conexion();

        $sql = "SELECT precio FROM precios WHERE fk_idcontratista = :idContratista AND fk_idconcepto = :idConcepto";
        $params = [
            ':idContratista' => $idContratista,
            ':idConcepto' => $idConcepto,
        ];

        if ($tecnologia !== '') {
            $sql .= " AND tecnologia = :tecnologia";
            $params[':tecnologia'] = $tecnologia;
        }
        if ($semana !== '') {
            $sql .= " AND semana = :semana";
            $params[':semana'] = $semana;
        }

        $stmt = $conexion->prepare($sql);
        $stmt->execute($params);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['precio'] ?? 0;
    }



    // NUEVA: obtener valor_defecto desde tipos_campo_emulador
    public function obtenerValorDefecto($idConcepto)
    {
        $conexion = $this->get_conexion();
        $sql = "SELECT valor_defecto FROM tipos_campo_emulador WHERE id_campos_emulador = :id LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id' => $idConcepto]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
if ($resultado && isset($resultado['valor_defecto'])) {
    return (float)$resultado['valor_defecto'];
}
return 0; // valor por defecto si no hay resultado
    }

    // NUEVA: obtener precio desde tabla 'precios' según contratista, concepto, tecnología y semana
    public function obtenerPrecioTabla($idContratista, $idConcepto)
{
    $conexion = $this->get_conexion();
    $sql = "SELECT precio FROM precios 
            WHERE fk_idcontratista = :idContratista 
              AND fk_idconcepto = :idConcepto 
            LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':idContratista' => $idContratista,
        ':idConcepto' => $idConcepto
    ]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado && isset($resultado['precio'])) {
        return (float)$resultado['precio'];
    }
    return 0;
}

public function GetFotos($folioPisa)
{
    $conexion = $this->get_conexion();
    $sql = "SELECT idtecnico_instalaciones_coordiapp, Foto_Casa_Cliente , Foto_Ont , No_Serie_ONT, Foto_INE, Foto_Puerto
            FROM tecnico_instalaciones_coordiapp
            WHERE Folio_Pisa = ?";
    $statement = $conexion->prepare($sql);
    $statement->execute([$folioPisa]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Devuelve exactamente lo que está en la base de datos
        return $row;
    }

    return false; // Si no encuentra registro
}

public function obtenerGrupo($foliopaco, $semana, $tipoEmulador, $fecha, $tipo_grupo = 'normal')
{
    $conexion = $this->get_conexion();

    // Revisar si ya existe
    $stmtGrupo = $conexion->prepare("SELECT id_grupo FROM folios_paco_grupo WHERE folio_paco_nombre = ?");
    $stmtGrupo->execute([$foliopaco]);
    $grupo = $stmtGrupo->fetch(PDO::FETCH_ASSOC);

    if ($grupo) {
        return $grupo['id_grupo'];
    } else {
        $stmtInsertGrupo = $conexion->prepare("
            INSERT INTO folios_paco_grupo (folio_paco_nombre, semana, tipoemulador, fecha_ingreso, tipo_grupo)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmtInsertGrupo->execute([$foliopaco, $semana, $tipoEmulador, $fecha, $tipo_grupo]);
        return $conexion->lastInsertId();
    }
}

public function insertarFolioPaco($datos)
{
    $conexion = $this->get_conexion();

    $sql = "INSERT INTO Folios_Paco 
    (id_grupo, idContratistas, tipoemulador, tecnologia, semana, folio_paco, fecha_ingreso, folio_pisa, telefono, tipo_tarea, cope, distrito, facilidades_terminal_puerto,
    bajante_aereo_25m, bajante_subterraneo_25m, bajante_aereo_50m, bajante_subterraneo_50m, bajante_aereo_75m, bajante_subterraneo_75m,
    bajante_aereo_100m, bajante_subterraneo_100m, bajante_aereo_125m, bajante_subterraneo_125m, bajante_aereo_150m, bajante_subterraneo_150m,
    bajante_aereo_175m, bajante_subterraneo_175m, bajante_aereo_200m, bajante_subterraneo_200m, bajante_aereo_250m, bajante_subterraneo_250m,
    bajante_aereo_300m, bajante_subterraneo_300m, tipo_instalacion, direccion, nombre_tecnico, nombre_cliente, latitud, longitud, expediente, fecha_liquidacion,
    numero_serie_modem, fecha_rechazo, tipo_tarea_tac, telefono_tac, cope_tac, num_serie_tac, nombre_tecnico_tac, expediente_tac, estatus_orden, alertas)
    VALUES
    (:id_grupo, :idContratistas, :tipoemulador, :tecnologia, :semana, :folio_paco, :fecha_ingreso, :folio_pisa, :telefono, :tipo_tarea, :cope, :distrito, :facilidades,
    :bajante_aereo_25m, :bajante_subterraneo_25m, :bajante_aereo_50m, :bajante_subterraneo_50m, :bajante_aereo_75m, :bajante_subterraneo_75m,
    :bajante_aereo_100m, :bajante_subterraneo_100m, :bajante_aereo_125m, :bajante_subterraneo_125m, :bajante_aereo_150m, :bajante_subterraneo_150m,
    :bajante_aereo_175m, :bajante_subterraneo_175m, :bajante_aereo_200m, :bajante_subterraneo_200m, :bajante_aereo_250m, :bajante_subterraneo_250m,
    :bajante_aereo_300m, :bajante_subterraneo_300m, :tipo_instalacion, :direccion, :nombre_tecnico, :nombre_cliente, :latitud, :longitud, :expediente, :fecha_liquidacion,
    :numero_serie_modem, :fecha_rechazo, :tipo_tarea_tac, :telefono_tac, :cope_tac, :num_serie_tac, :nombre_tecnico_tac, :expediente_tac, :estatus_orden, :alertas)";

    $stmt = $conexion->prepare($sql);
    $stmt->execute($datos);
}

public function ordenesrechazadas($idEmulador, $tecnologia, $semana, $idContratista)
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "SELECT 
                f.alertas,
                f.folio_pisa AS folio,
                f.folio_paco,
                f.telefono,
                f.telefono_tac,
                f.tipo_tarea,
                f.tipo_tarea_tac,
                f.cope,
                f.cope_tac,
                f.distrito,
                f.facilidades_terminal_puerto,
                f.tipo_instalacion,
                f.nombre_tecnico,
                f.nombre_tecnico_tac,
                f.numero_serie_modem,
                f.num_serie_tac,
                f.expediente,
                f.fecha_rechazo
            FROM Folios_Paco f
            INNER JOIN contratistas c ON f.idContratistas = c.idContratistas
            WHERE f.tipoemulador = :emulador
              AND f.tecnologia = :tecnologia
              AND f.semana = :semana
              AND f.idContratistas = :idContratista
              AND f.estatus_orden = 'rechazada'
            ORDER BY f.fecha_rechazo DESC";

    $statement = $conexion->prepare($sql);
    $statement->bindValue(":emulador", $idEmulador, PDO::PARAM_INT);
    $statement->bindValue(":tecnologia", $tecnologia, PDO::PARAM_STR);
    $statement->bindValue(":semana", $semana, PDO::PARAM_STR);
    $statement->bindValue(":idContratista", $idContratista, PDO::PARAM_INT);

    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}

public function getConceptosEmuladorA4()
{
    $conexion = $this->get_conexion();

    // Consulta para obtener todos los conceptos con su id y nombre
    $sql = "SELECT id_campos_emulador, nombre 
            FROM tipos_campo_emulador
            ORDER BY id_campos_emulador ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($registros)) {
        // Opcional: mensaje de depuración
        // echo "<p>No se encontraron conceptos en la tabla tipos_campo_emulador.</p>";
        return [];
    }

    return $registros;
}


public function obtenerPrecioA4($idContratista, $idConcepto)
{
    $conexion = $this->get_conexion();

    $sql = "SELECT precio
            FROM precios
            WHERE fk_idcontratista = :idContratista 
              AND fk_idconcepto = :idConcepto
            ORDER BY fechas_registro_precios DESC
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':idConcepto', $idConcepto, PDO::PARAM_INT);
    $stmt->execute();

    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    return $registro ? $registro['precio'] : null;
}


public function getPrecioConceptoContratistaA4($idContratista, $idConcepto, $semana = null)
{
    $conexion = $this->get_conexion();

    // Consulta para obtener el precio del concepto para el contratista
    $sql = "SELECT p.id, p.fk_idcontratista, p.fk_idconcepto, p.precio, p.tecnologia, p.semana, p.fechas_registro_precios,
                   t.nombre AS nombre_concepto
            FROM precios p
            INNER JOIN tipos_campo_emulador t 
                ON p.fk_idconcepto = t.id_campos_emulador
            WHERE p.fk_idcontratista = :idContratista
              AND p.fk_idconcepto = :idConcepto";

    // Si se pasa semana, filtramos también por semana
    if ($semana !== null) {
        $sql .= " AND p.semana = :semana";
    }

    $stmt = $conexion->prepare($sql);

    $params = [
        ':idContratista' => $idContratista,
        ':idConcepto' => $idConcepto
    ];

    if ($semana !== null) {
        $params[':semana'] = $semana;
    }

    $stmt->execute($params);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($registros)) {
        // Opcional: mensaje de depuración
        // echo "<p>No se encontraron precios para el contratista $idContratista y concepto $idConcepto.</p>";
        return [];
    }

    return $registros;
}

/*public function insertarFolioPaco($datos)
{
    $conexion = $this->get_conexion();

    $sql = "INSERT INTO Folios_Paco 
        (id_grupo, idContratistas, tipoemulador, tecnologia, semana, folio_paco, fecha_ingreso, folio_pisa, telefono, tipo_tarea, cope, distrito, facilidades_terminal_puerto,
        bajante_aereo_25m, bajante_subterraneo_25m, bajante_aereo_50m, bajante_subterraneo_50m, bajante_aereo_75m, bajante_subterraneo_75m,
        bajante_aereo_100m, bajante_subterraneo_100m, bajante_aereo_125m, bajante_subterraneo_125m, bajante_aereo_150m, bajante_subterraneo_150m,
        bajante_aereo_175m, bajante_subterraneo_175m, bajante_aereo_200m, bajante_subterraneo_200m, bajante_aereo_250m, bajante_subterraneo_250m,
        bajante_aereo_300m, bajante_subterraneo_300m, tipo_instalacion, direccion, nombre_tecnico, nombre_cliente, latitud, longitud, expediente, fecha_liquidacion,
        numero_serie_modem)
        VALUES
        (:id_grupo, :idContratistas, :tipoemulador, :tecnologia, :semana, :folio_paco, :fecha_ingreso, :folio_pisa, :telefono, :tipo_tarea, :cope, :distrito, :facilidades,
        :bajante_aereo_25m, :bajante_subterraneo_25m, :bajante_aereo_50m, :bajante_subterraneo_50m, :bajante_aereo_75m, :bajante_subterraneo_75m,
        :bajante_aereo_100m, :bajante_subterraneo_100m, :bajante_aereo_125m, :bajante_subterraneo_125m, :bajante_aereo_150m, :bajante_subterraneo_150m,
        :bajante_aereo_175m, :bajante_subterraneo_175m, :bajante_aereo_200m, :bajante_subterraneo_200m, :bajante_aereo_250m, :bajante_subterraneo_250m,
        :bajante_aereo_300m, :bajante_subterraneo_300m, :tipo_instalacion, :direccion, :nombre_tecnico, :nombre_cliente, :latitud, :longitud, :expediente, :fecha_liquidacion,
        :numero_serie_modem)";

    $stmt = $conexion->prepare($sql);
    $stmt->execute($datos);
} */

/*public function insertarOrdenesRechazadas($datos)
{
    $conexion = $this->get_conexion();

    $sql = "INSERT INTO ordenes_rechazadas 
        (id_grupo, idContratistas, tipoemulador, tecnologia, semana, folio_paco, fecha_ingreso, folio_pisa, telefono, tipo_tarea, cope, distrito, facilidades_terminal_puerto,
        bajante_aereo_25m, bajante_subterraneo_25m, bajante_aereo_50m, bajante_subterraneo_50m, bajante_aereo_75m, bajante_subterraneo_75m,
        bajante_aereo_100m, bajante_subterraneo_100m, bajante_aereo_125m, bajante_subterraneo_125m, bajante_aereo_150m, bajante_subterraneo_150m,
        bajante_aereo_175m, bajante_subterraneo_175m, bajante_aereo_200m, bajante_subterraneo_200m, bajante_aereo_250m, bajante_subterraneo_250m,
        bajante_aereo_300m, bajante_subterraneo_300m, tipo_instalacion, direccion, nombre_tecnico, nombre_cliente, latitud, longitud, expediente, fecha_liquidacion,
        numero_serie_modem, alertas, atendida,tipo_tarea_tac,telefono_tac,cope_tac,nombre_tecnico_tac,num_serie_tac)
        VALUES
        (:id_grupo, :idContratistas, :tipoemulador, :tecnologia, :semana, :folio_paco, :fecha_ingreso, :folio_pisa, :telefono, :tipo_tarea, :cope, :distrito, :facilidades,
        :bajante_aereo_25m, :bajante_subterraneo_25m, :bajante_aereo_50m, :bajante_subterraneo_50m, :bajante_aereo_75m, :bajante_subterraneo_75m,
        :bajante_aereo_100m, :bajante_subterraneo_100m, :bajante_aereo_125m, :bajante_subterraneo_125m, :bajante_aereo_150m, :bajante_subterraneo_150m,
        :bajante_aereo_175m, :bajante_subterraneo_175m, :bajante_aereo_200m, :bajante_subterraneo_200m, :bajante_aereo_250m, :bajante_subterraneo_250m,
        :bajante_aereo_300m, :bajante_subterraneo_300m, :tipo_instalacion, :direccion, :nombre_tecnico, :nombre_cliente, :latitud, :longitud, :expediente, :fecha_liquidacion,
        :numero_serie_modem, :alertas, :atendida,:tipo_tarea_tac,:telefono_tac,:cope_tac,:nombre_tecnico_tac,:num_serie_tac)";

    $stmt = $conexion->prepare($sql);
    //var_dump($datos);
    $stmt->execute($datos);
} */

/*public function insertarOrdenRechazada($datos)
{
    $conexion = $this->get_conexion();

    // SQL actualizado para incluir atendida, semana y tecnologia
    $sql = "INSERT INTO ordenes_rechazadas 
            (folio_pisa, fk_contratista, alertas, fecha_rechazo, atendida, semana, tecnologia)
            VALUES 
            (:folio_pisa, :fk_contratista, :alertas, :fecha_rechazo, :atendida, :semana, :tecnologia)";

    $stmt = $conexion->prepare($sql);
    $stmt->execute($datos);
} */

/*public function insertarPago($datos)
{
    $conexion = $this->get_conexion();

    $sql = "INSERT INTO pagos 
        (emulador, semana, folio_paco_texto, tecnologia, nombre_contratista, id_contratista,
        folio_pisa, telefono, tipo_tarea, cope, empresa, metraje_aereo, metraje_subterraneo, precio, subtotal)
        VALUES 
        (:emulador, :semana, :folio_paco_texto, :tecnologia, :nombre_contratista, :id_contratista,
         :folio_pisa, :telefono, :tipo_tarea, :cope, :empresa, :metraje_aereo, :metraje_subterraneo, :precio, :subtotal)";

    $statement = $conexion->prepare($sql);

    return $statement->execute([
        ':emulador' => $datos['emulador'],
        ':semana' => $datos['semana'],
        ':folio_paco_texto' => $datos['folio_paco_texto'],
        ':tecnologia' => $datos['tecnologia'],
        ':nombre_contratista' => $datos['nombre_contratista'],
        ':id_contratista' => $datos['id_contratista'],
        ':folio_pisa' => $datos['folio_pisa'],
        ':telefono' => $datos['telefono'],
        ':tipo_tarea' => $datos['tipo_tarea'],
        ':cope' => $datos['cope'],
        ':empresa' => $datos['empresa'],
        ':metraje_aereo' => $datos['metraje_aereo'],
        ':metraje_subterraneo' => $datos['metraje_subterraneo'],
        ':precio' => $datos['precio'],
        ':subtotal' => $datos['subtotal']
    ]);
} */

/*public function getPrecioPorContratistaConceptoSemanaTecnologia($idContratista, $idConcepto) {
    $conexion = $this->get_conexion();

    var_dump("Parametros recibidos:");
    var_dump("idContratista:", $idContratista);
    var_dump("idConcepto:", $idConcepto);

    $sql = "SELECT precio FROM precios 
            WHERE fk_idcontratista = :idContratista 
              AND fk_idconcepto = :idConcepto 
            ORDER BY fechas_registro_precios DESC 
            LIMIT 1";

    var_dump("Consulta SQL:", $sql);

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $statement->bindParam(':idConcepto', $idConcepto, PDO::PARAM_INT);

    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);

    var_dump("Resultado consulta:", $result);

    if ($result && isset($result['precio'])) {
        return (float)$result['precio'];
    }

    return 0;
} */


}

?>