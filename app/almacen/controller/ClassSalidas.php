<?php

    require_once(__DIR__ . '/../../cnx/cnx.php');

    class Salidas extends Conexion
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
                error_log("[Salidas::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
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
                error_log("[Salidas::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
                return false;
            }
        }

        /* FUNCION PARA OBTENER LAS SALIDAS DE UN CONTRATISTA */
        public function Salidas($idContratistas, $Fecha1, $Fecha2)
        {
            $sql = "SELECT FK_Contratista_T AS idContratista, Cantidad_T, Fecha_T, Folio_Salida_T, CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico,
            CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) AS Contratista FROM `salidas_contratista_t`
            INNER JOIN tecnicos ON FK_Tecnico_T = idTecnico
            INNER JOIN contratistas ON FK_Contratista_T = idContratistas
            WHERE FK_Contratista_T = COALESCE(NULLIF(:FK_Contratista_T, 0), FK_Contratista_T) AND Fecha_T BETWEEN :Fecha1 and :Fecha2
            ORDER BY Fecha_T DESC;";

            return $this->runQuery($sql, [
                ':FK_Contratista_T' => $idContratistas,
                ':Fecha1' => $Fecha1,
                ':Fecha2' => $Fecha2,
            ]);
        }

        /* FUNCION PARA OBTENER EL DETALLE DE UNA SALIDA (SON PUROS NUMEROS DE SERIE NO HAY MISCELANEOS*/
        public function SalidasDet($Folio_Salida_Det, $FK_Contratista_Salida_Det)
        {
            $sql = "SELECT CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico, Num_Serie_Salida_Det, Producto, Descripcion, 
            Tecnologia, Modelo FROM `salidas_contratistas` 
            INNER JOIN producto ON FK_Material_Salidas_Contratistas = idProducto 
            INNER JOIN tecnicos ON idTecnico = FK_Tecnico_Salida_Det 
            WHERE Folio_Salida_Det = :Folio_Salida_Det AND FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det";

            return $this->runQuery($sql, [
                ':Folio_Salida_Det' => $Folio_Salida_Det,
                ':FK_Contratista_Salida_Det' => $FK_Contratista_Salida_Det
            ]);
        }

        public function ObtenerEncabezadoSalidaPDF($Folio_Salida_T, $idContratistas)
        {
            $sql = "SELECT
                        s.Folio_Salida_T,
                        s.Fecha_T,
                        s.Cantidad_T,
                        CONCAT(t.Nombre_T, ' ', t.Apellidos_T) AS Tecnico,
                        CONCAT(c.Contratista, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS Contratista
                    FROM salidas_contratista_t AS s
                    INNER JOIN tecnicos AS t ON t.idTecnico = s.FK_Tecnico_T
                    INNER JOIN contratistas AS c ON c.idContratistas = s.FK_Contratista_T
                    WHERE s.Folio_Salida_T = :folio
                        AND s.FK_Contratista_T = :idContratistas
                    LIMIT 1";

            return $this->runQuery($sql, array(
                'folio' => (int)$Folio_Salida_T,
                'idContratistas' => (int)$idContratistas
            ));
        }

        public function ObtenerMaterialesSalidaPDF($Folio_Salida_T, $idContratista)
        {
            $sql = "SELECT
                        p.Producto,
                        p.Modelo,
                        p.Tecnologia,
                        COUNT(s.Num_Serie_Salida_Det) AS Cantidad
                    FROM salidas_contratistas AS s
                    INNER JOIN producto AS p ON p.idProducto = s.FK_Material_Salidas_Contratistas
                    WHERE s.Folio_Salida_Det = :folio
                        AND s.FK_Contratista_Salida_Det = :idContratista
                    GROUP BY p.idProducto, p.Producto, p.Modelo, p.Tecnologia
                    ORDER BY p.Producto, p.Modelo";

            return $this->runQuery($sql, array(
                'folio' => (int)$Folio_Salida_T,
                'idContratista' => (int)$idContratista
            ));
        }

        public function ObtenerSeriesSalidaPDF($Folio_Salida_T, $idContratista)
        {
            return $this->SalidasDet($Folio_Salida_T, $idContratista);
        }

        // OBTENER EL FOLIO PROVISIONAL
        public function FolioProvisionalSalida()
        {
            try {
                $conexion = $this->get_conexion();
                $stmt = $conexion->prepare(
                    "SELECT siguiente_folio
                     FROM folio_secuencia
                     WHERE id_tabla = 'salidas_contratista_t'"
                );
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                return $resultado ? (int)$resultado['siguiente_folio'] : 1;
            } catch (Exception $e) {
                error_log('[FolioProvisionalSalida] Excepcion: ' . $e->getMessage());
                return false;
            }
        }

        /* FUNCION PARA OBTENER EL SIGUIENTE FOLIO DE SALIDA DE CONTRATISTA SIN DUPLICADOS */
        public function FolioSeguroSalida()
        {
            $conexion = $this->get_conexion();

            try {
                $conexion->beginTransaction();
                $stmt = $conexion->prepare(
                    "SELECT siguiente_folio
                     FROM folio_secuencia
                     WHERE id_tabla = 'salidas_contratista_t'
                     FOR UPDATE"
                );
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$resultado) {
                    throw new Exception('No existe la secuencia salidas_contratista_t. Inicialice folio_secuencia.');
                }

                $folioActual = (int)$resultado['siguiente_folio'];
                $actualizar = $conexion->prepare(
                    "UPDATE folio_secuencia
                     SET siguiente_folio = siguiente_folio + 1
                     WHERE id_tabla = 'salidas_contratista_t'"
                );
                $actualizar->execute();

                $conexion->commit();
                return $folioActual;
            } catch (Exception $e) {
                if ($conexion->inTransaction()) {
                    $conexion->rollBack();
                }
                error_log('[FolioSeguroSalida] Excepcion: ' . $e->getMessage());
                return false;
            }
        }
    
        /* FUNCION PARA INSERTAR UNA NUEVA SALIDA DE CONTRATISTA */
        public function InsertSalidaTecnico($Folio_Salida_Det, $FK_Contratista_Salida_Det, $FK_Tecnico_Salida_Det, $Fecha_Salida_Det, $Num_Serie_Salida_Det, $Descripcion_Salida_Det, $FK_Material_Salidas_Contratistas)
        {
            $conexion = $this->get_conexion();
            $sql = "INSERT INTO salidas_contratistas(Folio_Salida_Det, FK_Contratista_Salida_Det, FK_Tecnico_Salida_Det, Fecha_Salida_Det, Num_Serie_Salida_Det, Descripcion_Salida_Det, FK_Material_Salidas_Contratistas) 
            VALUES (:Folio_Salida_Det, :FK_Contratista_Salida_Det, :FK_Tecnico_Salida_Det, :Fecha_Salida_Det, :Num_Serie_Salida_Det, :Descripcion_Salida_Det, :FK_Material_Salidas_Contratistas)";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Folio_Salida_Det', $Folio_Salida_Det, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $FK_Contratista_Salida_Det, PDO::PARAM_STR);
            $statement->bindParam(':FK_Tecnico_Salida_Det', $FK_Tecnico_Salida_Det, PDO::PARAM_STR);
            $statement->bindParam(':Fecha_Salida_Det', $Fecha_Salida_Det, PDO::PARAM_STR);
            $statement->bindParam(':Num_Serie_Salida_Det', $Num_Serie_Salida_Det, PDO::PARAM_STR);
            $statement->bindParam(':Descripcion_Salida_Det', $Descripcion_Salida_Det, PDO::PARAM_STR);
            $statement->bindParam(':FK_Material_Salidas_Contratistas', $FK_Material_Salidas_Contratistas, PDO::PARAM_STR);

            if (!$statement) {
                return "Error Registro";
            }

            $resultado = $statement->execute();
            return $resultado ? "Registro Exitoso" : "Error Registro";
        }

        /* FUNCION PARA ACTUALIZAR EL ESTATUS DE UNA SALIDA DE CONTRATISTA */
        public function EstatusSalidaTecnico($Num_Serie_S, $Ubicacion_Material)
        {
            $conexion = $this->get_conexion();
            $sql = "UPDATE material_det_salida
            SET Ubicacion_Material = :Ubicacion_Material
            WHERE Num_Serie_S = :Num_Serie_S";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Num_Serie_S', $Num_Serie_S, PDO::PARAM_STR);
            $statement->bindParam(':Ubicacion_Material', $Ubicacion_Material, PDO::PARAM_STR);

            if (!$statement) {
                return "Error Al Modificar";
            }

            $resultado = $statement->execute();
            return $resultado ? "Modificacion Exitosa" : "Error Al Modificar";
        }
        /* FUNCION PARA INSERTAR UNA NUEVA SALIDA DE CONTRATISTA */
        public function InsertSalidaTecnicoT($FK_Contratista_T, $FK_Tecnico_T, $Cantidad_T, $Fecha_T, $Folio_Salida_T)
        {
            $conexion = $this->get_conexion();
            $sql = "INSERT INTO salidas_contratista_t(FK_Contratista_T, FK_Tecnico_T, Cantidad_T, Fecha_T, Folio_Salida_T) 
            VALUES (:FK_Contratista_T, :FK_Tecnico_T, :Cantidad_T, :Fecha_T, :Folio_Salida_T)";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_T', $FK_Contratista_T, PDO::PARAM_STR);
            $statement->bindParam(':FK_Tecnico_T', $FK_Tecnico_T, PDO::PARAM_STR);
            $statement->bindParam(':Cantidad_T', $Cantidad_T, PDO::PARAM_STR);
            $statement->bindParam(':Fecha_T', $Fecha_T, PDO::PARAM_STR);
            $statement->bindParam(':Folio_Salida_T', $Folio_Salida_T, PDO::PARAM_STR);

            if (!$statement) {
                return "Error Registro";
            }

            $resultado = $statement->execute();
            return $resultado ? "Registro Exitoso" : "Error Registro";
        }
    }
?>