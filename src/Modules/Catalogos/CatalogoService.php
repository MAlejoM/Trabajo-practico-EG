<?php

namespace App\Modules\Catalogos;

use Exception;

class CatalogoService
{
    public static function getAll($categoria = null, $busqueda = null)
    {
        return CatalogoRepository::getAll($categoria, $busqueda);
    }

    public static function getCategorias()
    {
        return CatalogoRepository::getCategorias();
    }

    public static function getById($id)
    {
        return CatalogoRepository::getById($id);
    }

    public static function create($data)
    {
        self::validate($data);
        return CatalogoRepository::create($data);
    }

    public static function update($id, $data)
    {
        self::validate($data);
        return CatalogoRepository::update($id, $data);
    }

    public static function delete($id)
    {
        return CatalogoRepository::delete($id);
    }

    private static function validate($data)
    {
        if (empty($data['nombre'])) throw new Exception("El nombre es obligatorio.");
        if (!isset($data['stock']) || $data['stock'] < 0) throw new Exception("El stock no puede ser negativo.");
    }
}
