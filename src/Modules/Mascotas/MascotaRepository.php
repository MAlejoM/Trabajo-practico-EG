<?php

namespace App\Modules\Mascotas;

use App\Core\DB;

class MascotaRepository
{
    public static function getAll($mostrarInactivas = false)
    {
        $db = DB::getConn();
        $filtro_activo = $mostrarInactivas ? "" : "WHERE m.activo = 1";

        $sql = "
            SELECT 
                m.id,
                m.nombre,
                m.raza,
                m.color,
                m.foto,
                m.fechaDeNac,
                m.fechaMuerte,
                m.activo,
                u.nombre as nombre_cliente,
                u.apellido as apellido_cliente
            FROM mascotas m
            JOIN clientes c ON m.clienteId = c.id
            JOIN usuarios u ON c.usuarioId = u.id
            $filtro_activo
            ORDER BY m.nombre ASC
        ";

        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getByClienteId($clienteId)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT 
                m.id,
                m.nombre,
                m.raza,
                m.color,
                m.foto,
                m.fechaDeNac,
                m.fechaMuerte,
                m.activo
            FROM mascotas m
            WHERE m.clienteId = ? AND m.activo = 1
            ORDER BY m.nombre ASC
        ");
        $stmt->bind_param("i", $clienteId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getByClienteDni($dni)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT 
                m.id,
                m.nombre,
                m.raza,
                m.color,
                m.foto,
                m.fechaDeNac,
                m.fechaMuerte,
                m.activo
            FROM mascotas m
            JOIN clientes c ON m.clienteId = c.id
            JOIN usuarios u ON c.usuarioId = u.id
            WHERE u.dni = ? AND m.activo = 1
            ORDER BY m.nombre ASC
        ");
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getById($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT 
                m.*,
                u.nombre as nombre_cliente,
                u.apellido as apellido_cliente
            FROM mascotas m
            LEFT JOIN clientes c ON m.clienteId = c.id
            LEFT JOIN usuarios u ON c.usuarioId = u.id
            WHERE m.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function search($termino)
    {
        $db = DB::getConn();
        $search = "%" . $termino . "%";
        $stmt = $db->prepare("
            SELECT 
                m.id,
                m.clienteId,
                m.nombre,
                m.raza,
                m.color,
                m.foto,
                m.fechaDeNac,
                m.fechaMuerte,
                m.activo,
                u.nombre as nombre_cliente,
                u.apellido as apellido_cliente
            FROM mascotas m
            JOIN clientes c ON m.clienteId = c.id
            JOIN usuarios u ON c.usuarioId = u.id
            WHERE m.nombre LIKE ? OR u.nombre LIKE ? OR u.apellido LIKE ?
            ORDER BY m.nombre ASC
        ");
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function create($clienteId, $nombre, $raza, $color, $fechaDeNac, $fotoBlob = null)
    {
        $db = DB::getConn();
        if ($fotoBlob) {
            $stmt = $db->prepare("
                INSERT INTO mascotas (clienteId, nombre, raza, color, fechaDeNac, foto, activo) 
                VALUES (?, ?, ?, ?, ?, ?, 1)
            ");
            // 'b' para blob, pero usaremos 's' como en Novedades si es string binario, 
            // o send_long_data si es muy grande. Por simpleza y tamaño < 2MB, 's' suele funcionar en drivers modernos.
            // Si falla, usaremos send_long_data.
            $null = null;
            $stmt->bind_param("isssss", $clienteId, $nombre, $raza, $color, $fechaDeNac, $null);
            $stmt->send_long_data(5, $fotoBlob);
        } else {
            $stmt = $db->prepare("
                INSERT INTO mascotas (clienteId, nombre, raza, color, fechaDeNac, activo) 
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $stmt->bind_param("issss", $clienteId, $nombre, $raza, $color, $fechaDeNac);
        }

        $result = $stmt->execute();
        return $result ? $db->insert_id : false;
    }

    public static function update($id, $nombre, $raza, $color, $fechaNac, $fechaMuerte, $activo, $fotoBlob = null)
    {
        $db = DB::getConn();
        if ($fotoBlob !== null) {
            $stmt = $db->prepare("
                UPDATE mascotas 
                SET nombre = ?, raza = ?, color = ?, fechaDeNac = ?, fechaMuerte = ?, activo = ?, foto = ?
                WHERE id = ?
            ");
            $null = null;
            $stmt->bind_param("sssssibi", $nombre, $raza, $color, $fechaNac, $fechaMuerte, $activo, $null, $id);
            $stmt->send_long_data(6, $fotoBlob);
        } else {
            $stmt = $db->prepare("
                UPDATE mascotas 
                SET nombre = ?, raza = ?, color = ?, fechaDeNac = ?, fechaMuerte = ?, activo = ?
                WHERE id = ?
            ");
            $stmt->bind_param("sssssii", $nombre, $raza, $color, $fechaNac, $fechaMuerte, $activo, $id);
        }

        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE mascotas SET activo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public static function reactivate($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE mascotas SET activo = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
