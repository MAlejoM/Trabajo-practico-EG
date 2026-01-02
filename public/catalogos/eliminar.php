<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../../src/logic/catalogos.logic.php';

// Verificar que el usuario sea admin
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header('Location: ../catalogo_list.php');
  exit;
}

// Obtener ID del producto a eliminar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
  $resultado = eliminarCatalogo($id);

  if ($resultado) {
    $_SESSION['mensaje'] = 'Producto eliminado exitosamente';
    $_SESSION['tipo_mensaje'] = 'success';
  } else {
    $_SESSION['mensaje'] = 'Error al eliminar el producto';
    $_SESSION['tipo_mensaje'] = 'error';
  }
} else {
  $_SESSION['mensaje'] = 'ID de producto inválido';
  $_SESSION['tipo_mensaje'] = 'error';
}

header('Location: ../catalogo_list.php');
exit;
