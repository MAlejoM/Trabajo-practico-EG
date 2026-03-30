<?php

namespace App\Modules\Catalogos;

use App\Core\DB;

class CatalogoRepository
{
    const PAGE_SIZE = 5;

    public static function getAllPaginated($page, $categoria = null, $busqueda = null)
    {
        $db = DB::getConn();
        $page = max(1, (int)$page);

        $conditions = 'WHERE 1=1';
        $types  = '';
        $params = [];

        if ($categoria) {
            $conditions .= ' AND p.categoria = ?';
            $types  .= 's';
            $params[] = $categoria;
        }

        if ($busqueda) {
            $conditions .= ' AND (p.nombre LIKE ? OR p.descripcion LIKE ?)';
            $types  .= 'ss';
            $term    = '%' . $busqueda . '%';
            $params[] = $term;
            $params[] = $term;
        }

        $from = "FROM productos p INNER JOIN usuarios u ON p.usuarioId = u.id $conditions";

        // COUNT
        $countSql = "SELECT COUNT(*) as total $from";
        if (!empty($params)) {
            $stmt = $db->prepare($countSql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $total = $stmt->get_result()->fetch_assoc()['total'];
        } else {
            $total = $db->query($countSql)->fetch_assoc()['total'];
        }

        $totalPages = max(1, (int)ceil($total / self::PAGE_SIZE));
        $page   = min($page, $totalPages);
        $offset = ($page - 1) * self::PAGE_SIZE;
        $limit  = self::PAGE_SIZE;

        // DATA
        $dataSql    = "SELECT p.*, u.nombre as autorNombre, u.apellido as autorApellido $from ORDER BY p.nombre ASC LIMIT ? OFFSET ?";
        $dataTypes  = $types . 'ii';
        $dataParams = array_merge($params, [$limit, $offset]);

        $stmt = $db->prepare($dataSql);
        $stmt->bind_param($dataTypes, ...$dataParams);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return [
            'data'     => $data,
            'total'    => (int)$total,
            'pages'    => $totalPages,
            'page'     => $page,
            'per_page' => self::PAGE_SIZE,
        ];
    }

    public static function getAll($categoria = null, $busqueda = null)
    {
        $db = DB::getConn();
        $sql = "
            SELECT p.*, u.nombre as autorNombre, u.apellido as autorApellido 
            FROM productos p
            INNER JOIN usuarios u ON p.usuarioId = u.id
            WHERE 1=1
        ";

        $types = "";
        $params = [];

        if ($categoria) {
            $sql .= " AND p.categoria = ?";
            $types .= "s";
            $params[] = $categoria;
        }

        if ($busqueda) {
            $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?)";
            $types .= "ss";
            $term = "%" . $busqueda . "%";
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY p.nombre ASC";

        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function getCategorias()
    {
        $db = DB::getConn();
        $sql = "
            SELECT DISTINCT categoria 
            FROM productos 
            WHERE categoria IS NOT NULL AND categoria != ''
            ORDER BY categoria ASC
        ";
        $result = $db->query($sql);
        $categorias = [];
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row['categoria'];
        }
        return $categorias;
    }

    public static function getById($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("
            SELECT p.*, u.nombre as autorNombre, u.apellido as autorApellido 
            FROM productos p
            INNER JOIN usuarios u ON p.usuarioId = u.id
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public static function create($data)
    {
        $db = DB::getConn();
        if (!empty($data['imagen'])) {
            $stmt = $db->prepare("
                INSERT INTO productos (nombre, descripcion, precio, imagen, categoria, stock, usuarioId)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $null = null;
            $stmt->bind_param(
                "ssdbsii",
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $null,
                $data['categoria'],
                $data['stock'],
                $data['usuarioId']
            );
            $stmt->send_long_data(3, $data['imagen']);
        } else {
            $stmt = $db->prepare("
                INSERT INTO productos (nombre, descripcion, precio, categoria, stock, usuarioId)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssdsii",
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['categoria'],
                $data['stock'],
                $data['usuarioId']
            );
        }
        if ($stmt->execute()) {
            return $db->insert_id;
        }
        return false;
    }

    public static function update($id, $data)
    {
        $db = DB::getConn();
        if (isset($data['imagen']) && $data['imagen'] !== null) {
            $stmt = $db->prepare("
                UPDATE productos 
                SET nombre = ?, descripcion = ?, precio = ?, 
                    imagen = ?, categoria = ?, stock = ?
                WHERE id = ?
            ");
            $null = null;
            $stmt->bind_param(
                "ssdbsii",
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $null,
                $data['categoria'],
                $data['stock'],
                $id
            );
            $stmt->send_long_data(3, $data['imagen']);
        } else {
            $stmt = $db->prepare("
                UPDATE productos 
                SET nombre = ?, descripcion = ?, precio = ?, 
                    categoria = ?, stock = ?
                WHERE id = ?
            ");
            $stmt->bind_param(
                "ssdssi",
                $data['nombre'],
                $data['descripcion'],
                $data['precio'],
                $data['categoria'],
                $data['stock'],
                $id
            );
        }
        return $stmt->execute();
    }

    public static function delete($id)
    {
        $db = DB::getConn();
        $stmt = $db->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
