<?php 

	/**
	 * DESARROLLADO ING. ANTONIO CARDONA
	 */
    require_once($_SERVER['DOCUMENT_ROOT']."/cnx/cnx.php");

    class Tac extends Conexion
    {
        /**
         * Ejecuta una consulta SELECT y devuelve filas como array asociativo.
         * @param string $sql Consulta SQL con placeholders :name
         * @param array $params Asociativo ['name' => value]
         * @param bool $useAnalytics Usar conexión analytics si true, sino conexión local
         * @return array
         */
        protected function runQuery($sql, $params = array(), $useAnalytics = false)
        {
            $rows = array();
            $conexion = $this->get_conexion_analytics();

            try {
                $stmt = $conexion->prepare($sql);

                foreach ($params as $key => $val) {
                    $param = ':' . ltrim($key, ':');
                    if (is_int($val)) {
                        $stmt->bindValue($param, $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue($param, $val, PDO::PARAM_STR);
                    }
                }

                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (method_exists($stmt, 'closeCursor')) {
                    $stmt->closeCursor();
                }
            } catch (PDOException $e) {
                error_log("[Coordiapp::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
                return array();
            }

            return is_array($rows) ? $rows : array();
        }

        public function OrdenesTac($FK_Contratista_Tecnico, $Fecha1, $Fecha2)
        {
            $sql = "SELECT DISTINCT tac.Folio_Pisa, tac.TELEFONO, tac.NOM_DIVISION, tac.NOM_AREA, tac.NOM_CT, tac.Expediente, tac.Tecnico, tac.FECHA_LIQ
            FROM qm_tac tac
            JOIN tecnicos tec
            ON tac.Expediente = tec.NExpediente
            AND tec.FK_Contratista_Tecnico = :FK_Contratista_Tecnico
            WHERE tac.Calificador_Edo = 'COMPLETADA'
            AND tac.FECHA_LIQ >= :Fecha1
            AND tac.FECHA_LIQ < DATE_ADD(:Fecha2, INTERVAL 1 DAY);";

            return $this->runQuery($sql, [
                'FK_Contratista_Tecnico' => $FK_Contratista_Tecnico,
                'Fecha1' => $Fecha1,
                'Fecha2' => $Fecha2
            ]);
        }

    }

    Class Coordiapp extends Conexion
    {
        /**
         * Ejecuta una consulta SELECT y devuelve filas como array asociativo.
         * @param string $sql Consulta SQL con placeholders :name
         * @param array $params Asociativo ['name' => value]
         * @param bool $useAnalytics Usar conexión analytics si true, sino conexión local
         * @return array
         */
        protected function runQuery($sql, $params = array(), $useAnalytics = false)
        {
            $rows = array();
            $conexion = $useAnalytics ? $this->get_conexion_analytics() : $this->get_conexion();

            try {
                $stmt = $conexion->prepare($sql);

                foreach ($params as $key => $val) {
                    $param = ':' . ltrim($key, ':');
                    if (is_int($val)) {
                        $stmt->bindValue($param, $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue($param, $val, PDO::PARAM_STR);
                    }
                }

                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (method_exists($stmt, 'closeCursor')) {
                    $stmt->closeCursor();
                }
            } catch (PDOException $e) {
                error_log("[Coordiapp::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
                return array();
            }

            return is_array($rows) ? $rows : array();
        }
        
        public function OrdenesCoordiApp($FK_Contratista_Tecnico,$Fecha1,$Fecha2)
        {
            $sql = "SELECT * FROM View_Detalle_Coordiapp_Completadas
            WHERE Fecha_Coordiapp BETWEEN :Fecha1 and :Fecha2 and FK_Contratista_Tecnico = :FK_Contratista_Tecnico";

            return $this->runQuery($sql, [
                'FK_Contratista_Tecnico' => $FK_Contratista_Tecnico,
                'Fecha1' => $Fecha1,
                'Fecha2' => $Fecha2
            ]);
        }
    }
?>