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

 /*public function getTotalTac()
{
    $sql = "SELECT COUNT(1) AS total
            FROM seg_ns_prod_eval";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total'] : 0;
} */

    public function getTotalTac($fechaInicio = null, $fechaFin = null)
{
    $where = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $where = "WHERE FECHA_LIQD BETWEEN '$fechaInicio' AND '$fechaFin'";
    }

    $sql = "SELECT COUNT(1) AS total
            FROM seg_ns_prod_eval
            $where";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total'] : 0;
}

/*public function getTotalAlertas($fechaInicio = null, $fechaFin = null)
{
    $whereFechas = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $whereFechas = "WHERE FECHA_LIQD BETWEEN '$fechaInicio' AND '$fechaFin'";
    }

    $sql = "SELECT COUNT(*) AS total_alertas
            FROM (
                SELECT t.Num__Serie
                FROM qm_tac t
                INNER JOIN (
                    SELECT DISTINCT TELEFONO
                    FROM seg_ns_prod_eval
                    $whereFechas
                ) s ON s.TELEFONO = t.TELEFONO
                WHERE t.Calificador_Edo = 'COMPLETADA'
                GROUP BY t.Num__Serie
                HAVING COUNT(DISTINCT t.TELEFONO) > 1
            ) AS duplicados";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total_alertas'] : 0;
} */


public function obtenerRegistrosParaAlertas($fechaInicio = null, $fechaFin = null)
{
    $where = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $where = "WHERE FECHA_LIQD BETWEEN '$fechaInicio' AND '$fechaFin'";
    }

    $sql = "SELECT 
                Folio_Pisa AS folio,
                TELEFONO AS telefono,
                Tipo_de_Tarea AS tipoTarea,
                NOM_CT AS cope,
                FECHA_LIQ AS fechaLiquidacion,
                idContratista,
                Semana,
                Anio,
                0 AS metraje
            FROM seg_ns_prod_eval
            $where";

    return $this->runQueryAnalisis($sql);
}

public function limpiarAlertas($fechaInicio = null, $fechaFin = null)
{
    $where = "";

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $where = "WHERE Fecha_Liquidacion BETWEEN '$fechaInicio' AND '$fechaFin'";
    }

    $sql = "DELETE FROM Produccion_alertas $where";
    $this->runQuery($sql);
}


/*public function getTotalAlertas()
{
    echo "ENTRANDO A ALERTAS<br>"; // 👈 DEBUG

    $sql = "SELECT COUNT(*) AS total_alertas
FROM (
    SELECT t.Num__Serie
    FROM qm_tac t
    WHERE t.Calificador_Edo = 'COMPLETADA'
      AND t.TELEFONO IN (
          SELECT DISTINCT TELEFONO 
          FROM seg_ns_prod_eval
      )
    GROUP BY t.Num__Serie
    HAVING COUNT(DISTINCT t.TELEFONO) > 1
) AS duplicados;";

    $result = $this->runQueryAnalisis($sql);

    var_dump($result); // 👈 DEBUG

    return !empty($result) ? (int)$result[0]['total_alertas'] : 0;
} */

/*public function getTotalAlertas()
{
    $sql = "SELECT COUNT(*) AS total_alertas
            FROM (
                SELECT t.Num__Serie
                FROM qm_tac t
                INNER JOIN seg_ns_prod_eval s 
                    ON s.TELEFONO = t.TELEFONO
                WHERE t.Calificador_Edo = 'COMPLETADA'
                GROUP BY t.Num__Serie
                HAVING COUNT(DISTINCT t.TELEFONO) > 1
            ) AS duplicados";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total_alertas'] : 0;
} */

/*public function getSeriesDuplicadas()
{
    $sql = "
        SELECT COUNT(DISTINCT s.TELEFONO) AS total_duplicados
        FROM seg_ns_prod_eval s
        INNER JOIN qm_tac q ON s.TELEFONO = q.TELEFONO
        WHERE q.Calificador_Edo = 'COMPLETADA'
        AND q.Num__Serie IN (
            SELECT Num__Serie
            FROM qm_tac
            WHERE Calificador_Edo = 'COMPLETADA'
            GROUP BY Num__Serie
            HAVING COUNT(DISTINCT TELEFONO) > 1
        )
    ";

    $result = $this->runQueryAnalisis($sql);

    return !empty($result) ? (int)$result[0]['total_duplicados'] : 0;
} */



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