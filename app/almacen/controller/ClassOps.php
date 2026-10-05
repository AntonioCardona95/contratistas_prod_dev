<?php 

	/**
	 * DESARROLLADO ING. ANTONIO CARDONA
	 */
    //require_once($_SERVER['DOCUMENT_ROOT']."/cnx/cnx.php");
    require_once(__DIR__ . '/../../cnx/cnx.php');


    class Ops extends Conexion
    {
        // ====== FUNCIONES GENERICAS DE CONSULTA Y EJECUCION ====== //

        protected function runQuery($sql, $params = array(), $useAnalytics = false)
        {
            $rows = array();
            // En Salidas usamos la conexión principal
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
                // Loguear el error para diagnóstico y devolver array vacío
                error_log("[Tac::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
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
                return $stmt->execute(); // true o false
            } catch (PDOException $e) {
                error_log("[Tac::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
                return false;
            }
        }
        
        public function CountMaterialesAsignados($FK_Contratista_Salida_Det,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL FROM `salidas_contratistas`
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $FK_Contratista_Salida_Det, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }
        
        public function CountMaterialesSinInstalar($FK_Contratista_Salida_Det,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL FROM `salidas_contratistas`
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            AND Ont_Ubicacion = 'TECNICO'";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $FK_Contratista_Salida_Det, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function CountMaterialesInstalados($FK_Contratista_Salida_Det,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL FROM `salidas_contratistas`
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            AND Ont_Ubicacion = 'TERMINADO'";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $FK_Contratista_Salida_Det, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function TecnicosConMasONTDisponibles($FK_Contratista_Salida_Det,$Fecha1,$Fecha2)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL, NExpediente FROM salidas_contratistas
            INNER JOIN tecnicos ON FK_Tecnico_Salida_Det = idTecnico
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            AND Ont_Ubicacion = 'TECNICO'
            GROUP BY NExpediente
            ORDER BY TOTAL DESC
            LIMIT 10";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $FK_Contratista_Salida_Det, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function TecnicosConMasONTInstaladas($FK_Contratista_Salida_Det,$Fecha1,$Fecha2)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL, NExpediente FROM salidas_contratistas
            INNER JOIN tecnicos ON FK_Tecnico_Salida_Det = idTecnico
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            AND Ont_Ubicacion = 'TERMINADO'
            GROUP BY NExpediente
            ORDER BY TOTAL DESC
            LIMIT 10";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $FK_Contratista_Salida_Det, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }

        public function InstaladasVSNoInstaladas($FK_Contratista_Salida_Det,$Fecha1,$Fecha2)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) AS TOTAL, 
            CASE 
                WHEN Ont_Ubicacion = 'TECNICO' THEN 'OS SIN PROVISIÓN DE ONT'
                WHEN Ont_Ubicacion = 'TERMINADO' THEN 'OS CON PROVISIÓN DE ONT'
            END AS Ont_Ubicacion FROM salidas_contratistas
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            GROUP BY Ont_Ubicacion";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $FK_Contratista_Salida_Det, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;        
        }
        
        public function Entradas($idContratistas,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT Folio_Salida_ST, Almacen, Fecha_Salida_ST, Cantidad_ST, Tipo_MovS_T, CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) as Contratista FROM transferencias_t
            INNER JOIN almacenes ON FK_Almacen_ST = idAlmacenes
            INNER JOIN contratistas ON FK_Contra_ST = idContratistas
            WHERE FK_Contra_ST = :FK_Contra_ST AND Fecha_Salida_ST BETWEEN :Fecha1 and :Fecha2
            ORDER BY Folio_Salida_ST DESC;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contra_ST', $idContratistas, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        // OBTENER MATERIALES DE UNA ENTRADA (FOLIO_SALIDA)
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

        // OBTENER NÚMEROS DE SERIE DE UNA ENTRADA (FOLIO_SALIDA)
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

        // MANTENER PARA COMPATIBILIDAD HACIA ATRÁS
        public function EntradasDet($Folio_Salida_Material_Det_S)
        {
            return $this->ObtenerSeriesEntrada($Folio_Salida_Material_Det_S);
        }

        public function Salidas($idContratistas,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT Cantidad_T, Fecha_T, Folio_Salida_T, CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico, CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) AS Contratista FROM `salidas_contratista_t`
            INNER JOIN tecnicos ON FK_Tecnico_T = idTecnico
            INNER JOIN contratistas ON FK_Contratista_T = idContratistas
            WHERE FK_Contratista_T = :FK_Contratista_T AND Fecha_T BETWEEN :Fecha1 and :Fecha2
            ORDER BY Fecha_T DESC;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_T', $idContratistas, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function Traspasos($idContratistas,$Fecha1,$Fecha2)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT Comentarios_Traspaso, Cantidad_Traspaso, Folio_Traspaso_T, Fecha_Traspaso, CONCAT(origen.Nombre_T, ' ', origen.Apellidos_T) AS TecnicoOrigen, CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) AS Contratista, CONCAT(destino.Nombre_T, ' ', destino.Apellidos_T) AS TecnicoDestino FROM `traspasos_tecnicos_t`
            INNER JOIN tecnicos as origen ON fk_Tecnico_Origen_Traspaso_T = origen.idTecnico
            INNER JOIN tecnicos as destino ON fk_Tecnico_Destino_Traspaso_T = destino.idTecnico
            INNER JOIN contratistas ON fk_Contratista_Traspaso = idContratistas
            WHERE fk_Contratista_Traspaso = :fk_Contratista_Traspaso AND Fecha_Traspaso BETWEEN :Fecha1 and :Fecha2
            ORDER BY Folio_Traspaso_T DESC;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':fk_Contratista_Traspaso', $idContratistas, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function TraspasoDet($Folio_Traspaso)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT Numero_Serie_Tras, Folio_Traspaso, Fecha_Tras, CONCAT(origen.Nombre_T, ' ', origen.Apellidos_T) AS TecnicoOrigen, CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) AS Contratista, CONCAT(destino.Nombre_T, ' ', destino.Apellidos_T) AS TecnicoDestino FROM `traspasos_tecnicos`
            INNER JOIN tecnicos as origen ON fk_Tecnico_Origen_Tras = origen.idTecnico
            INNER JOIN tecnicos as destino ON fk_Tecnico_Destino_Tras = destino.idTecnico
            INNER JOIN contratistas ON fk_Contratista_Tras = idContratistas
            WHERE Folio_Traspaso = :Folio_Traspaso";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Folio_Traspaso', $Folio_Traspaso, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function SalidasDet($Folio_Salida_Det)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico, Num_Serie_Salida_Det, Producto, Descripcion, Tecnologia, Modelo FROM `salidas_contratistas` 
            INNER JOIN producto ON FK_Material_Salidas_Contratistas = idProducto 
            INNER JOIN tecnicos ON idTecnico = FK_Tecnico_Salida_Det 
            WHERE Folio_Salida_Det = :Folio_Salida_Det";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Folio_Salida_Det', $Folio_Salida_Det, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function CountONTDisponibles($idContratistas,$Fecha1,$Fecha2)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) as TOTAL FROM `material_det_salida`
            LEFT JOIN salidas_contratistas ON Num_Serie_S = Num_Serie_Salida_Det
            WHERE FK_Contratista_Salida = :FK_Contratista_Salida AND Num_Serie_Salida_Det IS NULL
            AND Fecha_Salida_S BETWEEN :Fecha1 and :Fecha2;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida', $idContratistas, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows; 
        }

        public function CountONTDisponiblesT($idContratistas)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT COUNT(*) as TOTAL FROM `material_det_salida`
            LEFT JOIN salidas_contratistas ON Num_Serie_S = Num_Serie_Salida_Det
            WHERE Num_Serie_Salida_Det IS NULL AND FK_Contratista_Salida = :FK_Contratista_Salida;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Salida', $idContratistas, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows; 
        }

        public function ONTDisponiblesAsignarDet($idContratistas)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT Folio_Salida_Material_Det_S, Fecha_Salida_S, Localidad_Material_S, Producto, Modelo, Num_Serie_S FROM `material_det_salida`
            LEFT JOIN salidas_contratistas ON Num_Serie_S = Num_Serie_Salida_Det
			INNER JOIN producto ON idProducto = FK_Material_S 
            WHERE FK_Contratista_Salida = :FK_Contratista_Salida AND Num_Serie_Salida_Det IS NULL
            ORDER BY Fecha_Salida_S DESC;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Salida', $idContratistas, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows; 
        }

        public function ONTAsignadosDet($idContratistas,$Fecha1,$Fecha2)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT Producto, Modelo, Num_Serie_Salida_Det, CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico, Fecha_Salida_Det FROM `salidas_contratistas`
            INNER JOIN producto ON idProducto = FK_Material_Salidas_Contratistas 
            INNER JOIN tecnicos ON FK_Tecnico_Salida_Det = idTecnico AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det
            ORDER BY Fecha_Salida_Det DESC;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $idContratistas, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows; 
        }

        public function ONTSinInstalarDet($idContratistas,$Fecha1,$Fecha2)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT Producto, Modelo, Num_Serie_Salida_Det, CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico, Fecha_Salida_Det FROM `salidas_contratistas`
            INNER JOIN producto ON idProducto = FK_Material_Salidas_Contratistas 
            INNER JOIN tecnicos ON FK_Tecnico_Salida_Det = idTecnico AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det AND Ont_Ubicacion = 'TECNICO'
            ORDER BY Fecha_Salida_Det DESC;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $idContratistas, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows; 
        }

        public function ONTInstaladosDet($idContratistas,$Fecha1,$Fecha2)
        {
            $rows = null;   
            $conexion = $this->get_conexion();
            $sql="SELECT Folio_Pisa, Fecha_Coordiapp, Producto, Modelo, Num_Serie_Salida_Det, CONCAT(Nombre_T, ' ', Apellidos_T) AS Tecnico, Fecha_Salida_Det FROM `salidas_contratistas`
            INNER JOIN producto ON idProducto = FK_Material_Salidas_Contratistas 
            INNER JOIN tecnicos ON FK_Tecnico_Salida_Det = idTecnico
            INNER JOIN tecnico_instalaciones_coordiapp ON Ont = Num_Serie_Salida_Det
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det AND Fecha_Salida_Det BETWEEN :Fecha1 and :Fecha2
            ORDER BY Fecha_Salida_Det DESC;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Fecha1', $Fecha1, PDO::PARAM_STR);
            $statement->bindParam(':Fecha2', $Fecha2, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $idContratistas, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows; 
        }

        public function UltimaSalida()
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT * FROM salidas_contratista_t 
            ORDER BY Fecha_T DESC LIMIT 1";
            $statement = $conexion->prepare($sql);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
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

        // OBTENER EL FOLIO SEGURO EL REAL QUE EVITA LA DUPLICIDAD
        public function FolioSeguroSalida()
        {
            $conexion = $this->get_conexion();

            try {
                $conexion->beginTransaction();

                // La fila debe existir antes de bloquearla. La inicializacion
                // de datos se realiza con el SQL de migracion indicado abajo.
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

        public function UltimaTraspaso()
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT * FROM traspasos_tecnicos_t
            ORDER BY Fecha_Traspaso DESC LIMIT 1";
            $statement = $conexion->prepare($sql);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function Tecnicos($FK_Contratista_Tecnico)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT * FROM tecnicos
            WHERE FK_Contratista_Tecnico = :FK_Contratista_Tecnico AND Estatus_Tecnico = 'ACTIVO'
            ORDER BY Nombre_T ASC";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Tecnico', $FK_Contratista_Tecnico, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function Materiales($FK_Contratista_Salida)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT * FROM material_det_salida
            INNER JOIN producto ON FK_Material_S = idProducto
            WHERE FK_Contratista_Salida = :FK_Contratista_Salida AND Ubicacion_Material IS NULL
            GROUP BY FK_Material_S
            ORDER BY Producto ASC";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Contratista_Salida', $FK_Contratista_Salida, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function LoadSeries($FK_Material_S, $FK_Contratista_Salida)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT * FROM material_det_salida
            WHERE FK_Contratista_Salida = :FK_Contratista_Salida AND FK_Material_S = :FK_Material_S AND Ubicacion_Material IS NULL";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Material_S', $FK_Material_S, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida', $FK_Contratista_Salida, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function LoadSeriesTraspasos($FK_Tecnico_Salida_Det, $FK_Contratista_Salida_Det)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT Num_Serie_Salida_Det FROM salidas_contratistas 
            WHERE FK_Contratista_Salida_Det = :FK_Contratista_Salida_Det AND FK_Tecnico_Salida_Det = :FK_Tecnico_Salida_Det AND Ont_Ubicacion = 'TECNICO';";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':FK_Tecnico_Salida_Det', $FK_Tecnico_Salida_Det, PDO::PARAM_STR);
            $statement->bindParam(':FK_Contratista_Salida_Det', $FK_Contratista_Salida_Det, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
        }

        public function InsertSalidaTecnico($Folio_Salida_Det,$FK_Contratista_Salida_Det,$FK_Tecnico_Salida_Det,$Fecha_Salida_Det,$Num_Serie_Salida_Det,$Descripcion_Salida_Det,$FK_Material_Salidas_Contratistas)
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
            else{
                $resultado = $statement->execute();
                return "Registro Exitoso";
            }
        }

        public function EstatusSalidaTecnico($Num_Serie_S,$Ubicacion_Material)
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
            else{
                $statement->execute();
                return "Modificacion Exitosa";
            }
        }

        public function UpdateTraspasoTecnico($fk_Tecnico_Origen_Tras,$fk_Tecnico_Destino_Tras,$Num_Serie_Salida_Det)
        {
            $conexion = $this->get_conexion();
            $sql = "UPDATE salidas_contratistas
            SET FK_Tecnico_Salida_Det = :fk_Tecnico_Destino_Tras
            WHERE FK_Tecnico_Salida_Det = :fk_Tecnico_Origen_Tras AND Num_Serie_Salida_Det = :Num_Serie_Salida_Det";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':fk_Tecnico_Origen_Tras', $fk_Tecnico_Origen_Tras, PDO::PARAM_STR);
            $statement->bindParam(':fk_Tecnico_Destino_Tras', $fk_Tecnico_Destino_Tras, PDO::PARAM_STR);
            $statement->bindParam(':Num_Serie_Salida_Det', $Num_Serie_Salida_Det, PDO::PARAM_STR);

            if (!$statement) {
                return "Error Al Modificar";
            }
            else{
                $statement->execute();
                return "Modificacion Exitosa";
            }
        }
        
        public function InsertSalidaTecnicoT($FK_Contratista_T,$FK_Tecnico_T,$Cantidad_T,$Fecha_T,$Folio_Salida_T)
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
            else{
                $resultado = $statement->execute();
                return "Registro Exitoso";
            }
        }
        
        public function InsertTraspasoTecnicoT($Folio_Traspaso_T,$fk_Tecnico_Origen_Traspaso_T,$fk_Tecnico_Destino_Traspaso_T,$fk_Contratista_Traspaso,$Cantidad_Traspaso,$Fecha_Traspaso,$Comentarios_Traspaso)
        {
            $conexion = $this->get_conexion();
            $sql = "INSERT INTO traspasos_tecnicos_t(Folio_Traspaso_T, fk_Tecnico_Origen_Traspaso_T, fk_Tecnico_Destino_Traspaso_T, 
            fk_Contratista_Traspaso, Cantidad_Traspaso, Fecha_Traspaso, Comentarios_Traspaso) 
            VALUES (:Folio_Traspaso_T, :fk_Tecnico_Origen_Traspaso_T, :fk_Tecnico_Destino_Traspaso_T, :fk_Contratista_Traspaso, 
            :Cantidad_Traspaso, :Fecha_Traspaso, :Comentarios_Traspaso)";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Folio_Traspaso_T', $Folio_Traspaso_T, PDO::PARAM_STR);
            $statement->bindParam(':fk_Tecnico_Origen_Traspaso_T', $fk_Tecnico_Origen_Traspaso_T, PDO::PARAM_STR);
            $statement->bindParam(':fk_Tecnico_Destino_Traspaso_T', $fk_Tecnico_Destino_Traspaso_T, PDO::PARAM_STR);
            $statement->bindParam(':fk_Contratista_Traspaso', $fk_Contratista_Traspaso, PDO::PARAM_STR);
            $statement->bindParam(':Cantidad_Traspaso', $Cantidad_Traspaso, PDO::PARAM_STR);
            $statement->bindParam(':Fecha_Traspaso', $Fecha_Traspaso, PDO::PARAM_STR);
            $statement->bindParam(':Comentarios_Traspaso', $Comentarios_Traspaso, PDO::PARAM_STR);

            if (!$statement) {
                return "Error Registro";
            }
            else{
                $resultado = $statement->execute();
                return "Registro Exitoso";
            }
        }
        
        public function InsertTraspasoTecnico($Folio_Traspaso,$fk_Tecnico_Origen_Tras,$fk_Tecnico_Destino_Tras,$fk_Contratista_Tras,$Numero_Serie_Tras,$Fecha_Tras)
        {
            $conexion = $this->get_conexion();
            $sql = "INSERT INTO traspasos_tecnicos(Folio_Traspaso, fk_Tecnico_Origen_Tras, fk_Tecnico_Destino_Tras, fk_Contratista_Tras, 
            Numero_Serie_Tras, Fecha_Tras)
            VALUES (:Folio_Traspaso, :fk_Tecnico_Origen_Tras, :fk_Tecnico_Destino_Tras, :fk_Contratista_Tras, 
            :Numero_Serie_Tras, :Fecha_Tras)";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Folio_Traspaso', $Folio_Traspaso, PDO::PARAM_STR);
            $statement->bindParam(':fk_Tecnico_Origen_Tras', $fk_Tecnico_Origen_Tras, PDO::PARAM_STR);
            $statement->bindParam(':fk_Tecnico_Destino_Tras', $fk_Tecnico_Destino_Tras, PDO::PARAM_STR);
            $statement->bindParam(':fk_Contratista_Tras', $fk_Contratista_Tras, PDO::PARAM_STR);
            $statement->bindParam(':Numero_Serie_Tras', $Numero_Serie_Tras, PDO::PARAM_STR);
            $statement->bindParam(':Fecha_Tras', $Fecha_Tras, PDO::PARAM_STR);

            if (!$statement) {
                return "Error Registro";
            }
            else{
                $resultado = $statement->execute();
                return "Registro Exitoso";
            }
        }


        // ========  METODOS DE ACEPTACION DE MATERIAL =========


        // SALIDAS PENDIENTES DE ESTE CONTRATISTA
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

        // SALIDAS RECHAZADAS DE ESTE CONTRATISTA
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

        // DETALLE DE MATERIALES DE UNA SALIDA TEMPORAL (PARA EL MODAL)
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

        // DETALLE DE NUMEROS DE SERIE DE UNA SALIDA TEMPORAL (PARA EL MODAL)
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

    class Tac extends Conexion
    {
        /**
         * Ejecuta un stored procedure en la conexión analytics y devuelve los resultados como array.
         * @param string $procedure Nombre del procedimiento (sin CALL)
         * @param array $params Asociativo de parámetros ['NombreParam' => valor]
         * @return array Resultados (vacío en caso de error o sin filas)
         */
        protected function callProcedure($procedure, $params = array())
        {
            $rows = array();
            $conexion = $this->get_conexion_analytics();

            try {
                // Construye placeholders a partir de las claves del array
                $placeholders = array();
                foreach ($params as $key => $val) {
                    $placeholders[] = ':' . $key;
                }

                $sql = "CALL {$procedure}(" . implode(',', $placeholders) . ")";
                $stmt = $conexion->prepare($sql);

                // Bind dinámico de parámetros
                foreach ($params as $key => $val) {
                    $param = ':' . $key;
                    if (is_int($val)) {
                        $stmt->bindValue($param, $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue($param, $val, PDO::PARAM_STR);
                    }
                }

                $stmt->execute();

                // fetchAll es apropiado y más simple
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Cerrar cursor es importante cuando se usan CALLs en algunos drivers
                if (method_exists($stmt, 'closeCursor')) {
                    $stmt->closeCursor();
                }

            } catch (PDOException $e) {
                // Registrar el error para depuración; devolver array vacío para no romper llamadores
                error_log("[Contratista::callProcedure] Error en procedure {$procedure}: " . $e->getMessage());
                return array();
            }

            return is_array($rows) ? $rows : array();
        }

        /**
         * Devuelve el total de órdenes TAC para un contratista/técnico en el rango de fechas.
         * Si el stored procedure devuelve una fila con campo 'TOTAL' se usará ese valor,
         * en caso contrario se devuelve el conteo de filas retornadas.
         * @return int
         */
        public function CountOntInstaladosTAC($FK_Contratista_Tecnico, $Fecha1, $Fecha2)
        {
            // Llamamos al SP que devuelve sólo el total
            $rows = $this->callProcedure('sp_count_ordenes_contratista_ont', [
                'Fecha1' => $Fecha1,
                'Fecha2' => $Fecha2,
                'FK_Contratista_Tecnico' => $FK_Contratista_Tecnico
            ]);

            if (empty($rows)) {
                return 0;
            }

            // Devolver TOTAL si existe, o 0
            return isset($rows[0]['TOTAL']) ? (int)$rows[0]['TOTAL'] : 0;
        }
    }
?>
