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

public function getConteoAlertasPorTipo($fecha1 = null, $fecha2 = null)
{
    $sql = "SELECT Tipo_Alerta, COUNT(*) AS total
            FROM Produccion_alertas_detalle";

    // Si vienen fechas, agregamos el filtro
    if ($fecha1 && $fecha2) {
        $sql .= " WHERE FECHA_LIQD BETWEEN '{$fecha1} 00:00:00' AND '{$fecha2} 23:59:59'";
    }

    $sql .= " GROUP BY Tipo_Alerta";

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


/*public function getConteoAlertasPorTipo()
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
} */

public function getConteoAlertasFotos()
{
    $sql = "SELECT Tipo_Alerta
            FROM Produccion_alertas_detalle
            WHERE Tipo_Alerta LIKE 'FOTO_ONT_FRONTAL_FALLIDA%'
               OR Tipo_Alerta LIKE 'FOTO ONT TRASERA FALLIDA%'
               OR Tipo_Alerta LIKE 'FOTO_CASA_CLIENTE_FALLIDA%'
               OR Tipo_Alerta LIKE 'FOTO OS FALLIDA%'
               OR Tipo_Alerta LIKE 'FOTO TERMINAL FALLIDA%'
               OR Tipo_Alerta LIKE 'FALTA_EVIDENCIA_FOTO_PUERTO%'
               OR Tipo_Alerta LIKE 'FOTO DOMICILIO FALLIDA MAL TOMADA%'
               OR Tipo_Alerta LIKE 'FOTO TERMINAL FALLIDA MAL TOMADA%'
               OR Tipo_Alerta LIKE 'FOTO LEDS FALLIDA MAL TOMADA%'";
    
    $result = $this->runQuery($sql);

    $categorias = [
        'FOTO_ONT_FRONTAL_FALLIDA' => 0,
        'FOTO ONT TRASERA FALLIDA' => 0,
        'FOTO_CASA_CLIENTE_FALLIDA' => 0,
        'FOTO OS FALLIDA' => 0,
        'FOTO TERMINAL FALLIDA' => 0,
        'FALTA_EVIDENCIA_FOTO_PUERTO' => 0,
        'FOTO DOMICILIO FALLIDA MAL TOMADA' => 0,
        'FOTO TERMINAL FALLIDA MAL TOMADA' => 0,
        'FOTO LEDS FALLIDA MAL TOMADA' => 0
    ];

    foreach ($result as $fila) {
        $alerta = trim($fila['Tipo_Alerta']);

        if (str_starts_with($alerta, 'FOTO DOMICILIO FALLIDA MAL TOMADA')) {
            $categorias['FOTO DOMICILIO FALLIDA MAL TOMADA']++;
        } elseif (str_starts_with($alerta, 'FOTO TERMINAL FALLIDA MAL TOMADA')) {
            $categorias['FOTO TERMINAL FALLIDA MAL TOMADA']++;
        } elseif (str_starts_with($alerta, 'FOTO LEDS FALLIDA MAL TOMADA')) {
            $categorias['FOTO LEDS FALLIDA MAL TOMADA']++;
        } elseif (isset($categorias[$alerta])) {
            $categorias[$alerta]++;
        }
    }

    return array_values($categorias);
}



public function getDetalleAlertasPorTipo(string $categoria): array
{
    $sql = "SELECT pad.*, 
                   CONCAT(c.Contratista, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo
            FROM Produccion_alertas_detalle pad
            LEFT JOIN contratistas c ON pad.FK_Contratista = c.idContratistas
            WHERE pad.Tipo_Alerta = :categoria";

    return $this->runQuery($sql, [':categoria' => $categoria]);
}

public function getDetalleAlertasFoto(string $categoria): array
{
    $sql = "SELECT pad.*, 
                   CONCAT(c.Contratista, ' ', c.apellido_paterno, ' ', c.apellido_materno) AS nombre_completo
            FROM Produccion_alertas_detalle pad
            LEFT JOIN contratistas c ON pad.FK_Contratista = c.idContratistas
            WHERE pad.Tipo_Alerta LIKE :categoria";

    // Agregar comodín al final para aceptar variantes largas
    $param = $categoria . '%';

    return $this->runQuery($sql, [':categoria' => $param]);
}







}