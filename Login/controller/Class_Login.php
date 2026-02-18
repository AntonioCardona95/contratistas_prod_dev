<?php
 ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    date_default_timezone_set('America/Monterrey');

	/**
	 * DESARROLLADO ING.ANTONIO CARDONA
	*/

    //require_once($_SERVER['DOCUMENT_ROOT']."/cnx/cnx.php");
    //require_once('/var/www/html/contratistas/cnx/cnx.php');
    require_once(__DIR__ . '/../../cnx/cnx.php');



	class Login extends Conexion
	{
        public function get_user($User_Contratista)
        {
            $rows = null;
            $conexion = $this->get_conexion();
            $sql = "SELECT * FROM contratistas WHERE User_Contratista = :User_Contratista";
            $statement = $conexion->prepare($sql);
            $statement->bindParam(":User_Contratista",$User_Contratista, PDO::PARAM_STR);
            $statement->execute();
            while ($result = $statement->fetch()) {
                $rows[] = $result;
            }
            return $rows;
        }   
	}
?>