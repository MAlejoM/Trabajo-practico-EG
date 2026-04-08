<?php

namespace App\Modules\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private static function crearMailer(): PHPMailer
    {
        if (!defined('MAILHOST')) {
            require_once PROJECT_ROOT . '/src/config.php';
        }

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = MAILHOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = USERNAME;
        $mail->Password   = PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SEND_FROM, SEND_FROM_NAME);
        $mail->addReplyTo(REPLY_TO, REPLY_TO_NAME);
        $mail->isHTML(true);

        return $mail;
    }

    public static function enviar(string $destinatario, string $asunto, string $mensajeHtml): bool
    {
        try {
            $mail = self::crearMailer();
            $mail->addAddress($destinatario);
            $mail->Subject = $asunto;
            $mail->Body    = $mensajeHtml;
            $mail->AltBody = strip_tags($mensajeHtml);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("[MailService] Error al enviar email a {$destinatario}: " . $e->getMessage());
            return false;
        }
    }

    public static function enviarRecuperacion(string $email, string $nombre, string $token): bool
    {
        $protocolo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $resetUrl  = $protocolo . '://' . $host . '/auth/reset_password.php?token=' . urlencode($token);

        $asunto = 'Recuperación de contraseña - Veterinaria San Antón';

        $nombreSeguro   = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $resetUrlSeguro = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #0d6efd; color: white; padding: 20px; text-align: center;">
                <h1 style="margin: 0;">Veterinaria San Antón</h1>
            </div>
            <div style="padding: 30px; background-color: #f8f9fa;">
                <h2>Hola {$nombreSeguro},</h2>
                <p>Recibimos una solicitud para restablecer tu contraseña.</p>
                <p>Hacé clic en el siguiente botón para crear una nueva contraseña:</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{$resetUrlSeguro}"
                       style="background-color: #0d6efd; color: white; padding: 12px 30px;
                              text-decoration: none; border-radius: 5px; font-size: 16px;">
                        Restablecer Contraseña
                    </a>
                </div>
                <p style="color: #6c757d; font-size: 14px;">
                    Este enlace expira en <strong>1 hora</strong>.<br>
                    Si no solicitaste este cambio, podés ignorar este email.
                </p>
                <hr style="border-color: #dee2e6;">
                <p style="color: #6c757d; font-size: 12px;">
                    Si el botón no funciona, copiá y pegá este enlace en tu navegador:<br>
                    <a href="{$resetUrlSeguro}">{$resetUrlSeguro}</a>
                </p>
            </div>
        </div>
        HTML;

        return self::enviar($email, $asunto, $html);
    }

    public static function enviarConfirmacionCambio(string $email, string $nombre): bool
    {
        $asunto = 'Contraseña actualizada - Veterinaria San Antón';

        $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #198754; color: white; padding: 20px; text-align: center;">
                <h1 style="margin: 0;">Veterinaria San Antón</h1>
            </div>
            <div style="padding: 30px; background-color: #f8f9fa;">
                <h2>Hola {$nombreSeguro},</h2>
                <p>Tu contraseña fue actualizada correctamente.</p>
                <p>Si no realizaste este cambio, contactanos de inmediato.</p>
                <p style="color: #6c757d; font-size: 12px; margin-top: 30px;">
                    Este es un email automático, por favor no lo respondas.
                </p>
            </div>
        </div>
        HTML;

        return self::enviar($email, $asunto, $html);
    }

    public static function enviarBienvenida(string $email, string $nombre, string $claveTextoPlano): bool
    {
        $protocolo   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host        = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $loginUrl    = $protocolo . '://' . $host . '/auth/login.php';

        $asunto = 'Bienvenido/a a Veterinaria San Antón - Tus credenciales de acceso';

        $nombreSeguro   = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $emailSeguro    = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $claveSegura    = htmlspecialchars($claveTextoPlano, ENT_QUOTES, 'UTF-8');
        $loginUrlSeguro = htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background-color: #0d6efd; color: white; padding: 20px; text-align: center;">
                <h1 style="margin: 0;">Veterinaria San Antón</h1>
            </div>
            <div style="padding: 30px; background-color: #f8f9fa;">
                <h2>¡Bienvenido/a, {$nombreSeguro}!</h2>
                <p>Tu cuenta ha sido creada exitosamente. A continuación encontrás tus credenciales para ingresar al sistema:</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <td style="padding: 10px; background-color: #e9ecef; font-weight: bold; width: 40%;">Usuario (email):</td>
                        <td style="padding: 10px; background-color: #ffffff;">{$emailSeguro}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; background-color: #e9ecef; font-weight: bold;">Contraseña:</td>
                        <td style="padding: 10px; background-color: #ffffff;">{$claveSegura}</td>
                    </tr>
                </table>
                <p>Te recomendamos cambiar tu contraseña luego de tu primer ingreso.</p>
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{$loginUrlSeguro}"
                       style="background-color: #0d6efd; color: white; padding: 12px 30px;
                              text-decoration: none; border-radius: 5px; font-size: 16px;">
                        Iniciar Sesión
                    </a>
                </div>
                <p style="color: #6c757d; font-size: 12px; margin-top: 30px;">
                    Este es un email automático, por favor no lo respondas.
                </p>
            </div>
        </div>
        HTML;

        return self::enviar($email, $asunto, $html);
    }
}
