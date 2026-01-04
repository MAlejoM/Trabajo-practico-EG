<?php

namespace App\Modules\Usuarios;

use App\Core\DB;

class UsuarioRepository
{
    public static function getAll($mostrarInactivos = false)
    {
        $db = DB::getConn();
        $filtro_activo = $mostrarInactivos ? "" : "WHERE u.activo = 1";

        $sql = "
            SELECT 
                u.id, u.email, u.nombre, u.apellido, u.activo,
                p.id as personal_id, c.id as cliente_id,
                CASE 
                    WHEN r.nombre IS NOT NULL THEN r.nombre
                    WHEN c.id IS NOT NULL THEN 'Cliente'
                    ELSE 'Sin Rol'
                END as rol_nombre,
                CASE 
                    WHEN p.id IS NOT NULL THEN 'Personal'
                    WHEN c.id IS NOT NULL THEN 'Cliente'
                    ELSE 'Desconocido'
                END as tipo_usuario
            FROM Usuarios u
            LEFT JOIN Personal p ON p.usuarioId = u.id
            LEFT JOIN Clientes c ON c.usuarioId = u.id
            LEFT JOIN Roles r ON p.rolId = r.id
            $filtro_activo
            ORDER BY u.id ASC
        ";

        $result = $db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getById($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("SELECT * FROM Usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function getUsuarioCompletoById($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT 
                u.id, u.email, u.nombre, u.apellido, u.activo,
                p.id as personal_id, p.rolId as rol_id,
                c.id as cliente_id, c.telefono, c.direccion, c.ciudad,
                r.nombre as rol_nombre,
                CASE 
                    WHEN p.id IS NOT NULL THEN 'Personal'
                    WHEN c.id IS NOT NULL THEN 'Cliente'
                    ELSE 'Desconocido'
                END as tipo_usuario
            FROM Usuarios u
            LEFT JOIN Personal p ON p.usuarioId = u.id
            LEFT JOIN Clientes c ON c.usuarioId = u.id
            LEFT JOIN Roles r ON p.rolId = r.id
            WHERE u.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function getByEmail($email, $excludeId = null)
    {
        $db = DB::getConn();
        if ($excludeId) {
            $stmt = $db->prepare("SELECT id FROM Usuarios WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $excludeId);
        } else {
            $stmt = $db->prepare("SELECT id FROM Usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function create($data)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("INSERT INTO Usuarios (email, nombre, apellido, clave, activo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssi",
            $data['email'],
            $data['nombre'],
            $data['apellido'],
            $data['clave'],
            $data['activo']
        );
        if ($stmt->execute()) {
            return $db->insert_id;
        }
        return false;
    }

    public static function update($id, $data)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE Usuarios SET email = ?, nombre = ?, apellido = ?, activo = ? WHERE id = ?");
        $stmt->bind_param(
            "sssii",
            $data['email'],
            $data['nombre'],
            $data['apellido'],
            $data['activo'],
            $id
        );
        return $stmt->execute();
    }

    public static function updatePassword($id, $hashedPassword)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE Usuarios SET clave = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $id);
        return $stmt->execute();
    }

    public static function setEstado($id, $activo)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE Usuarios SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $activo, $id);
        return $stmt->execute();
    }

    // --- CLIENTE ESPECIFICO ---
    public static function getClienteById($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("SELECT * FROM Clientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getAllClientes()
    {
        $db = DB::getConn();
        $sql = "
            SELECT c.id, u.nombre, u.apellido
            FROM Clientes c
            JOIN Usuarios u ON c.usuarioId = u.id
            WHERE u.activo = 1
            ORDER BY u.nombre ASC
        ";
        return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public static function updateCliente($id, $data)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE Clientes SET telefono = ?, direccion = ?, ciudad = ? WHERE id = ?");
        $stmt->bind_param("sssi", $data['telefono'], $data['direccion'], $data['ciudad'], $id);
        return $stmt->execute();
    }

    // --- PERSONAL ESPECIFICO ---
    public static function getPersonalById($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("SELECT * FROM Personal WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function getAllPersonal($soloActivos = true)
    {
        $db = DB::getConn();
        $filtro = $soloActivos ? "WHERE p.activo = 1" : "";
        $sql = "
            SELECT p.id, u.nombre, u.apellido
            FROM Personal p
            JOIN Usuarios u ON p.usuarioId = u.id
            $filtro
            ORDER BY u.nombre ASC
        ";
        return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
