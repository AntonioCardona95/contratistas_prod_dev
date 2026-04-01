<?php 
//ini_set('display_errors', 1); 
//ini_set('display_startup_errors', 1); 
//error_reporting(E_ALL);

/**
 * DESARROLLADO ING. ANTONIO CARDONA
 */
require_once($_SERVER['DOCUMENT_ROOT']."/cnx/cnx.php");

class Emulador extends Conexion
{
    public function OrdenesNoCapturadasCoordiApp($idContratistas, $Fecha1 = null, $Fecha2 = null)
    {
        try {

            $rows = [];
            $conexion = $this->get_conexion();  

            $conexion->exec("SET NAMES 'utf8mb4'");
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT 
                p.Folio_Pisa,
                p.Telefono,
                p.Tipo_Tarea,
                p.Cope,
                p.FK_Contratista,
                p.Fecha_Liquidacion,
                p.Semana,
                p.`año`,
                p.metraje,
                p.Fecha_Carga,
                t.Folio_Pisa AS folio_en_coordiapp
            FROM Produccion_contratista p
            LEFT JOIN tecnico_instalaciones_coordiapp t
                ON p.Folio_Pisa = t.Folio_Pisa
                AND TRIM(p.Telefono) = TRIM(t.Telefono)
            WHERE p.FK_Contratista = :idContratistas
              AND t.Folio_Pisa IS NULL";

            if (!empty($Fecha1) && !empty($Fecha2)) {
                $sql .= " AND (
                    p.Fecha_Carga BETWEEN :Fecha1 AND :Fecha2 
                    OR p.Fecha_Carga IS NULL 
                    OR p.Fecha_Carga = '0000-00-00'
                )";
            }

            $statement = $conexion->prepare($sql);

            $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);

            if (!empty($Fecha1) && !empty($Fecha2)) {
                $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
                $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            }

            $statement->execute();

            while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = $result;
            }

            return $rows;

        } catch (Exception $e) {
            return [];
        }
    }

// PRODUCCION CONTRATISTAS // 

public function ProduccionContratista($idContratistas, $Fecha1 = null, $Fecha2 = null)
{
    try {

        $rows = [];
        $conexion = $this->get_conexion();  

        $conexion->exec("SET NAMES 'utf8mb4'");
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT 
                    p.Folio_Pisa,
                    p.Telefono,
                    p.Tipo_Tarea,
                    p.Cope,
                    p.FK_Contratista,
                    p.Fecha_Liquidacion,
                    p.Semana,
                    p.`año`,
                    p.metraje,
                    p.Fecha_Carga
                FROM Produccion_contratista p
                WHERE p.FK_Contratista = :idContratistas";

        // ===============================
        // FILTRO POR FECHAS (Fecha_Carga)
        // ===============================
        if (!empty($Fecha1) && !empty($Fecha2)) {
            $sql .= " 
                AND p.Fecha_Carga BETWEEN :Fecha1 AND :Fecha2
                AND p.Fecha_Carga != '0000-00-00 00:00:00'
                AND p.Fecha_Carga NOT LIKE '%-00-%'
            ";
        }

        $statement = $conexion->prepare($sql);

        $statement->bindParam(':idContratistas', $idContratistas, PDO::PARAM_INT);

        // Bind de fechas solo si existen
        if (!empty($Fecha1) && !empty($Fecha2)) {
            $statement->bindParam(':Fecha1', $Fecha1);
            $statement->bindParam(':Fecha2', $Fecha2);
        }

        $statement->execute();

        while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $result;
        }

        return $rows;

    } catch (Exception $e) {
        return [];
    }
}

} 