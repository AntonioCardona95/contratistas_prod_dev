<?php

    require_once(__DIR__ . '/../../cnx/cnx.php');

    class Rechazo extends Conexion
    {
        // ====== FUNCIONES GENERICAS DE CONSULTA Y EJECUCION ====== //

        protected function runQuery($sql, $params = array(), $useAnalytics = false)
        {
            $rows = array();
            $conexion = $this->get_conexion();

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
                error_log("[Rechazo::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
                return array();
            }

            return is_array($rows) ? $rows : array();
        }

        protected function runExecute($sql, $params = array())
        {
            $conexion = $this->get_conexion();
            try {
                $stmt = $conexion->prepare($sql);
                foreach ($params as $key => $val) {
                    $param = ':' . ltrim($key, ':');
                    $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
                return $stmt->execute();
            } catch (PDOException $e) {
                error_log("[Rechazo::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
                return false;
            }
        }

        public function ObtenerRechazados($idContratistas, $Fecha1, $Fecha2)
        {
            $sql = "SELECT
                        t.idTransferencias_T, t.Folio_Salida_ST, t.Folio_Telmex_ST, t.Fecha_Salida_ST,
                        t.Cantidad_ST, t.Cantidad_Metros_T, t.Solicitante_Salida_T, t.DescripcionSalida_T,
                        t.Fecha_Respuesta,
                        a.Almacen, uNeg.UN AS Unidad_Negocio
                    FROM temp_transferencias_t t
                    INNER JOIN almacenes a ON t.FK_Almacen_ST = a.idAlmacenes
                    LEFT JOIN unegocio uNeg ON t.FK_UNS_T = uNeg.idUN
                    WHERE t.FK_Contra_ST = :idContratistas
                    AND t.Status_Temp = 'rechazado'
                    AND t.Fecha_Salida_ST BETWEEN :Fecha1 AND :Fecha2
                    ORDER BY t.idTransferencias_T DESC";

            return $this->runQuery($sql, [
                'idContratistas' => $idContratistas,
                'Fecha1' => $Fecha1,
                'Fecha2' => $Fecha2
            ]);
        }

    }

?>