<?php
require_once __DIR__ . '/../../src/autoload.php';

use App\Modules\Novedades\NovedadService;
use App\Core\SessionHandler;

// Verificar que el usuario sea admin
if (!SessionHandler::esAdmin()) {
  header('Location: index.php');
  exit;
}

// Obtener ID de la novedad a eliminar
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
  $resultado = NovedadService::delete($id);

  if ($resultado) {
    SessionHandler::setMensaje('Novedad eliminada exitosamente');
  } else {
    SessionHandler::setMensaje('Error al eliminar la novedad', 'danger');
  }
} else {
  SessionHandler::setMensaje('ID de novedad inválido', 'danger');
}

header('Location: index.php');
exit;
