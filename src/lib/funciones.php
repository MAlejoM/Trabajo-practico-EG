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

function get_all_atenciones()
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      a.id,
      a.fechaHora,
      a.motivo,
      a.estado,
      m.nombre as nombre_mascota,
      u.nombre as nombre_cliente,
      u.apellido as apellido_cliente
    FROM atenciones a
    JOIN mascotas m ON a.id_mascota = m.id
    JOIN clientes c ON m.cliente_id = c.id
    JOIN usuarios u ON c.usuarioId = u.id
    ORDER BY a.fecha DESC
  ");
  $stmt->execute();
  $result = $stmt->get_result();
  $atenciones = array();
  
  while ($row = $result->fetch_assoc()) {
    $atenciones[] = $row;
  }
  
  $stmt->close();
  $db->close();
  
  return $atenciones;
}

function get_atenciones_by_fecha($fecha)
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      a.id,
      a.fecha,
      a.motivo,
      a.estado,
      m.nombre as nombre_mascota,
      u.nombre as nombre_cliente,
      u.apellido as apellido_cliente
    FROM atenciones a
    JOIN mascotas m ON a.id_mascota = m.id
    JOIN clientes c ON m.clienteId = c.id
    JOIN usuarios u ON c.usuarioId = u.id
    WHERE DATE(a.fecha) = ?
    ORDER BY a.fecha ASC
  ");
  $stmt->bind_param("s", $fecha);
  $stmt->execute();
  $result = $stmt->get_result();
  $atenciones = array();
  
  while ($row = $result->fetch_assoc()) {
    $atenciones[] = $row;
  }
  
  $stmt->close();
  $db->close();
  
  return $atenciones;
}

function get_all_mascotas()
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      m.id,
      m.nombre,
      m.raza,
      m.color,
      m.foto,
      m.fechaDeNac,
      m.fechaMuerte,
      m.activo,
      u.nombre as nombre_cliente,
      u.apellido as apellido_cliente
    FROM mascotas m
    JOIN clientes c ON m.clienteId = c.id
    JOIN usuarios u ON c.usuarioId = u.id
    ORDER BY m.nombre ASC
  ");
  $stmt->execute();
  $result = $stmt->get_result();
  $mascotas = array();
  
  while ($row = $result->fetch_assoc()) {
    $mascotas[] = $row;
  }
  
  $stmt->close();
  $db->close();
  
  return $mascotas;
}

function get_cliente_completo_by_id($cliente_id)
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      c.id,
      u.nombre,
      u.apellido,
    FROM clientes c
    JOIN usuarios u ON c.usuarioId = u.id
    WHERE c.id = ? AND u.activo = 1
  ");
  $stmt->bind_param("i", $cliente_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $cliente = $result->fetch_assoc();
  $stmt->close();
  $db->close();
  
  return $cliente;
}

function get_all_clientes()
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      c.id,
      u.nombre,
      u.apellido
    FROM clientes c
    JOIN usuarios u ON c.usuarioId = u.id
    WHERE u.activo = 1
    ORDER BY u.nombre ASC
  ");
  $stmt->execute();
  $result = $stmt->get_result();
  $clientes = array();
  
  while ($row = $result->fetch_assoc()) {
    $clientes[] = $row;
  }
  
  $stmt->close();
  $db->close();
  
  return $clientes;
}
