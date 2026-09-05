<?php

require_once __DIR__ . '/../cnx/cnx.php';

function esAdministradorContratista()
{
    return ($_SESSION['rol'] ?? '') === 'ADMIN';
}

function requerirSesionAlmacen($soloAdministrador = false)
{
    if (empty($_SESSION['idContratistas'])) {
        header('Location: ../Login/modal/poweroff.php');
        exit;
    }

    if ($soloAdministrador && !esAdministradorContratista()) {
        http_response_code(403);
        exit('Acceso no autorizado.');
    }
}

function obtenerIdContratistaContexto()
{
    $idUsuario = (int)($_SESSION['idContratistas'] ?? 0);

    if (!esAdministradorContratista()) {
        return $idUsuario;
    }

    return (int)($_SESSION['contratistaSeleccionado'] ?? 0);
}

function obtenerContratistasParaSelector()
{
    $conexion = (new Conexion())->get_conexion();
    $sql = "SELECT idContratistas, CONCAT(Contratista, ' ', apellido_paterno, ' ', apellido_materno) AS nombre
            FROM contratistas
            WHERE COALESCE(Estatus_Vigente, 'ACTIVO') <> 'BAJA'
            ORDER BY Contratista, apellido_paterno, apellido_materno";
    $stmt = $conexion->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function cambiarContratistaContexto($idContratista)
{
    if (!esAdministradorContratista()) {
        return false;
    }

    $idContratista = (int)$idContratista;
    if ($idContratista < 0) {
        return false;
    }

    if ($idContratista > 0) {
        $conexion = (new Conexion())->get_conexion();
        $stmt = $conexion->prepare('SELECT idContratistas FROM contratistas WHERE idContratistas = :id LIMIT 1');
        $stmt->execute(['id' => $idContratista]);
        if (!$stmt->fetchColumn()) {
            return false;
        }
    }

    $_SESSION['contratistaSeleccionado'] = $idContratista;
    return true;
}
