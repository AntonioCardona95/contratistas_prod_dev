<?php

/**
 * Autor: Ing. Antonio Cardona
 * Estructura adaptada para la consulta de producción de contratistas
 */

require_once($_SERVER['DOCUMENT_ROOT'] . "/cnx/cnx.php");

class Produccion extends Conexion
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

/*public function getTotalTac($fecha_inicio, $fecha_fin)
{
    $sql = "SELECT COUNT(1) AS total
            FROM seg_ns_prod_eval
            WHERE FECHA_LIQD BETWEEN :inicio AND :fin";

    $result = $this->runQueryAnalisis($sql, [
        'inicio' => $fecha_inicio,
        'fin' => $fecha_fin
    ]);

    return !empty($result) ? (int)$result[0]['total'] : 0;
} */

    public function getTotalTac()
{
    $sql = "SELECT COUNT(1) AS total
            FROM seg_ns_prod_eval";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total'] : 0;
}

    /**
     * Obtiene la producción del contratista basándose en los expedientes y fechas.
     * 
     * @param string $Fecha1 Fecha de inicio
     * @param string $Fecha2 Fecha de fin
     * @param int $idContratistas ID del contratista
     * @return array Filas de producción del contratista
     */
    public function obtenerProduccion($Fecha1, $Fecha2, $idContratistas)
    {
        $sql = "SELECT
                    q.Folio_Pisa,
                    q.TELEFONO,
                    q.Tipo_de_Tarea,
                    q.FECHA_LIQ,
                    q.NOM_CT
                FROM
                    qm_tac q
                JOIN
                    tecnicos te ON q.Expediente = te.NExpediente
                JOIN
                    contratistas t ON te.FK_Contratista_Tecnico = t.idContratistas
                WHERE
                    te.FK_Contratista_Tecnico = :idContratistas
                    AND q.FECHA_LIQ BETWEEN :Fecha1 AND :Fecha2
                    AND q.Calificador_Edo = 'COMPLETADA'
                ORDER BY
                    q.FECHA_LIQ";

        // Ejecuta la consulta con los parámetros necesarios
        return $this->runQuery($sql, [
            'Fecha1' => $Fecha1,
            'Fecha2' => $Fecha2,
            'idContratistas' => $idContratistas
        ]);
    }
}
?>