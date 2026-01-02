<?php
require_once __DIR__ . '/../includes/db.php';

//get all 
function obtenerNovedades()
{
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT n.*, u.nombre as autorNombre, u.apellido as autorApellido 
            FROM novedades n
            INNER JOIN usuarios u ON n.usuarioId = u.id
            ORDER BY n.fechaPublicacion DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener novedades: " . $e->getMessage());
        return [];
    }
}

//obtener x id
function obtenerNovedadPorId($id)
{
    global $conn;
    try {
        $stmt = $conn->prepare("
            SELECT n.*, u.nombre as autorNombre, u.apellido as autorApellido 
            FROM novedades n
            INNER JOIN usuarios u ON n.usuarioId = u.id
            WHERE n.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener novedad: " . $e->getMessage());
        return null;
    }
}

//crear
function crearNovedad($titulo, $contenido, $imagen, $usuarioId)
{
    global $conn;
    try {
        $stmt = $conn->prepare("
            INSERT INTO novedades (titulo, contenido, imagen, usuarioId)
            VALUES (:titulo, :contenido, :imagen, :usuarioId)
        ");
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':imagen', $imagen, PDO::PARAM_LOB);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $conn->lastInsertId();
        }
        return false;
    } catch (PDOException $e) {
        error_log("Error al crear novedad: " . $e->getMessage());
        return false;
    }
}
//modif
function modificarNovedad($id, $titulo, $contenido, $imagen = null)
{
    global $conn;
    try {
        if ($imagen !== null) {
            $stmt = $conn->prepare("
                UPDATE novedades 
                SET titulo = :titulo, contenido = :contenido, imagen = :imagen
                WHERE id = :id
            ");
            $stmt->bindParam(':imagen', $imagen, PDO::PARAM_LOB);
        } else {
            $stmt = $conn->prepare("
                UPDATE novedades 
                SET titulo = :titulo, contenido = :contenido
                WHERE id = :id
            ");
        }

        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error al modificar novedad: " . $e->getMessage());
        return false;
    }
}

//delete
function eliminarNovedad($id)
{
    global $conn;
    try {
        $stmt = $conn->prepare("DELETE FROM novedades WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error al eliminar novedad: " . $e->getMessage());
        return false;
    }
}
