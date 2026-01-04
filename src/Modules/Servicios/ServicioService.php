<?php

namespace App\Modules\Servicios;

class ServicioService
{
    public static function getServiciosByPersonalId($personalId)
    {
        return ServicioRepository::getServiciosByPersonalId($personalId);
    }

    public static function getAll($mostrarInactivos = false)
    {
        return ServicioRepository::getAll($mostrarInactivos);
    }

    public static function getById($id)
    {
        return ServicioRepository::getById($id);
    }

    public static function create($data)
    {
        // Validaciones básicas
        if (empty($data['nombre']) || !isset($data['precio'])) {
            throw new \Exception("El nombre y el precio son obligatorios.");
        }

        $id = ServicioRepository::create($data['nombre'], $data['precio']);

        if ($id && isset($data['roles'])) {
            ServicioRepository::assignRoles($id, $data['roles']);
        }

        return $id;
    }

    public static function update($id, $data)
    {
        if (empty($data['nombre']) || !isset($data['precio'])) {
            throw new \Exception("El nombre y el precio son obligatorios.");
        }

        $success = ServicioRepository::update($id, $data['nombre'], $data['precio']);

        if ($success && isset($data['roles'])) {
            // Nota: Si no se envían roles, esto asume que es un array vacío y borra los roles.
            // Asegúrate que el formulario siempre envíe el campo 'roles' (aunque sea vacío) si se pretende gestionar roles.
            ServicioRepository::assignRoles($id, $data['roles']);
        }

        return $success;
    }

    public static function delete($id)
    {
        return ServicioRepository::delete($id);
    }

    public static function reactivate($id)
    {
        return ServicioRepository::reactivate($id);
    }

    public static function getRolesIds($id)
    {
        return ServicioRepository::getRolesIds($id);
    }

    public static function getAllRoles()
    {
        return ServicioRepository::getAllRoles();
    }
}
