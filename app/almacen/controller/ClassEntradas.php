<?php

    require_once(__DIR__ . '/../../cnx/cnx.php');

    class Entradas extends Conexion
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
                error_log("[Entradas::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
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
                error_log("[Entradas::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
                return false;
            }
        }

        /* FUNCION PARA OBTENER LAS ENTRADAS DE UN CONTRATISTA */
        public function Entradas($idContratistas, $Fecha1, $Fecha2)
        {
            $sql = "SELECT Folio_Salida_ST, Almacen, Fecha_Salida_ST, Cantidad_ST, Tipo_MovS_T, 
            CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) as Contratista FROM transferencias_t
            INNER JOIN almacenes ON FK_Almacen_ST = idAlmacenes
            INNER JOIN contratistas ON FK_Contra_ST = idContratistas
            WHERE FK_Contra_ST = :FK_Contra_ST AND Fecha_Salida_ST BETWEEN :Fecha1 AND :Fecha2
            ORDER BY Folio_Salida_ST DESC;";

            return $this->runQuery($sql, [
                ':FK_Contra_ST' => $idContratistas,
                ':Fecha1' => $Fecha1,
                ':Fecha2' => $Fecha2,
            ]);
        }


        /* FUNCION PARA OBTENER LOS MATERIALES DE UNA ENTRADA */
        public function ObtenerMaterialesEntrada($Folio_Salida)
        {
            $sql = "SELECT 
                    t.idTransferencias, 
                    p.Producto, 
                    p.Modelo, 
                    t.Tecnologia, 
                    p.UM, 
                    p.Presentacion,
                    t.Total_Piezas,
                    t.Total_Metros,
                    (SELECT COUNT(*) FROM material_det_salida m
                        WHERE m.Folio_Salida_Material_Det_S = :folio
                        AND m.FK_Material_S = t.FK_Producto_S) AS series_total
                    FROM transferencias t
                    INNER JOIN producto p ON t.FK_Producto_S = p.idProducto
                    WHERE t.Folio_Salida = :folio";

            return $this->runQuery($sql, ['folio' => $Folio_Salida]);
        }

        /* FUNCION PARA OBTENER LAS SERIES DE UNA ENTRADA */
        public function ObtenerSeriesEntrada($Folio_Salida)
        {
            $sql = "SELECT 
                    m.Num_Serie_S, 
                    p.Producto, 
                    p.Modelo,
                    p.Descripcion,
                    p.Tecnologia,
                    p.Presentacion,
                    p.UM
                    FROM material_det_salida m
                    INNER JOIN producto p ON m.FK_Material_S = p.idProducto
                    WHERE m.Folio_Salida_Material_Det_S = :folio";

            return $this->runQuery($sql, ['folio' => $Folio_Salida]);
        }

    
    }

?>