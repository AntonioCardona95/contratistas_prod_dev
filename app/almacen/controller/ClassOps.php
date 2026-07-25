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

        public function EntradasDet($Folio_Salida_Material_Det_S)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql="SELECT Num_Serie_S, Producto, Descripcion, Tecnologia, Modelo FROM material_det_salida
            INNER JOIN producto ON FK_Material_S = idProducto 
            WHERE Folio_Salida_Material_Det_S = :Folio_Salida_Material_Det_S
            ORDER BY Folio_Salida_Material_Det_S DESC;";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(':Folio_Salida_Material_Det_S', $Folio_Salida_Material_Det_S, PDO::PARAM_STR);
            $statement->execute();

            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }

            return $rows;
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

        // ACEPTAR UNA SALIDA COMPLETA
        public function AceptarSalida($Folio_Salida, $idContratistas)
        {
            $conexion = $this->get_conexion();
            try {
                $conexion->beginTransaction();

                // 1. VALIDAR QUE LA SALIDA EXISTE, ESTÁ PENDIENTE Y LE PERTENECE A ESTE CONTRATISTA
                $stmtCheck = $conexion->prepare("SELECT FK_User_ST FROM temp_transferencias_t 
                    WHERE Folio_Salida_ST = :folio AND FK_Contra_ST = :contratista AND Status_Temp = 'pendiente'
                    FOR UPDATE");
                $stmtCheck->bindValue(':folio', $Folio_Salida, PDO::PARAM_INT);
                $stmtCheck->bindValue(':contratista', $idContratistas, PDO::PARAM_INT);
                $stmtCheck->execute();
                $fila = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if (!$fila) {
                    $conexion->rollBack();
                    return ['success' => false, 'message' => 'Esta salida no existe, ya fue procesada o no te pertenece.'];
                }

                $idUsuarioNass = $fila['FK_User_ST'];

                // 2. MOVER LA CABECERA
                $conexion->exec("INSERT INTO transferencias_t 
                    (FK_Almacen_ST, FK_Contra_ST, Fecha_Salida_ST, Cantidad_ST, FK_User_ST, Folio_Salida_ST, 
                    Solicitante_Salida_T, Solicitante_SalidaA_T, DescripcionSalida_T, Tipo_MovS_T, Folio_Telmex_ST, 
                    FK_UNS_T, Comprobante_Salida_T, Hash_Archivo_Salidas, Estatus_ComprobanteS_T, 
                    Ubicacion_Material_ST, recibe_contratista_t, Cantidad_Metros_T)
                    SELECT FK_Almacen_ST, FK_Contra_ST, Fecha_Salida_ST, Cantidad_ST, FK_User_ST, Folio_Salida_ST, 
                        Solicitante_Salida_T, Solicitante_SalidaA_T, DescripcionSalida_T, Tipo_MovS_T, Folio_Telmex_ST, 
                        FK_UNS_T, Comprobante_Salida_T, Hash_Archivo_Salidas, Estatus_ComprobanteS_T, 
                        Ubicacion_Material_ST, recibe_contratista_t, Cantidad_Metros_T
                    FROM temp_transferencias_t
                    WHERE Folio_Salida_ST = " . (int)$Folio_Salida);

                // 3. MOVER EL DETALLE DE PRODUCTOS
                $conexion->exec("INSERT INTO transferencias 
                    (Folio_Salida, FK_Almacen_S, FK_Contratista_S, FK_Producto_S, Fecha_S, Cantidad_S, 
                    FK_User_S, FK_Tecnico_S, Folio_Telmex_S, FK_UNS, recibe_contratista, Tecnologia, 
                    Piezas_Sueltas, Total_Piezas, Total_Metros)
                    SELECT Folio_Salida, FK_Almacen_S, FK_Contratista_S, FK_Producto_S, Fecha_S, Cantidad_S, 
                        FK_User_S, FK_Tecnico_S, Folio_Telmex_S, FK_UNS, recibe_contratista, Tecnologia, 
                        Piezas_Sueltas, Total_Piezas, Total_Metros
                    FROM temp_transferencias
                    WHERE Folio_Salida = " . (int)$Folio_Salida);

                // 4. MOVER LOS NUMEROS DE SERIE
                $conexion->exec("INSERT INTO material_det_salida 
                    (Num_Serie_S, Alfanumerico_S, FK_Material_S, Localidad_Material_S, Folio_Salida_Material_Det_S, 
                    Fecha_Salida_S, FK_Contratista_Salida, Ubicacion_Material, Estatus_Ubicacion, 
                    Folio_Ultima_Transferencia, Folio_Ultima_Devolucion)
                    SELECT Num_Serie_S, Alfanumerico_S, FK_Material_S, Localidad_Material_S, Folio_Salida_Material_Det_S, 
                        Fecha_Salida_S, FK_Contratista_Salida, Ubicacion_Material, Estatus_Ubicacion, 
                        Folio_Ultima_Transferencia, Folio_Ultima_Devolucion
                    FROM temp_material_det_salida
                    WHERE Folio_Salida_Material_Det_S = " . (int)$Folio_Salida);

                // 5. ACTUALIZAR EL ESTATUS DE LOS NUMEROS DE SERIE: PENDING -> ASSIGMENT
                $conexion->exec("UPDATE materiales_det
                    SET Estatus_NS = 'ASSIGMENT'
                    WHERE Num_Serie IN (
                        SELECT Num_Serie_S FROM temp_material_det_salida 
                        WHERE Folio_Salida_Material_Det_S = " . (int)$Folio_Salida . "
                    )");

                // 6. MARCAR LA TEMPORAL COMO ACEPTADA (NO SE BORRA, QUEDA COMO HISTORIAL)
                $stmtUpdate = $conexion->prepare("UPDATE temp_transferencias_t
                    SET Status_Temp = 'aceptado', Fecha_Respuesta = NOW()
                    WHERE Folio_Salida_ST = :folio");
                $stmtUpdate->bindValue(':folio', $Folio_Salida, PDO::PARAM_INT);
                $stmtUpdate->execute();

                $conexion->commit();

                return ['success' => true, 'idUsuarioNass' => $idUsuarioNass];

            } catch (PDOException $e) {
                if ($conexion->inTransaction()) {
                    $conexion->rollBack();
                }
                error_log("[AceptarSalida] Error: " . $e->getMessage());
                return ['success' => false, 'message' => 'Error al procesar la aceptación.'];
            }
        }

        // RECHAZAR UNA SALIDA COMPLETA
        public function RechazarSalida($Folio_Salida, $idContratistas)
        {
            $conexion = $this->get_conexion();
            try {
                $conexion->beginTransaction();

                $stmtCheck = $conexion->prepare("SELECT FK_User_ST FROM temp_transferencias_t 
                    WHERE Folio_Salida_ST = :folio AND FK_Contra_ST = :contratista AND Status_Temp = 'pendiente'
                    FOR UPDATE");
                $stmtCheck->bindValue(':folio', $Folio_Salida, PDO::PARAM_INT);
                $stmtCheck->bindValue(':contratista', $idContratistas, PDO::PARAM_INT);
                $stmtCheck->execute();
                $fila = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if (!$fila) {
                    $conexion->rollBack();
                    return ['success' => false, 'message' => 'Esta salida no existe, ya fue procesada o no te pertenece.'];
                }

                $idUsuarioNass = $fila['FK_User_ST'];

                // LIBERAR LOS NUMEROS DE SERIE: PENDING -> NULL (VUELVEN A ESTAR DISPONIBLES)
                $conexion->exec("UPDATE materiales_det
                    SET Estatus_NS = NULL
                    WHERE Num_Serie IN (
                        SELECT Num_Serie_S FROM temp_material_det_salida 
                        WHERE Folio_Salida_Material_Det_S = " . (int)$Folio_Salida . "
                    )");

                // MARCAR LA TEMPORAL COMO RECHAZADA (NO SE TOCAN LAS TABLAS DEFINITIVAS)
                $stmtUpdate = $conexion->prepare("UPDATE temp_transferencias_t
                    SET Status_Temp = 'rechazado', Fecha_Respuesta = NOW()
                    WHERE Folio_Salida_ST = :folio");
                $stmtUpdate->bindValue(':folio', $Folio_Salida, PDO::PARAM_INT);
                $stmtUpdate->execute();

                $conexion->commit();

                return ['success' => true, 'idUsuarioNass' => $idUsuarioNass];

            } catch (PDOException $e) {
                if ($conexion->inTransaction()) {
                    $conexion->rollBack();
                }
                error_log("[RechazarSalida] Error: " . $e->getMessage());
                return ['success' => false, 'message' => 'Error al procesar el rechazo.'];
            }
        }

        // SALIDAS PENDIENTES DE ESTE CONTRATISTA
        public function ObtenerPendientes($idContratistas, $Fecha1, $Fecha2)
        {
            $sql = "SELECT 
                        t.idTransferencias_T, t.Folio_Salida_ST, t.Folio_Telmex_ST, t.Fecha_Salida_ST,
                        t.Cantidad_ST, t.Cantidad_Metros_T, t.Solicitante_Salida_T, t.DescripcionSalida_T,
                        a.Almacen, uNeg.UN AS Unidad_Negocio
                    FROM temp_transferencias_t t
                    INNER JOIN almacenes a ON t.FK_Almacen_ST = a.idAlmacenes
                    LEFT JOIN unegocio uNeg ON t.FK_UNS_T = uNeg.idUN
                    WHERE t.FK_Contra_ST = :idContratistas 
                    AND t.Status_Temp = 'pendiente'
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
                    ORDER BY t.Fecha_Respuesta DESC";

            return $this->runQuery($sql, [
                'idContratistas' => $idContratistas,
                'Fecha1' => $Fecha1,
                'Fecha2' => $Fecha2
            ]);
        }

        // DETALLE DE MATERIALES DE UNA SALIDA TEMPORAL (PARA EL MODAL)
        public function ObtenerDetalleMateriales($Folio_Salida)
        {
            $sql = "SELECT p.Producto, p.Modelo, t.Tecnologia, p.UM, p.Presentacion,
                        t.Cantidad_S, t.Piezas_Sueltas, t.Total_Piezas, t.Total_Metros
                    FROM temp_transferencias t
                    INNER JOIN producto p ON t.FK_Producto_S = p.idProducto
                    WHERE t.Folio_Salida = :folio";

            return $this->runQuery($sql, ['folio' => $Folio_Salida]);
        }

        // DETALLE DE NUMEROS DE SERIE DE UNA SALIDA TEMPORAL (PARA EL MODAL)
        public function ObtenerDetalleSeries($Folio_Salida)
        {
            $sql = "SELECT m.Num_Serie_S, p.Producto, p.Modelo
                    FROM temp_material_det_salida m
                    INNER JOIN producto p ON m.FK_Material_S = p.idProducto
                    WHERE m.Folio_Salida_Material_Det_S = :folio";

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