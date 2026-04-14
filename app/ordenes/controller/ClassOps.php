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

public function InsertarProduccion($folio, $telefono, $tipoTarea, $cope, $fechaLiquidacion, $idContratistas, $semana, $anio, $metraje)
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
        // NUEVA VALIDACIÓN: TELÉFONO REPETIDO + ORDEN
        // ===============================
        $fechaInicio = date("Y-m-d H:i:s", strtotime("$fechaTac -30 days"));
        $fechaFin    = $fechaTac; // hasta la fecha de liquidación TAC

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

        // Solo marcar revisión si el mismo teléfono aparece al menos 2 veces
        if ($rowOrden && $rowOrden['total'] >= 2) {
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
}

 

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
            $expedientes
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

private function verificarAlerta($conexion_local, $conexion_analisis, $folioPisa, $telefonoProduccion, $metrajeProduccion, $expedientes = [])
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
// ESCI
// ===============================
$telefonoLimpio = $this->soloNumeros($telefonoProduccion);

$stmtEsci = $conexion_analisis->prepare("
    SELECT 
        M_SERIE, 
        COUNT(DISTINCT TELEFONO) AS total,
        COUNT(DISTINCT TELEFONO) - 1 AS otros_telefonos,
        GROUP_CONCAT(DISTINCT TELEFONO) AS telefonos,
        MAX(DID_ACTIVO) AS estado
    FROM seg_ns_eci
    WHERE M_SERIE = (
        SELECT M_SERIE 
        FROM seg_ns_eci 
        WHERE TELEFONO = ? 
        LIMIT 1
    )
    GROUP BY M_SERIE
    LIMIT 1
");

$stmtEsci->execute([$telefonoLimpio]);
$dataEsci = $stmtEsci->fetch(PDO::FETCH_ASSOC) ?: [];

$nsEsci = $this->limpiar($dataEsci['M_SERIE'] ?? '');
$totalTelefonos = (int)($dataEsci['total'] ?? 0);
$otrosTelefonos = (int)($dataEsci['otros_telefonos'] ?? 0);
$estadoEsci = isset($dataEsci['estado']) ? (int)$dataEsci['estado'] : null;

// 🔹 NUEVO: teléfonos
$telefonosEsci = $dataEsci['telefonos'] ?? '';

if ($telefonosEsci !== '') {
    $telefonosEsci = implode(', ', array_unique(explode(',', $telefonosEsci)));
}

// ===============================
// ALERTA + DETALLE ESCI (NUEVO)
// ===============================
if ($nsEsci !== '' && $totalTelefonos > 1) {

    // SOLO ALERTA (NO usar agregarAlerta)
    $alertas[] = "Serie ONT duplicada en ESCI";

     $valorEsci[] = "Serie ESCI: $nsEsci";

    // DETALLE (NUEVA COLUMNA)
    $mensajeDetalleEsci = "El Numero de serie en ESCI: $nsEsci se uso $totalTelefonos veces";

    if ($telefonosEsci !== '') {
        $mensajeDetalleEsci .= ", fue usada en los telefonos $telefonosEsci";
    }

    $detalleSerieDuplicadaEsci[] = $mensajeDetalleEsci;
}

// ===============================
// ALERTAS DE ESTADO (SE QUEDAN IGUAL)
// ===============================
if ($nsEsci !== '' && $estadoEsci !== null) {

    if ($estadoEsci === 0) {
        $this->agregarAlerta($alertas, $valorEsci, "Servicio sin navegación en ESCI", "Serie ESCI: $nsEsci");
    }

    if ($estadoEsci === -1) {
        $this->agregarAlerta($alertas, $valorEsci, "Servicio inactivo en ESCI", "Serie ESCI: $nsEsci");
    }
}

        // ===============================
        // TAC EXACTO
        // ===============================
        $stmtFolio = $conexion_analisis->prepare("
            SELECT TELEFONO, Tipo_De_Tarea, NOM_CT, Tecnico, Num__Serie ,TECNOLOGIA,Expediente
            FROM qm_tac 
            WHERE Folio_Pisa = ? 
            AND TELEFONO = ? 
            AND Calificador_Edo = 'COMPLETADA'
            LIMIT 1
        ");
        $stmtFolio->execute([$folioPisa, $telefonoProduccion]);
        $tacDataExact = $stmtFolio->fetch(PDO::FETCH_ASSOC) ?: [];

        // ===============================
        // TAC GENERAL
        // ===============================
        $stmtFolioData = $conexion_analisis->prepare("
            SELECT TELEFONO, Tipo_De_Tarea, NOM_CT, Tecnico, Num__Serie ,TECNOLOGIA,Expediente
            FROM qm_tac
            WHERE Folio_Pisa = ? 
            AND Calificador_Edo = 'COMPLETADA'
            LIMIT 1
        ");
        $stmtFolioData->execute([$folioPisa]);
        $tacData = $stmtFolioData->fetch(PDO::FETCH_ASSOC) ?: [];

        // ===============================
// TAC - VALIDAR ONT DUPLICADA (OPTIMIZADO EN UNA SOLA QUERY)
// ===============================

$stmtOntTac = $conexion_analisis->prepare("
    SELECT 
        Num__Serie,
        COUNT(*) AS total,
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
    GROUP BY Num__Serie
    LIMIT 1
");

$stmtOntTac->execute([$folioPisa, $telefonoProduccion]);
$dataOntTac = $stmtOntTac->fetch(PDO::FETCH_ASSOC) ?: [];

$nsTacValidar = $this->limpiar($dataOntTac['Num__Serie'] ?? '');
$totalOntTac = (int)($dataOntTac['total'] ?? 0);
$telefonosTac = $dataOntTac['telefonos'] ?? '';
$foliosTac = $dataOntTac['folios'] ?? '';

// Limpieza (opcional)
if ($telefonosTac !== '') {
    $telefonosTac = implode(', ', array_unique(explode(',', $telefonosTac)));
}

if ($foliosTac !== '') {
    $foliosTac = implode(', ', array_unique(explode(',', $foliosTac)));
}

if ($nsTacValidar !== '' && $totalOntTac > 1) {

    // SOLO ALERTA (SIN DETALLE EN VALOR_TAC)
    $alertas[] = "ONT duplicada en TAC";

    // DETALLE SOLO EN NUEVA COLUMNA
    $mensajeDetalle = "El Numero de serie en TAC: $nsTacValidar se uso $totalOntTac veces";

    if ($telefonosTac !== '') {
        $mensajeDetalle .= ", fue usada en los telefonos $telefonosTac";
    }

    if ($foliosTac !== '') {
        $mensajeDetalle .= ", y en los folios $foliosTac";
    }

    $detalleSerieDuplicadaTac[] = $mensajeDetalle;
}
        
// ===============================
        // LOCAL COORDIAPP
        // ===============================
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
        $localData = $stmtLocal->fetch(PDO::FETCH_ASSOC) ?: [];

        $esCambaceo = intval($localData['orden_cambaceo'] ?? 0);

        // ===============================
        // VALIDACION TAC
        // ===============================
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
                    'local' => $localData['FK_Cope'] ? $this->limpiar($conexion_local->query("SELECT COPE FROM copes WHERE id = ".$localData['FK_Cope'])->fetchColumn()) : '',
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
        // FOTOS
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

public function ObtenerProduccionContratistaSinIncidencias($idContratista, $emulador, $semana, $tecnologia,$expedientes = [],
    $tolerancia = 5)
{
   /* echo "<pre>";
print_r($expedientes);
echo "</pre>";
exit; */


    $rows = [];
    $conexion_local    = $this->get_conexion();
    $conexion_analisis = $this->get_conexiondos();
    require_once __DIR__ . '/../funcionesemulador.php';


    //var_dump(realpath('/opt/proyectos/contratistas/app/ordenes/funcionesemulador.php'));


    //echo 'Intentando cargar: ' . realpath('/opt/proyectos/contratistas/app/ordenes/funcionesemulador.php');


    //require_once("/var/www/vhosts/ed-intra.com/contratistas.ed-intra.com/ordenes/funcionesemulador.php");
    //require_once("/opt/proyectos/contratistas/app/ordenes/funcionesemulador.php");


    $filtros          = obtenerFiltrosPorEmulador($emulador, $tecnologia);
    $prefijos         = $filtros['prefijos'];
    $excluir_prefijos = $filtros['excluir_prefijos'];

   /* echo '<pre>';
print_r($prefijos);
print_r($excluir_prefijos);
exit; */


// Debug transparente
/*echo '<h3>Contenido de $filtros</h3>';
print_r($filtros);

echo '<h3>Contenido de $prefijos</h3>';
print_r($prefijos);

echo '<h3>Contenido de $excluir_prefijos</h3>';
print_r($excluir_prefijos); */

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
    $expedientes = array_map('intval', $expedientes); // seguridad
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
    UPPER(TRIM(tic.Tecnologia)) = 'FIBRA'
    OR UPPER(TRIM(tic.Tecnologia)) LIKE 'FIBRA %'
)
  $condicionesTarea
  $condicionExpedientes";

    $stmt = $conexion_local->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->bindParam(':tecnologia', $tecnologia, PDO::PARAM_STR);
    $stmt->execute();

    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!is_array($filas) || empty($filas)) {
        return [];
    }

   /* echo "<pre>";
echo "RESULTADO DEL QUERY:\n";
print_r($filas);
exit; */

    foreach ($filas as $row) {
        $alerta = $this->verificarAlerta(
            $conexion_local,
            $conexion_analisis,
            $row['Folio_Pisa'],
            $row['Telefono'],
            $row['Metraje_Contratista'],
            $expedientes
        );

        //  DEBUG AQUÍ
    /* echo "<pre>";
    echo "Folio: " . $row['Folio_Pisa'] . "\n";
    print_r($alerta);
    echo "</pre>"; */

   /* echo "<pre>";
echo "Folio: " . $row['Folio_Pisa'] . "\n";
echo "Alerta: " . $alerta['Alerta'] . "\n";
echo "Dif Metraje: " . $alerta['Diferencia_Metraje'] . "\n";
echo "Dif Distancia: " . $alerta['Diferencia_Metraje_Distancia'] . "\n";
echo "---------------------------------\n";
echo "</pre>"; */




        // Se permite cierta tolerancia en diferencias
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