<?php

require_once __DIR__ . '/../lib/funciones.php';

/**
 * Da de baja una mascota (baja lógica)
 * @param int $mascota_id ID de la mascota
 * @return bool True si se dio de baja correctamente
 */
function dar_baja_mascota($mascota_id) {
    $db = conectarDb();
    $stmt = $db->prepare("UPDATE Mascotas SET activo = 0 WHERE id = ?");
    $stmt->bind_param("i", $mascota_id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $result;
}

/**
 * Reactiva una mascota previamente dada de baja
 * @param int $mascota_id ID de la mascota
 * @return bool True si se reactivó correctamente
 */
function reactivar_mascota($mascota_id) {
    $db = conectarDb();
    $stmt = $db->prepare("UPDATE Mascotas SET activo = 1 WHERE id = ?");
    $stmt->bind_param("i", $mascota_id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $result;
}

/**
 * Registra el fallecimiento de una mascota
 * @param int $mascota_id
 * @param string $fecha opcional, por defecto hoy
 * @return bool
 */
function registrar_fallecimiento_mascota($mascota_id, $fecha = null) {
    $db = conectarDb();
    if (!$fecha) $fecha = date('Y-m-d');
    
    $stmt = $db->prepare("UPDATE Mascotas SET activo = 0, fechaMuerte = ? WHERE id = ?");
    $stmt->bind_param("si", $fecha, $mascota_id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $result;
}
?>