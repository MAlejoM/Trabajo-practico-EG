<?php

namespace App\Modules\Atenciones;

class AtencionService
{
    public static function getAll()
    {
        return AtencionRepository::getAll();
    }

    public static function getById($id)
    {
        return AtencionRepository::getById($id);
    }

    public static function getByFecha($fecha)
    {
        return AtencionRepository::getByFecha($fecha);
    }

    public static function getByMascotaId($mascotaId)
    {
        return AtencionRepository::getByMascotaId($mascotaId);
    }

    public static function search($termino, $fecha = '')
    {
        return AtencionRepository::search($termino, $fecha);
    }

    public static function create($data)
    {
        $clienteId = intval($data['cliente_id'] ?? 0);
        $mascotaId = intval($data['mascota_id'] ?? 0);
        $personalId = intval($data['personal_id'] ?? 0);
        $fechaHora = $data['fecha'] . ' ' . $data['hora']; // Combinar inputs
        $titulo = trim($data['titulo'] ?? '');
        $servicioId = !empty($data['servicio_id']) ? intval($data['servicio_id']) : null;
        $descripcion = trim($data['descripcion'] ?? '');

        if (empty($titulo)) {
            throw new \Exception("El motivo (título) es obligatorio.");
        }
        if ($clienteId <= 0 || $mascotaId <= 0 || $personalId <= 0) {
            throw new \Exception("Faltan datos requeridos (Cliente, Mascota o Personal).");
        }

        return AtencionRepository::create($clienteId, $mascotaId, $personalId, $fechaHora, $titulo, $servicioId, $descripcion);
    }

    public static function update($id, $data)
    {
        $titulo = trim($data['titulo'] ?? '');
        $descripcion = trim($data['descripcion'] ?? '');
        $servicioId = !empty($data['servicio_id']) ? intval($data['servicio_id']) : null;
        $personalId = intval($data['personal_id'] ?? 0);
        $fechaHora = $data['fecha'] . ' ' . $data['hora'];
        $estado = $data['estado'] ?? 'Pendiente';

        if (empty($titulo)) {
            throw new \Exception("El motivo (título) es obligatorio.");
        }

        return AtencionRepository::update($id, $titulo, $descripcion, $servicioId, $personalId, $fechaHora, $estado);
    }

    public static function updateEstado($id, $estado)
    {
        return AtencionRepository::updateEstado($id, $estado);
    }

    public static function delete($id)
    {
        return AtencionRepository::delete($id);
    }
}
