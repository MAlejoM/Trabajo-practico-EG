<?php

// Iniciar sesión de forma segura si no está iniciada.
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

//conexion bd
function conectarDb()
{
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', ''); // Dejar vacío si no tienes contraseña en XAMPP/WAMP
  define('DB_NAME', 'users');

  $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
  }
  return $connection;
}

//consigo rol por dni
function rol($dni)
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT rol FROM datosusuario WHERE dni = ?");
  $stmt->bind_param("s", $dni); // 's' porque DNI es un string (puede tener ceros delante)
  $stmt->execute();
  $result = $stmt->get_result();
  $usuario = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return $usuario ? $usuario['rol'] : null;
}

//consigo apelnom por id
function nombre($id)
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT nombre, apellido FROM datosusuario WHERE id = ?");
  $stmt->bind_param("i", $id); // 'i' porque el ID es un integer
  $stmt->execute();
  $result = $stmt->get_result();
  $usuario = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return $usuario ? $usuario['nombre'] . ' ' . $usuario['apellido'] : 'Profesional no encontrado';
}

//consigo servicio por id
function servicio($id)
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT * FROM servicios WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $servicio = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return $servicio;
}

//consigo data persona por dni
function persona($dni)
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT * FROM datosusuario WHERE dni = ?");
  $stmt->bind_param("s", $dni);
  $stmt->execute();
  $result = $stmt->get_result();
  $persona = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return $persona;
}