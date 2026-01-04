<?php

namespace App\Modules\Novedades;

class NovedadService
{
    public static function getAll()
    {
        return NovedadRepository::getAll();
    }

    public static function getById($id)
    {
        return NovedadRepository::getById($id);
    }

    public static function create($data, $usuarioId)
    {
        if (empty($data['titulo']) || empty($data['contenido'])) {
            throw new \Exception("El título y el contenido son obligatorios.");
        }

        // Manejo de imagen
        $imagenBinaria = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagenBinaria = file_get_contents($_FILES['imagen']['tmp_name']);
        }

        return NovedadRepository::create($data['titulo'], $data['contenido'], $imagenBinaria, $usuarioId);
    }

    public static function update($id, $data)
    {
        if (empty($data['titulo']) || empty($data['contenido'])) {
            throw new \Exception("El título y el contenido son obligatorios.");
        }

        $imagenBinaria = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagenBinaria = file_get_contents($_FILES['imagen']['tmp_name']);
            return NovedadRepository::update($id, $data['titulo'], $data['contenido'], $imagenBinaria);
        }

        // Si no hay imagen nueva, actualizar solo texto
        return NovedadRepository::update($id, $data['titulo'], $data['contenido']);
    }

    public static function delete($id)
    {
        return NovedadRepository::delete($id);
    }
}
