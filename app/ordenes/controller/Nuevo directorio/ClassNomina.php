<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/cnx/cnx.php");

class Nomina extends Conexion
{
  public function insertarNomina($emuladorNombre, $semana, $tecnologia, $idContratista, $filas)
{
    $conexion = $this->get_conexion();
    $conexion->beginTransaction();

    try {
        // 1️⃣ Obtener idEmulador
        $stmt = $conexion->prepare("SELECT idEmulador FROM emulador WHERE nombre = :nombre LIMIT 1");
        $stmt->execute([':nombre' => $emuladorNombre]);
        $idEmulador = $stmt->fetchColumn();

        if (!$idEmulador) {
            throw new Exception("Emulador no encontrado: $emuladorNombre");
        }

        // 2️⃣ Crear folio único en folio_paco
        $folioPacoStr = $emuladorNombre . '_' . $semana;

        // 2a️⃣ Revisar si ya existe en folio_paco
        $stmt = $conexion->prepare("SELECT idFolioPaco FROM folio_paco WHERE folioPaco = :folioPaco LIMIT 1");
        $stmt->execute([':folioPaco' => $folioPacoStr]);
        $idFolioPaco = $stmt->fetchColumn();

        if (!$idFolioPaco) {
            // Insertar nuevo folio
            $stmt = $conexion->prepare("
                INSERT INTO folio_paco (folioPaco, idContratista, semana, tecnologia) 
                VALUES (:folioPaco, :idContratista, :semana, :tecnologia)
            ");
            $stmt->execute([
                ':folioPaco'      => $folioPacoStr,
                ':idContratista'  => $idContratista,
                ':semana'         => $semana,
                ':tecnologia'     => $tecnologia
            ]);
            $idFolioPaco = $conexion->lastInsertId();
        }

        // 3️⃣ Insertar en folios_produccion (sin folioPaco)
        $sql = "
            INSERT INTO folios_produccion
            (
                idFolioPaco,
                folio_pisa,
                idContratista,
                nombre_tecnico,
                idEmulador,
                tipo_tarea,
                tipo_instalacion,
                fecha,
                telefono_cliente,
                terminal,
                puerto,
                latitud,
                longitud,
                cope
            )
            VALUES
            (
                :idFolioPaco,
                :folio_pisa,
                :idContratista,
                :nombre_tecnico,
                :idEmulador,
                :tipo_tarea,
                :tipo_instalacion,
                :fecha,
                :telefono,
                :terminal,
                :puerto,
                :latitud,
                :longitud,
                :cope
            )
        ";
        $stmt = $conexion->prepare($sql);

        foreach ($filas as $fila) {
            $stmt->execute([
                ':idFolioPaco'      => $idFolioPaco,
                ':folio_pisa'       => $fila['folio_pisa'] ?? null,
                ':idContratista'    => $idContratista,
                ':nombre_tecnico'   => $fila['tecnico'] ?? null,
                ':idEmulador'       => $idEmulador,
                ':tipo_tarea'       => $fila['tipo_tarea'] ?? null,
                ':tipo_instalacion' => $fila['tipo_instalacion'] ?? null,
                ':fecha'            => isset($fila['fecha_liquidacion']) ? substr($fila['fecha_liquidacion'], 0, 10) : null,
                ':telefono'         => $fila['telefono'] ?? null,
                ':terminal'         => $fila['terminal'] ?? null,
                ':puerto'           => $fila['puerto'] ?? null,
                ':latitud'          => $fila['latitud'] ?? null,
                ':longitud'         => $fila['longitud'] ?? null,
                ':cope'             => $fila['cope'] ?? null
            ]);
        }

        $conexion->commit();
        return ['status' => 'ok', 'folioPaco' => $folioPacoStr, 'total' => count($filas)];

    } catch (Exception $e) {
        $conexion->rollBack();
        return ['status' => 'error', 'mensaje' => $e->getMessage()];
    }
}


// OBTENER FOLIO DEL EMULADOR //

public function obtenerIdEmuladorPorNombre(string $nombre)
{
    $cnx = $this->get_conexion();

    $stmt = $cnx->prepare("
        SELECT idEmulador
        FROM emulador
        WHERE nombre = :nombre
        LIMIT 1
    ");
    $stmt->execute([
        ':nombre' => $nombre
    ]);

    return $stmt->fetchColumn();
}

private function obtenerRangoMetraje(int $metraje): int
{
    if ($metraje <= 25) return 25;
    if ($metraje <= 50) return 50;
    if ($metraje <= 75) return 75;
    if ($metraje <= 100) return 100;
    if ($metraje <= 125) return 125;
    if ($metraje <= 150) return 150;
    if ($metraje <= 175) return 175;
    if ($metraje <= 200) return 200;
    if ($metraje <= 250) return 250;
    return 300;
}

// id concepto //

private function obtenerIdConcepto(
    int $idEmulador,
    string $tipoInstalacion,
    int $metraje
): int {

    $conexion = $this->get_conexion();

    $rango = $this->obtenerRangoMetraje($metraje);

    // Detectar tipo
    if (stripos($tipoInstalacion, 'SUBTERRANEO') !== false) {
        $like = "%Bajante subterraneo de {$rango}m%";
    } else {
        $like = "%Bajante aereo de {$rango}m%";
    }

    $sql = "
        SELECT idConcepto
        FROM conceptos
        WHERE idEmulador = :idEmulador
        AND nombre LIKE :nombre
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':idEmulador' => $idEmulador,
        ':nombre'     => $like
    ]);

    $id = $stmt->fetchColumn();

    if (!$id) {
        throw new Exception("No se encontró concepto para {$like}");
    }

    return (int)$id;
}




// INSERTAR FOLIOS PACO //

public function insertarFoliosPaco(
    $folioPaco,
    $idContratista,
    $idEmulador,
    $semana,
    $tecnologia,
    array $filas
) {
    $cnx = $this->get_conexion();
    $cnx->beginTransaction();

    $sql = "
        INSERT INTO foliospaco (
            Folio_Paco,
            semana,
            folio_pisa,
            idContratista,
            nombre_tecnico,
            idEmulador,
            idConcepto,
            tipo_tarea,
            tipo_instalacion,
            Fecha_Liquidacion,
            Fecha_Procesado,
            Estatus,
            Tecnologia,
            Metraje,
            telefono_cliente,
            terminal,
            puerto,
            latitud,
            longitud,
            cope,
            Expediente,
            Distrito,
            num_serie,
            Area,
            Division,
            Direccion,
            Cliente

        ) VALUES (
            :folio_paco,
            :semana,
            :folio_pisa,
            :idContratista,
            :nombre_tecnico,
            :idEmulador,
            :idConcepto,
            :tipo_tarea,
            :tipo_instalacion,
            :fecha_liquidacion,
            NOW(),
            'PENDIENTE',
            :tecnologia,
            :metraje,
            :telefono,
            :terminal,
            :puerto,
            :latitud,
            :longitud,
            :cope,
            :Expediente,
            :Distrito,
            :num_serie,
            :Area,
            :Division,
            :Direccion,
            :Cliente
        )
    ";

    $stmt = $cnx->prepare($sql);

    foreach ($filas as $f) {

        //  OBTENER CONCEPTO AQUÍ
        $idConcepto = $this->obtenerIdConcepto(
            $idEmulador,
            $f['tipo_instalacion'],
            (int)$f['metraje']
        );

        $stmt->execute([
            ':folio_paco'        => $folioPaco,
            ':semana'            => $semana,
            ':folio_pisa'        => $f['folio_pisa'],
            ':idContratista'     => $idContratista,
            ':nombre_tecnico'    => $f['tecnico'],
            ':idEmulador'        => $idEmulador,
            ':idConcepto'        => $idConcepto,
            ':tipo_tarea'        => $f['tipo_tarea'],
            ':tipo_instalacion'  => $f['tipo_instalacion'],
            ':fecha_liquidacion' => $f['fecha_liquidacion'] ?: null,
            ':tecnologia'        => $tecnologia,
            ':metraje'           => $f['metraje'] ?: null,
            ':telefono'          => $f['telefono'],
            ':terminal'          => $f['terminal'],
            ':puerto'            => $f['puerto'],
            ':latitud'           => $f['latitud'],
            ':longitud'          => $f['longitud'],
            ':cope'              => $f['cope'],
            ':Expediente'              => $f['Expediente'],
            ':Distrito'              => $f['Distrito'],
            ':num_serie'              => $f['num_serie'],
            ':Area'              => $f['Area'],
            ':Division'              => $f['Division'],
            ':Direccion'              => $f['Direccion'],
            ':Cliente' => !empty($f['Cliente']) ? $f['Cliente'] : null




        ]);
    }

    $cnx->commit();
}


/*public function insertarFoliosPaco(
        $folioPaco,
        $idContratista,
        $idEmulador,
        $semana,
        $tecnologia,
        array $filas
    ) {
        $cnx = $this->get_conexion();
        $cnx->beginTransaction();

        $sql = "
            INSERT INTO foliospaco (
                Folio_Paco,
                semana,
                folio_pisa,
                idContratista,
                nombre_tecnico,
                idEmulador,
                tipo_tarea,
                tipo_instalacion,
                Fecha_Liquidacion,
                Fecha_Procesado,
                Estatus,
                Tecnologia,
                Metraje,
                telefono_cliente,
                terminal,
                puerto,
                latitud,
                longitud,
                cope
            ) VALUES (
                :folio_paco,
                :semana,
                :folio_pisa,
                :idContratista,
                :nombre_tecnico,
                :idEmulador,
                :tipo_tarea,
                :tipo_instalacion,
                :fecha_liquidacion,
                NOW(),
                'PENDIENTE',
                :tecnologia,
                :metraje,
                :telefono,
                :terminal,
                :puerto,
                :latitud,
                :longitud,
                :cope
            )
        ";

        $stmt = $cnx->prepare($sql);

        foreach ($filas as $f) {

            $stmt->execute([
                ':folio_paco'        => $folioPaco,
                ':semana'            => $semana,
                ':folio_pisa'        => $f['folio_pisa'],
                ':idContratista'     => $idContratista,
                ':nombre_tecnico'    => $f['tecnico'],
                ':idEmulador'        => $idEmulador,
                ':tipo_tarea'        => $f['tipo_tarea'],
                ':tipo_instalacion'  => $f['tipo_instalacion'],
                ':fecha_liquidacion' => $f['fecha_liquidacion'] ?: null,
                ':tecnologia'        => $tecnologia,
                ':metraje'           => $f['metraje'] ?: null,
                ':telefono'          => $f['telefono'],
                ':terminal'          => $f['terminal'],
                ':puerto'            => $f['puerto'],
                ':latitud'           => $f['latitud'],
                ':longitud'          => $f['longitud'],
                ':cope'              => $f['cope']
            ]);
        }

        $cnx->commit();
    } */



}
