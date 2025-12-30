<?php

require_once __DIR__ . '/../lib/funciones.php';


// trae todos los usuarios con sus roles y tipos
function get_all_usuarios() {
    $db = conectarDb();
    $stmt = $db->prepare("
        SELECT 
            u.id,
            u.email,
            u.nombre,
            u.apellido,
            u.activo,
            p.id as personal_id,
            c.id as cliente_id,
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
        ORDER BY u.id ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $usuarios = array();
    
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    
    $stmt->close();
    $db->close();
    
    return $usuarios;
}

/**
 * Obtiene datos completos de un usuario específico por ID
 * @param int $usuario_id ID del usuario
 * @return array|null Datos del usuario o null si no existe
 */
function get_usuario_completo_by_id($usuario_id) {
    $db = conectarDb();
    $stmt = $db->prepare("
        SELECT 
            u.id,
            u.email,
            u.nombre,
            u.apellido,
            u.activo,
            p.id as personal_id,
            p.rolId as rol_id,
            c.id as cliente_id,
            c.telefono,
            c.direccion,
            c.ciudad,
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
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();
    $db->close();
    
    return $usuario;
}

/**
 * Actualiza datos de un usuario (solo para administrador)
 * @param int $usuario_id ID del usuario a actualizar
 * @param array $datos Datos a actualizar (email, nombre, apellido, activo)
 * @return bool True si se actualizó correctamente
 */
function update_usuario_admin($usuario_id, $datos) {
    $db = conectarDb();
    
    if (isset($datos['email'])) {
        $stmt = $db->prepare("SELECT id FROM Usuarios WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $datos['email'], $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            $db->close();
            return false; // Email duplicado
        }
        $stmt->close();
    }
    

    $stmt = $db->prepare("
        UPDATE Usuarios 
        SET email = ?, nombre = ?, apellido = ?, activo = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sssii",
        $datos['email'],
        $datos['nombre'],
        $datos['apellido'],
        $datos['activo'],
        $usuario_id
    );
    
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $result;
}

/**
 * Actualiza datos propios del personal (email, nombre, apellido)
 * @param int $usuario_id ID del usuario (debe ser el mismo que está logueado)
 * @param array $datos Datos a actualizar
 * @return bool True si se actualizó correctamente
 */

function update_usuario_personal($usuario_id, $datos) {
    $db = conectarDb();
    
    // Validar que el email no esté duplicado
    if (isset($datos['email'])) {
        $stmt = $db->prepare("SELECT id FROM Usuarios WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $datos['email'], $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            $db->close();
            return false;
        }
        $stmt->close();
    }
    
    $stmt = $db->prepare("
        UPDATE Usuarios 
        SET email = ?, nombre = ?, apellido = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sssi",
        $datos['email'],
        $datos['nombre'],
        $datos['apellido'],
        $usuario_id
    );
    
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $result;
}

/**
 * Cambia la contraseña de un usuario
 * @param int $usuario_id ID del usuario
 * @param string $clave_actual Contraseña actual (para validación)
 * @param string $nueva_clave Nueva contraseña
 * @return array Array con 'success' (bool) y 'mensaje' (string)
 */

function cambiar_contrasena($usuario_id, $clave_actual, $nueva_clave) {
    $db = conectarDb();
    
    // Obtener la clave actual hasheada
    $stmt = $db->prepare("SELECT clave FROM Usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
    $stmt->close();
    
    if (!$usuario) {
        $db->close();
        return ['success' => false, 'mensaje' => 'Usuario no encontrado'];
    }
    
    // Verificar contraseña actual
    // Soporta tanto contraseñas hasheadas como texto plano (para migración)
    $password_valida = false;
    
    // Primero intentar con password_verify (hash bcrypt)
    if (password_verify($clave_actual, $usuario['clave'])) {
        $password_valida = true;
    } 
    // Si falla, comparar como texto plano (para contraseñas legacy)
    elseif ($clave_actual === $usuario['clave']) {
        $password_valida = true;
    }
    
    if (!$password_valida) {
        $db->close();
        return ['success' => false, 'mensaje' => 'La contraseña actual es incorrecta'];
    }
    
    // Validar longitud de nueva contraseña
    if (strlen($nueva_clave) < 6) {
        $db->close();
        return ['success' => false, 'mensaje' => 'La nueva contraseña debe tener al menos 6 caracteres'];
    }
    
    // Hashear y actualizar nueva contraseña
    $nueva_clave_hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE Usuarios SET clave = ? WHERE id = ?");
    $stmt->bind_param("si", $nueva_clave_hash, $usuario_id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    
    if ($result) {
        return ['success' => true, 'mensaje' => 'Contraseña actualizada correctamente'];
    } else {
        return ['success' => false, 'mensaje' => 'Error al actualizar la contraseña'];
    }
}

/**
 * Valida si el usuario actual tiene permisos para editar a otro usuario
 * @param int $usuario_actual_id ID del usuario que intenta editar
 * @param int $usuario_editar_id ID del usuario a editar
 * @param string $rol_actual Rol del usuario actual
 * @return bool True si tiene permisos
 */

function validar_permisos_edicion($usuario_actual_id, $usuario_editar_id, $rol_actual) {
    // El administrador puede editar cualquier usuario
    if ($rol_actual === 'admin') {
        return true;
    }
    
    // El personal solo puede editar sus propios datos
    if ($usuario_actual_id == $usuario_editar_id) {
        return true;
    }
    
    return false;
}

/**
 * Actualiza datos de un cliente (teléfono y dirección)
 * Solo para uso del administrador
 * @param int $cliente_id ID del cliente
 * @param array $datos Datos a actualizar
 * @return bool True si se actualizó correctamente
 */

function update_cliente_datos($cliente_id, $datos) {
    $db = conectarDb();
    
    $stmt = $db->prepare("
        UPDATE Clientes 
        SET telefono = ?, direccion = ?, ciudad = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sssi",
        $datos['telefono'],
        $datos['direccion'],
        $datos['ciudad'],
        $cliente_id
    );
    
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $result;
}
?>
