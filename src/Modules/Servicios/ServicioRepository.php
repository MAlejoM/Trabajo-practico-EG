<?php

namespace App\Modules\Servicios;

use App\Core\DB;

class ServicioRepository
{
    public static function getServiciosByPersonalId($personalId)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("SELECT s.* 
            FROM servicios s
            JOIN rolesServicios rs ON s.id = rs.servicioId
            JOIN personal p ON rs.rolId = p.rolId
            WHERE p.id = ? AND s.activo = 1
            ORDER BY s.nombre ASC");
        $stmt->bind_param("i", $personalId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getAll($mostrarInactivos = false)
    {
        $db = DB::getConn();
        $filtro_activo = $mostrarInactivos ? "" : "WHERE activo = 1";
        $stmt = $db->prepare("SELECT * FROM servicios $filtro_activo ORDER BY nombre ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getById($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("SELECT * FROM servicios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function create($nombre, $precio)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("INSERT INTO servicios (nombre, precio, activo) VALUES (?, ?, 1)");
        $stmt->bind_param("sd", $nombre, $precio);
        $result = $stmt->execute();
        return $result ? $db->insert_id : false;
    }

    public static function update($id, $nombre, $precio)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE servicios SET nombre = ?, precio = ? WHERE id = ?");
        $stmt->bind_param("sdi", $nombre, $precio, $id);
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE servicios SET activo = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public static function reactivate($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("UPDATE servicios SET activo = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Gestión de Roles asignados al servicio
    public static function getRolesIds($servicioId)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("SELECT rolId FROM rolesservicios WHERE servicioId = ?");
        $stmt->bind_param("i", $servicioId);
        $stmt->execute();
        $result = $stmt->get_result();
        $roles = [];
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row['rolId'];
        }
        return $roles;
    }

    public static function assignRoles($servicioId, $rolIds)
    {
        $db = DB::getConn();
        $db->begin_transaction();
        try {
            // Eliminar existentes
            $stmt = $db->prepare("DELETE FROM rolesservicios WHERE servicioId = ?");
            $stmt->bind_param("i", $servicioId);
            $stmt->execute();

            // Insertar nuevos
            if (!empty($rolIds)) {
                $stmtIns = $db->prepare("INSERT INTO rolesservicios (servicioId, rolId) VALUES (?, ?)");
                foreach ($rolIds as $rolId) {
                    $stmtIns->bind_param("ii", $servicioId, $rolId);
                    $stmtIns->execute();
                }
            }
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollback();
            return false;
        }
    }

    // Helper temporal (debería ir en RolRepository)
    public static function getAllRoles()
    {
        $db = DB::getConn();
        $result = $db->query("SELECT * FROM roles ORDER BY nombre ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
