<?php

namespace App\Modules\Usuarios;

use Exception;

class UsuarioService
{
    public static function esAdmin()
    {
        return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
    }

    public static function esPersonal()
    {
        return isset($_SESSION['personal_id']);
    }

    public static function esCliente()
    {
        return isset($_SESSION['cliente_id']);
    }

    public static function getAll($mostrarInactivos = false)
    {
        return UsuarioRepository::getAll($mostrarInactivos);
    }

    public static function getById($id)
    {
        return UsuarioRepository::getById($id);
    }

    public static function getUsuarioCompletoById($id)
    {
        return UsuarioRepository::getUsuarioCompletoById($id);
    }

    public static function getClienteCompletoById($id)
    {
        $cliente = UsuarioRepository::getClienteById($id);
        $usuario = UsuarioRepository::getUsuarioCompletoById($cliente['usuarioId']);
        return array_merge($cliente, $usuario);
    }

    public static function create($data)
    {
        $db = \App\Core\DB::getConn();
        $db->begin_transaction();

        try {
            // 1. Validar email
            if (UsuarioRepository::getByEmail($data['email'])) {
                throw new Exception("El email ya está registrado.");
            }

            // 2. Crear base de usuario
            $id = UsuarioRepository::create([
                'email' => $data['email'],
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'clave' => password_hash($data['clave'], PASSWORD_DEFAULT),
                'activo' => 1
            ]);

            if (!$id) throw new Exception("Error al crear el usuario base.");

            // 3. Crear extensión (Cliente o Personal)
            if ($data['tipo'] === 'cliente') {
                $stmt = $db->prepare("INSERT INTO Clientes (id, usuarioId, telefono, direccion, ciudad) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisss", $id, $id, $data['telefono'], $data['direccion'], $data['ciudad']);
                $stmt->execute();
            } else {
                $stmt = $db->prepare("INSERT INTO Personal (id, usuarioId, rolId, activo) VALUES (?, ?, ?, 1)");
                $stmt->bind_param("iii", $id, $id, $data['rol_id']);
                $stmt->execute();
            }

            $db->commit();
            return $id;
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    public static function updateAdmin($id, $data)
    {
        // Validar email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido.");
        }

        // Verificar duplicado
        $existente = UsuarioRepository::getByEmail($data['email'], $id);
        if ($existente) {
            throw new Exception("El email ya está en uso por otro usuario.");
        }

        return UsuarioRepository::update($id, $data);
    }

    public static function updateClienteDatos($clienteId, $data)
    {
        return UsuarioRepository::updateCliente($clienteId, $data);
    }

    public static function cambiarPassword($usuarioId, $actual, $nueva)
    {
        $usuario = UsuarioRepository::getById($usuarioId);
        if (!$usuario) throw new Exception("Usuario no encontrado.");

        // Verificar actual (tolerando texto plano para legacy)
        if (!password_verify($actual, $usuario['clave']) && $actual !== $usuario['clave']) {
            throw new Exception("La contraseña actual es incorrecta.");
        }

        if (strlen($nueva) < 8) {
            throw new Exception("La nueva contraseña debe tener al menos 8 caracteres.");
        }

        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        return UsuarioRepository::update($usuarioId, ['clave' => $hash]);
    }

    public static function setEstado($id, $activo)
    {
        return UsuarioRepository::setEstado($id, $activo ? 1 : 0);
    }

    // --- Passthrough para Clientes/Personal ---
    public static function getAllClientes()
    {
        return UsuarioRepository::getAllClientes();
    }

    public static function getClienteById($id)
    {
        return UsuarioRepository::getClienteById($id);
    }

    public static function getAllPersonal($soloActivos = true)
    {
        return UsuarioRepository::getAllPersonal($soloActivos);
    }

    public static function getPersonalById($id)
    {
        return UsuarioRepository::getPersonalById($id);
    }
}
