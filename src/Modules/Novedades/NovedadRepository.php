<?php

namespace App\Modules\Novedades;

use App\Core\DB;

class NovedadRepository
{
    public static function getAll()
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT n.*, u.nombre as autorNombre, u.apellido as autorApellido 
            FROM novedades n
            INNER JOIN usuarios u ON n.usuarioId = u.id
            ORDER BY n.fechaPublicacion DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getById($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT n.*, u.nombre as autorNombre, u.apellido as autorApellido 
            FROM novedades n
            INNER JOIN usuarios u ON n.usuarioId = u.id
            WHERE n.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function create($titulo, $contenido, $imagen, $usuarioId)
    {
        $db = DB::getConn();
        // Nota: bind_param con 'b' (blob) es complejo en mysqli nativo sin send_long_data. 
        // Usaremos 's' (string) asumiendo que la imagen no excede max_allowed_packet, 
        // que es lo estándar para pequeñas imágenes.

        $sql = "INSERT INTO novedades (titulo, contenido, imagen, usuarioId) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        // El tipo 's' sirve para strings binarios también en la mayoría de configs modernas de mysqli
        $stmt->bind_param("sssi", $titulo, $contenido, $imagen, $usuarioId);

        $result = $stmt->execute();
        return $result ? $db->insert_id : false;
    }

    public static function update($id, $titulo, $contenido, $imagen = null)
    {
        $db = DB::getConn();

        if ($imagen !== null) {
            $stmt = $db->prepare("
                UPDATE novedades 
                SET titulo = ?, contenido = ?, imagen = ?
                WHERE id = ?
            ");
            $stmt->bind_param("sssi", $titulo, $contenido, $imagen, $id);
        } else {
            $stmt = $db->prepare("
                UPDATE novedades 
                SET titulo = ?, contenido = ?
                WHERE id = ?
            ");
            $stmt->bind_param("ssi", $titulo, $contenido, $id);
        }

        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("DELETE FROM novedades WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
