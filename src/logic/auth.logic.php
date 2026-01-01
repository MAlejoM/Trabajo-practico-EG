<?php
require_once __DIR__ . '/../lib/funciones.php';
//require_once __DIR__ . '/mail.logic.php';

function procesar_login($post_data)
{
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return null;
  }

  $email = $post_data['email'];
  $clave = $post_data['clave'];

  $db = conectarDb();
  
  // Buscar usuario sin filtrar por activo (necesitamos verificar el estado)
  $stmt = $db->prepare("
     SELECT 
      u.id,
      u.email,
      u.clave,
      u.nombre,
      u.apellido,
      u.activo,
      p.id as personal_id,
      c.id as cliente_id,
      r.nombre as rol_nombre
    FROM Usuarios u
    LEFT JOIN Personal p ON p.usuarioId = u.id
    LEFT JOIN Clientes c ON c.usuarioId = u.id
    LEFT JOIN Roles r ON p.rolId = r.id
    WHERE u.email = ?
  ");
  
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  // Si no existe el usuario, mensaje genérico por seguridad
  if ($result->num_rows === 0) {
    $stmt->close();
    $db->close();
    header('Location: login.php?error=1');
    exit();
  }

  $usuario = $result->fetch_assoc();
  $stmt->close();

  // Verificar si el usuario está inactivo (antes de validar contraseña)
  // Mensaje genérico por seguridad - no revelamos si existe pero está inactivo
  if ($usuario['activo'] == 0) {
    $db->close();
    header('Location: login.php?error=1');
    exit();
  }

  // Verificar contraseña - soporta tanto hash como texto plano (compatibilidad)
  $password_valida = false;
  
  // Primero intentar con password_verify (contraseñas hasheadas - seguro)
  if (password_verify($clave, $usuario['clave'])) {
    $password_valida = true;
  } 
  // Si falla, comparar como texto plano (legacy - para migración)
  elseif ($clave === $usuario['clave']) {
    $password_valida = true;
  }

  if (!$password_valida) {
    $db->close();
    header('Location: login.php?error=1');
    exit();
  }

  // Crear sesión solo si todas las validaciones pasaron
  session_start();
  $_SESSION['usuarioId'] = $usuario['id'];
  $_SESSION['nombre'] = $usuario['nombre'];
  $_SESSION['apellido'] = $usuario['apellido'];
  
  if ($usuario['personal_id']) {
    $_SESSION['personal_id'] = $usuario['personal_id'];
    $_SESSION['rol'] = $usuario['rol_nombre'];
  }
  
  if ($usuario['cliente_id']) {
    $_SESSION['cliente_id'] = $usuario['cliente_id'];
  }
  
  $db->close();
  header('Location: index.php');
  exit();
}

// aca deberia ir procesar_registro, procesar_validacion etc
