<?php

/**
 * Sistema de Envío de Emails - Veterinaria San Antón
 * Usa PHPMailer para enviar emails via Gmail SMTP
 */

require_once __DIR__ . '/../config.php';

// Cargar PHPMailer manualmente (instalación sin Composer)
$phpmailer_path = __DIR__ . '/../../vendor/PHPMailer/src/';
if (file_exists($phpmailer_path . 'PHPMailer.php')) {
    require_once $phpmailer_path . 'PHPMailer.php';
    require_once $phpmailer_path . 'Exception.php';
    require_once $phpmailer_path . 'SMTP.php';
    define('PHPMAILER_AVAILABLE', true);
} else {
    define('PHPMAILER_AVAILABLE', false);
}

// Use statements deben estar a nivel de archivo (fuera de bloques condicionales)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Enviar email de recuperación de contraseña
 * 
 * @param string $email Email del destinatario
 * @param string $nombre Nombre completo del usuario
 * @param string $token Token de recuperación (64 caracteres)
 * @return array ['success' => bool, 'message' => string]
 */
function enviar_email_recuperacion($email, $nombre, $token)
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $resetLink = $protocol . $host . "/public/reset_password.php?token=" . $token;

    $subject = "Recuperación de Contraseña - Veterinaria San Antón";
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
            .button { display: inline-block; background: #28a745; color: white !important; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { background: #f1f1f1; padding: 15px; text-align: center; color: #666; font-size: 12px; border-radius: 0 0 5px 5px; }
            .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2 style='margin:0'>🔐 Recuperación de Contraseña</h2>
            </div>
            <div class='content'>
                <p>Hola <strong>{$nombre}</strong>,</p>
                <p>Recibimos una solicitud para restablecer tu contraseña en <strong>Veterinaria San Antón</strong>.</p>
                <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
                <p style='text-align: center;'>
                    <a href='{$resetLink}' class='button'>Restablecer Contraseña</a>
                </p>
                <p>O copia y pega este enlace en tu navegador:</p>
                <p style='word-break: break-all; background: #fff; padding: 10px; border: 1px solid #ddd;'>{$resetLink}</p>
                <div class='warning'>
                    <p style='margin:0'><strong>⚠️ Importante:</strong></p>
                    <ul style='margin: 10px 0 0 0; padding-left: 20px;'>
                        <li>Este enlace expirará en <strong>1 hora</strong></li>
                        <li>Solo puedes usarlo <strong>una vez</strong></li>
                        <li>Si no solicitaste este cambio, ignora este email</li>
                    </ul>
                </div>
            </div>
            <div class='footer'>
                <p style='margin:0'>Veterinaria San Antón</p>
                <p style='margin:5px 0 0 0'>Este es un email automático, por favor no respondas.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    return enviar_email($email, $subject, $message);
}

/**
 * Enviar confirmación de cambio de contraseña
 * 
 * @param string $email Email del destinatario
 * @param string $nombre Nombre completo del usuario
 * @return array ['success' => bool, 'message' => string]
 */
function enviar_email_confirmacion_cambio($email, $nombre)
{
    $fecha_hora = date('d/m/Y H:i');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';

    $subject = "Contraseña Cambiada - Veterinaria San Antón";
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
            .footer { background: #f1f1f1; padding: 15px; text-align: center; color: #666; font-size: 12px; border-radius: 0 0 5px 5px; }
            .info-box { background: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; }
            .alert-box { background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2 style='margin:0'>✅ Contraseña Actualizada</h2>
            </div>
            <div class='content'>
                <p>Hola <strong>{$nombre}</strong>,</p>
                <p>Tu contraseña ha sido <strong>cambiada exitosamente</strong>.</p>
                <div class='info-box'>
                    <p style='margin:0'><strong>Detalles del cambio:</strong></p>
                    <ul style='margin: 10px 0 0 0; padding-left: 20px;'>
                        <li><strong>Fecha y hora:</strong> {$fecha_hora}</li>
                        <li><strong>Dirección IP:</strong> {$ip}</li>
                    </ul>
                </div>
                <div class='alert-box'>
                    <p style='margin:0'><strong>⚠️ ¿No fuiste tú?</strong></p>
                    <p style='margin: 10px 0 0 0;'>Si no realizaste este cambio, tu cuenta podría estar comprometida. Contacta inmediatamente con el administrador.</p>
                </div>
            </div>
            <div class='footer'>
                <p style='margin:0'>Veterinaria San Antón</p>
                <p style='margin:5px 0 0 0'>Este es un email automático, por favor no respondas.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    return enviar_email($email, $subject, $message);
}

/**
 * Función base para enviar emails usando PHPMailer
 * 
 * @param string $destinatario Email del destinatario
 * @param string $asunto Asunto del email
 * @param string $mensaje_html Contenido HTML del email
 * @return array ['success' => bool, 'message' => string]
 */
function enviar_email($destinatario, $asunto, $mensaje_html)
{
    // Verificar que PHPMailer esté disponible
    if (!PHPMAILER_AVAILABLE) {
        error_log("PHPMailer no está instalado. Ejecuta: composer require phpmailer/phpmailer");
        return [
            'success' => false,
            'message' => 'Sistema de email no configurado. Contacta al administrador.'
        ];
    }

    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = MAILHOST;             // Servidor SMTP (smtp.gmail.com)
        $mail->SMTPAuth = true;             // Habilitar autenticación SMTP
        $mail->Username = USERNAME;         // Email de la cuenta
        $mail->Password = PASSWORD;         // Contraseña de aplicación de Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Encriptación TLS
        $mail->Port = 587;                  // Puerto para TLS
        $mail->CharSet = 'UTF-8';           // Codificación de caracteres

        // Desactivar verificación SSL en desarrollo (solo para pruebas locales)
        if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
        }

        // Configuración del remitente y destinatario
        $mail->setFrom(SEND_FROM, SEND_FROM_NAME);
        $mail->addAddress($destinatario);
        $mail->addReplyTo(REPLY_TO, REPLY_TO_NAME);

        // Contenido del email
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje_html;
        $mail->AltBody = strip_tags($mensaje_html);  // Versión texto plano

        // Enviar
        $mail->send();

        return [
            'success' => true,
            'message' => 'Email enviado correctamente'
        ];
    } catch (Exception $e) {
        $errorMsg = "Error al enviar email: " . $mail->ErrorInfo;
        error_log($errorMsg);

        return [
            'success' => false,
            'message' => 'Error al enviar el email. Intenta de nuevo más tarde.'
        ];
    }
}

/**
 * Función de prueba para verificar configuración de email
 * Solo para desarrollo - eliminar en producción
 * 
 * @param string $email_prueba Email de prueba
 * @return array Resultado del envío
 */
function test_email_config($email_prueba = null)
{
    if (!$email_prueba) {
        return ['success' => false, 'message' => 'Debes proporcionar un email de prueba'];
    }

    $subject = "Test - Configuración de Email";
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>✅ Configuración de Email Correcta</h2>
        <p>Este es un email de prueba para verificar que la configuración de PHPMailer funciona correctamente.</p>
        <p><strong>Si estás leyendo esto, todo funciona perfecto!</strong></p>
        <hr>
        <p style='color:#666; font-size:12px'>Veterinaria San Antón - Sistema de Emails</p>
    </body>
    </html>
    ";

    return enviar_email($email_prueba, $subject, $message);
}
