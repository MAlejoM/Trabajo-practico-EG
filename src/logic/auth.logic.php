<?php
require_once __DIR__ . '/../lib/funciones.php';

function procesar_login($post_data)
{
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
  }

  $dni = $post_data['dni'];
  $contrasenia = $post_data['contrasenia'];

  $db = conectarDb();
  $stmt = $db->prepare("SELECT * FROM datosUsuario WHERE dni = ? AND contrasenia = ?");
  $stmt->bind_param("ss", $dni, $contrasenia);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    session_start();
    $_SESSION['dni'] = $usuario['dni'];
    $_SESSION['rol'] = $usuario['rol'];

    header('Location: dashboard.php');
    exit();
  } else {
    header('Location: login.php?error=1');
    exit();
  }
}
// aca deberia ir procesar_registro, procesar_validacion etc
