<?php

/**
 * Autor: Ing. Antonio Cardona
 * Estructura adaptada para la consulta de producción de contratistas
 */

require_once($_SERVER['DOCUMENT_ROOT'] . "/cnx/cnx.php");

class Resultados extends Conexion
{
    /**
     * Ejecuta una consulta SQL con parámetros y retorna los resultados.
     * 
     * @param string $sql Consulta SQL con placeholders (ej. :Fecha1)
     * @param array $params Asociativo con valores para los placeholders
     * @return array Filas devueltas (array de arrays asociativos). En error devuelve []
     */
    protected function runQuery($sql, $params = array())
    {
        $rows = array();
        $conexion = $this->get_conexion(); // Conexión a la base de datos

        try {
            $stmt = $conexion->prepare($sql); // Prepara la consulta

            foreach ($params as $key => $val) {
                $param = ':' . ltrim($key, ':');
                // Vincula los parámetros
                if (is_int($val)) {
                    $stmt->bindValue($param, $val, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($param, $val, PDO::PARAM_STR);
                }
            }

            $stmt->execute(); // Ejecuta la consulta
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtiene los resultados

            if (method_exists($stmt, 'closeCursor')) {
                $stmt->closeCursor();
            }
        } catch (PDOException $e) {
            // Loguea el error en caso de excepción
            error_log("[Produccion::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
            return array(); // Retorna un array vacío en caso de error
        }

        return is_array($rows) ? $rows : array(); // Retorna las filas si es un array, si no retorna array vacío
    }

    protected function runQueryAnalisis($sql, $params = array())
{
    $rows = array();
    $conexion = $this->get_conexion_analisis(); // 👈 IMPORTANTE

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
        error_log("[Produccion::runQueryAnalisis] Error: " . $e->getMessage());
        return array();
    }

    return is_array($rows) ? $rows : array();
}

public function getConteoAlertasPorTipo()
{
    $sql = "SELECT Tipo_Alerta, COUNT(*) AS total
            FROM Produccion_alertas_detalle
            GROUP BY Tipo_Alerta";

    $result = $this->runQuery($sql);

    // Inicializar todas las categorías en 0
    $categorias = [
        'NO EN COORDIAPP'=>0,'SIN NAVEGACION'=>0,'ONT_DUPLICADA_EN_TAC'=>0,'ONT_DUPLICADA_EN_ESCI'=>0,
        'TECNOLOGIA_DIFERENTE'=>0,'TIPO_TAREA_DIFERENTE'=>0,'NUMERO_SERIE_DIFERENTE'=>0,
        'ONT_NO_ENCONTRADA_EN_ALMACEN'=>0,'ONT_ASIGNADA_A_OTRO_CONTRATISTA'=>0,
        'ONT SIN ASIGNAR'=>0,'ORDEN A REVISION POR 30 DIAS'=>0,'SERVICIO INACTIVO'=>0,'ONT_NO_ASIGNADA_AL_CONTRATISTA'=>0
    ];

    // Rellenar con los resultados reales (normalizando el texto)
    foreach ($result as $fila) {
        $clave = strtoupper(trim($fila['Tipo_Alerta'])); // quita espacios y pone mayúsculas
        if (isset($categorias[$clave])) {
            $categorias[$clave] = (int)$fila['total'];
        }
    }

    return $categorias; // Devuelve array asociativo con todos los conteos
}



}