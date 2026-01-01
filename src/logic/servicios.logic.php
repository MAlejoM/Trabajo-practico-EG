<?php

require_once __DIR__ . '/../lib/funciones.php';

/**
 * Da de baja un servicio (baja lógica)
 * @param int $id ID del servicio
 * @return bool
 */
function dar_baja_servicio($id) {
    $db = conectarDb();
    $stmt = $db->prepare("UPDATE servicios SET activo = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $result;
}

/**
 * Reactiva un servicio
 * @param int $id ID del servicio
 * @return bool
 */
function reactivar_servicio($id) {
    $db = conectarDb();
    $stmt = $db->prepare("UPDATE servicios SET activo = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    
    return $result;
}
?>
