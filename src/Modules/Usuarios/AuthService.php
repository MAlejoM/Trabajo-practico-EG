<?php

namespace App\Modules\Usuarios;

use App\Core\DB;
use Exception;

class AuthService
{
    public static function login($email, $clave)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT u.*, p.id as personal_id, c.id as cliente_id, r.nombre as rol_nombre
            FROM Usuarios u
            LEFT JOIN Personal p ON p.usuarioId = u.id
            LEFT JOIN Clientes c ON c.usuarioId = u.id
            LEFT JOIN Roles r ON p.rolId = r.id
            WHERE u.email = ? AND u.activo = 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) return false;

        // Soporte para contraseñas legacy y hash
        if (password_verify($clave, $user['clave']) || $clave === $user['clave']) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['usuarioId'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['apellido'] = $user['apellido'];
            if ($user['personal_id']) {
                $_SESSION['personal_id'] = $user['personal_id'];
                $_SESSION['rol'] = $user['rol_nombre'];
            }
            if ($user['cliente_id']) {
                $_SESSION['cliente_id'] = $user['cliente_id'];
            }
            return true;
        }
        return false;
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
    }
}
