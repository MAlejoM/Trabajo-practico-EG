<?php
require_once __DIR__ . '/../lib/funciones.php';
//require_once __DIR__ . '/mail.logic.php';

function procesar_registro($post_data)
{
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return null;
  }

  $email = trim($post_data['email']);
  $password = $post_data['password'];
  $password_dup = $post_data['passwordDuplicada'];

  if ($password !== $password_dup) {
    return ['status' => 'error', 'message' => 'Las contraseñas no coinciden.'];
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return ['status' => 'error', 'message' => 'El formato del email no es válido.'];
  }


  $db = conectarDb();
  $stmt = $db->prepare("SELECT id FROM Usuarios WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  if ($stmt->get_result()->num_rows > 0) {
    return ['status' => 'error', 'message' => 'El Email ya se encuentra registrado.'];
  }
  $stmt->close();

  $password_hash = password_hash($password, PASSWORD_DEFAULT);
  
  // Start transaction since we need to insert into multiple tables
  $db->begin_transaction();
  
  try {
    // Insert into Usuarios first
    $stmt_insert = $db->prepare("INSERT INTO Usuarios (email, clave, nombre, apellido) VALUES (?, ?, '', '')");
    $stmt_insert->bind_param("ss", $email, $password_hash);
    $stmt_insert->execute();
    $usuario_id = $db->insert_id;

    // Insert into Clientes table (assuming this is for client registration)
    $stmt_cliente = $db->prepare("INSERT INTO Clientes (id, usuarioId) VALUES (?, ?)");
    $stmt_cliente->bind_param("ii", $usuario_id, $usuario_id);
    $stmt_cliente->execute();

    $db->commit();
    return ['status' => 'success', 'message' => 'Registro completado. Ya puedes iniciar sesión con tu email y clave.'];
  } catch (Exception $e) {
    $db->rollback();
    return ['status' => 'error', 'message' => 'Error al registrar el usuario: ' . $e->getMessage()];
  }

  if ($stmt_insert->execute()) {
    return ['status' => 'success', 'message' => 'Registro completado. Ya puedes iniciar sesión con tu email y calve.'];
  } else {
    return ['status' => 'error', 'message' => 'Error al registrar el usuario.'];
  }
}

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
      COALESCE(r.nombre, 'cliente') as rol_nombre,
      CASE 
        WHEN c.id IS NOT NULL THEN 'cliente'
        WHEN p.id IS NOT NULL THEN 'personal'
      END as tipo_usuario,
      COALESCE(p.id, c.id) as perfil_id
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

    if (password_verify($clave, $usuario['clave'])) {
      session_start();
      $_SESSION['id'] = $usuario['id'];
      $_SESSION['perfil_id'] = $usuario['perfil_id'];
      $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
      $_SESSION['rol'] = $usuario['rol_nombre'];
      $_SESSION['nombre'] = $usuario['nombre'];
      $_SESSION['apellido'] = $usuario['apellido'];
      header('Location: dashboard.php');
      exit();
    }
  }
  header('Location: login.php?error=1');
  exit();
}

// aca deberia ir procesar_registro, procesar_validacion etc
