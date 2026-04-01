<?php

    class Conexion
    {
        public function get_conexion()
        {
            try {
                $user = "erpintr1";     
                $pass = "Z@6!90qWIoa5fY";   
                $host = "67.217.246.65";
                $db = "db_apps";
                $conexion = new PDO("mysql:host=$host;dbname=$db",$user,$pass);               
                $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                if(!$conexion) throw new Exception('Error de conexion MSSQL: ');

                return $conexion;

            } catch(PDOException $e) {
                return $e->getMessage();
            }
        }
    }

?>