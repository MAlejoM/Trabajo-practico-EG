<?php

namespace App\Modules\Atenciones;

use App\Core\DB;

class AtencionRepository
{
    public static function getAll()
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT 
                a.id,
                a.fechaHora,
                a.titulo,
                a.descripcion,
                a.estado,
                a.personalId,
                m.nombre as nombre_mascota,
                uc.nombre as nombre_cliente,
                uc.apellido as apellido_cliente,
                up.nombre as nombre_personal,
                up.apellido as apellido_personal
            FROM atenciones a
            JOIN mascotas m ON a.mascotaId = m.id
            JOIN clientes c ON m.clienteId = c.id
            JOIN usuarios uc ON c.usuarioId = uc.id
            JOIN personal p ON a.personalId = p.id
            JOIN usuarios up ON p.usuarioId = up.id
            ORDER BY a.fechaHora DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getByFecha($fecha)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT 
                a.id,
                a.fechaHora as fecha,
                a.titulo as motivo,
                m.nombre as nombre_mascota,
                u.nombre as nombre_cliente,
                u.apellido as apellido_cliente
            FROM atenciones a
            JOIN mascotas m ON a.mascotaId = m.id
            JOIN clientes c ON m.clienteId = c.id
            JOIN usuarios u ON c.usuarioId = u.id
            WHERE DATE(a.fechaHora) = ?  
            ORDER BY a.fechaHora ASC
        ");
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getByMascotaId($mascotaId)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("SELECT * FROM atenciones WHERE mascotaId = ? ORDER BY fechaHora DESC");
        $stmt->bind_param("i", $mascotaId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getById($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT 
                a.*,
                m.nombre as nombre_mascota,
                uc.nombre as nombre_cliente,
                uc.apellido as apellido_cliente,
                up.nombre as nombre_personal,
                up.apellido as apellido_personal
            FROM atenciones a
            JOIN mascotas m ON a.mascotaId = m.id
            JOIN clientes c ON m.clienteId = c.id
            JOIN usuarios uc ON c.usuarioId = uc.id
            JOIN personal p ON a.personalId = p.id
            JOIN usuarios up ON p.usuarioId = up.id
            WHERE a.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function search($termino, $fecha = '')
    {
        $db = DB::getConn();
        $search = "%" . $termino . "%";

        $whereClause = "WHERE (m.nombre LIKE ? OR uc.nombre LIKE ? OR uc.apellido LIKE ? OR a.titulo LIKE ? OR a.descripcion LIKE ?)";
        $types = "sssss";
        $params = [$search, $search, $search, $search, $search];

        if (!empty($fecha)) {
            $whereClause .= " AND DATE(a.fechaHora) = ?";
            $types .= "s";
            $params[] = $fecha;
        }

        $sql = "
            SELECT 
                a.id,
                a.fechaHora,
                a.titulo,
                a.descripcion,
                a.estado,
                a.personalId,
                m.nombre as nombre_mascota,
                uc.nombre as nombre_cliente,
                uc.apellido as apellido_cliente,
                up.nombre as nombre_personal,
                up.apellido as apellido_personal
            FROM atenciones a
            JOIN mascotas m ON a.mascotaId = m.id
            JOIN clientes c ON m.clienteId = c.id
            JOIN usuarios uc ON c.usuarioId = uc.id
            JOIN personal p ON a.personalId = p.id
            JOIN usuarios up ON p.usuarioId = up.id
            $whereClause
            ORDER BY a.fechaHora DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function create($clienteId, $mascotaId, $personalId, $fechaHora, $titulo, $servicioId, $descripcion)
    {
        $db = DB::getConn();
        $servicioId = (!empty($servicioId)) ? $servicioId : null;

        // Nota: clienteId se pasa al insert, está en la tabla atenciones? 
        // Sí, en el código original: INSERT INTO atenciones (clienteId, ...)
        // Aunque esté redundante con mascotaId -> clienteId, si la tabla lo tiene, hay que llenarlo.

        $stmt = $db->prepare("
            INSERT INTO atenciones (clienteId, mascotaId, personalId, fechaHora, titulo, servicioId, descripcion) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiissis", $clienteId, $mascotaId, $personalId, $fechaHora, $titulo, $servicioId, $descripcion);
        $result = $stmt->execute();
        return $result ? $db->insert_id : false;
    }

    public static function update($id, $titulo, $descripcion, $servicioId, $personalId, $fechaHora, $estado)
    {
        $db = DB::getConn();
        $servicioId = (!empty($servicioId)) ? $servicioId : null;

        $stmt = $db->prepare("
            UPDATE atenciones 
            SET titulo = ?, descripcion = ?, servicioId = ?, personalId = ?, fechaHora = ?, estado = ? 
            WHERE id = ?
        ");
        $stmt->bind_param("ssiissi", $titulo, $descripcion, $servicioId, $personalId, $fechaHora, $estado, $id);
        return $stmt->execute();
    }

    public static function updateEstado($id, $estado)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE atenciones SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $estado, $id);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("DELETE FROM atenciones WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
