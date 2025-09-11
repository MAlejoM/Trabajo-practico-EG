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
  $stmt = $db->prepare("SELECT id FROM personal WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  if ($stmt->get_result()->num_rows > 0) {
    return ['status' => 'error', 'message' => 'El Email ya se encuentra registrado.'];
  }
  $stmt->close();

  $password_hash = password_hash($password, PASSWORD_DEFAULT);

  $rol_cliente_id = 3; // no hay inserts en bd aun 

  $stmt_insert = $db->prepare("INSERT INTO personal (email, clave, rol_id) VALUES (?, ?, ?)");
  $stmt_insert->bind_param("ssi", $email, $password_hash, $rol_cliente_id);

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
  $stmt = $db->prepare("SELECT personal.id, personal.clave, roles.nombre as rol_nombre FROM personal JOIN roles ON personal.rol_id = roles.id WHERE personal.email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    if (password_verify($clave, $usuario['clave'])) {
      session_start();
      $_SESSION['id'] = $usuario['id'];
      $_SESSION['rol'] = $usuario['rol_nombre'];
      header('Location: dashboard.php');
      exit();
    }
  }
  header('Location: login.php?error=1');
  exit();
}

// aca deberia ir procesar_registro, procesar_validacion etc
