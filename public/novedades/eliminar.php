<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../../src/autoload.php';

use App\Modules\Novedades\NovedadService;

// Verificar que el usuario sea admin
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header('Location: index.php');
  exit;
}

// Obtener ID de la novedad a eliminar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
  $resultado = NovedadService::delete($id);

  if ($resultado) {
    $_SESSION['mensaje'] = 'Novedad eliminada exitosamente';
    $_SESSION['tipo_mensaje'] = 'success';
  } else {
    $_SESSION['mensaje'] = 'Error al eliminar la novedad';
    $_SESSION['tipo_mensaje'] = 'error';
  }
} else {
  $_SESSION['mensaje'] = 'ID de novedad inválido';
  $_SESSION['tipo_mensaje'] = 'error';
}

header('Location: index.php');
exit;
