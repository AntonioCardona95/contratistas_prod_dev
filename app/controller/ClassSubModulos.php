<?php

    require_once(__DIR__ . '/../config/cnx.php');

    class SubModulos extends Conexion
    {
        /* FUNCION PARA OBTENER SUB-MÓDULOS POR TIPO */
        public function ObtenerSubModulosPorTipo($tipo_submodulo)
        {
            $rows = [];
            try {
                $conexion = $this->get_conexion();
                $sql = "SELECT nombre_submodulo, ruta_submodulo, icono_submodulo 
                        FROM sub_modulos_contratistas 
                        WHERE tipo_submodulo = :tipo_submodulo AND estado = 1 
                        ORDER BY orden ASC";
                
                $statement = $conexion->prepare($sql);
                $statement->bindParam(':tipo_submodulo', $tipo_submodulo, PDO::PARAM_STR);
                $statement->execute();
                
                while ($result = $statement->fetch(PDO::FETCH_ASSOC)) {
                    $rows[] = $result;
                }
            } catch (Exception $e) {
                error_log("Error al obtener sub-módulos: " . $e->getMessage());
            }
            
            return $rows;
        }

        /* FUNCION PARA OBTENER SUB-MÓDULOS DE OPERACIONES */
        public function ObtenerOperaciones()
        {
            return $this->ObtenerSubModulosPorTipo('OPERACIONES');
        }

        /* FUNCION PARA OBTENER SUB-MÓDULOS DE ALMACEN */
        public function ObtenerAlmacen()
        {
            return $this->ObtenerSubModulosPorTipo('ALMACEN');
        }
    }
?>
