<?php
class Conexion
{
    public function get_conexion()
    {
        try {
            $user = "erpintr1";     
            $pass = "$7nM7g9d5";   
            $host = "192.168.1.11";
            $db = "erpintr1_erp";
            $conexion = new PDO("mysql:host=$host;dbname=$db",$user,$pass);               
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            if(!$conexion) throw new Exception('Error de conexion MSSQL: ');

            return $conexion;

        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    public function get_conexion_analytics()
    {
        try {
            $host = '192.168.1.11';
            $user = 'erpintr1';
            $password = '$7nM7g9d5';
            $database = 'analisis_bd';
            
            $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (Exception $e) {
            throw new Exception("Error conectando a TAC: " . $e->getMessage());
        }
    }

    public function get_conexion_analisis()
    {
        $user = "Analytics_ED";
        $pass = "Enlace053715/";
        //$host = "192.168.1.11";
        $host = "192.168.1.198";
        $db   = "analisis";
        $dsn  = "mysql:host=$host;dbname=$db;charset=utf8";

        try {
            $conexion = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos remota: " . $e->getMessage());
        }
    }

    public function get_conexiondos()
    {
        $user = "erpintr1";
        $pass = "$7nM7g9d5";
        $host = "192.168.1.11";
        $db   = "analisis_bd";
        $dsn  = "mysql:host=$host;dbname=$db;charset=utf8";

        try {
            $conexion = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos remota: " . $e->getMessage());
        }
    }

    public function get_conexioncoordiapp()
        {
            try {
                $user = "erpintr1";     
                $pass = "$7nM7g9d5";   
                $host = "74.208.237.139";

                //$host = "localhost";
                $db = "erpintr1_erp";
                $conexion = new PDO("mysql:host=$host;dbname=$db",$user,$pass);               
                $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                if(!$conexion) throw new Exception('Error de conexion MSSQL: ');

                return $conexion;

            } catch(PDOException $e) {
                return $e->getMessage();
            }
        }
}
