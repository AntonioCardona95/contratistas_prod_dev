<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/cnx/cnx.php");

class Nomina extends Conexion
{
  


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

// id concepto //
private function obtenerIdConcepto(
    int $idEmulador,
    string $tipoInstalacion,
    int $metraje
): int {

    $conexion = $this->get_conexion();

    $rango = $this->obtenerRangoMetraje($metraje);

    // Normaliza el texto recibido
    $tipoInstalacion = strtoupper(trim($tipoInstalacion));

    // Detectar tipo
    if ($tipoInstalacion === 'SUBTERRANEA') {
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

// INSERTAR FOLIOS PACO PARA COBRE
public function insertarFoliosPacoCobre(
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

    // Buscar el concepto fijo de COBRE
    $stmtConcepto = $cnx->prepare("
        SELECT idConcepto
        FROM conceptos
        WHERE nombre = 'Plusvalia por tramo adicional de 50m. con bajante de 1 par'
        LIMIT 1
    ");
    $stmtConcepto->execute();
    $idConceptoCobre = $stmtConcepto->fetchColumn();

    if (!$idConceptoCobre) {
        throw new Exception("No se encontró concepto COBRE: Plusvalia por tramo adicional de 50m. con bajante de 1 par");
    }

    foreach ($filas as $f) {
        $stmt->execute([
            ':folio_paco'        => $folioPaco,
            ':semana'            => $semana,
            ':folio_pisa'        => $f['folio_pisa'],
            ':idContratista'     => $idContratista,
            ':nombre_tecnico'    => $f['tecnico'],
            ':idEmulador'        => $idEmulador,
            ':idConcepto'        => $idConceptoCobre, // ← siempre el fijo
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
            ':Expediente'        => $f['Expediente'],
            ':Distrito'          => $f['Distrito'],
            ':num_serie'         => $f['num_serie'],
            ':Area'              => $f['Area'],
            ':Division'          => $f['Division'],
            ':Direccion'         => $f['Direccion'],
            ':Cliente'           => !empty($f['Cliente']) ? $f['Cliente'] : null
        ]);
    }

    $cnx->commit();
}



}
