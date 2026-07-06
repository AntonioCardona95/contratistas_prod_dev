<?php

    require_once(__DIR__ . '/../config/cnx.php');

    class Ops extends Conexion
    {
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