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
            WHERE FK_Contra_ST = COALESCE(NULLIF(:FK_Contra_ST, 0), FK_Contra_ST) AND Fecha_Salida_ST BETWEEN :Fecha1 AND :Fecha2
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

        // METODO PARA EL PDF DE SALIDAS
        public function CabeceraPDF($Folio_Salida)
        {
            $sql = "SELECT 
                    t.Folio_Salida_ST,
                    t.Folio_Telmex_ST,
                    t.Fecha_Salida_ST,
                    t.Tipo_MovS_T,
                    t.DescripcionSalida_T,
                    t.Cantidad_ST,
                    t.Cantidad_Metros_T,
                    a.Almacen AS Almacen,
                    uNeg.UN AS Unidad_Negocio,
                    CONCAT(c.Contratista, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS Contratista,
                    t.FK_Contra_ST AS FK_Contratista_Original,
                    uSalida.Nombre AS Usuario,
                    uNeg.UN
                FROM transferencias_t AS t
                INNER JOIN almacenes AS a 
                    ON t.FK_Almacen_ST = a.idAlmacenes
                LEFT JOIN contratistas AS c 
                    ON t.FK_Contra_ST = c.idContratistas
                LEFT JOIN unegocio AS uNeg 
                    ON t.FK_UNS_T = uNeg.idUN
                INNER JOIN usuario AS uSalida 
                    ON t.FK_User_ST = uSalida.idUsuario
                WHERE 
				    t.Folio_Salida_ST = :Folio_Salida
                GROUP BY 
                    t.Folio_Salida_ST
                ORDER BY 
                    t.Folio_Salida_ST DESC;";

            return $this->runQuery($sql, [
                'Folio_Salida' => $Folio_Salida
            ]);
        }

        // METODO PARA OBTENER LOS DETALLES DE LOS PRODUCTOS DE UNA SALIDA PARA EL PDF
        public function DetallesPDF($Folio_Salida)
        {
            $sql = "SELECT 
                    t.Folio_Salida,
                    p.Producto,
                    p.Modelo,
                    t.tecnologia,
                    p.Tipo_Proyecto,
                    p.UM,
                    p.Presentacion,
                    t.Cantidad_S,
                    t.Piezas_Sueltas,
                    t.Total_Piezas,
                    t.Total_Metros,
                    u.Nombre, 
                    FK_Almacen_S,
                    FK_Producto_S,
                    FK_Contratista_S
                FROM transferencias t
                INNER JOIN producto p ON t.FK_Producto_S = p.idProducto
                INNER JOIN usuario u ON t.FK_User_S = u.idUsuario
                WHERE t.Folio_Salida = :Folio_Salida";

            return $this->runQuery($sql, [
                'Folio_Salida' => $Folio_Salida
            ]);
        }

        // METODO PARA OBTENER LOS NÚMEROS DE SERIE DE UNA SALIDA PARA EL PDF
        public function SeriesPDF($Folio_Salida)
        {
            $sql = "SELECT 
                    m.Num_Serie_S, 
                    p.Producto, 
                    p.Modelo,
                    m.FK_Material_S,
                    m.FK_Contratista_Salida,
                    m.Estatus_Ubicacion,
                    m.Folio_Ultima_Transferencia,
                    m.Folio_Ultima_Devolucion,
                    (
                        SELECT t.Folio_Transferencia_Material_Det_T
                        FROM material_det_transferencia t
                        WHERE t.Num_Serie_T = m.Num_Serie_S
                            AND t.Fecha_Transferencia_T <= m.Fecha_Salida_S
                            AND EXISTS (
                                SELECT 1
                                FROM transferencias_almacenes_t tipo
                                WHERE tipo.Folio_Transferencia_TT = t.Folio_Transferencia_Material_Det_T
                                    AND tipo.Tipo_Tarea IN ('Contratista-Contratista', 'Contratista-Cope')
                            )
                        ORDER BY t.Fecha_Transferencia_T DESC
                        LIMIT 1
                    ) AS Folio_Transferencia_Salida,
                    (
                        SELECT d.Folio_Devolucion_Material_Det
                        FROM material_det_devoluciones d
                        WHERE d.Num_SerieDev = m.Num_Serie_S
                            AND d.Fecha_Devolucion >= m.Fecha_Salida_S
                            AND NOT EXISTS (
                                SELECT 1
                                FROM material_det_salida siguiente
                                WHERE siguiente.Num_Serie_S = m.Num_Serie_S
                                    AND siguiente.Fecha_Salida_S > m.Fecha_Salida_S
                                    AND siguiente.Fecha_Salida_S <= d.Fecha_Devolucion
                            )
                        ORDER BY d.Fecha_Devolucion ASC
                        LIMIT 1
                    ) AS Folio_Devolucion_Salida,
                    trans.Tipo_Tarea AS Tipo_Transferencia,
                    trans.Fecha_TT AS Fecha_Transferencia,
                    trans.Motivo_Transferencia_T AS Motivo_Transferencia,
                    trans.FK_CopeD AS FK_Cope_Destino,
                    CASE
                        WHEN trans.Tipo_Tarea = 'Contratista-Cope'
                            THEN cp.COPE
                        ELSE CONCAT_WS(' ', c.Contratista, c.apellido_paterno, c.apellido_materno)
                    END AS Contratista_Destino
                    FROM material_det_salida m
                    INNER JOIN producto p ON m.FK_Material_S = p.idProducto 
                    LEFT JOIN contratistas c ON m.FK_Contratista_Salida = c.idContratistas
                    LEFT JOIN transferencias_almacenes_t trans ON trans.Folio_Transferencia_TT = (
                        SELECT t.Folio_Transferencia_Material_Det_T
                        FROM material_det_transferencia t
                        WHERE t.Num_Serie_T = m.Num_Serie_S
                            AND t.Fecha_Transferencia_T <= m.Fecha_Salida_S
                            AND EXISTS (
                                SELECT 1
                                FROM transferencias_almacenes_t tipo
                                WHERE tipo.Folio_Transferencia_TT = t.Folio_Transferencia_Material_Det_T
                                    AND tipo.Tipo_Tarea IN ('Contratista-Contratista', 'Contratista-Cope')
                            )
                        ORDER BY t.Fecha_Transferencia_T DESC
                        LIMIT 1
                    )
                    LEFT JOIN copes cp ON trans.FK_CopeD = cp.id
                WHERE m.Folio_Salida_Material_Det_S = :Folio_Salida";

            return $this->runQuery($sql, [
                'Folio_Salida' => $Folio_Salida
            ]);
        }

        // FUNCION PARA OBTENER LOS DATOS DE LA DEVOLUCION +
        public function DevolucionPDF($Folio){
            $sql = "SELECT d.Folio_Dev_T,
                d.Fecha_Dev_T,
                d.Tipo_Tarea,
                d.Tipo_MovD_T,
                d.Cantidad_Dev_T,
                CASE 
                    WHEN d.Solicitante_Dev REGEXP '^[0-9]+$' AND solicitante_user.Nombre IS NOT NULL THEN solicitante_user.Nombre
                    ELSE d.Solicitante_Dev
                END AS Solicitante_Dev,
                d.Motivo_Dev,
                COALESCE(alm.Almacen, 'N/A') AS Almacen,
                COALESCE(NULLIF(TRIM(CONCAT_WS(' ', coo.Contratista, coo.apellido_paterno, coo.apellido_materno)), ''), 'N/A') AS Contratista,
                COALESCE(NULLIF(TRIM(CONCAT_WS(' ', tec.Nombre_T, tec.Apellidos_T)), ''), 'N/A') AS Tecnico,
                u.Nombre
            FROM devolucion_almacen_t d
            LEFT JOIN almacenes AS alm ON d.FK_Alm_Dev_T = alm.idAlmacenes
            LEFT JOIN contratistas AS coo ON d.FK_Contra_Dev_T = coo.idContratistas
            LEFT JOIN tecnicos AS tec ON d.FK_Tecnico_Dev_T = tec.idTecnico
            INNER JOIN usuario AS u ON d.FK_User_Dev_T = u.idUsuario
            LEFT JOIN usuario AS solicitante_user ON d.Solicitante_Dev = solicitante_user.idUsuario AND d.Solicitante_Dev REGEXP '^[0-9]+$'
            WHERE d.Folio_Dev_T = :Folio";

            return $this->runQuery($sql, ['Folio' => $Folio]);
        }

        // FUNCION PARA OBTENER LOS DETALLES DE LA DEVOLUCION +
        public function DetallesDevolucionPDF($Folio){
            $sql = "SELECT d.Cantidad_Dev AS Cantidad_Dev_Det,
                p.Producto,
                p.Modelo,
                p.Presentacion,
                p.idProducto,
                d.Piezas_Sueltas,
                d.Total_Piezas,
                d.Total_Metros
            FROM devolucion_almacen d
            INNER JOIN producto p ON d.FK_Material_Dev = p.idProducto
            WHERE d.Folio_Dev = :Folio";
            return $this->runQuery($sql, ['Folio' => $Folio]);

        }

        // FUNCION PARA OBTENER LOS NUMEROS DE SERIE DE LA TRANSFERENCIA +
        public function ONTsDevoluciones($Folio){
            $sql = "SELECT Modelo, Producto, Num_SerieDev FROM material_det_devoluciones
            INNER JOIN producto ON material_det_devoluciones.FK_MaterialDev = producto.idProducto
            WHERE Folio_Devolucion_Material_Det = :Folio";
            return $this->runQuery($sql, ['Folio' => $Folio]);
        }

    
    }

?>