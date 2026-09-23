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


    // Producción filtrada por fechas (inicia en 0 hasta que selecciones fechas)
public function CountProduccionPorFechas($fechaInicio, $fechaFin)
{
    $sql = "SELECT COUNT(1) AS TOTAL
            FROM seg_ns_prod_eval
            WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    $params = array(
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin
    );

    return $this->runQuery($sql, $params, 'analisis');
}

// Producción total acumulada (sin filtro de fechas)
public function CountProduccionTotal()
{
    $sql = "SELECT COUNT(1) AS TOTAL
            FROM seg_ns_prod_eval";

    return $this->runQuery($sql, array(), 'analisis');
}

public function GetProduccionPorFechas($fechaInicio, $fechaFin)
{
    echo "<pre>Entrando a GetProduccionPorFechas\n";
    echo "FechaInicio: $fechaInicio\n";
    echo "FechaFin: $fechaFin\n</pre>";
    $sql = "SELECT 
                Expediente, Folio_Pisa, TELEFONO, Tipo_de_Tarea, NOM_CT,
                FECHA_LIQ, FECHA_LIQD, Semana, Sem, Anio,
                idContratista, Contratista, Tecnico, NOM_DIVISION, NOM_AREA,
                Num__Serie, DISTRITO, TERMINAL
            FROM seg_ns_prod_eval
            WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    $params = [
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin
    ];

    return $this->runQuery($sql, $params, 'analisis');
}


public function GetNumSeriesEnAlmacen(array $numSeries)
{
    echo "<pre>Entrando a GetNumSeriesEnAlmacen\n";
    echo "Cantidad de NumSeries recibidos: " . count($numSeries) . "\n";
    var_dump(array_slice($numSeries, 0, 5)); // muestra 5
    echo "</pre>";
    if (empty($numSeries)) return [];

    // Generar placeholders dinámicos
    $placeholders = implode(',', array_fill(0, count($numSeries), '?'));
    $sql = "SELECT Num_Serie FROM materiales_det WHERE Num_Serie IN ($placeholders)";

    $rows = $this->runQuery($sql, $numSeries, 'default');
    return array_column($rows, 'Num_Serie'); // devuelve solo los encontrados
}


// TEMPORAL DE PRUBA PRODUCCION //
/*public function GetProduccionPorFechas($fechaInicio, $fechaFin, $limit = 5)
{
    $sql = "SELECT *
            FROM seg_ns_prod_eval
            WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin
            ORDER BY FECHA_LIQ ASC
            LIMIT :limit";

    $params = array(
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin,
        ':limit'       => (int)$limit
    );

    return $this->runQuery($sql, $params, 'analisis');
} */

public function InsertProduccionDiaria(array $registro, bool $tieneSenalamiento)
{
    echo "<pre>Entrando a InsertProduccionDiaria\n";
    echo "Folio: {$registro['Folio_Pisa']} - Teléfono: {$registro['TELEFONO']} - NumSerie: {$registro['Num__Serie']}\n";
    echo "Tiene señalamiento: " . ($tieneSenalamiento ? 'SI' : 'NO') . "\n</pre>";
    $sql = "INSERT INTO seg_produccion_diaria (
                Expediente, Folio_Pisa, TELEFONO, Tipo_de_Tarea, NOM_CT,
                FECHA_LIQ, FECHA_LIQD, Semana, Sem, Anio,
                idContratista, Contratista, Tecnico, NOM_DIVISION, NOM_AREA,
                Num__Serie, DISTRITO, TERMINAL, Fecha_Insercion, Tiene_Senalamiento
            ) VALUES (
                :Expediente, :Folio_Pisa, :TELEFONO, :Tipo_de_Tarea, :NOM_CT,
                :FECHA_LIQ, :FECHA_LIQD, :Semana, :Sem, :Anio,
                :idContratista, :Contratista, :Tecnico, :NOM_DIVISION, :NOM_AREA,
                :Num__Serie, :DISTRITO, :TERMINAL, NOW(), :Tiene_Senalamiento
            )";

    $params = [
        ':Expediente'         => $registro['Expediente'],
        ':Folio_Pisa'         => $registro['Folio_Pisa'],
        ':TELEFONO'           => $registro['TELEFONO'],
        ':Tipo_de_Tarea'      => $registro['Tipo_de_Tarea'],
        ':NOM_CT'             => $registro['NOM_CT'],
        ':FECHA_LIQ'          => $registro['FECHA_LIQ'],
        ':FECHA_LIQD'         => $registro['FECHA_LIQD'],
        ':Semana'             => $registro['Semana'],
        ':Sem'                => $registro['Sem'],
        ':Anio'               => $registro['Anio'],
        ':idContratista'      => $registro['idContratista'],
        ':Contratista'        => $registro['Contratista'],
        ':Tecnico'            => $registro['Tecnico'],
        ':NOM_DIVISION'       => $registro['NOM_DIVISION'],
        ':NOM_AREA'           => $registro['NOM_AREA'],
        ':Num__Serie'         => $registro['Num__Serie'],
        ':DISTRITO'           => $registro['DISTRITO'],
        ':TERMINAL'           => $registro['TERMINAL'],
        ':Tiene_Senalamiento' => $tieneSenalamiento ? 1 : 0
    ];

    return $this->runExecute($sql, $params, 'default');
}


public function InsertSenalamiento(array $registro, string $tipoSenalamiento)
{
    echo "<pre>Entrando a InsertSenalamiento\n";
    echo "Folio: {$registro['Folio_Pisa']} - Teléfono: {$registro['TELEFONO']} - NumSerie: {$registro['Num__Serie']}\n";
    echo "Tipo señalamiento: $tipoSenalamiento\n</pre>";
    $sql = "INSERT INTO seg_produccion_senalamiento (
                Folio_Pisa, TELEFONO, FECHA_LIQ,
                Tipo_Senalamiento, Detalle, Fecha_Registro
            ) VALUES (
                :Folio_Pisa, :TELEFONO, :FECHA_LIQ,
                :Tipo_Senalamiento, :Detalle, NOW()
            )";

    $params = [
        ':Folio_Pisa'        => $registro['Folio_Pisa'],
        ':TELEFONO'          => $registro['TELEFONO'],
        ':FECHA_LIQ'         => $registro['FECHA_LIQ'],
        ':Tipo_Senalamiento' => $tipoSenalamiento,
        ':Detalle'           => "Num__Serie=" . $registro['Num__Serie'] . " no encontrado en almacén"
    ];

    return $this->runExecute($sql, $params, 'default');
}







}