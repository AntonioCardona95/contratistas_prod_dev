<?php

require_once(__DIR__ . '/../../cnx/cnx.php');

class Index extends Conexion
{
    protected function runQuery($sql, $params = array())
    {
        $rows = array();

        $conexion = $this->conexion();

        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                $param = ':' . ltrim($key, ':');
                $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (method_exists($stmt, 'closeCursor')) {
                $stmt->closeCursor();
            }
        } catch (PDOException $e) {
            error_log("[Index::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
            return array();
        }

        return is_array($rows) ? $rows : array();
    }

    protected function runExecute($sql, $params = array())
    {
        $conexion = $this->conexion();
        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                $param = ':' . ltrim($key, ':');
                $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("[Index::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }

    protected function callAnalyticsProcedure($procedure, $params = array())
    {
        $rows = array();
        $conexion = $this->get_conexion_analytics();

        try {
            $placeholders = array();
            foreach ($params as $key => $val) {
                $placeholders[] = ':' . $key;
            }

            $stmt = $conexion->prepare("CALL {$procedure}(" . implode(',', $placeholders) . ")");
            foreach ($params as $key => $val) {
                $stmt->bindValue(':' . $key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (method_exists($stmt, 'closeCursor')) {
                $stmt->closeCursor();
            }
        } catch (PDOException $e) {
            error_log("[Index::callAnalyticsProcedure] Error en {$procedure}: " . $e->getMessage());
        }

        return is_array($rows) ? $rows : array();
    }

    // GRAFICA 6: total de ONTs liquidadas reportadas por TAC
    public function CountOntInstaladosTAC($FK_Contratista_Tecnico, $Fecha1, $Fecha2)
    {
        $rows = $this->callAnalyticsProcedure('sp_count_ordenes_contratista_ont', array(
            'Fecha1' => $Fecha1,
            'Fecha2' => $Fecha2,
            'FK_Contratista_Tecnico' => $FK_Contratista_Tecnico
        ));

        return !empty($rows) && isset($rows[0]['TOTAL']) ? (int)$rows[0]['TOTAL'] : 0;
    }

    // TARJETA 1: ONTs total sin asignar
    public function CountONTDisponiblesT($idContratistas)
    {
        $sql = "SELECT COUNT(*) AS TOTAL FROM material_det_salida
                LEFT JOIN salidas_contratistas ON Num_Serie_S = Num_Serie_Salida_Det
                WHERE Num_Serie_Salida_Det IS NULL
                AND FK_Contratista_Salida = :FK_Contratista_Salida";

        return $this->runQuery($sql, array('FK_Contratista_Salida' => $idContratistas));
    }

    // TARJETA 2: ONTs sin asignar dentro del rango seleccionado
    public function CountONTDisponibles($idContratistas, $Fecha1, $Fecha2)
    {
        $sql = "SELECT COUNT(*) AS TOTAL FROM material_det_salida
                LEFT JOIN salidas_contratistas ON Num_Serie_S = Num_Serie_Salida_Det
                WHERE FK_Contratista_Salida = :FK_Contratista_Salida
                AND Num_Serie_Salida_Det IS NULL
                AND Fecha_Salida_S BETWEEN :Fecha1 AND :Fecha2";

        return $this->runQuery($sql, array(
            'FK_Contratista_Salida' => $idContratistas,
            'Fecha1' => $Fecha1,
            'Fecha2' => $Fecha2
        ));
    }

    // TARJETA 3: ONTs asignadas dentro del rango seleccionado
    public function CountMaterialesAsignados($FK_Contratista_Salida_Det, $Fecha1, $Fecha2)
    {
        $sql = "SELECT COUNT(*) AS TOTAL FROM salidas_contratistas
                WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det
                AND Fecha_Salida_Det BETWEEN :Fecha1 AND :Fecha2";

        return $this->runQuery($sql, array(
            'FK_Contratista_Salida_Det' => $FK_Contratista_Salida_Det,
            'Fecha1' => $Fecha1,
            'Fecha2' => $Fecha2
        ));
    }

    // TARJETA 4: ONTs sin liquidar dentro del rango seleccionado
    public function CountMaterialesSinInstalar($FK_Contratista_Salida_Det, $Fecha1, $Fecha2)
    {
        $sql = "SELECT COUNT(*) AS TOTAL FROM salidas_contratistas
                WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det
                AND Fecha_Salida_Det BETWEEN :Fecha1 AND :Fecha2
                AND Ont_Ubicacion = 'TECNICO'";

        return $this->runQuery($sql, array(
            'FK_Contratista_Salida_Det' => $FK_Contratista_Salida_Det,
            'Fecha1' => $Fecha1,
            'Fecha2' => $Fecha2
        ));
    }

    // TARJETA 5: ONTs liquidadas dentro del rango seleccionado
    public function CountMaterialesInstalados($FK_Contratista_Salida_Det, $Fecha1, $Fecha2)
    {
        $sql = "SELECT COUNT(*) AS TOTAL FROM salidas_contratistas
                WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det
                AND Fecha_Salida_Det BETWEEN :Fecha1 AND :Fecha2
                AND Ont_Ubicacion = 'TERMINADO'";

        return $this->runQuery($sql, array(
            'FK_Contratista_Salida_Det' => $FK_Contratista_Salida_Det,
            'Fecha1' => $Fecha1,
            'Fecha2' => $Fecha2
        ));
    }

    // DATOS PARA: tendencia diaria de asignadas, sin liquidar y liquidadas
    public function CountMaterialesPorDia($idContratistas, $Fecha1, $Fecha2)
    {
        $sql = "SELECT DATE(Fecha_Salida_Det) AS FECHA,
                       COUNT(*) AS ASIGNADAS,
                       SUM(CASE WHEN Ont_Ubicacion = 'TECNICO' THEN 1 ELSE 0 END) AS SIN_LIQUIDAR,
                       SUM(CASE WHEN Ont_Ubicacion = 'TERMINADO' THEN 1 ELSE 0 END) AS LIQUIDADAS
                FROM salidas_contratistas
                WHERE FK_Contratista_Salida_Det = :idContratistas
                AND Fecha_Salida_Det BETWEEN :Fecha1 AND :Fecha2
                GROUP BY DATE(Fecha_Salida_Det)
                ORDER BY FECHA";

        return $this->runQuery($sql, array(
            'idContratistas' => $idContratistas,
            'Fecha1' => $Fecha1,
            'Fecha2' => $Fecha2
        ));
    }

    // DATOS PARA: rendimiento y efectividad por técnico
    public function CountMaterialesPorTecnico($idContratistas, $Fecha1, $Fecha2)
    {
        $sql = "SELECT NExpediente,
                       CONCAT(Nombre_T, ' ', Apellidos_T) AS NOMBRE_TECNICO,
                       COUNT(*) AS ASIGNADAS,
                       SUM(CASE WHEN Ont_Ubicacion = 'TECNICO' THEN 1 ELSE 0 END) AS SIN_LIQUIDAR,
                       SUM(CASE WHEN Ont_Ubicacion = 'TERMINADO' THEN 1 ELSE 0 END) AS LIQUIDADAS
                FROM salidas_contratistas
                INNER JOIN tecnicos ON FK_Tecnico_Salida_Det = idTecnico
                WHERE FK_Contratista_Salida_Det = :idContratistas
                AND Fecha_Salida_Det BETWEEN :Fecha1 AND :Fecha2
                GROUP BY idTecnico, NExpediente, Nombre_T, Apellidos_T
                ORDER BY ASIGNADAS DESC";

        return $this->runQuery($sql, array(
            'idContratistas' => $idContratistas,
            'Fecha1' => $Fecha1,
            'Fecha2' => $Fecha2
        ));
    }

}