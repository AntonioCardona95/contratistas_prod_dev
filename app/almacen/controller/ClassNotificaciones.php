<?php

require_once(__DIR__ . '/../../cnx/cnx.php');

class Notificaciones extends Conexion
{
    protected function runQuery($sql, $params = array())
    {
        $rows = array();
        $conexion = $this->get_conexion();

        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                $param = ':' . ltrim($key, ':');
                $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (method_exists($stmt, 'closeCursor')) {
                $stmt->closeCursor();
            }
        } catch (PDOException $e) {
            error_log("[Notificaciones::runQuery] Error: " . $e->getMessage() . " SQL: " . $sql);
            return array();
        }

        return is_array($rows) ? $rows : array();
    }

    protected function runExecute($sql, $params = array())
    {
        $conexion = $this->get_conexion();
        try {
            $stmt = $conexion->prepare($sql);
            foreach ($params as $key => $val) {
                $param = ':' . ltrim($key, ':');
                $stmt->bindValue($param, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("[Notificaciones::runExecute] Error: " . $e->getMessage() . " SQL: " . $sql);
            return false;
        }
    }

    // INSERTAR NOTIFICACION
    public function Insertar($idEmisor, $idReceptor, $tipo, $mensaje, $urlDestino, $folioReferencia)
    {
        $sql = "INSERT INTO notificaciones_almacen 
                (id_emisor, id_receptor, tipo_notificacion, mensaje, url_destino, folio_referencia)
                VALUES (:id_emisor, :id_receptor, :tipo, :mensaje, :url_destino, :folio)";

        return $this->runExecute($sql, [
            'id_emisor' => $idEmisor,
            'id_receptor' => $idReceptor,
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'url_destino' => $urlDestino,
            'folio' => $folioReferencia
        ]);
    }

    // CONTAR NO LEIDAS (PARA EL CONTADOR DE LA CAMPANA)
    public function ContarNoLeidas($idUsuario)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM notificaciones_almacen 
                WHERE id_receptor = :id_usuario AND leido = 0";

        $resultado = $this->runQuery($sql, ['id_usuario' => $idUsuario]);
        return $resultado[0]['total'] ?? 0;
    }

    // OBTENER LAS NUEVAS DESDE EL ULTIMO ID VISTO (PARA EL TOAST DEL POLLING)
    public function ObtenerNuevas($idUsuario, $desdeId)
    {
        $sql = "SELECT id, mensaje, url_destino, tipo_notificacion, fecha_creacion
                FROM notificaciones_almacen 
                WHERE id_receptor = :id_usuario 
                  AND id > :desde_id 
                ORDER BY id ASC";

        return $this->runQuery($sql, [
            'id_usuario' => $idUsuario,
            'desde_id' => $desdeId
        ]);
    }

    // OBTENER LAS ULTIMAS N NOTIFICACIONES PARA POBLAR EL DROPDOWN AL CARGAR LA PAGINA (SIN TOAST)
    public function ObtenerRecientes($idUsuario, $limite = 10)
    {
        $sql = "SELECT id, mensaje, url_destino, tipo_notificacion, leido, fecha_creacion
                FROM notificaciones_almacen 
                WHERE id_receptor = :id_usuario 
                AND leido = 0
                ORDER BY id DESC 
                LIMIT " . (int)$limite;

        return $this->runQuery($sql, ['id_usuario' => $idUsuario]);
    }

    // MARCAR UNA NOTIFICACION COMO LEIDA
    public function MarcarComoLeida($idNotificacion)
    {
        $sql = "UPDATE notificaciones_almacen 
                SET leido = 1, fecha_leido = NOW() 
                WHERE id = :id";

        return $this->runExecute($sql, ['id' => $idNotificacion])
            ? "Marcado como leído"
            : "Error al marcar como leído";
    }
}