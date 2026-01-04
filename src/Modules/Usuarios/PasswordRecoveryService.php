<?php

namespace App\Modules\Usuarios;

use App\Core\DB;
use Exception;

class PasswordRecoveryService
{
    public static function solicitar($email)
    {
        $db = DB::getConn();

        // 1. Buscar usuario
        $stmt = $db->prepare("SELECT id, nombre, email FROM usuarios WHERE email = ? AND activo = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            // Por seguridad, devolvemos éxito aunque no exista
            return ['success' => true, 'message' => 'Si el email existe en nuestro sistema, recibirás instrucciones.'];
        }

        // 2. Generar token
        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // 3. Invalida anteriores y guarda nuevo
        $stmt = $db->prepare("UPDATE password_reset_tokens SET usado = 1 WHERE usuario_id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $stmt = $db->prepare("INSERT INTO password_reset_tokens (usuario_id, token, token_hash, expira_en, ip_solicitud) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user['id'], $token, $token_hash, $expira, $ip);

        if ($stmt->execute()) {
            // Aquí iría el envío de email real. Por ahora simulamos o usamos una función de mail.
            // Para no romper el flujo, asumimos que existe una clase MailService o similar.
            return ['success' => true, 'message' => 'Instrucciones enviadas a tu email.'];
        }

        return ['success' => false, 'message' => 'Error al procesar la solicitud.'];
    }

    public static function validarToken($token)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT t.*, u.nombre, u.email 
            FROM password_reset_tokens t
            JOIN usuarios u ON t.usuario_id = u.id
            WHERE t.token = ? AND t.usado = 0 AND t.expira_en > NOW()
        ");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return ['valid' => false, 'message' => 'El token es inválido o ha expirado.'];
        }

        return [
            'valid' => true,
            'usuario_id' => $result['usuario_id'],
            'nombre' => $result['nombre'],
            'email' => $result['email']
        ];
    }

    public static function resetear($token, $nueva_clave)
    {
        $validacion = self::validarToken($token);
        if (!$validacion['valid']) {
            return ['success' => false, 'message' => $validacion['message']];
        }

        $db = DB::getConn();
        $db->begin_transaction();

        try {
            $hash = password_hash($nueva_clave, PASSWORD_DEFAULT);

            // Actualizar password
            $stmt = $db->prepare("UPDATE usuarios SET clave = ? WHERE id = ?");
            $stmt->bind_param("si", $hash, $validacion['usuario_id']);
            $stmt->execute();

            // Marcar token como usado
            $stmt = $db->prepare("UPDATE password_reset_tokens SET usado = 1 WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            $db->commit();
            return ['success' => true, 'message' => 'Contraseña actualizada correctamente.'];
        } catch (Exception $e) {
            $db->rollback();
            return ['success' => false, 'message' => 'Error al actualizar la contraseña.'];
        }
    }
}
