<?php
require_once __DIR__ . '/../includes/db.php';

/**
 * Obtener todos los productos del catálogo
 */
function obtenerCatalogos($categoria = null, $busqueda = null)
{
  global $conn;
  try {
    $sql = "
            SELECT c.*, u.nombre as autorNombre, u.apellido as autorApellido 
            FROM productos c
            INNER JOIN usuarios u ON c.usuarioId = u.id
            WHERE 1=1
        ";

    $params = [];

    if ($categoria) {
      $sql .= " AND c.categoria = :categoria";
      $params[':categoria'] = $categoria;
    }

    if ($busqueda) {
      $sql .= " AND (c.nombre LIKE :busqueda OR c.descripcion LIKE :busqueda)";
      $params[':busqueda'] = '%' . $busqueda . '%';
    }

    $sql .= " ORDER BY c.nombre ASC";

    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
      $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    error_log("Error al obtener catálogos: " . $e->getMessage());
    return [];
  }
}

/**
 * Obtener categorías únicas del catálogo
 */
function obtenerCategorias()
{
  global $conn;
  try {
    $stmt = $conn->prepare("
            SELECT DISTINCT categoria 
            FROM productos 
            WHERE categoria IS NOT NULL AND categoria != ''
            ORDER BY categoria ASC
        ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  } catch (PDOException $e) {
    error_log("Error al obtener categorías: " . $e->getMessage());
    return [];
  }
}

/**
 * Obtener un producto por ID
 */
function obtenerCatalogoPorId($id)
{
  global $conn;
  try {
    $stmt = $conn->prepare("
            SELECT c.*, u.nombre as autorNombre, u.apellido as autorApellido 
            FROM productos c
            INNER JOIN usuarios u ON c.usuarioId = u.id
            WHERE c.id = :id
        ");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    error_log("Error al obtener producto: " . $e->getMessage());
    return null;
  }
}

/**
 * Crear nuevo producto en el catálogo
 */
function crearCatalogo($nombre, $descripcion, $precio, $imagen, $categoria, $stock, $usuarioId)
{
  global $conn;
  try {
    // Convertir precio vacío a NULL
    if (empty($precio) || trim($precio) === '') {
      $precio = null;
    }

    $stmt = $conn->prepare("
            INSERT INTO productos (nombre, descripcion, precio, imagen, categoria, stock, usuarioId)
            VALUES (:nombre, :descripcion, :precio, :imagen, :categoria, :stock, :usuarioId)
        ");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':imagen', $imagen, PDO::PARAM_LOB);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
    $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);

    if ($stmt->execute()) {
      return $conn->lastInsertId();
    }
    return false;
  } catch (PDOException $e) {
    error_log("Error al crear producto: " . $e->getMessage());
    return false;
  }
}

/**
 * Modificar producto existente
 */
function modificarCatalogo($id, $nombre, $descripcion, $precio, $imagen = null, $categoria, $stock)
{
  global $conn;
  try {
    if ($imagen !== null) {
      $stmt = $conn->prepare("
                UPDATE productos 
                SET nombre = :nombre, descripcion = :descripcion, precio = :precio, 
                    imagen = :imagen, categoria = :categoria, stock = :stock
                WHERE id = :id
            ");
      $stmt->bindParam(':imagen', $imagen, PDO::PARAM_LOB);
    } else {
      $stmt = $conn->prepare("
                UPDATE productos 
                SET nombre = :nombre, descripcion = :descripcion, precio = :precio, 
                    categoria = :categoria, stock = :stock
                WHERE id = :id
            ");
    }

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    return $stmt->execute();
  } catch (PDOException $e) {
    error_log("Error al modificar producto: " . $e->getMessage());
    return false;
  }
}

/**
 * Eliminar producto del catálogo
 */
function eliminarCatalogo($id)
{
  global $conn;
  try {
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
  } catch (PDOException $e) {
    error_log("Error al eliminar producto: " . $e->getMessage());
    return false;
  }
}
