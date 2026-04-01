<?php 

	/**
	 * DESARROLLADO ING. ANTONIO CARDONA
	 */
    //require_once($_SERVER['DOCUMENT_ROOT']."/cnx/cnx.php");
    require_once(__DIR__ . '/../../cnx/cnx.php');


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

    $sql = "SELECT nombre 
            FROM emulador
            ORDER BY nombre";

    $statement = $conexion->prepare($sql);
    $statement->execute();

    while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $result;
    }

    return $rows;
}

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

public function ExisteFolioProduccion($folio, $idContratistas)
{
    $conexion = $this->get_conexion();

    $sql = "SELECT 1
            FROM Produccion_contratista
            WHERE Folio_Pisa = :folio
              AND FK_Contratista = :idContratistas
            LIMIT 1";

    $statement = $conexion->prepare($sql);
    $statement->bindParam(':folio', $folio, PDO::PARAM_STR);
    $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchColumn() ? true : false;
}

public function InsertarProduccion($folio, $telefono, $tipoTarea, $cope, $fechaLiquidacion, $idContratistas, $semana)
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
}




public function CountOrdenesProduccion($idContratistas)
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
}

public function ConteoOrdenesCoordiapp($idContratistas)
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
}

public function ConteoOrdenesRechazadas($idContratistas)
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
}

public function OrdenesNoProcesadasPago($idContratistas)
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
    $conexion_local = $this->get_conexion(); // conexión principal
    //$conexion_analytics = $this->get_conexion_analytics(); // conexión remota
    $conexion_analisis = $this->get_conexion_analisis(); // conexión remota

    

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
        $alerta = $this->verificarAlerta($conexion_local, $conexion_analisis, $result['Folio_Pisa'], $expedientes);

        // Solo agregar si hay alguna alerta
        if (!empty($alerta)) {
            $result['Alerta'] = $alerta;
            $rows[] = $result;
        }
    }

    return $rows;
}

private function verificarAlerta($conexion_local, $conexion_analisis, $folioPisa, $expedientes = [])
{
    try {
        $alertas = [];

        // ===============================
        // 1️⃣ Verificar si existe Folio_Pisa en TAC
        // ===============================
        $sqlFolio = "SELECT TELEFONO, Tipo_De_Tarea, NOM_CT, Tecnico, Num__Serie 
                     FROM qm_tac 
                     WHERE Folio_Pisa = ? AND Calificador_Edo = 'COMPLETADA' 
                     LIMIT 1";
        $stmtFolio = $conexion_analisis->prepare($sqlFolio);
        $stmtFolio->execute([$folioPisa]);
        $folioData = $stmtFolio->fetch(PDO::FETCH_ASSOC);

        if (!$folioData) {
            // Folio no existe → alerta clásica
            $alertas[] = "No se encontró en TAC";
            $tacData = [
                'TELEFONO' => '',
                'Tipo_De_Tarea' => '',
                'NOM_CT' => '',
                'Tecnico' => '',
                'Num__Serie' => ''
            ];
        } else {
            // Folio sí existe → usamos los datos del folio
            $tacData = $folioData;

            // ===============================
            // Revisar expediente filtrado
            // ===============================
            if (!empty($expedientes)) {
                $in  = str_repeat('?,', count($expedientes) - 1) . '?';
                $sqlExp = "SELECT 1 FROM qm_tac 
                           WHERE Folio_Pisa = ? 
                             AND Expediente IN ($in) 
                             AND Calificador_Edo = 'COMPLETADA' LIMIT 1";
                $stmtExp = $conexion_analisis->prepare($sqlExp);
                $stmtExp->execute(array_merge([$folioPisa], $expedientes));
                $expEncontrado = $stmtExp->fetchColumn();

                if (!$expEncontrado) {
                    $alertas[] = "Expediente no encontrado en TAC";
                }
            }
        }

        // ===============================
        // 2️⃣ Datos locales (Coordiapp)
        // ===============================
        $sqlLocal = "
            SELECT Telefono, Tipo_Tarea, FK_Cope, FK_Tecnico_apps, Ont
            FROM tecnico_instalaciones_coordiapp
            WHERE Folio_Pisa = :folio
        ";
        $stmtLocal = $conexion_local->prepare($sqlLocal);
        $stmtLocal->bindParam(':folio', $folioPisa, PDO::PARAM_STR);
        $stmtLocal->execute();
        $localData = $stmtLocal->fetch(PDO::FETCH_ASSOC) ?: [
            'Telefono' => '',
            'Tipo_Tarea' => '',
            'FK_Cope' => '',
            'FK_Tecnico_apps' => '',
            'Ont' => ''
        ];

        // ===============================
        // 3️⃣ ALERTAS DE TELÉFONO
        // ===============================
        $telefonoTac = $tacData['TELEFONO'] ?? '';
        $telefonoLocal = $localData['Telefono'] ?? '';

        $tacVacio = trim((string)$telefonoTac) === '';
        $localVacio = trim((string)$telefonoLocal) === '';

        if ($tacVacio && $localVacio) {
            $alertas[] = "Teléfonos vacíos en ambas tablas";
        } elseif ($tacVacio) {
            $alertas[] = "Teléfono vacío en TAC";
        } elseif ($localVacio) {
            $alertas[] = "Teléfono vacío en Coordiapp";
        } else {
            $telTac = preg_replace('/\D/', '', $telefonoTac);
            $telLocal = preg_replace('/\D/', '', $telefonoLocal);
            if ($telTac !== $telLocal) {
                $alertas[] = "Teléfonos diferentes";
            }
        }

        // ===============================
        // 4️⃣ ALERTAS DE TIPO DE TAREA
        // ===============================
        $tareaTac = trim((string)($tacData['Tipo_De_Tarea'] ?? ''));
        $tareaLocal = trim((string)($localData['Tipo_Tarea'] ?? ''));

        if ($tareaTac === '' && $tareaLocal === '') {
            $alertas[] = "Tipo de tarea vacío en ambas tablas";
        } elseif ($tareaTac === '') {
            $alertas[] = "Tipo de tarea vacío en TAC";
        } elseif ($tareaLocal === '') {
            $alertas[] = "Tipo de tarea vacío en Coordiapp";
        } elseif (strcasecmp($tareaTac, $tareaLocal) !== 0) {
            $alertas[] = "Tipos de tarea diferentes";
        }

        // ===============================
        // 5️⃣ ALERTAS DE COPE
        // ===============================
        $copeTac = trim((string)($tacData['NOM_CT'] ?? ''));
        $fkCopeLocal = $localData['FK_Cope'] ?? null;

        $sqlCope = "SELECT COPE FROM copes WHERE id = :id";
        $stmtCope = $conexion_local->prepare($sqlCope);
        $stmtCope->bindParam(':id', $fkCopeLocal, PDO::PARAM_INT);
        $stmtCope->execute();
        $copeLocal = trim((string)$stmtCope->fetchColumn());

        if ($copeTac === '' && $copeLocal === '') {
            $alertas[] = "COPE vacío en ambas tablas";
        } elseif ($copeTac === '') {
            $alertas[] = "COPE vacío en TAC";
        } elseif ($copeLocal === '') {
            $alertas[] = "COPE vacío en Coordiapp";
        } elseif (strcasecmp($copeTac, $copeLocal) !== 0) {
            $alertas[] = "COPE diferentes";
        }

        // ===============================
        // 6️⃣ ALERTAS DE TÉCNICO
        // ===============================
        $tecnicoTac = trim((string)($tacData['Tecnico'] ?? ''));
        $fkTecnicoLocal = $localData['FK_Tecnico_apps'] ?? null;

        $sqlTec = "SELECT CONCAT(Nombre_T, ' ', Apellidos_T) AS NombreCompleto FROM tecnicos WHERE idTecnico = :id";
        $stmtTec = $conexion_local->prepare($sqlTec);
        $stmtTec->bindParam(':id', $fkTecnicoLocal, PDO::PARAM_INT);
        $stmtTec->execute();
        $tecnicoLocal = trim((string)$stmtTec->fetchColumn());

        if ($tecnicoTac === '' && $tecnicoLocal === '') {
            $alertas[] = "Técnico vacío en ambas tablas";
        } elseif ($tecnicoTac === '') {
            $alertas[] = "Técnico vacío en TAC";
        } elseif ($tecnicoLocal === '') {
            $alertas[] = "Técnico vacío en Coordiapp";
        } elseif (strcasecmp($tecnicoTac, $tecnicoLocal) !== 0) {
            $alertas[] = "Técnicos diferentes";
        }

        // ===============================
        // 7️⃣ ALERTAS DE NÚMERO DE SERIE
        // ===============================
        $numSerieTac = trim((string)($tacData['Num__Serie'] ?? ''));
        $numSerieLocal = trim((string)($localData['Ont'] ?? ''));

        if ($numSerieTac === '' && $numSerieLocal === '') {
            $alertas[] = "Número de serie vacío en ambas tablas";
        } elseif ($numSerieTac === '') {
            $alertas[] = "Número de serie vacío en TAC";
        } elseif ($numSerieLocal === '') {
            $alertas[] = "Número de serie vacío en Coordiapp";
        } elseif (strcasecmp($numSerieTac, $numSerieLocal) !== 0) {
            $alertas[] = "Números de serie diferentes";
        }

        // ===============================
        // 8️⃣ Devolver todas las alertas
        // ===============================
        return implode(' | ', $alertas);

    } catch (Exception $e) {
        return "Error en verificación: " . $e->getMessage();
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

public function ObtenerProduccionContratistaSinIncidencias($idContratista, $emulador, $semana, $tecnologia)
{
    $rows = [];
    $conexion_local = $this->get_conexion();
    $conexion_analisis = $this->get_conexion_analisis();

    require_once("/var/www/vhosts/ed-intra.com/contratistas.ed-intra.com/ordenes/funcionesemulador.php");

    $filtros = obtenerFiltrosPorEmulador($emulador, $tecnologia);
    $prefijos = $filtros['prefijos'];
    $excluir_prefijos = $filtros['excluir_prefijos'];

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
                tic.Metraje,              -- 👈 NUEVO
                tic.Tipo_Instalacion,     -- 👈 NUEVO
                tic.Direccion_Cliente,
                tic.Terminal,
                tic.Puerto,
                tic.Tipo_Instalacion,
                tic.Latitud,
                tic.Longitud,
                tic.Metraje,
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
              AND pc.Semana = :semana
              AND tic.Tecnologia = :emulador
              $condicionesTarea";

    $stmt = $conexion_local->prepare($sql);
    $stmt->bindParam(':idContratista', $idContratista, PDO::PARAM_INT);
    $stmt->bindParam(':semana', $semana, PDO::PARAM_STR);
    $stmt->bindParam(':emulador', $emulador, PDO::PARAM_STR);
    $stmt->execute();

    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($filas as $row) {
        $alerta = $this->verificarAlerta(
            $conexion_local,
            $conexion_analisis,
            $row['Folio_Pisa'],
            [$row['NExpediente']]
        );

        if (empty($alerta)) {
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