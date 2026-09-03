<?php

    require_once(__DIR__ . '/ClassOps.php');

    class Aceptacion extends Ops
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
                error_log("[Aceptacion::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
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
                error_log("[Aceptacion::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
                return false;
            }
        }

        public function ObtenerPendientes($idContratistas, $Fecha1, $Fecha2)
        {
            $sql = "SELECT t.idTransferencias_T, t.Folio_Salida_ST, t.Folio_Telmex_ST, t.Fecha_Salida_ST,
                    t.Cantidad_ST, t.Cantidad_Metros_T, t.Solicitante_Salida_T, t.DescripcionSalida_T,
                    a.Almacen, uNeg.UN AS Unidad_Negocio,
                    (SELECT CASE
                            WHEN EXISTS (SELECT 1 FROM temp_material_det_salida m WHERE m.Folio_Salida_Material_Det_S = t.Folio_Salida_ST AND m.Status_Item != 'cancelado')
                                THEN COALESCE((SELECT COUNT(*) FROM temp_material_det_salida m WHERE m.Folio_Salida_Material_Det_S = t.Folio_Salida_ST AND m.Status_Item = 'pendiente'), 0)
                                ELSE COALESCE(SUM(
                                    CASE
                                        WHEN tt.Status_Item IN ('pendiente','parcial') THEN GREATEST(tt.Total_Piezas - COALESCE(tt.Cantidad_Aceptada,0),0)
                                        ELSE 0
                                    END
                                ),0)
                        END
                    FROM temp_transferencias tt
                    WHERE tt.Folio_Salida = t.Folio_Salida_ST) AS Cantidad_Pendiente
                    FROM temp_transferencias_t t
                    INNER JOIN almacenes a ON t.FK_Almacen_ST = a.idAlmacenes
                    LEFT JOIN unegocio uNeg ON t.FK_UNS_T = uNeg.idUN
                    WHERE t.FK_Contra_ST = :idContratistas
                    AND t.Status_Temp IN ('pendiente','parcial')
                    AND (
                        EXISTS (SELECT 1 FROM temp_material_det_salida m
                                WHERE m.Folio_Salida_Material_Det_S = t.Folio_Salida_ST AND m.Status_Item = 'pendiente')
                        OR EXISTS (SELECT 1 FROM temp_transferencias tt
                                WHERE tt.Folio_Salida = t.Folio_Salida_ST AND tt.Status_Item = 'pendiente')
                    )
                    AND t.Fecha_Salida_ST BETWEEN :Fecha1 AND :Fecha2
                    ORDER BY t.Folio_Salida_ST DESC";

            return $this->runQuery($sql, [
                'idContratistas' => $idContratistas,
                'Fecha1' => $Fecha1,
                'Fecha2' => $Fecha2
            ]);
        }

        public function ObtenerDetalleMateriales($Folio_Salida)
        {
            $sql = "SELECT t.idTransferencias, p.Producto, p.Modelo, t.Tecnologia, p.UM, p.Presentacion,
                        t.Cantidad_S, t.Piezas_Sueltas, t.Total_Piezas, t.Total_Metros,
                        COALESCE(t.Cantidad_Aceptada, 0) AS Cantidad_Aceptada,
                        CASE WHEN EXISTS (
                                SELECT 1 FROM temp_material_det_salida m
                                WHERE m.Folio_Salida_Material_Det_S = :folio
                                  AND m.FK_Material_S = t.FK_Producto_S
                                  AND m.Status_Item != 'cancelado'
                            )
                            THEN COALESCE((
                                SELECT COUNT(*) FROM temp_material_det_salida m
                                WHERE m.Folio_Salida_Material_Det_S = :folio
                                  AND m.FK_Material_S = t.FK_Producto_S
                                  AND m.Status_Item = 'pendiente'
                            ), 0)
                            ELSE GREATEST(t.Total_Piezas - COALESCE(t.Cantidad_Aceptada, 0), 0)
                        END AS Cantidad_Pendiente,
                        t.FK_Producto_S,
                        (SELECT COUNT(*) FROM temp_material_det_salida m
                            WHERE m.Folio_Salida_Material_Det_S = :folio
                              AND m.FK_Material_S = t.FK_Producto_S
                              AND m.Status_Item != 'cancelado') AS series_total
                    FROM temp_transferencias t
                    INNER JOIN producto p ON t.FK_Producto_S = p.idProducto
                    WHERE t.Folio_Salida = :folio";

            return $this->runQuery($sql, ['folio' => $Folio_Salida]);
        }

        public function ObtenerDetalleSeries($Folio_Salida, $soloLectura = false)
        {
            $sql = "SELECT m.Num_Serie_S, m.Status_Item, m.Motivo_Rechazo, p.Producto, p.Modelo
                    FROM temp_material_det_salida m
                    INNER JOIN producto p ON m.FK_Material_S = p.idProducto
                    WHERE m.Folio_Salida_Material_Det_S = :folio
                      AND m.Status_Item != 'cancelado'";

            if (!$soloLectura) {
                $sql .= " AND m.Status_Item = 'pendiente'";
            }

            return $this->runQuery($sql, ['folio' => $Folio_Salida]);
        }

    }

?>