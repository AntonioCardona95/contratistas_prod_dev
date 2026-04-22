<?php 

ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL);

	/**
	 * DESARROLLADO ING. ANTONIO CARDONA
	 */
    require_once($_SERVER['DOCUMENT_ROOT']."/cnx/cnx.php");

    class Ops extends Conexion
    {
        // ===============================
    // CACHE (AQUI VA)
    // ===============================
private $cacheEsci = [];
private $cacheEsciTelefonos = [];
private $cacheEsciSerie = [];
private $cacheEsciData = [];

private $cacheTacExact = [];
private $cacheTacGeneral = [];
private $cacheTacOnt = [];

private $cacheLocal = [];

private $cacheCopes = [];


        public function CountOrdenesCoordiAppIncompletos($FK_Contratista_Tecnico,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();  
            $sql="SELECT COUNT(*) AS TOTAL FROM View_Detalle_Coordiapp_Incompletas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2 and FK_Contratista_Tecnico = :FK_Contratista_Tecnico";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Tecnico', $FK_Contratista_Tecnico, PDO::PARAM_STR);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

// NUEVA FUNCION CONSULTA EMULADORES //

/*public function ObtenerEmuladorContratista()
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "SELECT emulador
            FROM emuladores_nueva
            GROUP BY emulador
            ORDER BY emulador";

    $statement = $conexion->prepare($sql);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
} */

public function ObtenerEmuladorContratista()
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "SELECT DISTINCT nombre 
            FROM emulador
            ORDER BY nombre";

    $statement = $conexion->prepare($sql);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}


/*public function ObtenerEmuladorContratista()
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "SELECT nombre 
            FROM emulador
            ORDER BY nombre";

    $statement = $conexion->prepare($sql);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
} */

// ANTIGUA EMULADOR //

/*public function ObtenerEmuladorContratista()
{
    $rows = [];
    $conexion = $this->get_conexion();

    // Excluimos ALTAS y CAMBIOS DE DOMICILIO
    $sql = "SELECT DISTINCT emulador 
            FROM emuladores_nueva 
            WHERE emulador NOT IN ('ALTAS', 'CAMBIOS DE DOMICILIO')";

    $statement = $conexion->prepare($sql);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
} */



        /*public function getNombreEmulador()
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

        } */

        public function ObtenerTecnologiaEmuladorContratista()
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "SELECT DISTINCT tecnologia 
            FROM emulador
            ORDER BY tecnologia";

    $statement = $conexion->prepare($sql);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}

         // ANTIGUA TECNOLOGIA //

        /* public function ObtenerTecnologiaEmuladorContratista()
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

        } */

public function CountOrdenesCoordiApp($FK_Contratista_Tecnico,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2 and FK_Contratista_Tecnico = :FK_Contratista_Tecnico";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Tecnico', $FK_Contratista_Tecnico, PDO::PARAM_STR);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }
        
        public function OrdenesCoordiApp($FK_Contratista_Tecnico,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT * FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2 and FK_Contratista_Tecnico = :FK_Contratista_Tecnico";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Tecnico', $FK_Contratista_Tecnico, PDO::PARAM_STR);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function OrdenesCoordiAppIncompletas($FK_Contratista_Tecnico,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT * FROM View_Detalle_Coordiapp_Incompletas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2 and FK_Contratista_Tecnico = :FK_Contratista_Tecnico";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Tecnico', $FK_Contratista_Tecnico, PDO::PARAM_STR);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function TecnologiaKpi($FK_Contratista_Tecnico,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL, Tecnologia FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2 and FK_Contratista_Tecnico = :FK_Contratista_Tecnico
            GROUP BY Tecnologia
            ORDER BY TOTAL DESC";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Tecnico', $FK_Contratista_Tecnico, PDO::PARAM_STR);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function CountTopTecnico($FK_Contratista_Tecnico,$Fecha1,$Fecha2)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL, CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico, NExpediente FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2 and FK_Contratista_Tecnico = :FK_Contratista_Tecnico
            GROUP BY NExpediente
            ORDER BY TOTAL DESC
            LIMIT 10";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Tecnico', $FK_Contratista_Tecnico, PDO::PARAM_STR);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function ProduccionDivisiones($FK_Contratista_Tecnico,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL, Division FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2 and FK_Contratista_Tecnico = :FK_Contratista_Tecnico
            GROUP BY Division
            ORDER BY TOTAL DESC";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Tecnico', $FK_Contratista_Tecnico, PDO::PARAM_STR);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }  

        public function GetFotos($Folio_Pisa)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT * FROM tecnico_instalaciones_coordiapp 
            WHERE Folio_Pisa = :Folio_Pisa";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Folio_Pisa', $Folio_Pisa, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        } 

        public function OrdenesProduccion($FK_Contratista)
{
    $rows = null;
    $conexion = $this->get_conexion();

    $sql = "SELECT * FROM Produccion_contratista
            WHERE FK_Contratista = :FK_Contratista";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':FK_Contratista', $FK_Contratista, PDO::PARAM_INT);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}

public function VerificarFolios($folio, $telefono)
{
    $mensajes = [];
    $comparativo = '';

    // --- Validar en Coordiapp (conexion local)
    $conexionLocal = $this->get_conexion();
    $sql1 = "SELECT Folio_Pisa, Telefono FROM tecnico_instalaciones_coordiapp WHERE Folio_Pisa = :Folio LIMIT 1";
    $stmt1 = $conexionLocal->prepare($sql1);
    $stmt1->bindParam(':Folio', $folio, PDO::PARAM_STR);
    $stmt1->execute();
    $rowCoordiapp = $stmt1->fetch();

    if (!$rowCoordiapp) {
        $mensajes[] = "Folio No Capturado en Coordiapp";
    } else {
        if (empty($rowCoordiapp['Telefono'])) {
            $mensajes[] = "Telefono no capturado en Coordiapp";
        } elseif ($rowCoordiapp['Telefono'] !== $telefono) {
            $mensajes[] = "Telefono diferente en Coordiapp";
        }
    }

    // --- Validar en TAC (conexion analytics)
    $conexionAnalytics = $this->get_conexion_analytics();
    $sql2 = "SELECT Folio_Pisa, TELEFONO FROM qm_tac WHERE Folio_Pisa = :Folio LIMIT 1";
    $stmt2 = $conexionAnalytics->prepare($sql2);
    $stmt2->bindParam(':Folio', $folio, PDO::PARAM_STR);
    $stmt2->execute();
    $rowTac = $stmt2->fetch();

    if (!$rowTac) {
        $mensajes[] = "Folio No Existe en TAC";
    } else {
        if (empty($rowTac['TELEFONO'])) {
            $mensajes[] = "Telefono no existe en TAC";
        } elseif ($rowTac['TELEFONO'] !== $telefono) {
            $mensajes[] = "Telefono diferente en TAC";
        }
    }

    // --- Comparativo de teléfonos (solo si hay diferencia)
    if ($rowCoordiapp && $rowTac && $rowCoordiapp['Telefono'] !== $rowTac['TELEFONO']) {
        $comparativo = "
            <div style='display:flex; justify-content:space-between;'>
                <div><strong>Coordiapp</strong><br>" . htmlspecialchars($rowCoordiapp['Telefono']) . "</div>
                <div><strong>TAC</strong><br>" . htmlspecialchars($rowTac['TELEFONO']) . "</div>
            </div>
        ";
    }

    return [
        'alertas' => $mensajes,
        'comparativo' => $comparativo
    ];
}

/*public function InsertarProduccion($folio, $telefono, $tipoTarea, $cope, $fechaLiquidacion, $idContratistas, $semana)
{
    echo "<pre style='background:#111;color:#0f0;padding:10px;font-size:14px'>";
    echo "🟢 ENTRÓ A InsertarProduccion()\n\n";
    var_dump([
        'folio'            => $folio,
        'telefono'         => $telefono,
        'tipoTarea'        => $tipoTarea,
        'cope'             => $cope,
        'fechaLiquidacion' => $fechaLiquidacion,
        'idContratistas'   => $idContratistas,
        'semana'           => $semana
    ]);
    echo "</pre>";

    // ⛔ Detener ejecución para ver el resultado
    exit;

    // -------------------------
    // A partir de aquí NO llegará mientras esté el exit()
    // -------------------------

    $conexion = $this->get_conexion();
    $sql = "INSERT INTO Produccion_contratista 
            (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, FK_Contratista, Semana) 
            VALUES (:folio, :telefono, :tipoTarea, :cope, :fechaLiquidacion, :idContratistas, :semana)";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':folio', $folio, PDO::PARAM_STR);
    $statement->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $statement->bindParam(':tipoTarea', $tipoTarea, PDO::PARAM_STR);
    $statement->bindParam(':cope', $cope, PDO::PARAM_STR);
    $statement->bindParam(':fechaLiquidacion', $fechaLiquidacion, PDO::PARAM_STR);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_INT);

    return $statement->execute();
} */


/*public function InsertarProduccion($folio, $telefono, $tipoTarea, $cope, $fechaLiquidacion, $idContratistas, $semana)
{
    $conexion = $this->get_conexion();
    $sql = "INSERT INTO Produccion_contratista 
            (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, FK_Contratista, Semana) 
            VALUES (:folio, :telefono, :tipoTarea, :cope, :fechaLiquidacion, :idContratistas, :semana)";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(':folio', $folio, PDO::PARAM_STR);
    $statement->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $statement->bindParam(':tipoTarea', $tipoTarea, PDO::PARAM_STR);
    $statement->bindParam(':cope', $cope, PDO::PARAM_STR);
    $statement->bindParam(':fechaLiquidacion', $fechaLiquidacion, PDO::PARAM_STR);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    return $statement->execute();
} */

public function ExisteFolioProduccion($folio, $telefono, $idContratistas)
{
    $conexion = $this->get_conexion();

    $sql = "SELECT 1
            FROM Produccion_contratista
            WHERE FK_Contratista = :idContratistas
              AND (Folio_Pisa = :folio OR TELEFONO = :telefono)
            LIMIT 1";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':folio', $folio, PDO::PARAM_STR);
    $statement->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn() ? true : false;
}

/*public function InsertarProduccion($folio, $telefono, $tipoTarea, $cope, $fechaLiquidacion, $idContratistas, $semana, $anio, $metraje)
{
    //  CONEXIÓN LOCAL (INSERT)
    $conexion = $this->get_conexion();

    // CONEXIÓN ANALISIS (qm_tac)
    $conexion_analisis = $this->get_conexiondos();

    $conexion->exec("SET NAMES 'utf8mb4'");
    $conexion->exec("SET CHARACTER SET utf8mb4");

    $fechaCarga = date("Y-m-d H:i:s");

    // ===============================
    // CONSULTA EN BD ANALISIS
    // ===============================
    $sqlTac = "SELECT FECHA_LIQ 
               FROM qm_tac 
               WHERE Folio_Pisa = :folio 
               AND TRIM(UPPER(Calificador_Edo)) = 'COMPLETADA'
               LIMIT 1";

    $stmtTac = $conexion_analisis->prepare($sqlTac);
    $stmtTac->bindParam(':folio', $folio, PDO::PARAM_STR);
    $stmtTac->execute();
    $rowTac = $stmtTac->fetch(PDO::FETCH_ASSOC);

    $banderaRevision = 0;

    // ===============================
    // VALIDACIÓN REAL (HOY vs FECHA TAC)
    // ===============================
    if ($rowTac) {

        $fechaTac = $rowTac['FECHA_LIQ'];

        // Fecha actual
        $hoy = date("Y-m-d H:i:s");

        // Diferencia en días
        $segundos = strtotime($hoy) - strtotime($fechaTac);
        $diffDias = floor($segundos / (60 * 60 * 24));

        // Regla: más de 30 días → revisión
        if ($diffDias > 30) {
            $banderaRevision = 1;
        }

        
    }

    // ===============================
    // INSERT EN BD LOCAL
    // ===============================
    $sql = "INSERT INTO Produccion_contratista 
            (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, `Año`, metraje, Bandera_Revision) 
            VALUES (:folio, :telefono, :tipoTarea, :cope, :fechaLiquidacion, :fechaCarga, :idContratistas, :semana, :anio, :metraje, :banderaRevision)";
    
    $statement = $conexion->prepare($sql);

    $statement->bindParam(':folio', $folio, PDO::PARAM_STR);
    $statement->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $statement->bindParam(':tipoTarea', $tipoTarea, PDO::PARAM_STR);
    $statement->bindParam(':cope', $cope, PDO::PARAM_STR);
    $statement->bindParam(':fechaLiquidacion', $fechaLiquidacion, PDO::PARAM_STR);
    $statement->bindParam(':fechaCarga', $fechaCarga, PDO::PARAM_STR);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_STR);
    $statement->bindParam(':anio', $anio, PDO::PARAM_INT);
    $statement->bindParam(':metraje', $metraje, PDO::PARAM_INT);
    $statement->bindParam(':banderaRevision', $banderaRevision, PDO::PARAM_INT);

    $ejecutado = $statement->execute();

    return [
        'insertado' => $ejecutado,
        'revision' => $banderaRevision
    ];
} */

// DEBUG 
        /*
        echo "<pre>";
        echo "Folio: $folio\n";
        echo "TAC: $fechaTac\n";
        echo "HOY: $hoy\n";
        echo "DIF: $diffDias dias\n";
        echo "REVISION: $banderaRevision\n";
        echo "</pre>";
        */

// PRUEBA DON ORDEN TIPO //

public function InsertarProduccion(
    $folio,
    $telefono,
    $tipoTarea,
    $cope,
    $fechaLiquidacion,
    $idContratistas,
    $semana,
    $anio,
    $metraje
) {
    $conexion = $this->get_conexion();

    $conexion->exec("SET NAMES 'utf8mb4'");
    $conexion->exec("SET CHARACTER SET utf8mb4");

    $fechaCarga = date("Y-m-d H:i:s");

    $sql = "INSERT INTO Produccion_contratista 
            (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, anio, metraje) 
            VALUES (:folio, :telefono, :tipoTarea, :cope, :fechaLiquidacion, :fechaCarga, :idContratistas, :semana, :anio, :metraje)";
    
    $stmt = $conexion->prepare($sql);

    $ejecutado = $stmt->execute([
        ':folio' => $folio,
        ':telefono' => $telefono,
        ':tipoTarea' => $tipoTarea,
        ':cope' => $cope,
        ':fechaLiquidacion' => $fechaLiquidacion,
        ':fechaCarga' => $fechaCarga,
        ':idContratistas' => $idContratistas,
        ':semana' => $semana,
        ':anio' => $anio,
        ':metraje' => $metraje
    ]);

    return [
        'insertado' => $ejecutado,
        'fechaCarga' => $fechaCarga
    ];
}

public function InsertarAlertasMasivo($registros)
{
    if (empty($registros)) return;

    $conexion = $this->get_conexion();
    $conexion_analisis = $this->get_conexiondos();

    $conexion->exec("SET NAMES 'utf8mb4'");
    $conexion_analisis->exec("SET NAMES 'utf8mb4'");

    // =========================
    // 🔹 PREPARAR IDS
    // =========================
    $folios = array_unique(array_column($registros, 'folio'));
    $telefonos = array_unique(array_column($registros, 'telefono'));

    $inFolios = "'" . implode("','", $folios) . "'";
    $inTelefonos = "'" . implode("','", $telefonos) . "'";

    // =========================
    // 🔹 COORDIAPP
    // =========================
    $coordData = [];
    $sql = "SELECT Folio_Pisa, Ont, Tecnologia , Foto_Ont , validacion_LEDS_ONT_fallida, Foto_Casa_Cliente, validacion_foto_fallida,Foto_Puerto
            FROM tecnico_instalaciones_coordiapp
            WHERE Folio_Pisa IN ($inFolios)";
    foreach ($conexion->query($sql) as $row) {
        $coordData[$row['Folio_Pisa']] = $row;
    }

    // =========================
    // 🔹 TAC
    // =========================
    $tacData = [];
    $sql = "SELECT Folio_Pisa, TELEFONO, Num__Serie, TECNOLOGIA
            FROM qm_tac
            WHERE Folio_Pisa IN ($inFolios)
              AND Calificador_Edo = 'COMPLETADA'";
    foreach ($conexion_analisis->query($sql) as $row) {
        $tacData[$row['Folio_Pisa']] = $row;
    }

    // =========================
    // 🔥 ONT DUPLICADA TAC
    // =========================
    $sqlOntDuplicada = "
        SELECT Num__Serie
        FROM qm_tac
        WHERE Calificador_Edo = 'COMPLETADA'
          AND Num__Serie IS NOT NULL
          AND Num__Serie != ''
        GROUP BY Num__Serie
        HAVING COUNT(DISTINCT TELEFONO) > 1
    ";
    $ontDuplicadasTac = [];
    foreach ($conexion_analisis->query($sqlOntDuplicada) as $row) {
        $serie = strtoupper(trim($row['Num__Serie']));
        $ontDuplicadasTac[$serie] = true;
    }

    // =========================
    // 🔹 ESCI
    // =========================
    $esciData = [];
    $esciSeriesMap = [];
    $sql = "
        SELECT TELEFONO, M_SERIE, ULTIMO_EVENTO
        FROM seg_ns_eci
        WHERE TELEFONO IN ($inTelefonos)
    ";
    foreach ($conexion_analisis->query($sql) as $row) {
        $tel = trim($row['TELEFONO']);
        $serie = strtoupper(trim($row['M_SERIE']));
        if (!isset($esciData[$tel])) {
            $esciData[$tel] = $row;
            $esciData[$tel]['M_SERIE'] = $serie;
        }
        if (!empty($serie) && $serie !== 'DESCONOCIDO') {
            $esciSeriesMap[$serie][$tel] = true;
        }
    }

    // =========================
    // 🔥 INSERTS
    // =========================
    $stmtAlerta = $conexion->prepare("
        INSERT INTO Produccion_alertas
        (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, anio, metraje, Alerta, Detalle_Esci, Bandera_Revision)
        VALUES (?,?,?,?,?,?,?,?,?,?,?, ?, ?)
    ");

    $stmtDetalle = $conexion->prepare("
        INSERT INTO Produccion_alertas_detalle
        (Folio_Pisa, Tipo_Alerta, Activa, Fecha_Creacion, Detalle_Esci, Detalle_Tac)
        VALUES (?,?,?,?,?,?)
    ");

    // =========================
    // 🔥 LOOP PRINCIPAL
    // =========================
    foreach ($registros as $r) {

        $folio = $r['folio'];
        $telefono = trim($r['telefono']);

        $coord = $coordData[$folio] ?? null;
        $tac   = $tacData[$folio] ?? null;
        $esci  = $esciData[$telefono] ?? null;

        // SERIES
        $serieTac   = isset($tac['Num__Serie']) ? strtoupper(trim($tac['Num__Serie'])) : null;
        $serieCoord = isset($coord['Ont']) ? strtoupper(trim($coord['Ont'])) : null;
        $serieEsci  = $esci['M_SERIE'] ?? null;

        // MOTOR DE ALERTAS
        $resultadoAlertas = $this->evaluarAlertas([
            'folio' => $folio,
            'telefono' => $telefono,
            'coord' => $coord,
            'tac' => $tac,
            'esci' => $esci,
            'serieTac' => $serieTac,
            'serieCoord' => $serieCoord,
            'serieEsci' => $serieEsci,
            'ontDuplicadasTac' => $ontDuplicadasTac,
            'esciSeriesMap' => $esciSeriesMap,
            'idContratista' => $r['idContratista']
        ]);

        if (!empty($resultadoAlertas['alertas'])) {

            // INSERT en Produccion_alertas (concatenado)
            $stmtAlerta->execute([
                $folio,
                $telefono,
                $r['tipoTarea'] ?? null,
                $r['cope'] ?? null,
                $r['fechaLiquidacion'] ?? null,
                $r['fechaCarga'] ?? date('Y-m-d H:i:s'),
                $r['idContratista'] ?? null,
                $r['semana'] ?? null,
                $r['anio'] ?? null,
                $r['metraje'] ?? null,
                implode(' | ', array_unique($resultadoAlertas['alertas'])),
                $resultadoAlertas['detalleEsci'] ?? null,
                0
            ]);

            // INSERT en Produccion_alertas_detalle (una fila por cada alerta)
            foreach ($resultadoAlertas['alertas'] as $tipoAlerta) {
                $stmtDetalle->execute([
                    $folio,
                    $tipoAlerta,
                    1,
                    date('Y-m-d H:i:s'),
                    $resultadoAlertas['detalleEsci'] ?? null,
                    $resultadoAlertas['detalleTac'] ?? null
                ]);
            }
        }
    }
}


private function evaluarAlertas($data)
{
    $alertas = [];
    $detalleEsci = null;
    $detalleTac = null;

    $this->alertaOntDuplicadaTac($data, $alertas, $detalleTac);
    $this->alertaOntDuplicadaEsci($data, $alertas, $detalleEsci);
    $this->alertaSerieDesconocida($data, $alertas);
    $this->alertaEventoEsci($data, $alertas, $detalleEsci);
    $this->alertaSeriesDiferentes($data, $alertas);
    $this->alertaTecnologiaDiferente($data, $alertas);

    // 🔥 CORREGIDAS
    $this->alertaFotoOntFallida($data, $alertas); // ✅ SIN detalleTac
    $this->alertaLedsOntFallida($data, $alertas);
    $this->alertaTerminalFallida($data, $alertas);
    $this->alertaTerminalFallidaIA($data, $alertas);
    $this->alertaFotoDomicilioFallida($data, $alertas);
    $this->alertaOntAlmacen($data, $alertas);
    $this->alertaFotoCasaCliente($data, $alertas);
    

    return [
        'alertas' => $alertas,
        'detalleEsci' => $detalleEsci,
        'detalleTac' => $detalleTac
    ];
}

private function alertaTerminalFallida($data, &$alertas)
{
    $foto = $data['coord']['Foto_Puerto'] ?? '';

    if (empty($foto) || !preg_match('/\.(jpg|jpeg|png)$/i', $foto)) {
        $alertas[] = 'FOTO TERMINAL FALLIDA';
        return;
    }

    if (strpos($foto, '../') === false) {
        $alertas[] = 'FOTO TERMINAL FALLIDA';
        return;
    }
}



// 🔥 FOTO GENERAL (NO TAC)
private function alertaFotoOntFallida($data, &$alertas)
{
    $foto = $data['coord']['Foto_Ont'] ?? '';

    if (empty($foto) || !preg_match('/\.(jpg|jpeg|png)$/i', $foto)) {
        $alertas[] = 'FOTO_ONT_FRONTAL_FALLIDA';
        return;
    }

    if (strpos($foto, '../') === false) {
        $alertas[] = 'FOTO_ONT_FRONTAL_FALLIDA';
        return;
    }
}

// 🔥 FOTO CASA CLIENTE
private function alertaFotoCasaCliente($data, &$alertas)
{
    $foto = $data['coord']['Foto_Casa_Cliente'] ?? '';

    if (empty($foto) || !preg_match('/\.(jpg|jpeg|png)$/i', $foto)) {
        $alertas[] = 'FOTO_CASA_CLIENTE_FALLIDA';
        return;
    }

    if (strpos($foto, '../') === false) {
        $alertas[] = 'FOTO_CASA_CLIENTE_FALLIDA';
        return;
    }
}



// 🔥 LEDS
private function alertaLedsOntFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_LEDS_ONT_fallida'] ?? 0;

    if ((int)$valor === 1) {
        $alertas[] = 'EVIDENCIA_LEDS_ONT_FALLIDA';
    }
}

// 🔥 FOTO DOMICILIO
private function alertaFotoDomicilioFallida($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;

    if ((int)$valor === 1) {
        $alertas[] = 'FOTO_DOMICILIO_FALLIDA';
    }
}

private function alertaTerminalFallidaIA($data, &$alertas)
{
    $valor = $data['coord']['validacion_foto_fallida'] ?? 0;

    if ((int)$valor === 1) {
        $alertas[] = 'FOTO TERMINAL FALLIDA';
    }
}



// =========================
// 🔥 ALERTAS
// =========================

private function alertaOntDuplicadaTac($data, &$alertas, &$detalleTac)
{
    if ($data['serieTac'] && isset($data['ontDuplicadasTac'][$data['serieTac']])) {
        $alertas[] = 'ONT_DUPLICADA_EN_TAC';
        $detalleTac = "Serie {$data['serieTac']} duplicada en TAC";
    }
}

private function alertaOntDuplicadaEsci($data, &$alertas, &$detalleEsci)
{
    $serie = $data['serieEsci'];

    if ($serie && isset($data['esciSeriesMap'][$serie])) {
        if (count($data['esciSeriesMap'][$serie]) > 1) {
            $alertas[] = 'ONT_DUPLICADA_EN_ESCI';
            $detalleEsci = "Serie $serie duplicada en ESCI";
        }
    }
}

private function alertaSerieDesconocida($data, &$alertas)
{
    if ($data['serieEsci'] === 'DESCONOCIDO') {
        $alertas[] = 'SERIE_DESCONOCIDA';
    }
}

private function alertaEventoEsci($data, &$alertas, &$detalleEsci)
{
    $evento = $data['esci']['ULTIMO_EVENTO'] ?? null;
    $serie = $data['serieEsci'];

    if ($evento !== null) {

        if ($evento === 0) {
            $alertas[] = 'SIN_NAVEGACION';
            $detalleEsci = "Serie $serie sin navegación";
        }

        if ($evento === -1) {
            $alertas[] = 'SERVICIO_INACTIVO';
            $detalleEsci = "Serie $serie con servicio inactivo";
        }
    }
}

private function alertaSeriesDiferentes($data, &$alertas)
{
    // Verificar que todos los campos de número de serie existan
    $serieTac = $data['serieTac'];
    $serieCoord = $data['serieCoord'];
    $serieEsci = $data['serieEsci'];
    
    // Si alguna de las series es 'DESCONOCIDO' se puede omitir en la comparación
    if ($serieTac && $serieCoord && $serieEsci !== 'DESCONOCIDO') {
        // Compara los números de serie si no son "desconocidos"
        $series = array_filter([$serieTac, $serieCoord, $serieEsci]);

        // Si hay al menos dos valores diferentes en las series
        if (count($series) > 1 && count(array_unique($series)) !== 1) {
            $alertas[] = 'NUMERO_SERIE_DIFERENTE';
        }
    }

    // Comparar los teléfonos entre coordiap, tac y esci
    $telefonoCoord = $data['coord']['Telefono'] ?? null;
    $telefonoTac = $data['tac']['TELEFONO'] ?? null;
    $telefonoEsci = $data['esci']['TELEFONO'] ?? null;

    if ($telefonoCoord && $telefonoTac && $telefonoEsci) {
        if ($telefonoCoord !== $telefonoTac || $telefonoCoord !== $telefonoEsci || $telefonoTac !== $telefonoEsci) {
            $alertas[] = 'TELEFONOS_DIFERENTES';
        }
    }
}

/*private function alertaSeriesDiferentes($data, &$alertas)
{
    $series = array_filter([
        $data['serieTac'],
        $data['serieCoord'],
        $data['serieEsci']
    ]);

    if (count($series) > 1) {
        if (!(count(array_unique($series)) === 1)) {
            $alertas[] = 'NUMERO_SERIE_DIFERENTE';
        }
    }
} */

private function alertaTecnologiaDiferente($data, &$alertas)
{
    // Normalizar los valores de TAC y CoordIAPP
    $tecTacRaw   = $data['tac']['TECNOLOGIA'] ?? null;
    $tecCoordRaw = $data['coord']['Tecnologia'] ?? null;

    // 🔥 Normalizamos los valores
    $tecTac   = $this->normalizarTecnologiaAlertas($tecTacRaw);
    $tecCoord = $this->normalizarTecnologiaAlertas($tecCoordRaw);

    // Si la tecnología de TAC está definida y la de CoordIAPP está vacía
    if ($tecTac && !$tecCoord) {
        $alertas[] = 'TECNOLOGIA_DIFERENTE';
    }

    // Comparamos las tecnologías normalizadas
    elseif ($tecTac && $tecCoord && $tecTac !== $tecCoord) {
        $alertas[] = 'TECNOLOGIA_DIFERENTE';
    }
}

private function normalizarTecnologiaAlertas($valor)
{
    if (!$valor) return null;

    $valor = strtoupper(trim($valor));
    $valor = preg_replace('/\s+/', ' ', $valor);

    $valorSinEspacios = str_replace(' ', '', $valor);

    // =========================
    //  REGLAS INTELIGENTES
    // =========================

    //  CUALQUIER COSA QUE CONTENGA "FIBRA"
    if (strpos($valor, 'FIBRA') !== false) {
        return 'FIBRA';
    }

    //  COBRE
    if (strpos($valor, 'COBRE') !== false) {
        return 'COBRE';
    }

    // =========================
    //  MAPEO ESPECÍFICO
    // =========================
    $map = [
        'CAMBACEO' => 'FIBRA',
        'CAMBIOFIBRA' => 'FIBRA',
        'CAMBIO FIBRA' => 'FIBRA'
    ];

    return $map[$valor] ?? $map[$valorSinEspacios] ?? $valor;
}

private function alertaOntAlmacen($data, &$alertas)
{
    // 🔥 VIENE DE COORDIAPP
    $ont = isset($data['coord']['Ont']) 
        ? strtoupper(trim($data['coord']['Ont'])) 
        : '';

    if ($ont === '') return;

    // 🔥 NORMALIZAR
    $ont = $this->normalizarSerie($ont);

    $conexion = $this->get_conexion();

    $stmt = $conexion->prepare("
        SELECT FK_Contratista_Salida
        FROM material_det_salida
        WHERE REPLACE(UPPER(Num_Serie_S), ' ', '') = ?
        LIMIT 1
    ");

    $stmt->execute([$ont]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // =========================
    // 🔥 ALERTAS
    // =========================

    if (!$row) {
        $alertas[] = 'ONT_NO_ENCONTRADA_EN_ALMACEN';
        return;
    }

    if (empty($row['FK_Contratista_Salida'])) {
        $alertas[] = 'ONT_NO_ASIGNADA_AL_CONTRATISTA';
        return;
    }

    if ((int)$row['FK_Contratista_Salida'] !== (int)$data['idContratista']) {
        $alertas[] = 'ONT_ASIGNADA_A_OTRO_CONTRATISTA';
        return;
    }
}



/*private function normalizarTecnologiaAlertas($valor)
{
    if (!$valor) return null;

    // Convertir todo a mayúsculas y quitar espacios en blanco al principio y al final
    $valor = strtoupper(trim($valor));

    //  Mapeo de equivalencias
    $map = [
        'CAMBACEO' => 'FIBRA',         // CAMBACEO lo tratamos como FIBRA
        'CAMBIOFIBRA' => 'FIBRA',    // CAMBIO FIBRA lo tratamos como FIBRA
        'FIBRA' => 'FIBRA',           // FIBRA se mantiene como FIBRA
        'COBRE' => 'COBRE'            // COBRE se mantiene como COBRE
        // Puedes agregar más equivalencias si es necesario
    ];

    return $map[$valor] ?? $valor;  // Devuelve el valor mapeado o el valor original
} */


/*public function InsertarAlertasMasivo($registros)
{
    if (empty($registros)) return;

    $conexion = $this->get_conexion();
    $conexion_analisis = $this->get_conexiondos();

    $conexion->exec("SET NAMES 'utf8mb4'");
    $conexion_analisis->exec("SET NAMES 'utf8mb4'");

    // =========================
    // 🔹 PREPARAR IDS
    // =========================
    $folios = array_unique(array_column($registros, 'folio'));
    $telefonos = array_unique(array_column($registros, 'telefono'));

    $inFolios = "'" . implode("','", $folios) . "'";
    $inTelefonos = "'" . implode("','", $telefonos) . "'";

    // =========================
    // 🔹 COORDIAPP
    // =========================
    $coordData = [];
    $sql = "SELECT Folio_Pisa, Telefono, Tipo_Tarea, Ont, Tecnologia,
                   Foto_Casa_Cliente, Foto_Puerto, Foto_Ont, No_Serie_ONT, Foto_INE,
                   validacion_foto_fallida, validacion_LEDS_ONT_fallida
            FROM tecnico_instalaciones_coordiapp
            WHERE Folio_Pisa IN ($inFolios)";
    foreach ($conexion->query($sql) as $row) {
        $coordData[$row['Folio_Pisa']] = $row;
    }

    // =========================
    // 🔹 TAC
    // =========================
    $tacData = [];
    $sql = "SELECT Folio_Pisa, TELEFONO, Tipo_de_Tarea, Num__Serie, TECNOLOGIA
            FROM qm_tac
            WHERE Folio_Pisa IN ($inFolios)
              AND Calificador_Edo = 'COMPLETADA'";
    foreach ($conexion_analisis->query($sql) as $row) {
        $tacData[$row['Folio_Pisa']] = $row;
    }

    // =========================
    // 🔥 ONT DUPLICADA TAC
    // =========================
    $sqlOntDuplicada = "
        SELECT Num__Serie
        FROM qm_tac
        WHERE Calificador_Edo = 'COMPLETADA'
          AND Num__Serie IS NOT NULL
          AND Num__Serie != ''
        GROUP BY Num__Serie
        HAVING COUNT(DISTINCT TELEFONO) > 1
    ";

    $ontDuplicadas = [];
    foreach ($conexion_analisis->query($sqlOntDuplicada) as $row) {
        $serie = strtoupper(trim($row['Num__Serie']));
        $ontDuplicadas[$serie] = true;
    }

    // =========================
    // 🔹 ESCI
    // =========================
    $esciData = [];
    $seriesMap = [];

    $sql = "
        SELECT TELEFONO, M_SERIE, ULTIMO_EVENTO, FECHA_ULTIMO_EVENTO
        FROM seg_ns_eci
        WHERE M_SERIE IN (
            SELECT M_SERIE FROM seg_ns_eci WHERE TELEFONO IN ($inTelefonos)
        )
        ORDER BY FECHA_ULTIMO_EVENTO DESC
    ";

    foreach ($conexion_analisis->query($sql) as $row) {

        $tel = trim($row['TELEFONO']);
        $serie = strtoupper(trim($row['M_SERIE']));

        if (!isset($esciData[$tel])) {
            $row['M_SERIE'] = $serie;
            $esciData[$tel] = $row;
        }

        if (!empty($serie) && $serie !== 'DESCONOCIDO') {
            $seriesMap[$serie][] = $tel;
        }
    }

    // =========================
    // 🔥 PREPARES
    // =========================
    $stmtAlerta = $conexion->prepare("
        INSERT INTO Produccion_alertas
        (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, anio, metraje, Alerta)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)
    ");

    // 🔥 CAMBIO IMPORTANTE: ahora 7 campos
    $stmtDetalle = $conexion->prepare("
        INSERT INTO Produccion_alertas_detalle
        (Folio_Pisa, Tipo_Alerta, Activa, Fecha_Creacion, Fecha_Resolucion, Detalle_Esci, Detalle_Tac)
        VALUES (?,?,?,?,?,?,?)
    ");

    // =========================
    // 🔥 LOOP PRINCIPAL
    // =========================
    foreach ($registros as $r) {

        $folio = $r['folio'];
        $telefono = trim($r['telefono']);

        $coord = $coordData[$folio] ?? null;
        $tac   = $tacData[$folio] ?? null;
        $esci  = $esciData[$telefono] ?? null;

        $alertasResumen = [];
        $detalles = [];

        // 🔥 NUEVO
        $detalleEsci = null;
        $detalleTac = null;

        // =========================
        // NORMALIZACIÓN (NO TOCAR)
        // =========================
        $telefonoTac = $tac['TELEFONO'] ?? $telefono;

        $tipoTac = $tac['Tipo_de_Tarea'] ?? $r['tipoTarea'];
        $tipoCoord = $coord['Tipo_Tarea'] ?? null;

        $tipoTacNorm = $tipoTac ? strtoupper(trim($tipoTac)) : null;
        $tipoCoordNorm = $tipoCoord ? strtoupper(trim($tipoCoord)) : null;

        $serieTac = isset($tac['Num__Serie']) ? strtoupper(trim($tac['Num__Serie'])) : null;
        $serieCoord = isset($coord['Ont']) ? strtoupper(trim($coord['Ont'])) : null;

        $tecTac = isset($tac['TECNOLOGIA']) ? strtoupper(trim($tac['TECNOLOGIA'])) : null;
        $tecCoord = isset($coord['Tecnologia']) ? strtoupper(trim($coord['Tecnologia'])) : null;

        if ($tecTac) $tecTac = str_replace(' ', '', $tecTac);
        if ($tecCoord) $tecCoord = str_replace(' ', '', $tecCoord);

        if ($tecTac === 'CAMBACEO' || $tecTac === 'CAMBIOFIBRA') {
            $tecTac = 'FIBRA';
        }

        // =========================
        // 🔴 TODAS TUS VALIDACIONES (NO TOCAR)
        // =========================
        if ($coord) {

            $tipoTareaFoto = strtoupper(trim($coord['Tipo_Tarea'] ?? $tipoTac ?? ''));
            $codigoLiquidacion = strtoupper(trim($r['codigoLiquidacion'] ?? ''));

            $camposFotos = [];
            $usaPuertoCasa = false;
            $usaOntFoto = false;

            if (strpos($tipoTareaFoto, 'Q') === 0) {

                if (in_array($codigoLiquidacion, ['JUMPER/PARALELO EXTERIOR','CONECTOR MECANICO DAÑADO','CAMBIO DE PUERTO','CAMBIO DE TERMINAL'])) {
                    $camposFotos = ['Foto_Casa_Cliente','Foto_Puerto'];
                    $usaPuertoCasa = true;

                } elseif ($codigoLiquidacion === 'MODEM DAÑADO') {
                    $camposFotos = ['Foto_Casa_Cliente','Foto_Ont'];
                    $usaOntFoto = true;

                } elseif (in_array($codigoLiquidacion, ['CAMBIO PAR PRINCIPAL','CAMBIO PAR SECUNDARIO','ROSETA OPTICA','CAMBIO DE INTERIOR'])) {
                    $camposFotos = ['Foto_Casa_Cliente'];

                } else {
                    $camposFotos = ['Foto_Casa_Cliente','Foto_Puerto','Foto_Ont'];
                    $usaPuertoCasa = true;
                    $usaOntFoto = true;
                }

            } else {
                $camposFotos = ['Foto_Puerto','Foto_Casa_Cliente','Foto_Ont','No_Serie_ONT','Foto_INE'];
                $usaPuertoCasa = true;
                $usaOntFoto = true;
            }

            foreach ($camposFotos as $campo) {
                $valor = trim($coord[$campo] ?? '');
                if ($valor === '' || !preg_match('/\.(jpg|jpeg|png)$/i', $valor)) {
                    $alertasResumen[] = 'FOTO_FALTANTE';
                    break;
                }
            }

            if (!empty($coord['validacion_foto_fallida']) && $usaPuertoCasa) {
                $alertasResumen[] = 'FALLA_FOTO_GENERAL';
            }

            if (!empty($coord['validacion_LEDS_ONT_fallida']) && $usaOntFoto) {
                $alertasResumen[] = 'FALLA_FOTO_ONT';
            }
        }

        // =========================
        // ALERTAS BASE (NO TOCAR)
        // =========================
        if ($serieTac && isset($ontDuplicadas[$serieTac])) {
            $alertasResumen[] = 'ONT_DUPLICADA_EN_TAC';
        }

        $serieEsci = $esci['M_SERIE'] ?? null;
        $ultimoEvento = $esci['ULTIMO_EVENTO'] ?? null;

        if ($serieEsci === 'DESCONOCIDO') $alertasResumen[] = 'SERIE_DESCONOCIDA';
        if ($ultimoEvento === 0) $alertasResumen[] = 'SIN_NAVEGACION';
        if ($ultimoEvento === -1) $alertasResumen[] = 'SERVICIO_INACTIVO';

        if ($tipoTacNorm !== $tipoCoordNorm) $alertasResumen[] = 'TIPO_TAREA_DIFERENTE';
        if ($tecTac !== $tecCoord) $alertasResumen[] = 'TECNOLOGIA_DIFERENTE';
        if (!$coord) $alertasResumen[] = 'NO_EXISTE_COORDIAPP';

        // =========================
        // 🔥 DETALLE ESCI (AGREGADO)
        // =========================
        if ($serieEsci) {
            $detalleEsci = "El número de serie $serieEsci está registrado en ESCI";
        }

        // =========================
        // 🔥 DETALLE TAC (AGREGADO)
        // =========================
        if ($serieTac && isset($ontDuplicadas[$serieTac])) {
            $detalleTac = "El número de serie $serieTac está duplicado en TAC";
        }

        // =========================
        // NUMERO SERIE (NO TOCAR)
        // =========================
        $seriesValidas = array_filter([$serieTac, $serieCoord, $serieEsci]);

        if (count($seriesValidas) > 1) {
            if (!($serieTac === $serieCoord && $serieTac === $serieEsci)) {
                $alertasResumen[] = 'NUMERO_SERIE_DIFERENTE';
            }
        }

        // =========================
        // ONT ALMACEN (NO TOCAR)
        // =========================
        if ($serieCoord) {

            $ontAlmacen = $this->validarOntAlmacenNueva(
                $conexion,
                $serieCoord,
                $r['idContratista']
            );

            if ($ontAlmacen) {
                $alertasResumen[] = $ontAlmacen[0];

                $detalles[] = [
                    'tipo' => 'ONT_ALMACEN'
                ];
            }
        }

        // =========================
        // DETALLES (NO TOCAR)
        // =========================
        $alertasUnicas = array_unique($alertasResumen);

        foreach ($alertasUnicas as $alerta) {
            $detalles[] = [
                'tipo' => $alerta
            ];
        }

        // =========================
        // INSERT (SOLO AQUI CAMBIO)
        // =========================
        if (!empty($alertasResumen)) {

            $stmtAlerta->execute([
                $folio,
                $telefonoTac,
                $tipoTacNorm,
                $r['cope'],
                $r['fechaLiquidacion'],
                $r['fechaCarga'],
                $r['idContratista'],
                $r['semana'],
                $r['anio'],
                $r['metraje'],
                implode(' | ', $alertasUnicas)
            ]);

            foreach ($detalles as $d) {

                $stmtDetalle->execute([
                    $folio,
                    $d['tipo'],
                    1,
                    date('Y-m-d H:i:s'),
                    null,
                    $detalleEsci,
                    $detalleTac
                ]);
            }
        }
    }
}  */
private function validarOntAlmacenNueva($conexion_local, $ont, $idContratista)
{
    $ont = $this->normalizarSerie($ont);

    if ($ont === '') {
        return null;
    }

    $stmt = $conexion_local->prepare("
        SELECT FK_Contratista_Salida
        FROM material_det_salida
        WHERE REPLACE(UPPER(Num_Serie_S), ' ', '') = ?
        LIMIT 1
    ");

    $stmt->execute([$ont]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return ["ONT no se encuentra en almacén", "Serie: $ont"];
    }

    if (empty($row['FK_Contratista_Salida'])) {
        return ["ONT no asignada al contratista", "Serie: $ont"];
    }

    if ((int)$row['FK_Contratista_Salida'] !== (int)$idContratista) {
        return [
            "ONT asignada a otro contratista",
            "Serie: $ont (Asignado a: " . $row['FK_Contratista_Salida'] . ")"
        ];
    }

    return null;
}



// contros nuevos alertas //




///////////////////////////// PUEDE USARSE /////////////

/*public function InsertarAlerta(
    $folio,
    $telefonoTac,
    $tipoTareaTac,
    $cope,
    $fechaLiquidacion,
    $idContratistas,
    $semana,
    $anio,
    $metraje,
    $fechaCarga
) {
    $conexion = $this->get_conexion();
    $conexion_analisis = $this->get_conexiondos();

    $conexion->exec("SET NAMES 'utf8mb4'");
    $conexion_analisis->exec("SET NAMES 'utf8mb4'");

    $alertas = [];

    // =========================
    //  COORDIAPP (CORREGIDO: Ont)
    // =========================
    $stmt = $conexion->prepare("
        SELECT Telefono, Tipo_Tarea, Ont
        FROM tecnico_instalaciones_coordiapp
        WHERE Folio_Pisa = :folio
        LIMIT 1
    ");
    $stmt->execute([':folio' => $folio]);
    $coord = $stmt->fetch(PDO::FETCH_ASSOC);

    // =========================
    //  ESCI
    // =========================
    $stmt = $conexion_analisis->prepare("
        SELECT M_SERIE
        FROM seg_ns_eci
        WHERE TELEFONO = :telefono
        ORDER BY FECHA_ULTIMO_EVENTO DESC
        LIMIT 1
    ");
    $stmt->execute([':telefono' => $telefonoTac]);
    $esci = $stmt->fetch(PDO::FETCH_ASSOC);

    // =========================
    //  TAC (CORRECTO)
    // =========================
    $stmt = $conexion_analisis->prepare("
        SELECT TELEFONO, Tipo_de_Tarea, Num__Serie
        FROM qm_tac
        WHERE Folio_Pisa = :folio
        LIMIT 1
    ");
    $stmt->execute([':folio' => $folio]);
    $tac = $stmt->fetch(PDO::FETCH_ASSOC);

    // =========================
    //  NORMALIZACIÓN
    // =========================

    // TAC
    $telefonoTacDB = isset($tac['TELEFONO']) ? trim($tac['TELEFONO']) : trim($telefonoTac);
    $tipoTacDB = isset($tac['Tipo_de_Tarea']) ? trim($tac['Tipo_de_Tarea']) : trim($tipoTareaTac);
    $serieTac = isset($tac['Num__Serie']) ? trim($tac['Num__Serie']) : null;

    // COORDIAPP
    $telefonoCoord = isset($coord['Telefono']) ? trim($coord['Telefono']) : null;
    $tipoCoord = isset($coord['Tipo_Tarea']) ? trim($coord['Tipo_Tarea']) : null;
    $serieCoord = isset($coord['Ont']) ? trim($coord['Ont']) : null;

    // ESCI
    $serieEsci = isset($esci['M_SERIE']) ? trim($esci['M_SERIE']) : null;

    // =========================
    //  COMPARACIONES
    // =========================

    //  TELÉFONO
    if ($telefonoCoord && $telefonoTacDB !== $telefonoCoord) {
        $alertas[] = [
            'tipo'=>'TELEFONO_DIFERENTE',
            'tac'=>$telefonoTacDB,
            'coord'=>$telefonoCoord,
            'esci'=>null,
            'desc'=>'Teléfono diferente'
        ];
    }

    //  SERIE (usa ESCI si TAC viene vacío)
    $serieBase = !empty($serieTac) ? $serieTac : $serieEsci;

    if ($serieCoord && $serieBase && $serieBase !== $serieCoord) {
        $alertas[] = [
            'tipo'=>'SERIE_DIFERENTE',
            'tac'=>$serieTac,
            'coord'=>$serieCoord,
            'esci'=>$serieEsci,
            'desc'=>'Serie diferente'
        ];
    }

    //  TIPO TAREA
    if ($tipoCoord && $tipoTacDB !== $tipoCoord) {
        $alertas[] = [
            'tipo'=>'TIPO_TAREA_DIFERENTE',
            'tac'=>$tipoTacDB,
            'coord'=>$tipoCoord,
            'esci'=>null,
            'desc'=>'Tipo tarea diferente'
        ];
    }

    //  NO EXISTE EN COORDIAPP
    if (!$coord) {
        $alertas[] = [
            'tipo'=>'NO_EXISTE_COORDIAPP',
            'tac'=>$folio,
            'coord'=>null,
            'esci'=>null,
            'desc'=>'No existe en Coordiapp'
        ];
    }

    // =========================
    //  INSERT
    // =========================
    if (!empty($alertas)) {

        $alertaTexto = implode(' | ', array_column($alertas, 'tipo'));

        $stmt = $conexion->prepare("
            INSERT INTO Produccion_alertas
            (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, anio, metraje, Alerta)
            VALUES (:folio,:tel,:tipo,:cope,:fliq,:fcarga,:fk,:sem,:anio,:met,:alerta)
        ");

        $stmt->execute([
            ':folio' => $folio,
            ':tel' => $telefonoTacDB,
            ':tipo' => $tipoTacDB,
            ':cope' => $cope,
            ':fliq' => $fechaLiquidacion,
            ':fcarga' => $fechaCarga,
            ':fk' => $idContratistas,
            ':sem' => $semana,
            ':anio' => $anio,
            ':met' => $metraje,
            ':alerta' => $alertaTexto
        ]);

        $idAlerta = $conexion->lastInsertId();

        $stmtDetalle = $conexion->prepare("
            INSERT INTO Produccion_alertas_detalle
            (fk_alerta, tipo_alerta, valor_tac, valor_coordiapp, valor_esci, descripcion)
            VALUES (:fk,:tipo,:tac,:coord,:esci,:desc)
        ");

        foreach ($alertas as $a) {
            $stmtDetalle->execute([
                ':fk' => $idAlerta,
                ':tipo' => $a['tipo'],
                ':tac' => $a['tac'],
                ':coord' => $a['coord'],
                ':esci' => $a['esci'],
                ':desc' => $a['desc']
            ]);
        }
    }
} */




/*public function InsertarAlerta($folio, $telefono, $tipoTarea, $cope, $fechaLiquidacion, $idContratistas, $semana, $anio, $metraje, $fechaCarga)
{
    $conexion = $this->get_conexion();
    $conexion_analisis = $this->get_conexiondos();

    $alerta = null;
    $detalleEsci = null;

    // Buscar datos en seg_ns_eci
    $sqlEsci = "SELECT TELEFONO, M_SERIE, ULTIMO_EVENTO 
                FROM seg_ns_eci 
                WHERE TELEFONO = :telefono 
                LIMIT 1";
    $stmtEsci = $conexion_analisis->prepare($sqlEsci);
    $stmtEsci->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $stmtEsci->execute();
    $rowEsci = $stmtEsci->fetch(PDO::FETCH_ASSOC);

    if ($rowEsci) {
        $mSerie = trim($rowEsci['M_SERIE']);
        $ultimoEvento = intval($rowEsci['ULTIMO_EVENTO']);

        // Caso: número de serie desconocido
        if (strtoupper($mSerie) === 'DESCONOCIDO') {
            $alerta = 'NUMERO DE SERIE DESCONOCIDO';
        }

        // Caso: sin navegación
        if ($ultimoEvento === 0) {
            $alerta = 'SIN NAVEGACION';
        }

        // Caso: servicio inactivo
        if ($ultimoEvento === -1) {
            $alerta = 'SERVICIO INACTIVO';
        }

        // Caso: duplicados, pero solo si el número de serie NO es DESCONOCIDO
        if (strtoupper($mSerie) !== 'DESCONOCIDO') {
            $sqlDup = "SELECT TELEFONO 
                       FROM seg_ns_eci
                       WHERE M_SERIE = :mSerie";
            $stmtDup = $conexion_analisis->prepare($sqlDup);
            $stmtDup->bindParam(':mSerie', $mSerie, PDO::PARAM_STR);
            $stmtDup->execute();
            $telefonosDup = $stmtDup->fetchAll(PDO::FETCH_COLUMN);

            // Filtrar teléfonos únicos
            $telefonosUnicos = array_unique($telefonosDup);

            if (count($telefonosUnicos) > 1) {
                $alerta = 'ONT DUPLICADA EN ESCI';
                $detalleEsci = "El número de serie $mSerie se utilizó en los teléfonos: " . implode(", ", $telefonosUnicos);
            }
        }
    }

    // Insertar alerta si aplica
    if ($alerta !== null) {
        $sqlAlert = "INSERT INTO Produccion_alertas 
                     (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, anio, metraje, Alerta, Detalle_Esci) 
                     VALUES (:folio, :telefono, :tipoTarea, :cope, :fechaLiquidacion, :fechaCarga, :idContratistas, :semana, :anio, :metraje, :alerta, :detalleEsci)";
        
        $stmtAlert = $conexion->prepare($sqlAlert);
        $stmtAlert->bindParam(':folio', $folio, PDO::PARAM_STR);
        $stmtAlert->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmtAlert->bindParam(':tipoTarea', $tipoTarea, PDO::PARAM_STR);
        $stmtAlert->bindParam(':cope', $cope, PDO::PARAM_STR);
        $stmtAlert->bindParam(':fechaLiquidacion', $fechaLiquidacion, PDO::PARAM_STR);
        $stmtAlert->bindParam(':fechaCarga', $fechaCarga, PDO::PARAM_STR);
        $stmtAlert->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
        $stmtAlert->bindParam(':semana', $semana, PDO::PARAM_STR);
        $stmtAlert->bindParam(':anio', $anio, PDO::PARAM_INT);
        $stmtAlert->bindParam(':metraje', $metraje, PDO::PARAM_INT);
        $stmtAlert->bindParam(':alerta', $alerta, PDO::PARAM_STR);
        $stmtAlert->bindParam(':detalleEsci', $detalleEsci, PDO::PARAM_STR);

        $stmtAlert->execute();
    }
} */


/*public function InsertarProduccion($folio, $telefono, $tipoTarea, $cope, $fechaLiquidacion, $idContratistas, $semana, $anio, $metraje)
{
    $conexion = $this->get_conexion();
    $conexion_analisis = $this->get_conexiondos();

    $conexion->exec("SET NAMES 'utf8mb4'");
    $fechaCarga = date("Y-m-d H:i:s");

    // 🔥 UNA SOLA CONSULTA (TODO EL CÁLCULO AQUÍ)
    $sql = "
    SELECT 
        t.FECHA_LIQ,
        CASE 
            WHEN TIMESTAMPDIFF(DAY, t.FECHA_LIQ, NOW()) > 30 THEN 1
            WHEN (
                SELECT COUNT(*)
                FROM qm_tac t2
                WHERE t2.Telefono = :telefono
                AND t2.TIPO = 'ORDEN'
                AND t2.Calificador_Edo = 'COMPLETADA'
                AND t2.FECHA_LIQ BETWEEN DATE_SUB(t.FECHA_LIQ, INTERVAL 30 DAY) AND t.FECHA_LIQ
            ) >= 2 THEN 1
            ELSE 0
        END AS banderaRevision
    FROM qm_tac t
    WHERE t.Folio_Pisa = :folio
    AND t.Calificador_Edo = 'COMPLETADA'
    LIMIT 1
    ";

    $stmt = $conexion_analisis->prepare($sql);
    $stmt->bindParam(':folio', $folio);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $banderaRevision = 0;

    if ($row) {
        $banderaRevision = (int)$row['banderaRevision'];
    }

    // ===============================
    // INSERT
    // ===============================
    $sqlInsert = "INSERT INTO Produccion_contratista 
        (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, anio, metraje, Bandera_Revision) 
        VALUES (:folio, :telefono, :tipoTarea, :cope, :fechaLiquidacion, :fechaCarga, :idContratistas, :semana, :anio, :metraje, :banderaRevision)";
    
    $statement = $conexion->prepare($sqlInsert);

    $statement->bindParam(':folio', $folio);
    $statement->bindParam(':telefono', $telefono);
    $statement->bindParam(':tipoTarea', $tipoTarea);
    $statement->bindParam(':cope', $cope);
    $statement->bindParam(':fechaLiquidacion', $fechaLiquidacion);
    $statement->bindParam(':fechaCarga', $fechaCarga);
    $statement->bindParam(':idContratistas', $idContratistas);
    $statement->bindParam(':semana', $semana);
    $statement->bindParam(':anio', $anio);
    $statement->bindParam(':metraje', $metraje);
    $statement->bindParam(':banderaRevision', $banderaRevision);

    $ejecutado = $statement->execute();

    return [
        'insertado' => $ejecutado,
        'revision' => $banderaRevision
    ];
} */

/*public function InsertarProduccion($folio, $telefono, $tipoTarea, $cope, $fechaLiquidacion, $idContratistas, $semana, $anio, $metraje)
{
    $conexion = $this->get_conexion();
    $conexion_analisis = $this->get_conexiondos();

    $conexion->exec("SET NAMES 'utf8mb4'");
    $conexion->exec("SET CHARACTER SET utf8mb4");

    $fechaCarga = date("Y-m-d H:i:s");

    // ===============================
    // CONSULTA EN BD ANALISIS (TAC)
    // ===============================
    $sqlTac = "SELECT FECHA_LIQ 
               FROM qm_tac 
               WHERE Folio_Pisa = :folio 
               AND TRIM(UPPER(Calificador_Edo)) = 'COMPLETADA'
               LIMIT 1";

    $stmtTac = $conexion_analisis->prepare($sqlTac);
    $stmtTac->bindParam(':folio', $folio, PDO::PARAM_STR);
    $stmtTac->execute();
    $rowTac = $stmtTac->fetch(PDO::FETCH_ASSOC);

    $banderaRevision = 0;

    if ($rowTac) {
        $fechaTac = $rowTac['FECHA_LIQ'];
        $hoy = date("Y-m-d H:i:s");

        $segundos = strtotime($hoy) - strtotime($fechaTac);
        $diffDias = floor($segundos / (60 * 60 * 24));

        // Regla: más de 30 días → revisión
        if ($diffDias > 30) {
            $banderaRevision = 1;
        }

        // ===============================
        // VALIDACIÓN: TELÉFONO REPETIDO + ORDEN
        // ===============================
        $fechaInicio = date("Y-m-d H:i:s", strtotime("$fechaTac -30 days"));
        $fechaFin    = $fechaTac;

        $sqlOrden = "SELECT COUNT(*) AS total
                     FROM qm_tac
                     WHERE Telefono = :telefono
                     AND TRIM(UPPER(TIPO)) = 'ORDEN'
                     AND TRIM(UPPER(Calificador_Edo)) = 'COMPLETADA'
                     AND FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

        $stmtOrden = $conexion_analisis->prepare($sqlOrden);
        $stmtOrden->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmtOrden->bindParam(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
        $stmtOrden->bindParam(':fechaFin', $fechaFin, PDO::PARAM_STR);
        $stmtOrden->execute();
        $rowOrden = $stmtOrden->fetch(PDO::FETCH_ASSOC);

        if ($rowOrden && $rowOrden['total'] >= 2) {
            $banderaRevision = 1;
        }
    }

    // ===============================
    // INSERT EN BD LOCAL
    // ===============================
    $sql = "INSERT INTO Produccion_contratista 
            (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, anio, metraje, Bandera_Revision) 
            VALUES (:folio, :telefono, :tipoTarea, :cope, :fechaLiquidacion, :fechaCarga, :idContratistas, :semana, :anio, :metraje, :banderaRevision)";
    
    $statement = $conexion->prepare($sql);

    $statement->bindParam(':folio', $folio, PDO::PARAM_STR);
    $statement->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $statement->bindParam(':tipoTarea', $tipoTarea, PDO::PARAM_STR);
    $statement->bindParam(':cope', $cope, PDO::PARAM_STR);
    $statement->bindParam(':fechaLiquidacion', $fechaLiquidacion, PDO::PARAM_STR);
    $statement->bindParam(':fechaCarga', $fechaCarga, PDO::PARAM_STR);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_STR);
    $statement->bindParam(':anio', $anio, PDO::PARAM_INT);
    $statement->bindParam(':metraje', $metraje, PDO::PARAM_INT);
    $statement->bindParam(':banderaRevision', $banderaRevision, PDO::PARAM_INT);

    $ejecutado = $statement->execute();

    return [
        'insertado' => $ejecutado,
        'revision' => $banderaRevision
    ];
} */



 

/*public function InsertarProduccion($folio, $telefono, $tipoTarea, $cope, $fechaLiquidacion, $idContratistas, $semana, $anio, $metraje)
{
    $conexion = $this->get_conexion();

    // Forzar UTF-8 para que reconozca bien la columna Año
    $conexion->exec("SET NAMES 'utf8mb4'");
    $conexion->exec("SET CHARACTER SET utf8mb4");

    // Fecha actual del sistema
    $fechaCarga = date("Y-m-d H:i:s");

    $sql = "INSERT INTO Produccion_contratista 
            (Folio_Pisa, Telefono, Tipo_Tarea, Cope, Fecha_Liquidacion, Fecha_Carga, FK_Contratista, Semana, `Año`, metraje) 
            VALUES (:folio, :telefono, :tipoTarea, :cope, :fechaLiquidacion, :fechaCarga, :idContratistas, :semana, :anio, :metraje)";
    
    $statement = $conexion->prepare($sql);

    $statement->bindParam(':folio', $folio, PDO::PARAM_STR);
    $statement->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $statement->bindParam(':tipoTarea', $tipoTarea, PDO::PARAM_STR);
    $statement->bindParam(':cope', $cope, PDO::PARAM_STR);
    $statement->bindParam(':fechaLiquidacion', $fechaLiquidacion, PDO::PARAM_STR);
    $statement->bindParam(':fechaCarga', $fechaCarga, PDO::PARAM_STR);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_STR); // Semana es varchar
    $statement->bindParam(':anio', $anio, PDO::PARAM_INT);
    $statement->bindParam(':metraje', $metraje, PDO::PARAM_INT);

    return $statement->execute();
} */







/*public function CountOrdenesProduccion($idContratistas)
{
    $conexion = $this->get_conexion();  
    $sql = "SELECT COUNT(*) AS TOTAL 
            FROM Produccion_contratista
            WHERE FK_Contratista = :idContratistas";
    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['TOTAL'] : 0;       
} */

// nueva con fecha carga //

public function CountOrdenesProduccion($idContratistas, $semana = null)
{
    $conexion = $this->get_conexion();  

    if ($semana !== null) {

        //  CON semana
        $sql = "SELECT COUNT(*) AS TOTAL 
                FROM Produccion_contratista
                WHERE FK_Contratista = :idContratistas
                  AND Semana = :semana
                  AND DATE(Fecha_Carga) = (
                        SELECT DATE(MAX(Fecha_Carga))
                        FROM Produccion_contratista
                        WHERE FK_Contratista = :idContratistas
                  )";

        $statement = $conexion->prepare($sql);
        $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
        $statement->bindParam(':semana', $semana, PDO::PARAM_INT);

    } else {

        //  SIN semana → TODO lo del día
        $sql = "SELECT COUNT(*) AS TOTAL 
                FROM Produccion_contratista
                WHERE FK_Contratista = :idContratistas
                  AND DATE(Fecha_Carga) = (
                        SELECT DATE(MAX(Fecha_Carga))
                        FROM Produccion_contratista
                        WHERE FK_Contratista = :idContratistas
                  )";

        $statement = $conexion->prepare($sql);
        $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    }

    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['TOTAL'] : 0;       
}



/*public function CountOrdenesProduccion($idContratistas, $semana)
{
    $conexion = $this->get_conexion();  

    $sql = "SELECT COUNT(*) AS TOTAL 
            FROM Produccion_contratista
            WHERE FK_Contratista = :idContratistas
              AND Semana = :semana";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['TOTAL'] : 0;       
} */

// NUEVA CON FECHA DE CARGA //

public function ConteoOrdenesCoordiapp($idContratistas, $semana = null)
{
    $conexion = $this->get_conexion();  

    if ($semana !== null) {

        $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
                FROM Produccion_contratista pc
                INNER JOIN tecnico_instalaciones_coordiapp tic 
                    ON pc.Folio_Pisa = tic.Folio_Pisa
                    AND pc.Telefono = tic.Telefono
                WHERE pc.FK_Contratista = :idContratistas
                  AND pc.Semana = :semana
                  AND DATE(pc.Fecha_Carga) = (
                        SELECT DATE(MAX(Fecha_Carga))
                        FROM Produccion_contratista
                        WHERE FK_Contratista = :idContratistas
                  )";

        $statement = $conexion->prepare($sql);
        $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
        $statement->bindParam(':semana', $semana, PDO::PARAM_INT);

    } else {

        //  SIN semana
        $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
                FROM Produccion_contratista pc
                INNER JOIN tecnico_instalaciones_coordiapp tic 
                    ON pc.Folio_Pisa = tic.Folio_Pisa
                    AND pc.Telefono = tic.Telefono
                WHERE pc.FK_Contratista = :idContratistas
                  AND DATE(pc.Fecha_Carga) = (
                        SELECT DATE(MAX(Fecha_Carga))
                        FROM Produccion_contratista
                        WHERE FK_Contratista = :idContratistas
                  )";

        $statement = $conexion->prepare($sql);
        $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    }

    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['TOTAL'] : 0;
}


/*public function ConteoOrdenesCoordiapp($idContratistas, $semana)
{
    $conexion = $this->get_conexion();  

    $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
            FROM Produccion_contratista pc
            INNER JOIN tecnico_instalaciones_coordiapp tic 
                ON pc.Folio_Pisa = tic.Folio_Pisa
                AND pc.Telefono = tic.Telefono
            WHERE pc.FK_Contratista = :idContratistas
              AND pc.Semana = :semana";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['TOTAL'] : 0;
} */

/*public function ConteoOrdenesCoordiapp($idContratistas, $semana)
{
    $conexion = $this->get_conexion();  

    $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
            FROM Produccion_contratista pc
            INNER JOIN tecnico_instalaciones_coordiapp tic 
                ON pc.Folio_Pisa = tic.Folio_Pisa
            WHERE pc.FK_Contratista = :idContratistas
              AND pc.Semana = :semana";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['TOTAL'] : 0;
} */

/*public function ConteoOrdenesCoordiapp($idContratistas)
{
    $conexion = $this->get_conexion();  
    $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
            FROM Produccion_contratista pc
            INNER JOIN tecnico_instalaciones_coordiapp tic 
                ON pc.Folio_Pisa = tic.Folio_Pisa
            WHERE pc.FK_Contratista = :idContratistas";
    
    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['TOTAL'] : 0;
} */

// nueva con fecha carga //

public function ConteoOrdenesRechazadas($idContratistas, $semana = null)
{
    $conexion = $this->get_conexion();  

    if ($semana !== null) {

        $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
                FROM Produccion_contratista pc
                LEFT JOIN tecnico_instalaciones_coordiapp tic 
                    ON pc.Folio_Pisa = tic.Folio_Pisa
                    AND pc.Telefono = tic.Telefono
                WHERE pc.FK_Contratista = :idContratistas
                  AND pc.Semana = :semana
                  AND DATE(pc.Fecha_Carga) = (
                        SELECT DATE(MAX(Fecha_Carga))
                        FROM Produccion_contratista
                        WHERE FK_Contratista = :idContratistas
                  )
                  AND tic.Folio_Pisa IS NULL";

        $statement = $conexion->prepare($sql);
        $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
        $statement->bindParam(':semana', $semana, PDO::PARAM_INT);

    } else {

        //  SIN semana
        $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
                FROM Produccion_contratista pc
                LEFT JOIN tecnico_instalaciones_coordiapp tic 
                    ON pc.Folio_Pisa = tic.Folio_Pisa
                    AND pc.Telefono = tic.Telefono
                WHERE pc.FK_Contratista = :idContratistas
                  AND DATE(pc.Fecha_Carga) = (
                        SELECT DATE(MAX(Fecha_Carga))
                        FROM Produccion_contratista
                        WHERE FK_Contratista = :idContratistas
                  )
                  AND tic.Folio_Pisa IS NULL";

        $statement = $conexion->prepare($sql);
        $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    }

    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['TOTAL'] : 0;
}


public function ObtenerSemanaPorUltimaCarga($idContratistas)
{
    $conexion = $this->get_conexion();

    $sql = "SELECT Semana
            FROM Produccion_contratista
            WHERE FK_Contratista = :idContratistas
            ORDER BY Fecha_Carga DESC
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? (int)$row['Semana'] : null;
}



public function ObtenerUltimaSemanaReal($idContratistas)
{
    try {
        $conexion = $this->get_conexion();

        $sql = "SELECT Semana 
                FROM Produccion_contratista 
                WHERE FK_Contratista = :id
                ORDER BY idProduccion DESC 
                LIMIT 1";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id', $idContratistas, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? (int)$row['Semana'] : null;

    } catch (Exception $e) {
        return null;
    }
}




/* public function ObtenerUltimaSemanaContratista($idContratistas)
{
    $conexion = $this->get_conexion();

    $sql = "SELECT Semana 
            FROM Produccion_contratista
            WHERE FK_Contratista = :idContratistas
            ORDER BY Fecha_Carga DESC
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? (int)$row['Semana'] : null;
}*/ 




/*public function ConteoOrdenesRechazadas($idContratistas, $semana)
{
    $conexion = $this->get_conexion();  

    $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
            FROM Produccion_contratista pc
            LEFT JOIN tecnico_instalaciones_coordiapp tic 
                ON pc.Folio_Pisa = tic.Folio_Pisa
                AND pc.Telefono = tic.Telefono
            WHERE pc.FK_Contratista = :idContratistas
              AND pc.Semana = :semana
              AND tic.Folio_Pisa IS NULL";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['TOTAL'] : 0;
} */

/* public function ConteoOrdenesRechazadas($idContratistas, $semana)
{
    $conexion = $this->get_conexion();  

    $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
            FROM Produccion_contratista pc
            LEFT JOIN tecnico_instalaciones_coordiapp tic 
                ON pc.Folio_Pisa = tic.Folio_Pisa
            WHERE pc.FK_Contratista = :idContratistas
              AND pc.Semana = :semana
              AND tic.Folio_Pisa IS NULL";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['TOTAL'] : 0;
} */

/*public function ConteoOrdenesRechazadas($idContratistas)
{
    $conexion = $this->get_conexion();  
    $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
            FROM Produccion_contratista pc
            LEFT JOIN tecnico_instalaciones_coordiapp tic 
                ON pc.Folio_Pisa = tic.Folio_Pisa
            WHERE pc.FK_Contratista = :idContratistas
              AND tic.Folio_Pisa IS NULL";
    
    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['TOTAL'] : 0;
} */

/* public function OrdenesNoProcesadasPago($idContratistas, $semana, $anio)
{
    $rows = [];
    $conexion = $this->get_conexion();

   $sql = "
SELECT 
    pc.Folio_Pisa, 
    pc.Telefono, 
    pc.Tipo_Tarea, 
    pc.Cope, 
    pc.Fecha_Liquidacion, 
    pc.Semana
FROM Produccion_contratista pc
LEFT JOIN tecnico_instalaciones_coordiapp tic
    ON pc.Folio_Pisa = tic.Folio_Pisa
    AND TRIM(pc.Telefono) = TRIM(tic.Telefono)  
LEFT JOIN tecnicos t
    ON tic.FK_Tecnico_apps = t.idTecnico
WHERE pc.FK_Contratista = :idContratistas
  AND tic.Folio_Pisa IS NULL  
  AND pc.Semana = :semana
  AND YEAR(pc.Fecha_Liquidacion) = :anio
";


    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    $statement->bindParam(':anio', $anio, PDO::PARAM_INT);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
} */

public function OrdenesNoProcesadasPago($idContratistas, $semana, $anio)
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "
    SELECT 
        pc.Folio_Pisa, 
        pc.Telefono, 
        pc.Tipo_Tarea, 
        pc.Cope, 
        pc.Fecha_Liquidacion, 
        pc.Semana,
        pc.Fecha_Carga
    FROM Produccion_contratista pc
    LEFT JOIN tecnico_instalaciones_coordiapp tic
        ON pc.Folio_Pisa = tic.Folio_Pisa
        AND TRIM(pc.Telefono) = TRIM(tic.Telefono)  
    LEFT JOIN tecnicos t
        ON tic.FK_Tecnico_apps = t.idTecnico
    WHERE pc.FK_Contratista = :idContratistas
      AND tic.Folio_Pisa IS NULL
    ";

    //  Condiciones dinámicas
    if (!empty($anio)) {
        $sql .= " AND YEAR(pc.Fecha_Liquidacion) = :anio";
    }

    if (!empty($semana)) {
        $sql .= " AND pc.Semana = :semana";
    } else {
        // ⚡ Si no hay semana, usar la fecha de carga del día actual
        $sql .= " AND DATE(pc.Fecha_Carga) = CURDATE()";
    }

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);

    if (!empty($anio)) {
        $statement->bindParam(':anio', $anio, PDO::PARAM_INT);
    }
    if (!empty($semana)) {
        $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    }

    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}

public function OrdenesNoProcesadasPagoHoy($idContratistas)
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "
    SELECT 
        pc.Folio_Pisa, 
        pc.Telefono, 
        pc.Tipo_Tarea, 
        pc.Cope, 
        pc.Fecha_Liquidacion, 
        pc.Semana,
        pc.Fecha_Carga
    FROM Produccion_contratista pc
    LEFT JOIN tecnico_instalaciones_coordiapp tic
        ON pc.Folio_Pisa = tic.Folio_Pisa
        AND TRIM(pc.Telefono) = TRIM(tic.Telefono)  
    LEFT JOIN tecnicos t
        ON tic.FK_Tecnico_apps = t.idTecnico
    WHERE pc.FK_Contratista = :idContratistas
      AND tic.Folio_Pisa IS NULL
      AND DATE(pc.Fecha_Carga) = CURDATE()
    ";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

     // Debug: ver resultados en pantalla
    /*echo "<pre>";
    print_r($rows);
    echo "</pre>"; */

    return $rows;
}


//función Conteo de ordenes de producción están asociadas a un contratista específico durante una semana determinada registradas en coordiapp

public function ConteoPrenomina($idContratistas, $semana)
{
    $conexion = $this->get_conexion();  

    $sql = "SELECT COUNT(pc.Folio_Pisa) AS TOTAL
            FROM Produccion_contratista pc
            INNER JOIN tecnico_instalaciones_coordiapp tic 
                ON pc.Folio_Pisa = tic.Folio_Pisa
            INNER JOIN tecnicos t 
                ON tic.FK_Tecnico_apps = t.idTecnico
            INNER JOIN contratistas c 
                ON t.FK_Contratista_Tecnico = c.idContratistas
            WHERE c.idContratistas = :idContratistas
              AND pc.Semana = :semana";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_STR);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['TOTAL'] : 0;
}

public function ObtenerExpedientesPorContratista($idContratistas)
{
    $rows = [];
    $conexion = $this->get_conexion();
    //$conexion = $this->get_conexiondos();

    $sql = "SELECT NExpediente 
            FROM tecnicos 
            WHERE FK_Contratista_Tecnico = :idContratistas 
              AND NExpediente IS NOT NULL";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}

        
        public function ObtenerProduccionAlertas($idContratistas, $semana)
{
    $conexion = $this->get_conexion();

    $sql = "SELECT * 
            FROM Produccion_alertas 
            WHERE FK_Contratista = :idContratistas
            AND Semana = :semana";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratistas', $idContratistas);
    $stmt->bindParam(':semana', $semana);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


/*public function OrdenesConIncidencias($idContratistas, $expedientes = [], $semana = null)
{
    $rows = [];
    $conexion_local = $this->get_conexion(); // conexión principal
    $conexion_analytics = $this->get_conexion_analytics(); // conexión remota

    // Consulta base
    $sql = "
        SELECT pc.Folio_Pisa, pc.Telefono, pc.Tipo_Tarea, pc.Cope, pc.Fecha_Liquidacion, pc.Semana
        FROM Produccion_contratista pc
        LEFT JOIN tecnico_instalaciones_coordiapp tic
            ON pc.Folio_Pisa = tic.Folio_Pisa
        LEFT JOIN tecnicos t
            ON tic.FK_Tecnico_apps = t.idTecnico
            AND t.FK_Contratista_Tecnico = :idContratistas
        WHERE pc.FK_Contratista = :idContratistas
          AND tic.Folio_Pisa IS NULL
    ";

    // Filtrar por semana si se proporcionó
    if (!empty($semana)) {
        $sql .= " AND pc.Semana = :semana";
    }

    $statement = $conexion_local->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    if (!empty($semana)) {
        $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    }

    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        // Pasar $expedientes a verificarAlerta
        $result['Alerta'] = $this->verificarAlerta($conexion_local, $conexion_analytics, $result['Folio_Pisa'], $expedientes);
        $rows[] = $result;
    }

    return $rows;
} */

/*public function OrdenesConIncidencias($idContratistas, $expedientes = [], $semana = null)
{
    $rows = [];
    $conexion_local = $this->get_conexion(); // conexión principal
    $conexion_analytics = $this->get_conexion_analytics(); // conexión remota

    // Solo Folios que existen en Coordiapp
    $sql = "
        SELECT pc.Folio_Pisa, pc.Telefono, pc.Tipo_Tarea, pc.Cope, pc.Fecha_Liquidacion, pc.Semana
        FROM Produccion_contratista pc
        INNER JOIN tecnico_instalaciones_coordiapp tic
            ON pc.Folio_Pisa = tic.Folio_Pisa
        WHERE pc.FK_Contratista = :idContratistas
    ";

    if (!empty($semana)) {
        $sql .= " AND pc.Semana = :semana";
    }

    $statement = $conexion_local->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    if (!empty($semana)) {
        $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    }

    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        // Verificar alertas
        $alerta = $this->verificarAlerta($conexion_local, $conexion_analytics, $result['Folio_Pisa'], $expedientes);

        // Solo agregar si hay alguna alerta
        if (!empty($alerta)) {
            $result['Alerta'] = $alerta;
            $rows[] = $result;
        }
    }

    return $rows;
} */

public function OrdenesConIncidencias($idContratistas, $expedientes = [], $semana = null)
{




    $rows = [];
    $conexion_local = $this->get_conexion(); 
    $conexion_analisis = $this->get_conexiondos(); 

    $sql = "
    SELECT pc.Folio_Pisa, pc.Telefono, pc.Tipo_Tarea, pc.Cope, 
           pc.Fecha_Liquidacion, pc.Semana, pc.metraje
    FROM Produccion_contratista pc
    INNER JOIN tecnico_instalaciones_coordiapp tic
        ON pc.Folio_Pisa = tic.Folio_Pisa
        AND pc.Telefono = tic.Telefono
        AND tic.Estatus_Orden = 'COMPLETADA'
    WHERE pc.FK_Contratista = :idContratistas
";


    /*$sql = "
        SELECT pc.Folio_Pisa, pc.Telefono, pc.Tipo_Tarea, pc.Cope, 
               pc.Fecha_Liquidacion, pc.Semana, pc.metraje
        FROM Produccion_contratista pc
        INNER JOIN tecnico_instalaciones_coordiapp tic
            ON pc.Folio_Pisa = tic.Folio_Pisa
            AND pc.Telefono = tic.Telefono
        WHERE pc.FK_Contratista = :idContratistas
    "; */

    if (!empty($semana)) {
        $sql .= " AND pc.Semana = :semana";
    }

    $statement = $conexion_local->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);

    if (!empty($semana)) {
        $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    }

    $statement->execute();

    

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {

        $alertaData = $this->verificarAlerta(
            $conexion_local,
            $conexion_analisis,
            $result['Folio_Pisa'],
            $result['Telefono'],
            $result['metraje'],
            $expedientes,
            $idContratistas
        );

        if (!empty($alertaData['Alerta'])) {

            $result['Alerta'] = $alertaData['Alerta'];
            $result['Detalle_Serie_Duplicada_TAC'] = $alertaData['Detalle_Serie_Duplicada_TAC'] ?? '';
            $result['Detalle_Serie_Duplicada_ESCI'] = $alertaData['Detalle_Serie_Duplicada_ESCI'] ?? '';
            $result['Valor_TAC'] = $alertaData['Valor_TAC'];
            $result['Valor_Coordiapp'] = $alertaData['Valor_Coordiapp'];
            $result['Valor_Contratista'] = $alertaData['Valor_Contratista'];
            $result['Valor_ESCI'] = $alertaData['Valor_ESCI'];
            $result['Diferencia_Metraje'] = $alertaData['Diferencia_Metraje'];

            // CAMPOS NUEVOS
            $result['Metraje_Distancia'] = $alertaData['Metraje_Distancia'];
            $result['Diferencia_Metraje_Distancia'] = $alertaData['Diferencia_Metraje_Distancia'];
            

            $rows[] = $result;
        }
    }

    return $rows;
}


private function normalizarNombre($nombre)
{
    $nombre = strtoupper($nombre);

    $nombre = strtr($nombre, [
        'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N'
    ]);

    $nombre = preg_replace('/[^A-Z\s]/', '', $nombre);
    $nombre = preg_replace('/\s+/', ' ', $nombre);

    return trim($nombre);
}

/*private function sonMismoTecnico($nombre1, $nombre2)
{
    $n1 = $this->normalizarNombre($nombre1);
    $n2 = $this->normalizarNombre($nombre2);

    if ($n1 === '' || $n2 === '') return false;

    if ($n1 === $n2) return true;

    similar_text($n1, $n2, $porcentaje);

    if ($porcentaje >= 80) return true;

    $palabras1 = explode(' ', $n1);
    $palabras2 = explode(' ', $n2);

    $coincidencias = array_intersect($palabras1, $palabras2);

    if (count($coincidencias) >= 2) return true;

    return false;
} */

private function normalizarTecnologia($tec)
{
    $tec = strtoupper(trim($tec));

    // CAMBACEO también es FIBRA
    if (strpos($tec, 'CAMBACEO') !== false) {
        return 'FIBRA';
    }

    if (strpos($tec, 'FIBRA') !== false) {
        return 'FIBRA';
    }

    if (strpos($tec, 'COBRE') !== false) {
        return 'COBRE';
    }

    return $tec;
}

private function normalizarSerie($serie)
{
    // Quitar espacios al inicio y fin
    $serie = trim($serie);

    // Quitar TODOS los espacios internos
    $serie = preg_replace('/\s+/', '', $serie);

    // Convertir a mayúsculas
    $serie = strtoupper($serie);

    // Opcional: quitar caracteres raros invisibles
    $serie = preg_replace('/[^A-Z0-9]/', '', $serie);

    return $serie;
}

private function limpiar($valor) {
    return trim((string)($valor ?? ''));
}

private function soloNumeros($valor) {
    return preg_replace('/\D/', '', $this->limpiar($valor));
}

private function agregarAlerta(&$alertas, &$origen, $mensaje, $valor) {
    $alertas[] = $mensaje;
    $origen[] = $valor;
}

private function verificarAlerta($conexion_local, $conexion_analisis, $folioPisa, $telefonoProduccion, $metrajeProduccion, $expedientes = [],$idContratistas)
{
  

    try {
        

        $alertas = [];
        $valorTac = [];
        $valorLocal = [];
        $valorContratista = [];
        $valorEsci = [];
        $detalleSerieDuplicadaTac = [];
        $detalleSerieDuplicadaEsci = [];

        $diferenciaMetraje = 0;
        $metrajeDistancia = 0;
        $diferenciaMetrajeDistancia = 0;

      // ===============================
// ESCI CORREGIDO (LOGICA REAL)
// ===============================
$telefonoLimpio = $this->soloNumeros($telefonoProduccion);

// -------------------------------
// 1. OBTENER SERIE DESDE TELEFONO
// -------------------------------
 if (!isset($this->cacheEsciSerie[$telefonoLimpio])) {

            $stmt = $conexion_analisis->prepare("
                SELECT M_SERIE, MAX(DID_ACTIVO) AS estado
                FROM seg_ns_eci
                WHERE TELEFONO = ?
                AND TELEFONO IS NOT NULL
                AND TELEFONO <> ''
                AND TELEFONO <> '0'
                LIMIT 1
            ");

            $stmt->execute([$telefonoLimpio]);
            $this->cacheEsciSerie[$telefonoLimpio] = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        }

$dataSerie = $this->cacheEsciSerie[$telefonoLimpio];

$nsEsci = strtoupper(trim($this->limpiar($dataSerie['M_SERIE'] ?? '')));
$estadoEsci = isset($dataSerie['estado']) ? (int)$dataSerie['estado'] : null;

// -------------------------------
// VALIDAR SERIE VALIDA
// -------------------------------
$esSerieInvalida = (
    $nsEsci === '' ||
    strpos($nsEsci, 'DESCONOCIDO') !== false
);

// -------------------------------
// 2. SI HAY SERIE → BUSCAR TODOS LOS TELEFONOS
// -------------------------------
$telefonosDiferentes = [];

if (!$esSerieInvalida) {

    if (!isset($this->cacheEsciTelefonos[$nsEsci])) {

        $stmt = $conexion_analisis->prepare("
            SELECT DISTINCT TELEFONO
FROM seg_ns_eci
WHERE M_SERIE = ?
  AND TELEFONO IS NOT NULL
  AND TELEFONO > 0;
        ");

        $stmt->execute([$nsEsci]);
        $this->cacheEsciTelefonos[$nsEsci] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    $telefonosSerie = $this->cacheEsciTelefonos[$nsEsci];

    foreach ($telefonosSerie as $tel) {
        $telLimpio = preg_replace('/\D/', '', $tel);

        if ($telLimpio !== '' && $telLimpio !== '0' && $telLimpio !== $telefonoLimpio) {
            $telefonosDiferentes[] = $telLimpio;
        }
    }

    $telefonosDiferentes = array_unique($telefonosDiferentes);
}

// -------------------------------
// 3. ALERTA DUPLICADOS
// -------------------------------
if (!$esSerieInvalida && count($telefonosDiferentes) > 0) {

    $alertas[] = "Serie ONT duplicada en Nav";
    $valorEsci[] = "Serie Nav: $nsEsci";

    $mensaje = "El Numero de serie en Nav: $nsEsci se uso en telefonos diferentes";

    if (!empty($telefonosDiferentes)) {
        $mensaje .= ": " . implode(', ', $telefonosDiferentes);
    }

    $detalleSerieDuplicadaEsci[] = $mensaje;
}

// -------------------------------
// 4. ALERTAS DE ESTADO
// -------------------------------
if (!$esSerieInvalida && $estadoEsci !== null) {

    if ($estadoEsci === 0) {
        $this->agregarAlerta($alertas, $valorEsci, "Servicio sin navegación", "Serie ESCI: $nsEsci");
    }

    if ($estadoEsci === -1) {
        $this->agregarAlerta($alertas, $valorEsci, "Servicio inactivo", "Serie ESCI: $nsEsci");
    }
}
        // ===============================
        // TAC EXACTO (CACHE)
        // ===============================
        if (!isset($this->cacheTacExact[$folioPisa][$telefonoProduccion])) {
            $stmtFolio = $conexion_analisis->prepare("
                SELECT TELEFONO, Tipo_De_Tarea, NOM_CT, Tecnico, Num__Serie ,TECNOLOGIA,Expediente
                FROM qm_tac 
                WHERE Folio_Pisa = ? 
                AND TELEFONO = ? 
                AND Calificador_Edo = 'COMPLETADA'
                LIMIT 1
            ");
            $stmtFolio->execute([$folioPisa, $telefonoProduccion]);
            $this->cacheTacExact[$folioPisa][$telefonoProduccion] = $stmtFolio->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $tacDataExact = $this->cacheTacExact[$folioPisa][$telefonoProduccion];

        // ===============================
        // TAC GENERAL (CACHE)
        // ===============================
        if (!isset($this->cacheTacGeneral[$folioPisa])) {
            $stmtFolioData = $conexion_analisis->prepare("
                SELECT TELEFONO, Tipo_De_Tarea, NOM_CT, Tecnico, Num__Serie ,TECNOLOGIA,Expediente
                FROM qm_tac
                WHERE Folio_Pisa = ? 
                AND Calificador_Edo = 'COMPLETADA'
                LIMIT 1
            ");
            $stmtFolioData->execute([$folioPisa]);
            $this->cacheTacGeneral[$folioPisa] = $stmtFolioData->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $tacData = $this->cacheTacGeneral[$folioPisa];

        // ===============================
        // TAC ONT DUPLICADA (CACHE)
        // ===============================
        if (!isset($this->cacheTacOnt[$folioPisa][$telefonoProduccion])) {

            $stmtOntTac = $conexion_analisis->prepare("
                SELECT 
                    Num__Serie,
                    GROUP_CONCAT(DISTINCT TELEFONO) AS telefonos,
                    GROUP_CONCAT(DISTINCT Folio_Pisa) AS folios
                FROM qm_tac
                WHERE Num__Serie = (
                    SELECT Num__Serie
                    FROM qm_tac
                    WHERE Folio_Pisa = ?
                    AND TELEFONO = ?
                    AND Calificador_Edo = 'COMPLETADA'
                    LIMIT 1
                )
                AND Calificador_Edo = 'COMPLETADA'
                AND TELEFONO IS NOT NULL
                AND TELEFONO <> ''
                AND TELEFONO <> '0'
                GROUP BY Num__Serie
                LIMIT 1
            ");

            $stmtOntTac->execute([$folioPisa, $telefonoProduccion]);
            $this->cacheTacOnt[$folioPisa][$telefonoProduccion] = $stmtOntTac->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $dataOntTac = $this->cacheTacOnt[$folioPisa][$telefonoProduccion];

        $nsTacValidar = $this->limpiar($dataOntTac['Num__Serie'] ?? '');
        $telefonosTac = $dataOntTac['telefonos'] ?? '';
        $foliosTac    = $dataOntTac['folios'] ?? '';

        $telefonosArray = [];

        if ($telefonosTac !== '') {
            $telefonosArray = array_map(function($tel) {
                return preg_replace('/\D/', '', trim($tel));
            }, explode(',', $telefonosTac));
        }

        $telefonosLimpios = array_filter($telefonosArray, function($tel) {
            return $tel !== '' && $tel !== '0';
        });

        $telefonosUnicos = array_values(array_unique($telefonosLimpios));

        $nsTacUpper = strtoupper(trim($nsTacValidar));

        $esSerieInvalida = (
            $nsTacUpper === '' ||
            strpos($nsTacUpper, 'NO OBTENIDO') !== false ||
            strpos($nsTacUpper, 'DESCONOCIDO') !== false
        );

        if (!$esSerieInvalida && count($telefonosUnicos) > 1) {

            $alertas[] = "ONT duplicada en TAC";

            if (count($telefonosUnicos) > 1) {
                $ultimo = array_pop($telefonosUnicos);
                $telefonosMostrar = implode(', ', $telefonosUnicos) . ' y ' . $ultimo;
            } else {
                $telefonosMostrar = $telefonosUnicos[0] ?? '';
            }

            $mensajeDetalle = "El Numero de serie en TAC: $nsTacValidar se uso en telefonos diferentes";

            if ($telefonosMostrar !== '') {
                $mensajeDetalle .= ": $telefonosMostrar";
            }

            if ($foliosTac !== '') {
                $mensajeDetalle .= ", y en los folios $foliosTac";
            }

            $detalleSerieDuplicadaTac[] = $mensajeDetalle;
        }

        // ===============================
        // LOCAL (CACHE)
        // ===============================
        if (!isset($this->cacheLocal[$folioPisa][$telefonoProduccion])) {

            $stmtLocal = $conexion_local->prepare("
                SELECT Telefono, Tipo_Tarea, FK_Cope, FK_Tecnico_apps, Ont, 
                       Foto_Puerto, Foto_Casa_Cliente, Foto_Ont, 
                       No_Serie_ONT, Foto_INE, Metraje,
                       Latitud_Terminal, Longitud_Terminal,
                       Latitud, Longitud, Tecnologia,
                       Tipo_Instalacion, validacion_tipo_instalacion,
                       validacion_foto_fallida,
                       validacion_LEDS_ONT_fallida,
                       orden_cambaceo,
                       Codigo_Liquidacion
                FROM tecnico_instalaciones_coordiapp
                WHERE Folio_Pisa = :folio
                  AND Telefono = :telefono
                  AND Estatus_Orden = 'COMPLETADA'
                LIMIT 1
            ");

            $stmtLocal->execute([':folio' => $folioPisa, ':telefono' => $telefonoProduccion]);
            $this->cacheLocal[$folioPisa][$telefonoProduccion] = $stmtLocal->fetch(PDO::FETCH_ASSOC) ?: [];
        }

        $localData = $this->cacheLocal[$folioPisa][$telefonoProduccion];

        // ===============================
        // CACHE COPES
        // ===============================
        if (empty($this->cacheCopes)) {
            $this->cacheCopes = $conexion_local->query("SELECT id, COPE FROM copes")->fetchAll(PDO::FETCH_KEY_PAIR);
        }

        // ===============================
        // VALIDAR ONT EN ALMACEN
        // ===============================
        if ($res = $this->validarOntAlmacen(
            $conexion_local,
            $localData['Ont'] ?? '',
            $idContratistas
        )) {
            $this->agregarAlerta($alertas, $valorLocal, $res[0], $res[1]);
        }

        $esCambaceo = intval($localData['orden_cambaceo'] ?? 0);

        if (empty($tacData) && $esCambaceo !== 1) {
            $this->agregarAlerta($alertas, $valorTac, "No se encuentra en TAC", "Folio Pisa: $folioPisa");
        }

        // ===============================
        // DISTANCIA
        // ===============================
        $lat1 = floatval($localData['Latitud_Terminal'] ?? 0);
        $lon1 = floatval($localData['Longitud_Terminal'] ?? 0);
        $lat2 = floatval($localData['Latitud'] ?? 0);
        $lon2 = floatval($localData['Longitud'] ?? 0);

        if ($lat1 != 0 && $lon1 != 0 && $lat2 != 0 && $lon2 != 0) {
            $earthRadius = 6371000;
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                 sin($dLon / 2) * sin($dLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $metrajeDistancia = round($earthRadius * $c, 2);
        }

        // ===============================
        // VALIDACIONES TAC vs LOCAL
        // ===============================
        if (!empty($tacData)) {

            $nsTAC = $this->limpiar($tacData['Num__Serie'] ?? '');
            $nsLocal = $this->limpiar($localData['Ont'] ?? '');
            $nsEsci_cmp = $this->normalizarSerie($nsEsci);

            $nsTAC_cmp = $this->normalizarSerie($nsTAC);
            $nsLocal_cmp = $this->normalizarSerie($nsLocal);

            $alertaNS = (
                $nsTAC_cmp !== $nsLocal_cmp &&
                !($nsTAC_cmp === '' && $nsLocal_cmp === $nsEsci_cmp)
            );

            $comparaciones = [
                [
                    'tac' => $this->soloNumeros($tacData['TELEFONO'] ?? ''),
                    'local' => $this->soloNumeros($localData['Telefono'] ?? ''),
                    'mensaje' => 'Teléfonos diferentes',
                    'formato' => 'Teléfono %s: %s'
                ],
                [
                    'tac' => $this->limpiar($tacData['NOM_CT'] ?? ''),
                    'local' => $localData['FK_Cope'] ? $this->limpiar($this->cacheCopes[$localData['FK_Cope']] ?? '') : '',
                    'mensaje' => 'COPE diferentes',
                    'formato' => 'COPE %s: %s'
                ],
                [
                    'tac' => $this->normalizarTecnologia($this->limpiar($tacData['TECNOLOGIA'] ?? '')),
                    'local' => $this->normalizarTecnologia($this->limpiar($localData['Tecnologia'] ?? '')),
                    'mensaje' => 'Tecnología diferente',
                    'formato' => 'Tecnología %s: %s'
                ],
                [
                    'tac' => $nsTAC,
                    'local' => $nsLocal,
                    'mensaje' => 'Números de serie diferentes',
                    'formato' => 'Serie %s: %s',
                    'alerta' => $alertaNS
                ],
            ];

            foreach ($comparaciones as $cmp) {

                $tac = $this->limpiar($cmp['tac']);
                $local = $this->limpiar($cmp['local']);

                $disparar = $cmp['alerta'] ?? ($tac !== $local || ($tac === '' && $local === ''));

                if ($disparar) {

                    $this->agregarAlerta($alertas, $valorTac, $cmp['mensaje'], sprintf($cmp['formato'], 'TAC', $tac ?: 'Vacío'));
                    $valorLocal[] = sprintf($cmp['formato'], 'Coordiapp', $local ?: 'Vacío');

                    if ($cmp['mensaje'] === 'Números de serie diferentes') {
                        $valorEsci[] = "Serie ESCI: " . ($nsEsci ?: 'Sin dato');
                    }
                }
            }

            $tipoTacBase   = substr(strtoupper($this->limpiar($tacData['Tipo_De_Tarea'] ?? '')), 0, 7);
            $tipoLocalBase = substr(strtoupper($this->limpiar($localData['Tipo_Tarea'] ?? '')), 0, 7);

            if ($tipoTacBase !== $tipoLocalBase || ($tipoTacBase === '' && $tipoLocalBase === '')) {
                $alertas[] = "Tipo de tarea diferente";
                $valorTac[] = "Tipo TAC: " . ($tacData['Tipo_De_Tarea'] ?? 'Vacío');
                $valorLocal[] = "Tipo Coordiapp: " . ($localData['Tipo_Tarea'] ?? 'Vacío');
            }
        }

        // ===============================
        // EXPEDIENTE
        // ===============================
        $expedienteTac = $this->limpiar($tacDataExact['Expediente'] ?? $tacData['Expediente'] ?? '');
        $expedientesNormalizados = array_filter(array_map([$this, 'limpiar'], $expedientes));

        if ($expedienteTac === '' || !in_array($expedienteTac, $expedientesNormalizados, true)) {

            $this->agregarAlerta($alertas, $valorTac, "Expediente diferente", "Expediente TAC: " . ($expedienteTac ?: 'Vacío'));

            $expedienteMostrar = !empty($expedientesNormalizados) ? reset($expedientesNormalizados) : 'Sin expedientes';

            $valorLocal[] = "Expediente Coordiapp: " . $expedienteMostrar;
        }

        // ===============================
        // FOTOS (NO SE TOCÓ)
        // ===============================
        $tipoTarea = strtoupper($this->limpiar($localData['Tipo_Tarea'] ?? ''));
        $codigoLiquidacion = strtoupper($this->limpiar($localData['Codigo_Liquidacion'] ?? ''));

        $camposFotos = [];
        $usaPuertoCasa = false;
        $usaOnt = false;

        if (strpos($tipoTarea, 'Q') === 0) {
             if (in_array($codigoLiquidacion, ['JUMPER/PARALELO EXTERIOR','CONECTOR MECANICO DAÑADO','CAMBIO DE PUERTO','CAMBIO DE TERMINAL'])) {
                $camposFotos = ['Foto_Casa_Cliente','Foto_Puerto'];
                $usaPuertoCasa = true;
            } elseif ($codigoLiquidacion === 'MODEM DAÑADO') {
                $camposFotos = ['Foto_Casa_Cliente','Foto_Ont'];
                $usaOnt = true;
            } elseif (in_array($codigoLiquidacion, ['CAMBIO PAR PRINCIPAL','CAMBIO PAR SECUNDARIO','ROSETA OPTICA','CAMBIO DE INTERIOR'])) {
                $camposFotos = ['Foto_Casa_Cliente'];
            } elseif (in_array($codigoLiquidacion, ['CAMBIO PARALELO Y MODEM','OTRO'])) {
                $camposFotos = ['Foto_Casa_Cliente','Foto_Puerto','Foto_Ont'];
                $usaPuertoCasa = true;
                $usaOnt = true;
            }

        } else {
            $camposFotos = [
                'Foto_Puerto' => "Falta Evidencia Fotografica del Puerto",
                'Foto_Casa_Cliente' => "Falta evidencia de la casa",
                'Foto_Ont' => "Falta Evidencia Fotografica ONT frontal",
                'No_Serie_ONT' => "Falta Evidencia ONT trasera",
                'Foto_INE' => "Falta Evidencia orden de servicio"
            ];

            $usaPuertoCasa = true;
            $usaOnt = true;
        }

        foreach ($camposFotos as $campo => $mensaje) {

            if (is_int($campo)) {
                $campo = $mensaje;
                $mensaje = "Falta evidencia en $campo";
            }

            $valor = $this->limpiar($localData[$campo] ?? '');

            if ($valor === '' || !preg_match('/\.(jpg|jpeg|png)$/i', $valor)) {
                $this->agregarAlerta($alertas, $valorLocal, $mensaje, "$campo inválido");
            }
        }

        if (!empty($localData['validacion_foto_fallida']) && $usaPuertoCasa) {
            $alertas[] = "Fallo En la Evidencia Fotografica";
            $valorLocal[] = "Error en Foto Terminal o Domicilio";
        }

        if (!empty($localData['validacion_LEDS_ONT_fallida']) && $usaOnt) {
            $alertas[] = "Fallo En la Evidencia Fotografica De Ont";
            $valorLocal[] = "Error en Foto_Ont";
        }

        return [
            'Alerta' => implode(' | ', $alertas),
            'Valor_TAC' => implode(' | ', $valorTac),
            'Valor_Coordiapp' => implode(' | ', $valorLocal),
            'Valor_Contratista' => implode(' | ', $valorContratista),
            'Valor_ESCI' => implode(' | ', $valorEsci),
            'Detalle_Serie_Duplicada_TAC' => implode(' | ', $detalleSerieDuplicadaTac),
            'Detalle_Serie_Duplicada_ESCI' => implode(' | ', $detalleSerieDuplicadaEsci),
            'Diferencia_Metraje' => $diferenciaMetraje,
            'Metraje_Distancia' => $metrajeDistancia,
            'Diferencia_Metraje_Distancia' => $diferenciaMetrajeDistancia
        ];

    } catch (Exception $e) {
        return [
            'Alerta' => "Error: " . $e->getMessage(),
            'Valor_TAC' => '',
            'Valor_Coordiapp' => '',
            'Valor_Contratista' => '',
            'Valor_ESCI' => '',
            'Detalle_Serie_Duplicada_TAC' => '',
            'Detalle_Serie_Duplicada_ESCI' => '',
            'Diferencia_Metraje' => 0,
            'Metraje_Distancia' => 0,
            'Diferencia_Metraje_Distancia' => 0
        ];
    }
}


private function validarOntAlmacen($conexion_local, $ont, $idContratista)
{
    $ont = $this->normalizarSerie($ont);

    if ($ont === '') {
        return null;
    }

    $stmt = $conexion_local->prepare("
        SELECT FK_Contratista_Salida
        FROM material_det_salida
        WHERE REPLACE(UPPER(Num_Serie_S), ' ', '') = ?
        LIMIT 1
    ");

    $stmt->execute([$ont]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return ["ONT no se encuentra en almacén", "Serie: $ont"];
    }

    if (empty($row['FK_Contratista_Salida'])) {
        return ["ONT no asignada al contratista", "Serie: $ont"];
    }

    if ((int)$row['FK_Contratista_Salida'] !== (int)$idContratista) {
        return [
            "ONT asignada a otro contratista",
            "Serie: $ont (Asignado a: ".$row['FK_Contratista_Salida'].")"
        ];
    }

    return null;
}

public function ConteoAlertas($idContratistas, $semana)
{
    $conexion = $this->get_conexion();  

    $sql = "SELECT COUNT(DISTINCT pc.Folio_Pisa) AS TOTAL
            FROM Produccion_contratista pc
            INNER JOIN tecnico_instalaciones_coordiapp tic 
                ON pc.Folio_Pisa = tic.Folio_Pisa
            INNER JOIN tecnicos t 
                ON tic.FK_Tecnico_apps = t.idTecnico
            INNER JOIN contratistas c 
                ON t.FK_Contratista_Tecnico = c.idContratistas
            WHERE c.idContratistas = :idContratistas
              AND pc.Semana = :semana
              AND pc.Alerta IS NOT NULL
              AND pc.Alerta != ''";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->bindParam(':semana', $semana, PDO::PARAM_STR);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['TOTAL'] : 0;
}

/*public function OrdenesConIncidencias($idContratistas, $semana = null)
{
    $rows = [];
    $conexion_local = $this->get_conexion(); // conexión principal
    $conexion_analytics = $this->get_conexion_analytics(); // conexión remota

    // Consulta base
    $sql = "
        SELECT pc.Folio_Pisa, pc.Telefono, pc.Tipo_Tarea, pc.Cope, pc.Fecha_Liquidacion, pc.Semana
        FROM Produccion_contratista pc
        LEFT JOIN tecnico_instalaciones_coordiapp tic
            ON pc.Folio_Pisa = tic.Folio_Pisa
        LEFT JOIN tecnicos t
            ON tic.FK_Tecnico_apps = t.idTecnico
            AND t.FK_Contratista_Tecnico = :idContratistas
        WHERE pc.FK_Contratista = :idContratistas
          AND tic.Folio_Pisa IS NULL
    ";

    // Agregar filtro por semana si se proporcionó
    if (!empty($semana)) {
        $sql .= " AND pc.Semana = :semana";
    }

    $statement = $conexion_local->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    if (!empty($semana)) {
        $statement->bindParam(':semana', $semana, PDO::PARAM_INT);
    }

    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $folio = $result['Folio_Pisa'];

        // 🔍 Verificar si existe ese Folio_Pisa en la base de datos remota (qm_tac)
        $sql_check = "SELECT 1 FROM qm_tac WHERE Folio_Pisa = :folio LIMIT 1";
        $check_stmt = $conexion_analytics->prepare($sql_check);
        $check_stmt->bindParam(':folio', $folio, PDO::PARAM_STR);
        $check_stmt->execute();

        // Si no se encontró en la base remota, agregar alerta
        $result['Alerta'] = ($check_stmt->rowCount() === 0) ? 'No Se Encontro En Tac' : '';

        $rows[] = $result;
    }

    return $rows;
} */

/*public function OrdenesConIncidencias($idContratistas)
{
    $rows = [];
    $conexion_local = $this->get_conexion(); // conexión principal

    // conexión remota
    $conexion_analytics = $this->get_conexion_analytics();

    $sql = "
        SELECT pc.Folio_Pisa, pc.Telefono, pc.Tipo_Tarea, pc.Cope, pc.Fecha_Liquidacion, pc.Semana
        FROM Produccion_contratista pc
        LEFT JOIN tecnico_instalaciones_coordiapp tic
            ON pc.Folio_Pisa = tic.Folio_Pisa
        LEFT JOIN tecnicos t
            ON tic.FK_Tecnico_apps = t.idTecnico
            AND t.FK_Contratista_Tecnico = :idContratistas
        WHERE pc.FK_Contratista = :idContratistas
          AND tic.Folio_Pisa IS NULL
    ";

    $statement = $conexion_local->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $folio = $result['Folio_Pisa'];

        // 🔍 Verificar si existe ese Folio_Pisa en la base de datos remota (qm_tac)
        $sql_check = "SELECT 1 FROM qm_tac WHERE Folio_Pisa = :folio LIMIT 1";
        $check_stmt = $conexion_analytics->prepare($sql_check);
        $check_stmt->bindParam(':folio', $folio, PDO::PARAM_STR);
        $check_stmt->execute();

        // Si no se encontró en la base remota, agregar alerta
        if ($check_stmt->rowCount() === 0) {
            $result['Alerta'] = 'No Se Encontro En Tac';
        } else {
            $result['Alerta'] = ''; // No mostrar nada si sí existe
        }

        $rows[] = $result;
    }

    return $rows;
} */

/*public function OrdenesConIncidencias($idContratistas)
{
    $rows = null;
    $conexion = $this->get_conexion();

    $sql = "
        SELECT pc.Folio_Pisa, pc.Telefono, pc.Tipo_Tarea, pc.Cope, pc.Fecha_Liquidacion, pc.Semana
        FROM Produccion_contratista pc
        LEFT JOIN tecnico_instalaciones_coordiapp tic
            ON pc.Folio_Pisa = tic.Folio_Pisa
        LEFT JOIN tecnicos t
            ON tic.FK_Tecnico_apps = t.idTecnico
            AND t.FK_Contratista_Tecnico = :idContratistas
        WHERE pc.FK_Contratista = :idContratistas
          AND tic.Folio_Pisa IS NULL
    ";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
} */

/*public function VerificarFolios($folio,$telefono)
{
    $mensajes = [];

    // --- Validar en Coordiapp (conexion local)
    $conexionLocal = $this->get_conexion();
    $sql1 = "SELECT 1 FROM tecnico_instalaciones_coordiapp WHERE Folio_Pisa = :Folio LIMIT 1";
    $stmt1 = $conexionLocal->prepare($sql1);
    $stmt1->bindParam(':Folio', $folio, PDO::PARAM_STR);
    $stmt1->execute();
    if (!$stmt1->fetch()) {
        $mensajes[] = "Folio No Capturado en Coordiapp";
    }

    // --- Validar en TAC (conexion analytics)
    $conexionAnalytics = $this->get_conexion_analytics();
    $sql2 = "SELECT 1 FROM qm_tac WHERE Folio_Pisa = :Folio LIMIT 1";
    $stmt2 = $conexionAnalytics->prepare($sql2);
    $stmt2->bindParam(':Folio', $folio, PDO::PARAM_STR);
    $stmt2->execute();
    if (!$stmt2->fetch()) {
        $mensajes[] = "Folio No Existe en TAC";
    }

    return $mensajes; // Devuelve arreglo de mensajes, vacío si no hay alertas
} */


// BUSCAR FOLIO EN tecnico_instalaciones_coordiapp

/*public function ExisteFolioCoordiapp($folio)
{
    $conexion = $this->get_conexion();

    $sql = "SELECT 1 
            FROM tecnico_instalaciones_coordiapp 
            WHERE Folio_Pisa = :Folio 
            LIMIT 1";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':Folio', $folio, PDO::PARAM_STR);
    $statement->execute();

    return $statement->fetch() ? true : false;
} */

/*public function ObtenerProduccionContratista($idContratista, $emulador, $semana, $tecnologia)
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "SELECT pc.Folio_Pisa, tic.Telefono, tic.Tecnologia, tic.Tipo_Tarea
            FROM Produccion_contratista AS pc
            INNER JOIN tecnico_instalaciones_coordiapp AS tic
                ON pc.Folio_Pisa = tic.Folio_Pisa
            WHERE pc.FK_Contratista = :idContratista
              AND pc.Semana = :semana
              AND tic.Tecnologia = :emulador"; // aquí emulador = FIBRA o COBRE

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->bindParam(':emulador', $emulador, PDO::PARAM_STR);
    $stmt->execute();

    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
} */

/*public function ObtenerProduccionContratista($idContratista, $emulador, $semana, $tecnologia)
{
    $rows = [];
    $conexion = $this->get_conexion();

    $sql = "SELECT 
                pc.Folio_Pisa,
                tic.Telefono,
                tic.Tecnologia,
                tic.Tipo_Tarea,
                c.COPE,
                t.Nombre_T,
                t.Apellidos_T,
                t.NExpediente,
                tic.Distrito,
                tic.Ont AS Numero_Serie,
                tic.Fecha_Coordiapp AS Fecha_Liquidacion,
                a.area,
                d.Division
            FROM Produccion_contratista AS pc
            INNER JOIN tecnico_instalaciones_coordiapp AS tic
                ON pc.Folio_Pisa = tic.Folio_Pisa
            LEFT JOIN tecnicos AS t
                ON t.idTecnico = tic.FK_Tecnico_apps
            LEFT JOIN copes AS c
                ON c.id = tic.FK_Cope
            LEFT JOIN areas AS a
                ON a.idAreas = c.FK_Area
            LEFT JOIN divisiones AS d
                ON d.idDivision = a.FK_Division
            WHERE pc.FK_Contratista = :idContratista
              AND pc.Semana = :semana
              AND tic.Tecnologia = :emulador";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->bindParam(':emulador', $emulador, PDO::PARAM_STR);
    $stmt->execute();

    while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
} */

/*public function ObtenerProduccionContratistaSinIncidencias($idContratista, $emulador, $semana, $tecnologia)
{
    $rows = [];
    $conexion_local = $this->get_conexion(); // local
    $conexion_analytics = $this->get_conexion_analytics(); // remoto

    // Incluir la función que devuelve los prefijos
    require_once("/var/www/vhosts/ed-intra.com/contratistas.ed-intra.com/ordenes/funcionesemulador.php");
    $filtros = obtenerFiltrosPorEmulador($emulador, $tecnologia);
    $prefijos = $filtros['prefijos'];
    $excluir_prefijos = $filtros['excluir_prefijos'];

    // Construir la parte del WHERE para Tipo_Tarea según prefijos
    $condicionesTarea = "";
    if (!empty($prefijos)) {
        $likeParts = [];
        foreach ($prefijos as $prefijo) {
            $likeParts[] = "tic.Tipo_Tarea LIKE :prefijo_" . $prefijo;
        }
        $condicionesTarea .= " AND (" . implode(" OR ", $likeParts) . ")";
    }

    if (!empty($excluir_prefijos)) {
        foreach ($excluir_prefijos as $prefijo) {
            $condicionesTarea .= " AND tic.Tipo_Tarea NOT LIKE :excluir_" . $prefijo;
        }
    }

    // Consulta principal (igual a la tuya) + condiciones de Tipo_Tarea
    $sql = "SELECT 
                pc.Folio_Pisa,
                tic.Telefono,
                tic.Tecnologia,
                tic.Tipo_Tarea,
                c.COPE,
                t.Nombre_T,
                t.Apellidos_T,
                t.NExpediente,
                tic.Distrito,
                tic.Ont AS Numero_Serie,
                tic.Fecha_Coordiapp AS Fecha_Liquidacion,
                a.area,
                d.Division,
                tic.FK_Cope,
                tic.FK_Tecnico_apps
            FROM Produccion_contratista AS pc
            INNER JOIN tecnico_instalaciones_coordiapp AS tic
                ON pc.Folio_Pisa = tic.Folio_Pisa
            LEFT JOIN tecnicos AS t
                ON t.idTecnico = tic.FK_Tecnico_apps
            LEFT JOIN copes AS c
                ON c.id = tic.FK_Cope
            LEFT JOIN areas AS a
                ON a.idAreas = c.FK_Area
            LEFT JOIN divisiones AS d
                ON d.idDivision = a.FK_Division
            WHERE pc.FK_Contratista = :idContratista
              AND pc.Semana = :semana
              AND tic.Tecnologia = :emulador
              $condicionesTarea";

    $stmt = $conexion_local->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->bindParam(':emulador', $emulador, PDO::PARAM_STR);

    // Bind dinámico de prefijos
    if (!empty($prefijos)) {
        foreach ($prefijos as $prefijo) {
            $stmt->bindValue(':prefijo_' . $prefijo, $prefijo . '%', PDO::PARAM_STR);
        }
    }

    // Bind dinámico de exclusiones
    if (!empty($excluir_prefijos)) {
        foreach ($excluir_prefijos as $prefijo) {
            $stmt->bindValue(':excluir_' . $prefijo, $prefijo . '%', PDO::PARAM_STR);
        }
    }

    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Usamos la función de verificación de alertas
        $alerta = $this->verificarAlerta($conexion_local, $conexion_analytics, $row['Folio_Pisa'], [$row['NExpediente']]);

        // Si no hay alertas, agregar al resultado
        if (empty($alerta)) {
            $rows[] = $row;
        }
    }

    return $rows;
} */

/*public function ObtenerProduccionContratistaSinIncidencias($idContratista, $emulador, $semana, $tecnologia)
{
    echo "<pre>";
    echo "=== DATOS RECIBIDOS EN LA FUNCIÓN ===\n";
    echo "ID Contratista: " . var_export($idContratista, true) . "\n";
    echo "Emulador: " . var_export($emulador, true) . "\n";
    echo "Semana: " . var_export($semana, true) . "\n";
    echo "Tecnología: " . var_export($tecnologia, true) . "\n";
    echo "</pre>";

    $rows = [];
    $conexion_local = $this->get_conexion(); // local
    $conexion_analytics = $this->get_conexion_analytics(); // remoto

    // Incluir la función que devuelve los prefijos
    require_once("/var/www/vhosts/ed-intra.com/contratistas.ed-intra.com/ordenes/funcionesemulador.php");

    $filtros = obtenerFiltrosPorEmulador($emulador, $tecnologia);

    echo "<pre>";
    echo "=== FILTROS GENERADOS POR obtenerFiltrosPorEmulador() ===\n";
    print_r($filtros);
    echo "</pre>";

    $prefijos = $filtros['prefijos'];
    $excluir_prefijos = $filtros['excluir_prefijos'];

    echo "<pre>";
    echo "=== PREFIJOS ===\n";
    print_r($prefijos);
    echo "=== EXCLUIR PREFIJOS ===\n";
    print_r($excluir_prefijos);
    echo "</pre>";

    // Construir condiciones Tipo_Tarea
    $condicionesTarea = "";
    if (!empty($prefijos)) {
        $likes = [];
        foreach ($prefijos as $prefijo) {
            $likes[] = "tic.Tipo_Tarea LIKE '" . $prefijo . "%'";
        }
        $condicionesTarea .= " AND (" . implode(" OR ", $likes) . ")";
    }

    if (!empty($excluir_prefijos)) {
        foreach ($excluir_prefijos as $prefijo) {
            $condicionesTarea .= " AND tic.Tipo_Tarea NOT LIKE '" . $prefijo . "%'";
        }
    }

    echo "<pre>";
    echo "=== CONDICIONES DE TIPO_TAREA GENERADAS ===\n";
    echo $condicionesTarea . "\n";
    echo "</pre>";

    // Consulta SQL base
    $sql = "SELECT 
                pc.Folio_Pisa,
                tic.Telefono,
                tic.Tecnologia,
                tic.Tipo_Tarea,
                c.COPE,
                t.Nombre_T,
                t.Apellidos_T,
                t.NExpediente,
                tic.Distrito,
                tic.Ont AS Numero_Serie,
                tic.Fecha_Coordiapp AS Fecha_Liquidacion,
                a.area,
                d.Division,
                tic.FK_Cope,
                tic.FK_Tecnico_apps
            FROM Produccion_contratista AS pc
            INNER JOIN tecnico_instalaciones_coordiapp AS tic
                ON pc.Folio_Pisa = tic.Folio_Pisa
            LEFT JOIN tecnicos AS t
                ON t.idTecnico = tic.FK_Tecnico_apps
            LEFT JOIN copes AS c
                ON c.id = tic.FK_Cope
            LEFT JOIN areas AS a
                ON a.idAreas = c.FK_Area
            LEFT JOIN divisiones AS d
                ON d.idDivision = a.FK_Division
            WHERE pc.FK_Contratista = :idContratista
              AND pc.Semana = :semana
              AND tic.Tecnologia = :emulador
              $condicionesTarea";

    echo "<pre>";
    echo "=== CONSULTA SQL FINAL ===\n";
    echo $sql . "\n";
    echo "</pre>";

    $stmt = $conexion_local->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->bindParam(':emulador', $emulador, PDO::PARAM_STR);
    $stmt->execute();

    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay resultados, fallback dinámico usando solo los prefijos existentes en los datos
    if (empty($filas) && !empty($prefijos)) {
        echo "<pre>-- No hay resultados con todos los prefijos, aplicando fallback solo con prefijos existentes --</pre>";

        // Solo tomar los prefijos que realmente existen en los datos actuales
        $sql = "SELECT 
                    pc.Folio_Pisa,
                    tic.Telefono,
                    tic.Tecnologia,
                    tic.Tipo_Tarea,
                    c.COPE,
                    t.Nombre_T,
                    t.Apellidos_T,
                    t.NExpediente,
                    tic.Distrito,
                    tic.Ont AS Numero_Serie,
                    tic.Fecha_Coordiapp AS Fecha_Liquidacion,
                    a.area,
                    d.Division,
                    tic.FK_Cope,
                    tic.FK_Tecnico_apps
                FROM Produccion_contratista AS pc
                INNER JOIN tecnico_instalaciones_coordiapp AS tic
                    ON pc.Folio_Pisa = tic.Folio_Pisa
                LEFT JOIN tecnicos AS t
                    ON t.idTecnico = tic.FK_Tecnico_apps
                LEFT JOIN copes AS c
                    ON c.id = tic.FK_Cope
                LEFT JOIN areas AS a
                    ON a.idAreas = c.FK_Area
                LEFT JOIN divisiones AS d
                    ON d.idDivision = a.FK_Division
                WHERE pc.FK_Contratista = :idContratista
                  AND pc.Semana = :semana
                  AND tic.Tecnologia = :emulador
                  AND tic.Tipo_Tarea LIKE 'TS%'"; // prefijo real existente

        $stmt = $conexion_local->prepare($sql);
        $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
        $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
        $stmt->bindParam(':emulador', $emulador, PDO::PARAM_STR);
        $stmt->execute();

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar alertas
    foreach ($filas as $row) {
        $alerta = $this->verificarAlerta($conexion_local, $conexion_analytics, $row['Folio_Pisa'], [$row['NExpediente']]);
        if (empty($alerta)) {
            $rows[] = $row;
        }
    }

    echo "<pre>";
    echo "=== TOTAL DE REGISTROS SIN ALERTA ===\n";
    echo count($rows) . " registros encontrados.\n";
    echo "</pre>";

    return $rows;
} */

/*public function ObtenerProduccionContratistaSinIncidencias($idContratista, $emulador, $semana, $tecnologia)
{
    $rows = [];
    $conexion_local = $this->get_conexion(); // local
    $conexion_analytics = $this->get_conexion_analytics(); // remoto

    // Incluir la función que devuelve los prefijos
    require_once("/var/www/vhosts/ed-intra.com/contratistas.ed-intra.com/ordenes/funcionesemulador.php");
    $filtros = obtenerFiltrosPorEmulador($emulador, $tecnologia);
    $prefijos = $filtros['prefijos'];
    $excluir_prefijos = $filtros['excluir_prefijos'];

    // Construir condiciones Tipo_Tarea directamente en SQL
    $condicionesTarea = "";
    if (!empty($prefijos)) {
        $likes = [];
        foreach ($prefijos as $prefijo) {
            $likes[] = "tic.Tipo_Tarea LIKE '" . $prefijo . "%'";
        }
        $condicionesTarea .= " AND (" . implode(" OR ", $likes) . ")";
    }

    if (!empty($excluir_prefijos)) {
        foreach ($excluir_prefijos as $prefijo) {
            $condicionesTarea .= " AND tic.Tipo_Tarea NOT LIKE '" . $prefijo . "%'";
        }
    }

    // Consulta principal
    $sql = "SELECT 
                pc.Folio_Pisa,
                tic.Telefono,
                tic.Tecnologia,
                tic.Tipo_Tarea,
                c.COPE,
                t.Nombre_T,
                t.Apellidos_T,
                t.NExpediente,
                tic.Distrito,
                tic.Ont AS Numero_Serie,
                tic.Fecha_Coordiapp AS Fecha_Liquidacion,
                a.area,
                d.Division,
                tic.FK_Cope,
                tic.FK_Tecnico_apps
            FROM Produccion_contratista AS pc
            INNER JOIN tecnico_instalaciones_coordiapp AS tic
                ON pc.Folio_Pisa = tic.Folio_Pisa
            LEFT JOIN tecnicos AS t
                ON t.idTecnico = tic.FK_Tecnico_apps
            LEFT JOIN copes AS c
                ON c.id = tic.FK_Cope
            LEFT JOIN areas AS a
                ON a.idAreas = c.FK_Area
            LEFT JOIN divisiones AS d
                ON d.idDivision = a.FK_Division
            WHERE pc.FK_Contratista = :idContratista
              AND pc.Semana = :semana
              AND tic.Tecnologia = :emulador
              $condicionesTarea";

    $stmt = $conexion_local->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->bindParam(':emulador', $emulador, PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // Verificar alertas
        $alerta = $this->verificarAlerta($conexion_local, $conexion_analytics, $row['Folio_Pisa'], [$row['NExpediente']]);

        if (empty($alerta)) {
            $rows[] = $row;
        }
    }

    return $rows;
} */

public function ObtenerProduccionContratistaSinIncidencias(
    $idContratista,
    $emulador,
    $semana,
    $tecnologia,
    $expedientes = [],
    $tolerancia = 5
)
{
    $rows = [];
    $conexion_local    = $this->get_conexion();
    $conexion_analisis = $this->get_conexiondos();

    require_once __DIR__ . '/../funcionesemulador.php';

    $filtros          = obtenerFiltrosPorEmulador($emulador, $tecnologia);
    $prefijos         = $filtros['prefijos'];
    $excluir_prefijos = $filtros['excluir_prefijos'];

    $condicionesTarea = "";

    if (!empty($prefijos)) {
        $likes = [];
        foreach ($prefijos as $prefijo) {
            $likes[] = "UPPER(TRIM(tic.Tipo_Tarea)) LIKE '" . strtoupper($prefijo) . "%'";
        }
        $condicionesTarea .= " AND (" . implode(" OR ", $likes) . ")";
    }

    if (!empty($excluir_prefijos)) {
        foreach ($excluir_prefijos as $prefijo) {
            $condicionesTarea .= " AND UPPER(TRIM(tic.Tipo_Tarea)) NOT LIKE '" . strtoupper($prefijo) . "%'";
        }
    }

    $condicionExpedientes = "";

    if (!empty($expedientes)) {
        $expedientes = array_map('intval', $expedientes);
        $listaExpedientes = implode(',', $expedientes);
        $condicionExpedientes = " AND t.NExpediente IN ($listaExpedientes)";
    }

    $sql = "SELECT 
        pc.Folio_Pisa,
        tic.Telefono,
        tic.Tecnologia,
        tic.Tipo_Tarea,
        c.COPE,
        t.Nombre_T,
        t.Apellidos_T,
        t.NExpediente,
        tic.Distrito,
        tic.Ont AS Numero_Serie,
        tic.Fecha_Coordiapp AS Fecha_Liquidacion,
        a.area,
        d.Division,
        tic.FK_Cope,
        tic.FK_Tecnico_apps,
        tic.Metraje AS Metraje_Coordiapp,
        pc.metraje AS Metraje_Contratista,
        tic.Tipo_Instalacion,
        tic.Direccion_Cliente,
        tic.Terminal,
        tic.Puerto,
        tic.Latitud,
        tic.Longitud,
        tic.Cliente_Titular
    FROM Produccion_contratista AS pc
    INNER JOIN tecnico_instalaciones_coordiapp AS tic
        ON pc.Folio_Pisa = tic.Folio_Pisa
    LEFT JOIN tecnicos AS t
        ON t.idTecnico = tic.FK_Tecnico_apps
    LEFT JOIN copes AS c
        ON c.id = tic.FK_Cope
    LEFT JOIN areas AS a
        ON a.idAreas = c.FK_Area
    LEFT JOIN divisiones AS d
        ON d.idDivision = a.FK_Division
    WHERE pc.FK_Contratista = :idContratista
      AND TRIM(pc.Semana) = :semana
      AND (
        UPPER(TRIM(tic.Tecnologia)) = :tecnologia
        OR UPPER(TRIM(tic.Tecnologia)) LIKE CONCAT(:tecnologia, ' %')
      )
      $condicionesTarea
      $condicionExpedientes";

    $stmt = $conexion_local->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->bindParam(':tecnologia', $tecnologia, PDO::PARAM_STR);
    $stmt->execute();

    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($filas)) {
        return [];
    }

    foreach ($filas as $row) {

        $alerta = $this->verificarAlerta(
    $conexion_local,
    $conexion_analisis,
    $row['Folio_Pisa'],
    $row['Telefono'],
    $row['Metraje_Contratista'],
    $idContratista,   // ✅ 6to
    $expedientes      // ✅ 7mo
);

        if (
            empty($alerta['Alerta']) &&
            $alerta['Diferencia_Metraje'] <= $tolerancia &&
            $alerta['Diferencia_Metraje_Distancia'] <= $tolerancia
        ) {
            $rows[] = $row;
        }
    }

    return $rows;
}


// posible insersion //
public function InsertarFoliosProduccion($idContratista, $emulador, $tecnologia, $semana, $folioPaco)
{
    $conexion = $this->get_conexion();

    // Obtener ID del emulador
    $qEmu = $conexion->prepare(
        "SELECT idEmulador FROM emulador WHERE nombre = :emu"
    );
    $qEmu->bindParam(':emu', $emulador);
    $qEmu->execute();
    $idEmulador = $qEmu->fetchColumn();

    if (!$idEmulador) {
        return false;
    }

    // Obtener registros base (MISMA CONSULTA que la tabla)
    $sql = "
        SELECT 
            Folio_Pisa,
            Tipo_Tarea,
            Tecnologia,
            Telefono,
            Terminal,
            Puerto,
            Latitud,
            Longitud
        FROM View_Produccion_Sin_Incidencias
        WHERE FK_Contratista = :idContratista
          AND UPPER(Emulador) = :emulador
          AND Tecnologia = :tecnologia
          AND Semana = :semana
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':idContratista' => $idContratista,
        ':emulador'      => $emulador,
        ':tecnologia'    => $tecnologia,
        ':semana'        => $semana
    ]);

    $insert = $conexion->prepare("
        INSERT INTO folios_produccion (
            folioPaco,
            folio_pisa,
            idContratista,
            idEmulador,
            tipo_tarea,
            tecnologia,
            telefono_cliente,
            terminal,
            puerto,
            latitud,
            longitud,
            Estatus
        ) VALUES (
            :folioPaco,
            :folio_pisa,
            :idContratista,
            :idEmulador,
            :tipo_tarea,
            :tecnologia,
            :telefono,
            :terminal,
            :puerto,
            :latitud,
            :longitud,
            'PENDIENTEPAGO'
        )
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $insert->execute([
            ':folioPaco'     => $folioPaco,
            ':folio_pisa'    => $row['Folio_Pisa'],
            ':idContratista'=> $idContratista,
            ':idEmulador'    => $idEmulador,
            ':tipo_tarea'    => $row['Tipo_Tarea'],
            ':tecnologia'    => $row['Tecnologia'],
            ':telefono'      => $row['Telefono'],
            ':terminal'      => $row['Terminal'],
            ':puerto'        => $row['Puerto'],
            ':latitud'       => $row['Latitud'],
            ':longitud'      => $row['Longitud']
        ]);
    }

    return true;
}





    }

?>