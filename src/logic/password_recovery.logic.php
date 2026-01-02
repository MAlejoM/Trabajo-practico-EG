<?php

/**
 * Sistema de Recuperación de Contraseña
 * Gestiona tokens de recuperación y cambio de contraseña
 */

require_once __DIR__ . '/../lib/funciones.php';
require_once __DIR__ . '/mail.logic.php';

/**
 * Solicitar recuperación de contraseña
 * 
 * @param string $email Email del usuario
 * @return array ['success' => bool, 'message' => string]
 */
function solicitar_recuperacion($email)
{
  $db = conectarDb();

  // Verificar que el usuario existe y está activo
  $stmt = $db->prepare("SELECT id, nombre, apellido, activo FROM Usuarios WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    $stmt->close();
    $db->close();
    // Por seguridad, no revelar si el email existe o no
    return [
      'success' => true,
      'message' => 'Si el email existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña.'
    ];
  }

  $usuario = $result->fetch_assoc();
  $stmt->close();

  // Verificar que el usuario esté activo
  if ($usuario['activo'] == 0) {
    $db->close();
    return [
      'success' => false,
      'message' => 'Usuario inactivo. Contacte con administración.'
    ];
  }

  // Generar token único de 64 caracteres
  $token = bin2hex(random_bytes(32));
  $token_hash = hash('sha256', $token);
  $expira_en = date('Y-m-d H:i:s', strtotime('+1 hour'));
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

  // Invalidar tokens anteriores del usuario
  $stmt = $db->prepare("UPDATE password_reset_tokens SET usado = 1 WHERE usuario_id = ? AND usado = 0");
  $stmt->bind_param("i", $usuario['id']);
  $stmt->execute();
  $stmt->close();

  // Insertar nuevo token
  $stmt = $db->prepare("
        INSERT INTO password_reset_tokens (usuario_id, token, token_hash, expira_en, ip_solicitud)
        VALUES (?, ?, ?, ?, ?)
    ");
  $stmt->bind_param("issss", $usuario['id'], $token, $token_hash, $expira_en, $ip);

  if (!$stmt->execute()) {
    $stmt->close();
    $db->close();
    return ['success' => false, 'message' => 'Error al procesar la solicitud.'];
  }

  $stmt->close();
  $db->close();

  // Enviar email con el token
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
  $db = conectarDb();
  $token_hash = hash('sha256', $token);

  $stmt = $db->prepare("
        SELECT t.id, t.usuario_id, t.expira_en, t.usado, u.email, u.nombre, u.apellido
        FROM password_reset_tokens t
        JOIN Usuarios u ON t.usuario_id = u.id
        WHERE t.token = ? AND t.token_hash = ?
    ");
  $stmt->bind_param("ss", $token, $token_hash);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    $stmt->close();
    $db->close();
    return ['valid' => false, 'message' => 'Token inválido o no encontrado.'];
  }

  $data = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  // Verificar expiración
  if (strtotime($data['expira_en']) < time()) {
    return ['valid' => false, 'message' => 'El token ha expirado. Solicita uno nuevo.'];
  }

  // Verificar que no haya sido usado
  if ($data['usado'] == 1) {
    return ['valid' => false, 'message' => 'El token ya ha sido utilizado.'];
  }

  return [
    'valid' => true,
    'usuario_id' => $data['usuario_id'],
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
  // Validar token primero
  $validacion = validar_token($token);
  if (!$validacion['valid']) {
    return ['success' => false, 'message' => $validacion['message']];
  }

  $db = conectarDb();

  // Hashear la nueva contraseña
  $password_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

  // Actualizar la contraseña del usuario
  $stmt = $db->prepare("UPDATE Usuarios SET clave = ? WHERE id = ?");
  $stmt->bind_param("si", $password_hash, $validacion['usuario_id']);

  if (!$stmt->execute()) {
    $stmt->close();
    $db->close();
    return ['success' => false, 'message' => 'Error al actualizar la contraseña.'];
  }

  $stmt->close();

  // Marcar el token como usado
  $token_hash = hash('sha256', $token);
  $stmt = $db->prepare("UPDATE password_reset_tokens SET usado = 1 WHERE token = ? AND token_hash = ?");
  $stmt->bind_param("ss", $token, $token_hash);
  $stmt->execute();
  $stmt->close();

  $db->close();

  // Enviar email de confirmación
  enviar_email_confirmacion_cambio($validacion['email'], $validacion['nombre']);

  return [
    'success' => true,
    'message' => 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.'
  ];
}

/**
 * Limpiar tokens expirados (ejecutar periódicamente)
 * 
 * @return int Número de tokens eliminados
 */
function limpiar_tokens_expirados()
{
  $db = conectarDb();
  $stmt = $db->prepare("DELETE FROM password_reset_tokens WHERE expira_en < NOW() OR usado = 1");
  $stmt->execute();
  $affected = $stmt->affected_rows;
  $stmt->close();
  $db->close();

  return $affected;
}
