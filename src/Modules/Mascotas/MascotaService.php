<?php

namespace App\Modules\Mascotas;

class MascotaService
{
    public static function getAll($mostrarInactivas = false)
    {
        return MascotaRepository::getAll($mostrarInactivas);
    }

    public static function search($termino)
    {
        return MascotaRepository::search($termino);
    }

    public static function getById($id)
    {
        return MascotaRepository::getById($id);
    }

    public static function getByClienteId($clienteId)
    {
        return MascotaRepository::getByClienteId($clienteId);
    }

    public static function getByClienteDni($dni)
    {
        return MascotaRepository::getByClienteDni($dni);
    }

    public static function create($data, $files)
    {
        $nombre = trim($data['nombre'] ?? '');
        $clienteId = intval($data['cliente_id'] ?? 0);
        $raza = trim($data['raza'] ?? '');
        $color = trim($data['color'] ?? '');
        $fechaDeNac = $data['fechaDeNac'] ?? null;

        if (empty($nombre)) {
            throw new \Exception("El nombre de la mascota es obligatorio.");
        }
        if ($clienteId <= 0) {
            throw new \Exception("Debe seleccionar un cliente válido.");
        }

        $fotoBlob = null;
        if (isset($files['foto']) && $files['foto']['size'] > 0) {
            if ($files['foto']['size'] > 2 * 1024 * 1024) {
                throw new \Exception("La imagen no debe pesar más de 2MB.");
            }
            $fotoBlob = file_get_contents($files['foto']['tmp_name']);
        }

        return MascotaRepository::create($clienteId, $nombre, $raza, $color, $fechaDeNac, $fotoBlob);
    }

    public static function update($id, $data, $files)
    {
        $nombre = trim($data['nombre'] ?? '');
        $raza = trim($data['raza'] ?? '');
        $color = trim($data['color'] ?? '');
        $fechaNac = !empty($data['fechaDeNac']) ? $data['fechaDeNac'] : null;
        $fechaMuerte = !empty($data['fechaMuerte']) ? $data['fechaMuerte'] : null;
        $activo = isset($data['activo']) ? 1 : 0;

        if (empty($nombre)) {
            throw new \Exception("El nombre de la mascota es obligatorio.");
        }

        $fotoBlob = null;
        if (isset($files['foto']) && $files['foto']['error'] === UPLOAD_ERR_OK) {
            if ($files['foto']['size'] > 2 * 1024 * 1024) {
                throw new \Exception("La imagen no debe pesar más de 2MB.");
            }
            $fotoBlob = file_get_contents($files['foto']['tmp_name']);
        }

        return MascotaRepository::update($id, $nombre, $raza, $color, $fechaNac, $fechaMuerte, $activo, $fotoBlob);
    }

    public static function delete($id)
    {
        return MascotaRepository::delete($id);
    }

    public static function reactivate($id)
    {
        return MascotaRepository::reactivate($id);
    }
}
