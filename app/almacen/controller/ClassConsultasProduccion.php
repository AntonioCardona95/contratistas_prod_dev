<?php

/**
 * Autor: Tu Nombre / Equipo de Desarrollo
 * Clase encargada de la gestión de Nóminas y Contratistas
 */

require_once($_SERVER['DOCUMENT_ROOT'] . "/cnx/cnx.php");

class ClassConsultasProduccion extends Conexion
{
    /**
     * Ejecuta consultas SELECT usando la conexión principal PDO
     */
    protected function runQuery($sql, $params = array(), $conexionTipo = 'default')
    {
        $rows = array();

        switch ($conexionTipo) {
            case 'analisis':
            case 'analytics':
                $conexion = $this->get_conexion_analisis();
                break;
            case 'nass':
                $conexion = $this->get_conexion_nass();
                break;
            case 'dos':
                $conexion = $this->get_conexiondos();
                break;
            default:
                $conexion = $this->get_conexion();
                break;
        }

        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                if (is_int($key)) {
                    $stmt->bindValue($key + 1, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                } else {
                    $param = ':' . ltrim($key, ':');
                    $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (method_exists($stmt, 'closeCursor')) {
                $stmt->closeCursor();
            }
        } catch (PDOException $e) {
            error_log("[ClassConsultasProduccion::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
            return array();
        }

        return is_array($rows) ? $rows : array();
    }

    protected function runExecute($sql, $params = array(), $conexionTipo = 'default')
    {
        switch ($conexionTipo) {
            case 'analisis':
            case 'analytics':
                $conexion = $this->get_conexion_analisis();
                break;
            case 'nass':
                $conexion = $this->get_conexion_nass();
                break;
            case 'dos':
                $conexion = $this->get_conexiondos();
                break;
            default:
                $conexion = $this->get_conexion();
                break;
        }

        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                if (is_int($key)) {
                    $stmt->bindValue($key + 1, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                } else {
                    $param = ':' . ltrim($key, ':');
                    $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }
            $stmt->execute();
            return (int)$conexion->lastInsertId();
        } catch (PDOException $e) {
            error_log("[ClassConsultasProduccion::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
            return 0;
        }
    }

    public function CountProduccionTotal($fechaInicio, $fechaFin)
{
    $sql = "SELECT COUNT(1) AS TOTAL
            FROM seg_ns_prod_eval
            WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    $params = array(
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin
    );

    // Usamos la conexión 'analisis' porque ahí está la tabla
    return $this->runQuery($sql, $params, 'analisis');
}




}