<?php

    require_once(__DIR__ . '/../../cnx/cnx.php');

    class Traspasos extends Conexion
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

        public function Traspasos($idContratistas, $Fecha1, $Fecha2)
        {
            $sql = "SELECT Comentarios_Traspaso, Cantidad_Traspaso, Folio_Traspaso_T, Fecha_Traspaso,
                    CONCAT(origen.Nombre_T, ' ', origen.Apellidos_T) AS TecnicoOrigen,
                    CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) AS Contratista,
                    CONCAT(destino.Nombre_T, ' ', destino.Apellidos_T) AS TecnicoDestino
                    FROM traspasos_tecnicos_t
                    INNER JOIN tecnicos AS origen ON fk_Tecnico_Origen_Traspaso_T = origen.idTecnico
                    INNER JOIN tecnicos AS destino ON fk_Tecnico_Destino_Traspaso_T = destino.idTecnico
                    INNER JOIN contratistas ON fk_Contratista_Traspaso = idContratistas
                    WHERE fk_Contratista_Traspaso = COALESCE(NULLIF(:fk_Contratista_Traspaso, 0), fk_Contratista_Traspaso)
                    AND Fecha_Traspaso BETWEEN :Fecha1 AND :Fecha2
                    ORDER BY Folio_Traspaso_T DESC";

            return $this->runQuery($sql, [
                'fk_Contratista_Traspaso' => $idContratistas,
                'Fecha1' => $Fecha1,
                'Fecha2' => $Fecha2
            ]);
        }

        public function TraspasoDet($Folio_Traspaso)
        {
            $sql = "SELECT Numero_Serie_Tras, Folio_Traspaso, Fecha_Tras,
                    CONCAT(origen.Nombre_T, ' ', origen.Apellidos_T) AS TecnicoOrigen,
                    CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) AS Contratista,
                    CONCAT(destino.Nombre_T, ' ', destino.Apellidos_T) AS TecnicoDestino
                    FROM traspasos_tecnicos
                    INNER JOIN tecnicos AS origen ON fk_Tecnico_Origen_Tras = origen.idTecnico
                    INNER JOIN tecnicos AS destino ON fk_Tecnico_Destino_Tras = destino.idTecnico
                    INNER JOIN contratistas ON fk_Contratista_Tras = idContratistas
                    WHERE Folio_Traspaso = :Folio_Traspaso";

            return $this->runQuery($sql, [
                'Folio_Traspaso' => $Folio_Traspaso
            ]);
        }

        public function UltimaTraspaso()
        {
            return $this->runQuery(
                'SELECT * FROM traspasos_tecnicos_t ORDER BY Fecha_Traspaso DESC LIMIT 1'
            );
        }

        public function Tecnicos($idContratista)
        {
            return $this->runQuery(
                "SELECT * FROM tecnicos
                 WHERE FK_Contratista_Tecnico = :idContratista
                 AND Estatus_Tecnico = 'ACTIVO'
                 ORDER BY Nombre_T ASC",
                ['idContratista' => $idContratista]
            );
        }

        public function LoadSeriesTraspasos($idTecnico, $idContratista)
        {
            return $this->runQuery(
                "SELECT Num_Serie_Salida_Det
                 FROM salidas_contratistas
                 WHERE FK_Contratista_Salida_Det = :idContratista
                 AND FK_Tecnico_Salida_Det = :idTecnico
                 AND Ont_Ubicacion = 'TECNICO'
                 ORDER BY Num_Serie_Salida_Det ASC",
                [
                    'idTecnico' => $idTecnico,
                    'idContratista' => $idContratista
                ]
            );
        }

        public function UpdateTraspasoTecnico($idTecnicoOrigen, $idTecnicoDestino, $numeroSerie)
        {
            return $this->runExecute(
                "UPDATE salidas_contratistas
                 SET FK_Tecnico_Salida_Det = :idTecnicoDestino
                 WHERE FK_Tecnico_Salida_Det = :idTecnicoOrigen
                 AND Num_Serie_Salida_Det = :numeroSerie",
                [
                    'idTecnicoOrigen' => $idTecnicoOrigen,
                    'idTecnicoDestino' => $idTecnicoDestino,
                    'numeroSerie' => $numeroSerie
                ]
            );
        }

        public function InsertTraspasoTecnicoT($folio, $idTecnicoOrigen, $idTecnicoDestino, $idContratista, $cantidad, $fecha, $comentarios)
        {
            return $this->runExecute(
                "INSERT INTO traspasos_tecnicos_t
                    (Folio_Traspaso_T, fk_Tecnico_Origen_Traspaso_T, fk_Tecnico_Destino_Traspaso_T,
                     fk_Contratista_Traspaso, Cantidad_Traspaso, Fecha_Traspaso, Comentarios_Traspaso)
                 VALUES
                    (:folio, :idTecnicoOrigen, :idTecnicoDestino, :idContratista,
                     :cantidad, :fecha, :comentarios)",
                [
                    'folio' => $folio,
                    'idTecnicoOrigen' => $idTecnicoOrigen,
                    'idTecnicoDestino' => $idTecnicoDestino,
                    'idContratista' => $idContratista,
                    'cantidad' => $cantidad,
                    'fecha' => $fecha,
                    'comentarios' => $comentarios
                ]
            );
        }

        public function InsertTraspasoTecnico($folio, $idTecnicoOrigen, $idTecnicoDestino, $idContratista, $numeroSerie, $fecha)
        {
            return $this->runExecute(
                "INSERT INTO traspasos_tecnicos
                    (Folio_Traspaso, fk_Tecnico_Origen_Tras, fk_Tecnico_Destino_Tras,
                     fk_Contratista_Tras, Numero_Serie_Tras, Fecha_Tras)
                 VALUES
                    (:folio, :idTecnicoOrigen, :idTecnicoDestino, :idContratista,
                     :numeroSerie, :fecha)",
                [
                    'folio' => $folio,
                    'idTecnicoOrigen' => $idTecnicoOrigen,
                    'idTecnicoDestino' => $idTecnicoDestino,
                    'idContratista' => $idContratista,
                    'numeroSerie' => $numeroSerie,
                    'fecha' => $fecha
                ]
            );
        }

    }

?>