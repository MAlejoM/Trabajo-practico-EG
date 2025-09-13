<?php

// Iniciar sesión de forma segura si no está iniciada.
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

//conexion bd
function conectarDb()
{
  if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
  if (!defined('DB_USER')) define('DB_USER', 'root');
  if (!defined('DB_PASS')) define('DB_PASS', '');

  if (!defined('DB_NAME')) define('DB_NAME', 'veterinaria_db');

  $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
  }
  return $connection;
}

//consigo rol por id
function get_personal_by_id($personal_id)
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT * FROM personal WHERE id = ?");
  $stmt->bind_param("i", $personal_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $personal = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return $personal;
}

//consigo apelnom por id
function get_cliente_by_id($cliente_id)
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT * FROM clientes WHERE id = ?");
  $stmt->bind_param("i", $cliente_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $cliente = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return $cliente;
}

//consigo servicio por id
function get_servicio_by_id($id)
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

function get_all_catalogo()
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT * FROM productos WHERE activo = 1");
  $stmt->execute();
  $result = $stmt->get_result();
  $catalogo = array();
  
  while ($row = $result->fetch_assoc()) {
    $catalogo[] = $row;
  }
  
  $stmt->close();
  $db->close();
  
  return $catalogo;
}
