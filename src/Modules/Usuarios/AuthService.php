<?php

namespace App\Modules\Usuarios;

use App\Core\DB;
use App\Core\SessionHandler;
use Exception;

class AuthService
{
    public static function login($email, $clave)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT u.*, p.id as personal_id, c.id as cliente_id, r.nombre as rol_nombre
            FROM usuarios u
            LEFT JOIN personal p ON p.usuarioId = u.id
            LEFT JOIN clientes c ON c.usuarioId = u.id
            LEFT JOIN roles r ON p.rolId = r.id
            WHERE u.email = ? AND u.activo = 1
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) return false;

        // Soporte para contraseñas legacy y hash
        if (password_verify($clave, $user['clave']) || $clave === $user['clave']) {
            SessionHandler::poblar($user);
            return true;
        }
        return false;
    }

    public static function logout()
    {
        SessionHandler::destruir();
    }
}
