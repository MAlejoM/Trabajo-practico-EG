<?php

require_once __DIR__ . '/../lib/funciones.php';
require_once __DIR__ . '/mail.logic.php';

if (!defined('RECOVERY_SECRET')) define('RECOVERY_SECRET', 'vet_recovery_secret_key_2024');

/**
 * Genera un token firmado con información de expiración (Tipo JWT simplificado)
 * 
 * @param int $usuario_id
 * @param string $email
 * @return string Token base64.firma
 */
function generar_token_recuperacion($usuario_id, $email)
{
  $payload = [
    'uid' => $usuario_id,
    'email' => $email,
    'exp' => time() + 3600
  ];

  $base64Payload = base64_encode(json_encode($payload));
  $signature = hash_hmac('sha256', $base64Payload, RECOVERY_SECRET);

  return $base64Payload . "." . $signature;
}

/**
 * Verifica el token y retorna el payload si es válido
 * 
 * @param string $token
 * @return array|false Payload decoded o false si es inválido/expirado
 */
function verificar_token_recuperacion($token)
{
  $partes = explode('.', $token);
  if (count($partes) !== 2) return false;

  list($base64Payload, $signatureRecibida) = $partes;


  $signatureEsperada = hash_hmac('sha256', $base64Payload, RECOVERY_SECRET);
  if (!hash_equals($signatureEsperada, $signatureRecibida)) {
    return false;
  }

  $payload = json_decode(base64_decode($base64Payload), true);


  if (!isset($payload['exp']) || $payload['exp'] < time()) {
    return false;
  }

  return $payload;
}

/**
 * Solicitar recuperación de contraseña
 * 
 * @param string $email Email del usuario
 * @return array ['success' => bool, 'message' => string]
 */
function solicitar_recuperacion($email)
{
  $db = conectarDb();

  $stmt = $db->prepare("SELECT id, nombre, apellido, activo FROM Usuarios WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    $stmt->close();
    $db->close();
    return [
      'success' => true,
      'message' => 'Si el email existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña.'
    ];
  }

  $usuario = $result->fetch_assoc();
  $stmt->close();

  if ($usuario['activo'] == 0) {
    $db->close();
    return [
      'success' => false,
      'message' => 'Usuario inactivo. Contacte con administración.'
    ];
  }

  $token = generar_token_recuperacion($usuario['id'], $email);
  $stmt = $db->prepare("UPDATE Usuarios SET recovery_token = ? WHERE id = ?");
  $stmt->bind_param("si", $token, $usuario['id']);

  if (!$stmt->execute()) {
    $stmt->close();
    $db->close();
    return ['success' => false, 'message' => 'Error al procesar la solicitud.'];
  }

  $stmt->close();
  $db->close();

  $nombre_completo = $usuario['nombre'] . ' ' . $usuario['apellido'];
  $resultado_email = enviar_email_recuperacion($email, $nombre_completo, $token);

  if ($resultado_email['success']) {
    return [
      'success' => true,
      'message' => 'Se ha enviado un email con instrucciones para recuperar tu contraseña.'
    ];
  } else {
    return [
      'success' => false,
      'message' => 'Error al enviar el email. Intenta de nuevo más tarde.'
    ];
  }
}

/**
 * Validar token de recuperación
 * 
 * @param string $token Token a validar
 * @return array ['valid' => bool, 'message' => string, 'usuario_id' => int, 'email' => string, 'nombre' => string]
 */
function validar_token($token)
{
  $payload = verificar_token_recuperacion($token);
  if (!$payload) {
    return ['valid' => false, 'message' => 'Token inválido o expirado. Solicita uno nuevo.'];
  }

  $db = conectarDb();

  $stmt = $db->prepare("
        SELECT id, email, nombre, apellido 
        FROM Usuarios 
        WHERE id = ? AND recovery_token = ? AND activo = 1
    ");
  $stmt->bind_param("is", $payload['uid'], $token);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    $stmt->close();
    $db->close();
    return ['valid' => false, 'message' => 'Este enlace ya no es válido porque se ha generado uno nuevo o la cuenta ya no está activa.'];
  }

  $data = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return [
    'valid' => true,
    'usuario_id' => $data['id'],
    'email' => $data['email'],
    'nombre' => $data['nombre'] . ' ' . $data['apellido']
  ];
}

/**
 * Resetear contraseña usando un token
 * 
 * @param string $token Token de recuperación
 * @param string $nueva_contrasena Nueva contraseña
 * @return array ['success' => bool, 'message' => string]
 */
function resetear_contrasena($token, $nueva_contrasena)
{
  $validacion = validar_token($token);
  if (!$validacion['valid']) {
    return ['success' => false, 'message' => $validacion['message']];
  }

  $db = conectarDb();

  $password_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

  $stmt = $db->prepare("UPDATE Usuarios SET clave = ?, recovery_token = NULL WHERE id = ?");
  $stmt->bind_param("si", $password_hash, $validacion['usuario_id']);

  if (!$stmt->execute()) {
    $stmt->close();
    $db->close();
    return ['success' => false, 'message' => 'Error al actualizar la contraseña.'];
  }

  $stmt->close();
  $db->close();

  enviar_email_confirmacion_cambio($validacion['email'], $validacion['nombre']);

  return [
    'success' => true,
    'message' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.'
  ];
}
