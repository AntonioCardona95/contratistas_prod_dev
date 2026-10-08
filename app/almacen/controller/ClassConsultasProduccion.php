<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/cnx/cnx.php");

class ClassConsultasProduccion extends Conexion
{
    protected function runQuery($sql, $params = array(), $conexionTipo = 'default', $limit = 1000, $offset = 0)
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
            case 'erpprint':
                $conexion = $this->conexion();
                break;
            default:
                $conexion = $this->get_conexion();
                break;
        }

        try {
            // Agregamos paginación automática
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $conexion->prepare($sql);

            foreach ($params as $key => $val) {
                if (is_int($key)) {
                    $stmt->bindValue($key + 1, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                } else {
                    $param = ':' . ltrim($key, ':');
                    $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
            }

            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

            $stmt->execute();

            $rows = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = $row;
            }

            if (method_exists($stmt, 'closeCursor')) {
                $stmt->closeCursor();
            }
            return $rows;
        } catch (PDOException $e) {
            error_log("Error en runQuery: " . $e->getMessage());
            return array();
        }
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
            case 'erpprint':
                $conexion = $this->conexion();
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
            error_log("Error en runExecute: " . $e->getMessage());
            return 0;
        }
    }



    /**
 * Obtiene la lista completa de contratistas con su nombre completo
 */
public function obtenerTodosContratistas()
{
    $sql = "SELECT 
                idContratistas,
                CONCAT_WS(' ', Contratista, apellido_paterno, apellido_materno) AS nombre_completo
            FROM contratistas
            ORDER BY Contratista ASC";

    return $this->runQuery($sql, array(), 'erpprint');
}


/**
 * Obtiene la lista completa de áreas
 */
public function obtenerTodasAreas()
{
    $sql = "SELECT 
                idAreas,
                area
            FROM areas
            ORDER BY area ASC";

    return $this->runQuery($sql, array(), 'erpprint');
}


public function obtenerDivisionPorArea($idArea) {
    $sql = "SELECT d.idDivision, d.Division
            FROM divisiones d
            INNER JOIN areas a ON a.FK_Division = d.idDivision
            WHERE a.idAreas = ?";
    $result = $this->runQuery($sql, [$idArea], 'erpprint');
    return $result ? $result[0] : null; // cada área tiene una sola división
}

public function obtenerCopesPorArea($idArea) {
    $sql = "SELECT id, COPE
            FROM copes
            WHERE FK_Area = ?
            ORDER BY COPE ASC";
    return $this->runQuery($sql, [$idArea], 'erpprint');
}





/**
 * Cuenta el total de producción en seg_ns_prod_eval para la card de fechas.
 */
public function CountProduccionPorFechas(
    $fechaInicio = null, 
    $fechaFin = null, 
    $fechascard1 = null, 
    $fechascard2 = null, 
    $idContratista = null,
    $tipoTarea = null,
    $idArea = null,
    $idDivision = null,
    $idCope = null
) {
    // Asignar fechas de la card si existen (evitando pasar null a trim)
    $fIni = !empty($fechascard1) ? trim($fechascard1 ?? '') : trim($fechaInicio ?? '');
    $fFin = !empty($fechascard2) ? trim($fechascard2 ?? '') : trim($fechaFin ?? '');

    $where = array();
    $params = array();

    // Convertir de DD/MM/YYYY a YYYY-MM-DD si vienen en formato latino
    if (!empty($fIni) && strpos($fIni, '/') !== false) {
        $p = explode('/', $fIni);
        if (count($p) === 3) { $fIni = "{$p[2]}-{$p[1]}-{$p[0]}"; }
    }
    if (!empty($fFin) && strpos($fFin, '/') !== false) {
        $p = explode('/', $fFin);
        if (count($p) === 3) { $fFin = "{$p[2]}-{$p[1]}-{$p[0]}"; }
    }

    // Filtro por rango de fechas (utiliza FECHA_LIQD o FECHA_LIQ)
    if (!empty($fIni) && !empty($fFin)) {
        $where[] = "FECHA_LIQD BETWEEN :fechaInicio AND :fechaFin";
        $params[':fechaInicio'] = $fIni;
        $params[':fechaFin']    = $fFin;
    } elseif (!empty($fIni)) {
        $where[] = "FECHA_LIQD >= :fechaInicio";
        $params[':fechaInicio'] = $fIni;
    } elseif (!empty($fFin)) {
        $where[] = "FECHA_LIQD <= :fechaFin";
        $params[':fechaFin'] = $fFin;
    }

    // Filtro por Contratista (Soporta ID numérico en idContratista o Nombre en Contratista)
    if (!empty($idContratista) && $idContratista !== '-- Seleccionar --' && $idContratista !== '0') {
        if (is_numeric($idContratista)) {
            $where[] = "idContratista = :idContratista";
            $params[':idContratista'] = (int)$idContratista;
        } else {
            $where[] = "Contratista = :idContratista";
            $params[':idContratista'] = trim($idContratista ?? '');
        }
    }

    // Filtro por Tipo de Tarea
    if (!empty($tipoTarea)) {
        switch (strtoupper(trim($tipoTarea ?? ''))) {
            case 'ALTAS':
                $where[] = "(Tipo_de_Tarea LIKE 'A%' AND Tipo_de_Tarea NOT LIKE 'A4%')";
                break;

            case 'A4':
                $where[] = "Tipo_de_Tarea LIKE 'A4%'";
                break;

            case 'MIGRACIONES':
            case 'MIGRACION':
                $where[] = "(Tipo_de_Tarea LIKE 'FC%' OR Tipo_de_Tarea LIKE 'TI%' OR Tipo_de_Tarea LIKE 'TS%' OR Tipo_de_Tarea LIKE 'TV%')";
                break;

            case 'CAMBIOS DE DOMICILIO':
                $where[] = "Tipo_de_Tarea LIKE 'D%'";
                break;

            case 'QUEJAS':
                $where[] = "(Tipo_de_Tarea LIKE 'Q%' OR Tipo_de_Tarea LIKE 'R%' OR Tipo_de_Tarea LIKE 'C%' OR Tipo_de_Tarea LIKE 'EB%' OR Tipo_de_Tarea LIKE 'EI%' OR Tipo_de_Tarea LIKE 'EU%' OR Tipo_de_Tarea LIKE 'GD%' OR Tipo_de_Tarea LIKE 'MG%' OR Tipo_de_Tarea LIKE 'F597%' OR Tipo_de_Tarea LIKE 'F841%')";
                break;
        }
    }

    // Filtros por Área, División y CT/Cope con las columnas correctas de la tabla
    if (!empty($idArea)) {
        $where[] = "NOM_AREA = :idArea";
        $params[':idArea'] = $idArea;
    }

    // Corregido también por seguridad en caso de recibir null
    if (!empty($idDivision)) {
        $where[] = "NOM_DIVISION = :idDivision";
        $params[':idDivision'] = $idDivision;
    }

    if (!empty($idCope)) {
        $where[] = "NOM_CT = :idCope";
        $params[':idCope'] = $idCope;
    }

    // Consulta SQL
    $sql = "SELECT COUNT(1) AS TOTAL FROM seg_ns_prod_eval";

    if (count($where) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    return $this->runQuery($sql, $params, 'analisis');
}

    /**
     * Cuenta el total acumulado histórico de producción
     */
    public function CountProduccionTotal()
    {
        $sql = "SELECT COUNT(1) AS TOTAL
                FROM seg_ns_prod_eval";

        return $this->runQuery($sql, array(), 'analisis');
    }

 public function CountSinNavegacionPorFechas($fechaInicio, $fechaFin)
{
    // Mantiene las fechas completas con hora si vienen con ella, o les asigna el rango del día completo
    $f1 = (strlen($fechaInicio) <= 10) ? $fechaInicio . ' 00:00:00' : $fechaInicio;
    $f2 = (strlen($fechaFin) <= 10)    ? $fechaFin . ' 23:59:59'    : $fechaFin;

    $sql = "SELECT COUNT(1) AS TOTAL
            FROM seg_produccion_navegacion
            WHERE (
                Flag_Navega = 0 
                OR Flag_Navega IS NULL 
                OR DID_ACTIVO = 0
                OR Estatus_Navegacion = 'SIN NAVEGACION'
                OR Estatus_Navegacion LIKE '%NO REGISTRADO%'
            )
            AND FECHA_LIQ >= :fechaInicio 
            AND FECHA_LIQ <= :fechaFin";

    $params = array(
        ':fechaInicio' => $f1,
        ':fechaFin'    => $f2
    );

    $res = $this->runQuery($sql, $params, 'erpprint');

    return isset($res[0]['TOTAL']) ? (int)$res[0]['TOTAL'] : 0;
}



    /*public function CountSinNavegacionPorFechas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT COUNT(1) AS TOTAL
                FROM seg_produccion_navegacion
                WHERE FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin
                  AND Flag_Navega = 0";

        $params = [
            ':fechaInicio' => $fechaInicio,
            ':fechaFin'    => $fechaFin
        ];

        $res = $this->runQuery($sql, $params, 'erpprint');
        return isset($res[0]['TOTAL']) ? (int)$res[0]['TOTAL'] : 0;
    }  */

    /**
     * Obtiene los registros de producción para el rango de fechas especificado
     */
    public function GetProduccionPorFechas($fechaInicio, $fechaFin)
    {
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

    /**
     * Determina si un número de serie se considera inválido o nulo
     */
    public function esSerieInvalida(?string $serie): bool
    {
        if (empty($serie)) return true;
        
        $serieLimpia = strtoupper(trim($serie));
        $invalidas = ['NS_NOOBTENIDO', 'NS_NO OBTENIDO', 'NS NO OBTENIDO', 'NS NOOBTENIDO', 'N/A', 'NULL', 'UNDEFINED', '0', 'DESCONOCIDO'];
        
        return in_array($serieLimpia, $invalidas, true);
    }

    /**
     * Obtiene el detalle ordenado de ESCI por cada teléfono proporcionado
     */
    public function GetDetalleEciPorTelefonos(array $telefonos): array
    {
        if (empty($telefonos)) return [];

        $telefonos = array_unique(array_filter($telefonos));
        if (empty($telefonos)) return [];

        $placeholders = implode(',', array_fill(0, count($telefonos), '?'));
        
        $sql = "SELECT TELEFONO, M_SERIE, FECHA_ULTIMO_EVENTO, F_CONSULTA, DID_ACTIVO, TECNOLOGIA
                FROM seg_ns_eci
                WHERE TELEFONO IN ($placeholders)
                ORDER BY TELEFONO, F_CONSULTA DESC, FECHA_ULTIMO_EVENTO DESC";

        $rows = $this->runQuery($sql, array_values($telefonos), 'analisis');

        $mapaEci = [];
        foreach ($rows as $row) {
            $tel = trim($row['TELEFONO']);
            if (!isset($mapaEci[$tel])) {
                $mapaEci[$tel] = [
                    'TELEFONO'            => $tel,
                    'M_SERIE'             => strtoupper(trim($row['M_SERIE'] ?? '')),
                    'FECHA_ULTIMO_EVENTO' => $row['FECHA_ULTIMO_EVENTO'] ?? null,
                    'F_CONSULTA'          => $row['F_CONSULTA'] ?? null,
                    'DID_ACTIVO'          => isset($row['DID_ACTIVO']) ? (int)$row['DID_ACTIVO'] : null,
                    'TECNOLOGIA'          => $row['TECNOLOGIA'] ?? null
                ];
            }
        }

        return $mapaEci;
    }

    

    /**
     * Valida qué números de serie existen actualmente en la tabla de materiales (Almacén)
     */
    public function GetNumSeriesEnAlmacen(array $numSeries)
    {
        $numSeries = array_unique(array_filter($numSeries));
        if (empty($numSeries)) return [];

        $placeholders = implode(',', array_fill(0, count($numSeries), '?'));
        $sql = "SELECT Num_Serie FROM materiales_det WHERE Num_Serie IN ($placeholders)";

        $rows = $this->runQuery($sql, array_values($numSeries), 'erpprint');
        return array_column($rows, 'Num_Serie');
    }

    /**
     * Procesa e inserta/actualiza producción, señalamientos y estados de navegación.
     * Mantiene la producción sin duplicar registros y actualiza dinámicamente el estatus de navegación.
     */
    public function InsertBatchProduccionYSenalamiento(array $registros, array $mapExistentes, array $mapEciDetalle)
    {
        
        if (empty($registros)) return true;

        $conexion = $this->conexion(); // Conexión 'erpprint'

        try {
            $conexion->beginTransaction();

            $batchSize = 200;
            $senalamientos = [];
            $navegaciones  = [];

            // -------------------------------------------------------------
            // 1. INSERCIÓN / ACTUALIZACIÓN EN PRODUCCIÓN DIARIA
            // -------------------------------------------------------------
            $sqlDiariaBase = "INSERT INTO seg_produccion_diaria (
                                Expediente, Folio_Pisa, TELEFONO, Tipo_de_Tarea, NOM_CT,
                                FECHA_LIQ, FECHA_LIQD, Semana, Sem, Anio,
                                idContratista, Contratista, Tecnico, NOM_DIVISION, NOM_AREA,
                                Num__Serie, Serie_Eci, Existe_Serie_Prod_Almacen, Existe_Serie_Eci_Almacen,
                                DISTRITO, TERMINAL, Fecha_Insercion, Tiene_Senalamiento
                            ) VALUES ";

            // Si la producción ya existía, actualizamos datos evaluados sin duplicar filas
            $sqlDiariaDuplicate = " ON DUPLICATE KEY UPDATE
                                    Serie_Eci = VALUES(Serie_Eci),
                                    Existe_Serie_Prod_Almacen = VALUES(Existe_Serie_Prod_Almacen),
                                    Existe_Serie_Eci_Almacen = VALUES(Existe_Serie_Eci_Almacen),
                                    Tiene_Senalamiento = VALUES(Tiene_Senalamiento)";

            $chunks = array_chunk($registros, $batchSize);

            foreach ($chunks as $chunk) {
                $values = [];
                $params = [];

                foreach ($chunk as $reg) {
                    $tel       = trim($reg['TELEFONO'] ?? '');
                    $folioPisa = trim($reg['Folio_Pisa'] ?? '');
                    $fechaLiq  = $reg['FECHA_LIQ'] ?? '';

                    $serieProd = trim($reg['Num__Serie'] ?? '');
                    
                    // Datos de ESCI para el teléfono
                    $eciInfo   = $mapEciDetalle[$tel] ?? null;
                    $serieEci  = $eciInfo['M_SERIE'] ?? null;

                    $prodEsValida = !$this->esSerieInvalida($serieProd);
                    $eciEsValida  = $serieEci !== null && !$this->esSerieInvalida($serieEci);

                    // Evaluaciones de almacén
                    $existeProdEnAlmacen = $prodEsValida && isset($mapExistentes[$serieProd]);
                    $existeEciEnAlmacen  = $eciEsValida && isset($mapExistentes[$serieEci]);

                    // Estatus de Navegación Actualizado
                    $didActivo = $eciInfo['DID_ACTIVO'] ?? null;
                    $fEvento   = $eciInfo['FECHA_ULTIMO_EVENTO'] ?? null;
                    $fConsulta = $eciInfo['F_CONSULTA'] ?? null;

                    $estatusNav = 'NO REGISTRADO EN ESCI';
                    $flagNavega = 0;

                    if ($eciInfo !== null) {
                        if ($didActivo === 1) {
                            $estatusNav = 'NAVEGANDO';
                            $flagNavega = 1;
                        } elseif ($didActivo === 0) {
                            $estatusNav = 'SIN NAVEGACION';
                            $flagNavega = 0;
                        } elseif ($didActivo === -1) {
                            $estatusNav = 'SERVICIO INACTIVO';
                            $flagNavega = 0;
                        } elseif ($this->esSerieInvalida($serieEci)) {
                            $estatusNav = 'SIN NAVEGACION';
                            $flagNavega = 0;
                        }
                    }

                    // Acumulamos datos para seg_produccion_navegacion
                    $navegaciones[] = [
                        'Folio_Pisa'          => $folioPisa,
                        'TELEFONO'            => $tel,
                        'FECHA_LIQ'           => $fechaLiq,
                        'DID_ACTIVO'          => $didActivo,
                        'Estatus_Navegacion'  => $estatusNav,
                        'Flag_Navega'         => $flagNavega,
                        'Num__Serie_Eci'      => $serieEci,
                        'FECHA_ULTIMO_EVENTO' => $fEvento,
                        'F_CONSULTA'          => $fConsulta
                    ];

                    // Evaluación de Señalamientos
                    $hayDiscrepanciaSeries = false;
                    if ($prodEsValida && $eciEsValida && strtoupper($serieProd) !== strtoupper($serieEci)) {
                        $hayDiscrepanciaSeries = true;

                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'NUMEROS DE SERIE DIFERENTES PARA EL TELEFONO',
                            'Detalle'           => "Producción traía ($serieProd) y ECI traía ($serieEci)"
                        ];
                    }

                    if (!$prodEsValida) {
                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'SERIE TAC INVALIDA O VACIA',
                            'Detalle'           => "Producción registro serie sin obtener o vacia: '$serieProd'"
                        ];
                    } elseif (!$existeProdEnAlmacen) {
                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'NO ENCONTRADO EN ALMACEN',
                            'Detalle'           => "Serie " . $serieProd . " (Origen: TAC) NO fue encontrada en almacen"
                        ];
                    }

                    if ($eciEsValida && !$existeEciEnAlmacen) {
                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'NO ENCONTRADO EN ALMACEN',
                            'Detalle'           => "Serie " . $serieEci . " (Origen: ECI) NO fue encontrada en almacen"
                        ];
                    }

                    if ($flagNavega === 0) {
                        $senalamientos[] = [
                            'Folio_Pisa'        => $folioPisa,
                            'TELEFONO'          => $tel,
                            'FECHA_LIQ'         => $fechaLiq,
                            'Tipo_Senalamiento' => 'SIN NAVEGACION EN ESCI',
                            'Detalle'           => "Estatus: $estatusNav" . ($fEvento ? " | Última vez que navego: $fEvento" : "")
                        ];
                    }

                    $tieneFallos = $hayDiscrepanciaSeries || !$prodEsValida || !$existeProdEnAlmacen || ($eciEsValida && !$existeEciEnAlmacen) || ($flagNavega === 0);
                    $tieneSenalamiento = $tieneFallos ? 1 : 0;

                    $values[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

                    array_push(
                        $params,
                        $reg['Expediente'], $folioPisa, $tel, $reg['Tipo_de_Tarea'], $reg['NOM_CT'],
                        $fechaLiq, $reg['FECHA_LIQD'], $reg['Semana'], $reg['Sem'], $reg['Anio'],
                        $reg['idContratista'], $reg['Contratista'], $reg['Tecnico'], $reg['NOM_DIVISION'], $reg['NOM_AREA'],
                        $serieProd, $serieEci, $existeProdEnAlmacen ? 1 : 0, $existeEciEnAlmacen ? 1 : 0,
                        $reg['DISTRITO'], $reg['TERMINAL'], $tieneSenalamiento
                    );
                }

                $stmtDiaria = $conexion->prepare($sqlDiariaBase . implode(',', $values) . $sqlDiariaDuplicate);
                $stmtDiaria->execute($params);
            }

            // -------------------------------------------------------------
            // 2. ACTUALIZACIÓN DINÁMICA EN SEGUIMIENTO DE NAVEGACIÓN
            // -------------------------------------------------------------
            if (!empty($navegaciones)) {
                $chunksNav = array_chunk($navegaciones, $batchSize);
                $sqlNavBase = "INSERT INTO seg_produccion_navegacion (
                                    Folio_Pisa, TELEFONO, FECHA_LIQ, DID_ACTIVO, Estatus_Navegacion,
                                    Flag_Navega, Num__Serie_Eci, FECHA_ULTIMO_EVENTO, F_CONSULTA, Fecha_Evaluacion
                                ) VALUES ";

                $sqlNavDuplicate = " ON DUPLICATE KEY UPDATE
                                    DID_ACTIVO = VALUES(DID_ACTIVO),
                                    Estatus_Navegacion = VALUES(Estatus_Navegacion),
                                    Flag_Navega = VALUES(Flag_Navega),
                                    Num__Serie_Eci = VALUES(Num__Serie_Eci),
                                    FECHA_ULTIMO_EVENTO = VALUES(FECHA_ULTIMO_EVENTO),
                                    F_CONSULTA = VALUES(F_CONSULTA),
                                    Fecha_Evaluacion = NOW()";

                foreach ($chunksNav as $chunk) {
                    $valuesNav = [];
                    $paramsNav = [];

                    foreach ($chunk as $nav) {
                        $valuesNav[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                        array_push(
                            $paramsNav,
                            $nav['Folio_Pisa'],
                            $nav['TELEFONO'],
                            $nav['FECHA_LIQ'],
                            $nav['DID_ACTIVO'],
                            $nav['Estatus_Navegacion'],
                            $nav['Flag_Navega'],
                            $nav['Num__Serie_Eci'],
                            $nav['FECHA_ULTIMO_EVENTO'],
                            $nav['F_CONSULTA']
                        );
                    }

                    $stmtNav = $conexion->prepare($sqlNavBase . implode(',', $valuesNav) . $sqlNavDuplicate);
                    $stmtNav->execute($paramsNav);
                }
            }

            // -------------------------------------------------------------
            // 3. LIMPIEZA Y REFRESH DE SEÑALAMIENTOS DE NAVEGACIÓN
            // -------------------------------------------------------------
            // Borramos los señalamientos de 'SIN NAVEGACION EN ESCI' de los folios a procesar 
            // para que si ya navegan, el señalamiento desaparezca automáticamente.
            $foliosLote = array_unique(array_column($registros, 'Folio_Pisa'));
            if (!empty($foliosLote)) {
                $phFolios = implode(',', array_fill(0, count($foliosLote), '?'));
                $stmtClean = $conexion->prepare("DELETE FROM seg_produccion_senalamiento 
                                                 WHERE Folio_Pisa IN ($phFolios) 
                                                   AND Tipo_Senalamiento = 'SIN NAVEGACION EN ESCI'");
                $stmtClean->execute(array_values($foliosLote));
            }

            // Inserción de los nuevos señalamientos calculados
            if (!empty($senalamientos)) {
                $chunksSenalamiento = array_chunk($senalamientos, $batchSize);
                $sqlSenalBase = "INSERT INTO seg_produccion_senalamiento (
                                    Folio_Pisa, TELEFONO, FECHA_LIQ, Tipo_Senalamiento, Detalle, Fecha_Registro
                                ) VALUES ";

                foreach ($chunksSenalamiento as $chunk) {
                    $valuesSen = [];
                    $paramsSen = [];

                    foreach ($chunk as $sen) {
                        $valuesSen[] = "(?, ?, ?, ?, ?, NOW())";
                        array_push(
                            $paramsSen,
                            $sen['Folio_Pisa'],
                            $sen['TELEFONO'],
                            $sen['FECHA_LIQ'],
                            $sen['Tipo_Senalamiento'],
                            $sen['Detalle']
                        );
                    }

                    $stmtSenal = $conexion->prepare($sqlSenalBase . implode(',', $valuesSen));
                    $stmtSenal->execute($paramsSen);
                }
            }

            $conexion->commit();
            return true;

        } catch (Exception $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            error_log("Error en InsertBatchProduccionYSenalamiento: " . $e->getMessage());
            return false;
        }
    }


    /**
 * Obtiene registros completos de producción (diaria + navegación + señalamientos)
 * en un rango de fechas
 */
public function VisualizarTablaFechas($fechaInicio, $fechaFin)
{
    $sql = "SELECT 
                d.Folio_Pisa,
                d.TELEFONO,
                d.Tipo_de_Tarea,
                d.Contratista,
                d.Tecnico,
                d.NOM_DIVISION,
                d.NOM_AREA,
                d.Num__Serie,
                d.Serie_Eci,
                d.Existe_Serie_Prod_Almacen,
                d.Existe_Serie_Eci_Almacen,
                d.FECHA_LIQ,
                d.Semana,
                d.Anio,
                d.Tiene_Senalamiento,
                n.Estatus_Navegacion,
                n.Flag_Navega,
                n.FECHA_ULTIMO_EVENTO,
                s.Tipo_Senalamiento,
                s.Detalle AS Detalle_Senalamiento
            FROM seg_produccion_diaria d
            LEFT JOIN seg_produccion_navegacion n 
                ON d.Folio_Pisa = n.Folio_Pisa 
               AND d.TELEFONO = n.TELEFONO 
               AND d.FECHA_LIQ = n.FECHA_LIQ
            LEFT JOIN seg_produccion_senalamiento s 
                ON d.Folio_Pisa = s.Folio_Pisa 
               AND d.TELEFONO = s.TELEFONO 
               AND d.FECHA_LIQ = s.FECHA_LIQ
            WHERE d.FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    $params = array(
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin
    );

    return $this->runQuery($sql, $params, 'analisis');
}



/*public function ConsultarProduccionDiaria($fechaInicio, $fechaFin, $idContratista = null)
{
    $params = [
        ':fechaInicio' => $fechaInicio, 
        ':fechaFin'    => $fechaFin
    ];

    $sql = "SELECT p.*, 
                   s.Tipo_Senalamiento, 
                   s.Detalle AS Detalle_Senalamiento,
                   n.Estatus_Navegacion, 
                   n.Flag_Navega, 
                   n.FECHA_ULTIMO_EVENTO,
                   c_asig.Contratista AS Contratista_Asignado
            FROM seg_produccion_diaria p
            LEFT JOIN seg_produccion_senalamiento s 
                ON p.Folio_Pisa = s.Folio_Pisa 
               AND p.TELEFONO = s.TELEFONO 
               AND p.FECHA_LIQ = s.FECHA_LIQ
            LEFT JOIN seg_produccion_navegacion n 
                ON p.Folio_Pisa = n.Folio_Pisa 
               AND p.TELEFONO = n.TELEFONO 
               AND p.FECHA_LIQ = n.FECHA_LIQ
            LEFT JOIN material_det_salida mds
                ON p.Num__Serie COLLATE utf8mb4_general_ci = mds.Num_Serie_S COLLATE utf8mb4_general_ci
               AND (mds.Estatus_Ubicacion != 'DEVOLUCION' OR mds.Estatus_Ubicacion IS NULL)
            LEFT JOIN contratistas c_asig
                ON mds.FK_Contratista_Salida = c_asig.idContratistas
            WHERE p.FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    if (!empty($idContratista) && $idContratista != 0) {
        $sql .= " AND p.idContratista = :idContratista";
        $params[':idContratista'] = $idContratista;
    }

    $sql .= " ORDER BY p.FECHA_LIQ DESC";

    try {
        return $this->runQuery($sql, $params, 'erpprint');
    } catch (PDOException $e) {
        // En producción se recomienda registrar el error en log en lugar de romper la ejecución
        error_log("ERROR SQL en ConsultarProduccionDiaria: " . $e->getMessage());
        return [];
    }
} */



/**
 * Obtiene el listado de producción diaria filtrado por rango de fechas y opcionalmente por contratista.
 *
 * @param string $fechaInicio Fecha de inicio en formato YYYY-MM-DD HH:MM:SS
 * @param string $fechaFin Fecha de fin en formato YYYY-MM-DD HH:MM:SS
 * @param int|string|null $idContratista (Opcional) ID del contratista para filtrar. Si es null u 0, trae todo.
 * @return array
 */
/*public function ConsultarProduccionDiaria($fechaInicio, $fechaFin, $idContratista = null)
{
    $sql = "SELECT 
                p.Expediente,
                p.Folio_Pisa,
                p.TELEFONO,
                p.Tipo_de_Tarea,
                p.NOM_CT,
                p.FECHA_LIQ,
                p.Semana,
                p.Anio,
                p.idContratista,
                p.Contratista,
                p.Tecnico,
                p.NOM_DIVISION,
                p.NOM_AREA,
                p.Num__Serie,
                p.Serie_Eci,
                p.Existe_Serie_Prod_Almacen,
                p.Existe_Serie_Eci_Almacen,
                p.DISTRITO,
                p.TERMINAL,
                p.Tiene_Senalamiento,
                s.Tipo_Senalamiento,
                s.Detalle AS Detalle_Senalamiento,
                n.Estatus_Navegacion,
                n.Flag_Navega,
                n.FECHA_ULTIMO_EVENTO
            FROM seg_produccion_diaria p
            LEFT JOIN seg_produccion_senalamiento s 
                ON p.Folio_Pisa = s.Folio_Pisa 
               AND p.TELEFONO = s.TELEFONO 
               AND p.FECHA_LIQ = s.FECHA_LIQ
            LEFT JOIN seg_produccion_navegacion n 
                ON p.Folio_Pisa = n.Folio_Pisa 
               AND p.TELEFONO = n.TELEFONO 
               AND p.FECHA_LIQ = n.FECHA_LIQ
            WHERE p.FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    $params = array(
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin
    );

    // Si viene un ID de contratista válido (diferente de 0, vacíos o null), agregamos el filtro
    if (!empty($idContratista) && $idContratista != 0) {
        $sql .= " AND p.idContratista = :idContratista";
        $params[':idContratista'] = $idContratista;
    }

    $sql .= " ORDER BY p.FECHA_LIQ DESC";

    return $this->runQuery($sql, $params, 'erpprint'); 
} */

    public function ConsultarProduccionDiaria($fechaInicio, $fechaFin, $idContratista = null, $tipoTarea = null)
{
    $sql = "SELECT 
                p.Expediente,
                p.Folio_Pisa,
                p.TELEFONO,
                p.Tipo_de_Tarea,
                p.NOM_CT,
                p.FECHA_LIQ,
                p.Semana,
                p.Anio,
                p.idContratista,
                p.Contratista,
                p.Tecnico,
                p.NOM_DIVISION,
                p.NOM_AREA,
                p.Num__Serie,
                p.Serie_Eci,
                p.Existe_Serie_Prod_Almacen,
                p.Existe_Serie_Eci_Almacen,
                p.DISTRITO,
                p.TERMINAL,
                p.Tiene_Senalamiento,
                s.Tipo_Senalamiento,
                s.Detalle AS Detalle_Senalamiento,
                n.Estatus_Navegacion,
                n.Flag_Navega,
                n.FECHA_ULTIMO_EVENTO,
                CONCAT_WS(' ', c_asig.Contratista, c_asig.apellido_paterno, c_asig.apellido_materno) AS Contratista_Asignado,
                CONCAT_WS(' ', t.Nombre_T, t.Apellidos_T) AS Tecnico_Asignado,
                tic.Ont AS Ont_Coordiapp
            FROM seg_produccion_diaria p
            LEFT JOIN seg_produccion_senalamiento s 
                ON p.Folio_Pisa = s.Folio_Pisa 
               AND p.TELEFONO = s.TELEFONO 
               AND p.FECHA_LIQ = s.FECHA_LIQ
            LEFT JOIN seg_produccion_navegacion n 
                ON p.Folio_Pisa = n.Folio_Pisa 
               AND p.TELEFONO = n.TELEFONO 
               AND p.FECHA_LIQ = n.FECHA_LIQ
            LEFT JOIN material_det_salida mds
                ON p.Num__Serie = mds.Num_Serie_S
               AND (mds.Ubicacion_Material IS NULL OR mds.Ubicacion_Material != 'DEVOLUCION')
            LEFT JOIN contratistas c_asig
                ON mds.FK_Contratista_Salida = c_asig.idContratistas
            LEFT JOIN salidas_contratistas sc
                ON p.Num__Serie = sc.Num_Serie_Salida_Det
            LEFT JOIN tecnicos t
                ON sc.FK_Tecnico_Salida_Det = t.idTecnico
            LEFT JOIN tecnico_instalaciones_coordiapp tic
                ON p.Folio_Pisa = tic.Folio_Pisa
               AND p.TELEFONO = tic.Telefono
               AND tic.Estatus_Orden = 'COMPLETADA'
               AND tic.Step_Registro = 5
            WHERE p.FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    $params = array(
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin
    );

    if (!empty($idContratista) && $idContratista != 0) {
        $sql .= " AND p.idContratista = :idContratista";
        $params[':idContratista'] = $idContratista;
    }

    // Filtrado dinámico por Tipo de Tarea
    if (!empty($tipoTarea)) {
        $mapaTareas = [
            'ALTAS'                => ['A'],
            'CAMBIOS DE DOMICILIO' => ['D'],
            'MIGRACION'            => ['FC', 'TI', 'TS', 'TV'],
            'A4'                   => ['A4'],
            'QUEJAS'               => ['Q', 'R', 'C', 'EB', 'EI', 'EU', 'GD', 'MG', 'F597', 'F841']
        ];

        if (isset($mapaTareas[$tipoTarea])) {
            $prefijos = $mapaTareas[$tipoTarea];
            $condicionesLike = [];

            foreach ($prefijos as $index => $prefijo) {
                $paramName = ":prefijo_" . $index;
                $condicionesLike[] = "p.Tipo_de_Tarea LIKE " . $paramName;
                $params[$paramName] = $prefijo . '%';
            }

            $sql .= " AND (" . implode(" OR ", $condicionesLike) . ")";
        }
    }

    $sql .= " ORDER BY p.FECHA_LIQ DESC";

    return $this->runQuery($sql, $params, 'erpprint');
}


    // prueba FUNCIONA //

/*public function ConsultarProduccionDiaria($fechaInicio, $fechaFin, $idContratista = null)
{
    $sql = "SELECT 
                p.Expediente,
                p.Folio_Pisa,
                p.TELEFONO,
                p.Tipo_de_Tarea,
                p.NOM_CT,
                p.FECHA_LIQ,
                p.Semana,
                p.Anio,
                p.idContratista,
                p.Contratista,
                p.Tecnico,
                p.NOM_DIVISION,
                p.NOM_AREA,
                p.Num__Serie,
                p.Serie_Eci,
                p.Existe_Serie_Prod_Almacen,
                p.Existe_Serie_Eci_Almacen,
                p.DISTRITO,
                p.TERMINAL,
                p.Tiene_Senalamiento,
                s.Tipo_Senalamiento,
                s.Detalle AS Detalle_Senalamiento,
                n.Estatus_Navegacion,
                n.Flag_Navega,
                n.FECHA_ULTIMO_EVENTO,
                CONCAT_WS(' ', c_asig.Contratista, c_asig.apellido_paterno, c_asig.apellido_materno) AS Contratista_Asignado,
                CONCAT_WS(' ', t.Nombre_T, t.Apellidos_T) AS Tecnico_Asignado,
                tic.Ont AS Ont_Coordiapp
            FROM seg_produccion_diaria p
            LEFT JOIN seg_produccion_senalamiento s 
                ON p.Folio_Pisa = s.Folio_Pisa 
               AND p.TELEFONO = s.TELEFONO 
               AND p.FECHA_LIQ = s.FECHA_LIQ
            LEFT JOIN seg_produccion_navegacion n 
                ON p.Folio_Pisa = n.Folio_Pisa 
               AND p.TELEFONO = n.TELEFONO 
               AND p.FECHA_LIQ = n.FECHA_LIQ
            LEFT JOIN material_det_salida mds
                ON p.Num__Serie = mds.Num_Serie_S
               AND (mds.Ubicacion_Material IS NULL OR mds.Ubicacion_Material != 'DEVOLUCION')
            LEFT JOIN contratistas c_asig
                ON mds.FK_Contratista_Salida = c_asig.idContratistas
            LEFT JOIN salidas_contratistas sc
                ON p.Num__Serie = sc.Num_Serie_Salida_Det
            LEFT JOIN tecnicos t
                ON sc.FK_Tecnico_Salida_Det = t.idTecnico
            LEFT JOIN tecnico_instalaciones_coordiapp tic
                ON p.Folio_Pisa = tic.Folio_Pisa
               AND p.TELEFONO = tic.Telefono
               AND tic.Estatus_Orden = 'COMPLETADA'
               AND tic.Step_Registro = 5
            WHERE p.FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    $params = array(
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin
    );

    if (!empty($idContratista) && $idContratista != 0) {
        $sql .= " AND p.idContratista = :idContratista";
        $params[':idContratista'] = $idContratista;
    }

    $sql .= " ORDER BY p.FECHA_LIQ DESC";

    return $this->runQuery($sql, $params, 'erpprint'); 
} */


// con impresiones //

/*public function ConsultarProduccionDiaria($fechaInicio, $fechaFin, $idContratista = null)
{
    $sql = "SELECT 
                p.Expediente,
                p.Folio_Pisa,
                p.TELEFONO,
                p.Tipo_de_Tarea,
                p.NOM_CT,
                p.FECHA_LIQ,
                p.Semana,
                p.Anio,
                p.idContratista,
                p.Contratista,
                p.Tecnico,
                p.NOM_DIVISION,
                p.NOM_AREA,
                p.Num__Serie,
                p.Serie_Eci,
                p.Existe_Serie_Prod_Almacen,
                p.Existe_Serie_Eci_Almacen,
                p.DISTRITO,
                p.TERMINAL,
                p.Tiene_Senalamiento,
                s.Tipo_Senalamiento,
                s.Detalle AS Detalle_Senalamiento,
                n.Estatus_Navegacion,
                n.Flag_Navega,
                n.FECHA_ULTIMO_EVENTO,
                c_asig.Contratista AS Contratista_Asignado
            FROM seg_produccion_diaria p
            LEFT JOIN seg_produccion_senalamiento s 
                ON p.Folio_Pisa = s.Folio_Pisa 
               AND p.TELEFONO = s.TELEFONO 
               AND p.FECHA_LIQ = s.FECHA_LIQ
            LEFT JOIN seg_produccion_navegacion n 
                ON p.Folio_Pisa = n.Folio_Pisa 
               AND p.TELEFONO = n.TELEFONO 
               AND p.FECHA_LIQ = n.FECHA_LIQ
            LEFT JOIN material_det_salida mds
                ON p.Num__Serie = mds.Num_Serie_S
               AND (mds.Ubicacion_Material IS NULL OR mds.Ubicacion_Material != 'DEVOLUCION')
            LEFT JOIN contratistas c_asig
                ON mds.FK_Contratista_Salida = c_asig.idContratistas
            WHERE p.FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";

    $params = array(
        ':fechaInicio' => $fechaInicio,
        ':fechaFin'    => $fechaFin
    );

    if (!empty($idContratista) && $idContratista != 0) {
        $sql .= " AND p.idContratista = :idContratista";
        $params[':idContratista'] = $idContratista;
    }

    $sql .= " ORDER BY p.FECHA_LIQ DESC";

    // Ejecutar la consulta
    $rows = $this->runQuery($sql, $params, 'erpprint');

    // 🔎 Debug: imprimir todo lo que devuelve
    echo "<pre>";
    echo "Consulta ejecutada:\n" . $sql . "\n\n";
    echo "Parámetros:\n";
    var_dump($params);
    echo "\nResultados:\n";
    var_dump($rows);
    echo "</pre>";

    return $rows;
} */


  /**
 * Cuenta el total de equipos/ONT que NO han ingresado a almacén.
 * Permite filtrar por fechas, contratista, tipo de tarea, área, división y cope.
 */
public function CountOntSinEntradaAlmacen(
    $fechaInicio = null, 
    $fechaFin = null, 
    $fechascard1 = null, 
    $fechascard2 = null, 
    $idContratista = null,
    $tipoTarea = null,
    $idArea = null,
    $idDivision = null,
    $idCope = null
) {
    // Determinar qué rango de fechas usar (prioridad a las fechas de la card si existen)
    $fIni = !empty($fechascard1) ? $fechascard1 : $fechaInicio;
    $fFin = !empty($fechascard2) ? $fechascard2 : $fechaFin;

    $where = array(
        "Existe_Serie_Prod_Almacen = 0",
        "Existe_Serie_Eci_Almacen = 0"
    );
    $params = array();

    // Filtro de rango de fechas
    if (!empty($fIni) && !empty($fFin)) {
        $where[] = "FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";
        $params[':fechaInicio'] = $fIni;
        $params[':fechaFin']    = $fFin;
    }

    // Filtro opcional por contratista
    if (!empty($idContratista)) {
        $where[] = "idContratista = :idContratista";
        $params[':idContratista'] = $idContratista;
    }

    // Filtro opcional por Tipo de Tarea
    if (!empty($tipoTarea)) {
        switch (strtoupper(trim($tipoTarea))) {
            case 'ALTAS':
                // Inician con 'A' pero NO son 'A4'
                $where[] = "(Tipo_de_Tarea LIKE 'A%' AND Tipo_de_Tarea NOT LIKE 'A4%')";
                break;

            case 'A4':
                // Exactamente inician con 'A4'
                $where[] = "Tipo_de_Tarea LIKE 'A4%'";
                break;

            case 'MIGRACIONES':
            case 'MIGRACION':
                // Inician con 'FC', 'TI', 'TS' o 'TV'
                $where[] = "(Tipo_de_Tarea LIKE 'FC%' OR Tipo_de_Tarea LIKE 'TI%' OR Tipo_de_Tarea LIKE 'TS%' OR Tipo_de_Tarea LIKE 'TV%')";
                break;

            case 'CAMBIOS DE DOMICILIO':
                // Inician con 'D'
                $where[] = "Tipo_de_Tarea LIKE 'D%'";
                break;

            case 'QUEJAS':
                // Inician con 'Q', 'R', 'C', 'EB', 'EI', 'EU', 'GD', 'MG', 'F597' o 'F841'
                $where[] = "(Tipo_de_Tarea LIKE 'Q%' OR Tipo_de_Tarea LIKE 'R%' OR Tipo_de_Tarea LIKE 'C%' OR Tipo_de_Tarea LIKE 'EB%' OR Tipo_de_Tarea LIKE 'EI%' OR Tipo_de_Tarea LIKE 'EU%' OR Tipo_de_Tarea LIKE 'GD%' OR Tipo_de_Tarea LIKE 'MG%' OR Tipo_de_Tarea LIKE 'F597%' OR Tipo_de_Tarea LIKE 'F841%')";
                break;
        }
    }

    // Filtro opcional por Área
    if (!empty($idArea)) {
        $where[] = "idAreas = :idArea";
        $params[':idArea'] = $idArea;
    }

    // Filtro opcional por División
    if (!empty($idDivision)) {
        $where[] = "idDivisiones = :idDivision";
        $params[':idDivision'] = $idDivision;
    }

    // Filtro opcional por Cope
    if (!empty($idCope)) {
        $where[] = "idCopes = :idCope";
        $params[':idCope'] = $idCope;
    }

    // Construir la consulta SQL
    $sql = "SELECT COUNT(1) AS TOTAL 
            FROM seg_produccion_diaria 
            WHERE " . implode(" AND ", $where);

    return $this->runQuery($sql, $params, 'erpprint');
}


/**
 * Cuenta el total de ONT / equipos que SÍ ingresaron a almacén.
 * Permite filtrar por fechas principales, fechas de card, contratista, tipo de tarea, área, división y cope.
 */
public function CountOntConEntradaAlmacen(
    $fechaInicio = null, 
    $fechaFin = null, 
    $fechascard1 = null, 
    $fechascard2 = null, 
    $idContratista = null,
    $tipoTarea = null,
    $idArea = null,
    $idDivision = null,
    $idCope = null
) {
    // Determinar qué rango de fechas usar (prioridad a las fechas de la card si existen)
    $fIni = !empty($fechascard1) ? $fechascard1 : $fechaInicio;
    $fFin = !empty($fechascard2) ? $fechascard2 : $fechaFin;

    $where = array(
        "Existe_Serie_Prod_Almacen = 1",
        "Existe_Serie_Eci_Almacen = 1"
    );
    $params = array();

    // Filtro opcional por rango de fechas
    if (!empty($fIni) && !empty($fFin)) {
        $where[] = "FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";
        $params[':fechaInicio'] = $fIni;
        $params[':fechaFin']    = $fFin;
    }

    // Filtro opcional por contratista
    if (!empty($idContratista)) {
        $where[] = "idContratista = :idContratista";
        $params[':idContratista'] = $idContratista;
    }

    // Filtro opcional por Tipo de Tarea
    if (!empty($tipoTarea)) {
        switch (strtoupper(trim($tipoTarea))) {
            case 'ALTAS':
                // Inician con 'A' pero NO son 'A4'
                $where[] = "(Tipo_de_Tarea LIKE 'A%' AND Tipo_de_Tarea NOT LIKE 'A4%')";
                break;

            case 'A4':
                // Exactamente inician con 'A4'
                $where[] = "Tipo_de_Tarea LIKE 'A4%'";
                break;

            case 'MIGRACIONES':
            case 'MIGRACION':
                // Inician con 'FC', 'TI', 'TS' o 'TV'
                $where[] = "(Tipo_de_Tarea LIKE 'FC%' OR Tipo_de_Tarea LIKE 'TI%' OR Tipo_de_Tarea LIKE 'TS%' OR Tipo_de_Tarea LIKE 'TV%')";
                break;

            case 'CAMBIOS DE DOMICILIO':
                // Inician con 'D'
                $where[] = "Tipo_de_Tarea LIKE 'D%'";
                break;

            case 'QUEJAS':
                // Inician con 'Q', 'R', 'C', 'EB', 'EI', 'EU', 'GD', 'MG', 'F597' o 'F841'
                $where[] = "(Tipo_de_Tarea LIKE 'Q%' OR Tipo_de_Tarea LIKE 'R%' OR Tipo_de_Tarea LIKE 'C%' OR Tipo_de_Tarea LIKE 'EB%' OR Tipo_de_Tarea LIKE 'EI%' OR Tipo_de_Tarea LIKE 'EU%' OR Tipo_de_Tarea LIKE 'GD%' OR Tipo_de_Tarea LIKE 'MG%' OR Tipo_de_Tarea LIKE 'F597%' OR Tipo_de_Tarea LIKE 'F841%')";
                break;
        }
    }

    // Filtro opcional por Área
    if (!empty($idArea)) {
        $where[] = "idAreas = :idArea";
        $params[':idArea'] = $idArea;
    }

    // Filtro opcional por División
    if (!empty($idDivision)) {
        $where[] = "idDivisiones = :idDivision";
        $params[':idDivision'] = $idDivision;
    }

    // Filtro opcional por Cope
    if (!empty($idCope)) {
        $where[] = "idCopes = :idCope";
        $params[':idCope'] = $idCope;
    }

    // Construir la consulta SQL
    $sql = "SELECT COUNT(1) AS TOTAL 
            FROM seg_produccion_diaria 
            WHERE " . implode(" AND ", $where);

    return $this->runQuery($sql, $params, 'erpprint');
}
/**
 * Obtiene el conteo de ONT con y sin entrada a almacén para TODAS las divisiones.
 * Permite filtrar por fechas principales, fechas de card y/o contratista.
 */
public function CountOntAlmacenPorDivision($fechaInicio = null, $fechaFin = null, $fechascard1 = null, $fechascard2 = null, $idContratista = null)
{
    // Determinar qué rango de fechas usar (prioridad a las fechas de la card si existen)
    $fIni = !empty($fechascard1) ? $fechascard1 : $fechaInicio;
    $fFin = !empty($fechascard2) ? $fechascard2 : $fechaFin;

    $joinConditions = array(
        "(TRIM(p.NOM_DIVISION) = TRIM(d.Division) OR p.NOM_DIVISION LIKE CONCAT('%', d.Division, '%'))"
    );
    $params = array();

    // Filtro opcional de rango de fechas
    if (!empty($fIni) && !empty($fFin)) {
        $joinConditions[] = "p.FECHA_LIQ BETWEEN :fechaInicio AND :fechaFin";
        $params[':fechaInicio'] = $fIni;
        $params[':fechaFin']    = $fFin;
    }

    // Filtro opcional por contratista
    if (!empty($idContratista)) {
        $joinConditions[] = "p.idContratista = :idContratista";
        $params[':idContratista'] = $idContratista;
    }

    // Construcción de la consulta SQL
    $sql = "SELECT 
                d.Division AS DIVISION,
                COALESCE(SUM(CASE WHEN p.Existe_Serie_Prod_Almacen = 1 AND p.Existe_Serie_Eci_Almacen = 1 THEN 1 ELSE 0 END), 0) AS CON_ALMACEN,
                COALESCE(SUM(CASE WHEN p.Existe_Serie_Prod_Almacen = 0 AND p.Existe_Serie_Eci_Almacen = 0 THEN 1 ELSE 0 END), 0) AS SIN_ALMACEN
            FROM divisiones d
            LEFT JOIN seg_produccion_diaria p 
                ON " . implode(" AND ", $joinConditions) . "
            GROUP BY d.idDivision, d.Division 
            ORDER BY d.idDivision ASC";

    return $this->runQuery($sql, $params, 'erpprint');
}


/**
 * Obtiene el detalle de órdenes agrupadas por área para un contratista en un rango de fechas
 */
/*public function ObtenerDetalleOrdenesPorArea($idContratista, $fechaInicio, $fechaFin)
{
    $sql = "SELECT 
                p.Area,
                p.Division,
                p.Cope,
                SUM(CASE WHEN p.Tipo_Trabajo = 'ALTA' THEN 1 ELSE 0 END) AS Altas,
                SUM(CASE WHEN p.Tipo_Trabajo = 'MIGRACION' THEN 1 ELSE 0 END) AS Migraciones,
                SUM(CASE WHEN p.Tipo_Trabajo = 'CAMBIO DE DOMICILIO' THEN 1 ELSE 0 END) AS CambiosDomicilio,
                SUM(CASE WHEN p.Tipo_Trabajo = 'QUEJA' THEN 1 ELSE 0 END) AS Quejas,
                SUM(CASE WHEN p.Tipo_Trabajo = 'A4' THEN 1 ELSE 0 END) AS A4,
                SUM(CASE WHEN p.Entrada_Almacen = 1 THEN 1 ELSE 0 END) AS TotalConAlmacen,
                SUM(CASE WHEN p.Entrada_Almacen = 0 THEN 1 ELSE 0 END) AS TotalSinAlmacen
            FROM produccion p
            WHERE p.idContratistas = :idContratista
              AND p.Fecha_Liquidacion BETWEEN :fechaInicio AND :fechaFin
            GROUP BY p.Area, p.Division, p.Cope
            ORDER BY p.Area ASC";

    $params = array(
        ':idContratista' => $idContratista,
        ':fechaInicio'   => $fechaInicio,
        ':fechaFin'      => $fechaFin
    );

    // Se usa runQuery respetando el motor de base de datos de la clase
    return $this->runQuery($sql, $params, 'erpprint');
} */

public function ObtenerDetalleOrdenesPorArea($idContratista)
{
    $sql = "SELECT 
                p.NOM_AREA AS Area,
                p.NOM_DIVISION AS Division,
                p.NOM_CT AS Cope,
                
                /* ALTAS: Inicia con 'A' pero NO es 'A4' */
                SUM(CASE WHEN UPPER(TRIM(p.Tipo_de_Tarea)) LIKE 'A%' AND UPPER(TRIM(p.Tipo_de_Tarea)) != 'A4' THEN 1 ELSE 0 END) AS Altas,
                
                /* MIGRACION: Inicia con 'FC', 'TI', 'TS' o 'TV' */
                SUM(CASE WHEN UPPER(TRIM(p.Tipo_de_Tarea)) REGEXP '^(FC|TI|TS|TV)' THEN 1 ELSE 0 END) AS Migraciones,
                
                /* CAMBIOS DE DOMICILIO: Inicia con 'D' */
                SUM(CASE WHEN UPPER(TRIM(p.Tipo_de_Tarea)) LIKE 'D%' THEN 1 ELSE 0 END) AS CambiosDomicilio,
                
                /* QUEJAS: Inicia con 'Q', 'R', 'C', 'EB', 'EI', 'EU', 'GD', 'MG', 'F597' o 'F841' */
                SUM(CASE WHEN UPPER(TRIM(p.Tipo_de_Tarea)) REGEXP '^(Q|R|C|EB|EI|EU|GD|MG|F597|F841)' THEN 1 ELSE 0 END) AS Quejas,
                
                /* A4: Exactamente 'A4' */
                SUM(CASE WHEN UPPER(TRIM(p.Tipo_de_Tarea)) = 'A4' THEN 1 ELSE 0 END) AS A4,
                
                /* TOTAL CON ALMACÉN: Si al menos una de las dos series entró (1) */
                SUM(CASE WHEN (p.Existe_Serie_Prod_Almacen = 1 OR p.Existe_Serie_Eci_Almacen = 1) THEN 1 ELSE 0 END) AS TotalConAlmacen,
                
                /* TOTAL SIN ALMACÉN: Si ambas series están fuera de almacén (0) */
                SUM(CASE WHEN (p.Existe_Serie_Prod_Almacen = 0 AND p.Existe_Serie_Eci_Almacen = 0) THEN 1 ELSE 0 END) AS TotalSinAlmacen

            FROM seg_produccion_diaria p
            WHERE p.idContratista = :idContratista
            GROUP BY p.NOM_AREA, p.NOM_DIVISION, p.NOM_CT
            ORDER BY p.NOM_AREA ASC, p.NOM_CT ASC";

    $params = [':idContratista' => $idContratista];

    try {
        $resultado = $this->runQuery($sql, $params, 'erpprint');
        return is_array($resultado) ? $resultado : array();
    } catch (Exception $e) {
        error_log("Error en ObtenerDetalleOrdenesPorArea: " . $e->getMessage());
        return array();
    }
}




}




