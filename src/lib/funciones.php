<?php

include_once __DIR__ . "/../includes/error_handler.php";

// Iniciar sesión de forma segura si no está iniciada.
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/**
 * ==========================================
 * BASE DE DATOS
 * ==========================================
 */

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

/**
 * ==========================================
 * USUARIOS Y SEGURIDAD
 * ==========================================
 */

function get_usuario_by_id($id)
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $usuario = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return $usuario;
}

function verificar_es_admin()
{
  return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

/**
 * ==========================================
 * CLIENTES
 * ==========================================
 */

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

function get_cliente_completo_by_id($cliente_id)
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      c.id,
      u.nombre,
      u.apellido
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

/**
 * ==========================================
 * PERSONAL
 * ==========================================
 */

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

function get_all_personal()
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      p.id,
      u.nombre,
      u.apellido
    FROM personal p
    JOIN usuarios u ON p.usuarioId = u.id
    WHERE p.activo = 1
    ORDER BY u.nombre ASC
  ");
  $stmt->execute();
  $result = $stmt->get_result();
  $personal = array();

  while ($row = $result->fetch_assoc()) {
    $personal[] = $row;
  }

  $stmt->close();
  $db->close();

  return $personal;
}

/**
 * ==========================================
 * MASCOTAS
 * ==========================================
 */

function get_all_mascotas($mostrar_inactivas = false)
{
  $db = conectarDb();
  $filtro_activo = $mostrar_inactivas ? "" : "WHERE m.activo = 1";

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
    $filtro_activo
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

function get_mascotas_by_cliente_id($cliente_id)
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
      m.activo
    FROM mascotas m
    WHERE m.clienteId = ? AND m.activo = 1
    ORDER BY m.nombre ASC
  ");
  $stmt->bind_param("i", $cliente_id);
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

function get_mascota_by_id($mascota_id)
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT * FROM mascotas WHERE id = ?");
  $stmt->bind_param("i", $mascota_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $mascota = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return $mascota;
}

function search_mascotas($termino)
{
  $db = conectarDb();
  $search = "%" . $termino . "%";
  $stmt = $db->prepare("
    SELECT 
      m.id,
      m.clienteId,
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
    WHERE m.nombre LIKE ? OR u.nombre LIKE ? OR u.apellido LIKE ?
    ORDER BY m.nombre ASC
  ");
  $stmt->bind_param("sss", $search, $search, $search);
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

/**
 * ==========================================
 * ATENCIONES
 * ==========================================
 */

function get_all_atenciones($mostrar_inactivas = false)
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      a.id,
      a.fechaHora as fecha,
      a.titulo,
      a.descripcion,
      a.estado,
      a.personalId,
      m.nombre as nombre_mascota,
      uc.nombre as nombre_cliente,
      uc.apellido as apellido_cliente,
      up.nombre as nombre_personal,
      up.apellido as apellido_personal
    FROM atenciones a
    JOIN mascotas m ON a.mascotaId = m.id
    JOIN clientes c ON m.clienteId = c.id
    JOIN usuarios uc ON c.usuarioId = uc.id
    JOIN personal p ON a.personalId = p.id
    JOIN usuarios up ON p.usuarioId = up.id
    ORDER BY a.fechaHora DESC
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

function get_atenciones_by_fecha($fecha, $mostrar_inactivas = false)
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      a.id,
      a.fechaHora as fecha,
      a.titulo as motivo,
      m.nombre as nombre_mascota,
      u.nombre as nombre_cliente,
      u.apellido as apellido_cliente
    FROM atenciones a
    JOIN mascotas m ON a.mascotaId = m.id
    JOIN clientes c ON m.clienteId = c.id
    JOIN usuarios u ON c.usuarioId = u.id
    WHERE DATE(a.fechaHora) = ?  
    ORDER BY a.fechaHora ASC
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

function get_atenciones_by_mascota($mascota_id)
{
  $db = conectarDb();
  $stmt = $db->prepare("SELECT * FROM atenciones WHERE mascotaId = ? ORDER BY fechaHora DESC");
  $stmt->bind_param("i", $mascota_id);
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

function search_atenciones($termino, $fecha = '')
{
  $db = conectarDb();
  $search = "%" . $termino . "%";

  $whereClause = "WHERE (m.nombre LIKE ? OR uc.nombre LIKE ? OR uc.apellido LIKE ? OR a.titulo LIKE ? OR a.descripcion LIKE ?)";
  $types = "sssss";
  $params = [$search, $search, $search, $search, $search];

  if (!empty($fecha)) {
    $whereClause .= " AND DATE(a.fechaHora) = ?";
    $types .= "s";
    $params[] = $fecha;
  }

  $stmt = $db->prepare("
    SELECT 
      a.id,
      a.fechaHora,
      a.titulo,
      a.descripcion,
      a.estado,
      a.personalId,
      m.nombre as nombre_mascota,
      uc.nombre as nombre_cliente,
      uc.apellido as apellido_cliente,
      up.nombre as nombre_personal,
      up.apellido as apellido_personal
    FROM atenciones a
    JOIN mascotas m ON a.mascotaId = m.id
    JOIN clientes c ON m.clienteId = c.id
    JOIN usuarios uc ON c.usuarioId = uc.id
    JOIN personal p ON a.personalId = p.id
    JOIN usuarios up ON p.usuarioId = up.id
    $whereClause
    ORDER BY a.fechaHora DESC
  ");

  $stmt->bind_param($types, ...$params);
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

function get_atencion_by_id($id)
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT 
      a.*,
      m.nombre as nombre_mascota,
      uc.nombre as nombre_cliente,
      uc.apellido as apellido_cliente,
      up.nombre as nombre_personal,
      up.apellido as apellido_personal
    FROM atenciones a
    JOIN mascotas m ON a.mascotaId = m.id
    JOIN clientes c ON m.clienteId = c.id
    JOIN usuarios uc ON c.usuarioId = uc.id
    JOIN personal p ON a.personalId = p.id
    JOIN usuarios up ON p.usuarioId = up.id
    WHERE a.id = ?
  ");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  $atencion = $result->fetch_assoc();
  $stmt->close();
  $db->close();

  return $atencion;
}

function insert_atencion($clienteId, $mascotaId, $personalId, $fechaHora, $titulo, $servicioId, $descripcion)
{
  $db = conectarDb();
  $servicioId = (!empty($servicioId)) ? $servicioId : null;

  $stmt = $db->prepare("
    INSERT INTO atenciones (clienteId, mascotaId, personalId, fechaHora, titulo, servicioId, descripcion) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
  ");
  $stmt->bind_param("iiissis", $clienteId, $mascotaId, $personalId, $fechaHora, $titulo, $servicioId, $descripcion);
  $result = $stmt->execute();
  $newId = $db->insert_id;
  $stmt->close();
  $db->close();

  return $result ? $newId : false;
}

function update_atencion($id, $titulo, $descripcion, $servicioId, $personalId, $fechaHora, $estado)
{
  $db = conectarDb();
  $servicioId = (!empty($servicioId)) ? $servicioId : null;

  $stmt = $db->prepare("
    UPDATE atenciones 
    SET titulo = ?, descripcion = ?, servicioId = ?, personalId = ?, fechaHora = ?, estado = ? 
    WHERE id = ?
  ");
  $stmt->bind_param("ssiissi", $titulo, $descripcion, $servicioId, $personalId, $fechaHora, $estado, $id);
  $result = $stmt->execute();
  $stmt->close();
  $db->close();

  return $result;
}

function update_atencion_estado($id, $estado)
{
  $db = conectarDb();
  $stmt = $db->prepare("UPDATE atenciones SET estado = ? WHERE id = ?");
  $stmt->bind_param("si", $estado, $id);
  $result = $stmt->execute();
  $stmt->close();
  $db->close();

  return $result;
}

function delete_atencion($id)
{
  $db = conectarDb();
  $stmt = $db->prepare("DELETE FROM atenciones WHERE id = ?");
  $stmt->bind_param("i", $id);
  $result = $stmt->execute();
  $stmt->close();
  $db->close();

  return $result;
}

/**
 * ==========================================
 * SERVICIOS
 * ==========================================
 */

function get_all_servicios($mostrar_inactivos = false)
{
  $db = conectarDb();
  $filtro_activo = $mostrar_inactivos ? "" : "WHERE activo = 1";

  $stmt = $db->prepare("
    SELECT * FROM servicios 
    $filtro_activo
    ORDER BY nombre ASC
  ");
  $stmt->execute();
  $result = $stmt->get_result();
  $servicios = array();

  while ($row = $result->fetch_assoc()) {
    $servicios[] = $row;
  }

  $stmt->close();
  $db->close();

  return $servicios;
}

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

function get_servicios_by_personal($personalId)
{
  $db = conectarDb();
  $stmt = $db->prepare("
    SELECT s.* 
    FROM servicios s
    JOIN rolesServicios rs ON s.id = rs.servicioId
    JOIN personal p ON rs.rolId = p.rolId
    WHERE p.id = ? AND s.activo = 1
    ORDER BY s.nombre ASC
  ");
  $stmt->bind_param("i", $personalId);
  $stmt->execute();
  $result = $stmt->get_result();
  $servicios = array();

  while ($row = $result->fetch_assoc()) {
    $servicios[] = $row;
  }

  $stmt->close();
  $db->close();

  return $servicios;
}

/**
 * ==========================================
 * CATALOGO / PRODUCTOS
 * ==========================================
 */

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
