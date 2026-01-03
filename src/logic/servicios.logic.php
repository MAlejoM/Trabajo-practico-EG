<?php

require_once __DIR__ . '/../lib/funciones.php';

/**
 * Obtiene todos los roles disponibles en el sistema
 * @return array
 */
function get_all_roles()
{
    $db = conectarDb();
    $result = $db->query("SELECT * FROM roles ORDER BY nombre ASC");
    $roles = [];
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }
    $db->close();
    return $roles;
}

/**
 * Obtiene los IDs de los roles asignados a un servicio
 * @param int $servicio_id
 * @return array Lista de IDs de roles
 */
function get_roles_ids_by_servicio($servicio_id)
{
    $db = conectarDb();
    $stmt = $db->prepare("SELECT rolId FROM rolesservicios WHERE servicioId = ?");
    $stmt->bind_param("i", $servicio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $roles = [];
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['rolId'];
    }
    $stmt->close();
    $db->close();
    return $roles;
}

/**
 * Inserta un nuevo servicio
 * @param string $nombre
 * @param float $precio
 * @return int|false ID del nuevo servicio o false
 */
function insertar_servicio($nombre, $precio)
{
    $db = conectarDb();
    $stmt = $db->prepare("INSERT INTO servicios (nombre, precio, activo) VALUES (?, ?, 1)");
    $stmt->bind_param("sd", $nombre, $precio);
    $result = $stmt->execute();
    $id = $db->insert_id;
    $stmt->close();
    $db->close();
    return $result ? $id : false;
}

/**
 * Actualiza la información de un servicio
 * @param int $id
 * @param string $nombre
 * @param float $precio
 * @return bool
 */
function actualizar_servicio($id, $nombre, $precio)
{
    $db = conectarDb();
    $stmt = $db->prepare("UPDATE servicios SET nombre = ?, precio = ? WHERE id = ?");
    $stmt->bind_param("sdi", $nombre, $precio, $id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    return $result;
}

/**
 * Asigna roles a un servicio, eliminando las asignaciones previas
 * @param int $servicio_id
 * @param array $rol_ids
 * @return bool
 */
function asignar_roles_a_servicio($servicio_id, $rol_ids)
{
    $db = conectarDb();
    $db->begin_transaction();

    try {
        // Eliminar asignaciones existentes
        $stmt_del = $db->prepare("DELETE FROM rolesservicios WHERE servicioId = ?");
        $stmt_del->bind_param("i", $servicio_id);
        $stmt_del->execute();
        $stmt_del->close();

        // Insertar nuevas asignaciones
        if (!empty($rol_ids)) {
            $stmt_ins = $db->prepare("INSERT INTO rolesservicios (servicioId, rolId) VALUES (?, ?)");
            foreach ($rol_ids as $rol_id) {
                $stmt_ins->bind_param("ii", $servicio_id, $rol_id);
                $stmt_ins->execute();
            }
            $stmt_ins->close();
        }

        $db->commit();
        $db->close();
        return true;
    } catch (Exception $e) {
        $db->rollback();
        $db->close();
        return false;
    }
}

/**
 * Da de baja un servicio (baja lógica)
 * @param int $id ID del servicio
 * @return bool
 */
function dar_baja_servicio($id)
{
    $db = conectarDb();
    $stmt = $db->prepare("UPDATE servicios SET activo = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();

    return $result;
}

/**
 * Reactiva un servicio
 * @param int $id ID del servicio
 * @return bool
 */
function reactivar_servicio($id)
{
    $db = conectarDb();
    $stmt = $db->prepare("UPDATE servicios SET activo = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();

    return $result;
}
