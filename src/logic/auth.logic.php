<?php
require_once __DIR__ . '/../lib/funciones.php';
//require_once __DIR__ . '/mail.logic.php';

function procesar_login($post_data)
{
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
  }

  $email = $post_data['email'];
  $clave = $post_data['clave'];

  $db = conectarDb();
  $stmt = $db->prepare("
     SELECT 
      u.id,
      u.email,
      u.clave,
      u.nombre,
      u.apellido,
      p.id as personal_id,
      c.id as cliente_id,
      r.nombre as rol_nombre
    FROM Usuarios u
    LEFT JOIN Personal p ON p.usuarioId = u.id
    LEFT JOIN Clientes c ON c.usuarioId = u.id
    LEFT JOIN Roles r ON p.rolId = r.id
    WHERE u.email = ? AND u.activo = 1
  ");
  
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

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

    if ($password_valida) {
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
      
      header('Location: index.php');
      exit();
    }
  }
}

// aca deberia ir procesar_registro, procesar_validacion etc
