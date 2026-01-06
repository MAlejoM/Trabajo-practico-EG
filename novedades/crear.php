<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once __DIR__ . '/../src/Templates/header.php';
// require_once __DIR__ . '/../src/logic/novedades.logic.php'; // REMOVED

use App\Modules\Novedades\NovedadService;

// Verificar que el usuario sea admin
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header('Location: index.php');
  exit;
}

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $resultado = NovedadService::create($_POST, $_SESSION['usuarioId']);
    if ($resultado) {
      $_SESSION['mensaje'] = 'Novedad creada exitosamente';
      $_SESSION['tipo_mensaje'] = 'success';
      header('Location: index.php');
      exit;
    } else {
      $mensaje = 'Error al crear la novedad';
      $tipoMensaje = 'danger';
    }
  } catch (Exception $e) {
    $mensaje = $e->getMessage();
    $tipoMensaje = 'danger';
  }
}
?>

<div class="container py-4">
  <div class="row g-4">
    <aside class="col-md-4 col-lg-3 d-none d-md-block">
      <div class="card sticky-top" style="top: 1rem;">
        <div class="card-header fw-semibold">Menú principal</div>
        <div class="card-body d-grid gap-2">
          <?php include_once __DIR__ . '/../src/Templates/menu_lateral.php'; ?>
        </div>
      </div>
    </aside>

    <div class="col-12 col-md-8 col-lg-9">
      <div class="card">
        <div class="card-header">
          <h1 class="h4 mb-0">
            <i class="fas fa-newspaper me-2"></i>
            Nueva Novedad
          </h1>
        </div>
        <div class="card-body">
          <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipoMensaje; ?> alert-dismissible fade show">
              <?php echo htmlspecialchars($mensaje); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="titulo" class="form-label">Título *</label>
              <input type="text"
                class="form-control"
                id="titulo"
                name="titulo"
                required
                maxlength="200"
                value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>"
                placeholder="Ingrese el título de la novedad">
            </div>

            <div class="mb-3">
              <label for="contenido" class="form-label">Contenido *</label>
              <textarea class="form-control"
                id="contenido"
                name="contenido"
                required
                rows="8"
                placeholder="Escriba el contenido completo de la novedad..."><?php echo isset($_POST['contenido']) ? htmlspecialchars($_POST['contenido']) : ''; ?></textarea>
            </div>

            <div class="mb-3">
              <label for="imagen" class="form-label">Imagen (opcional)</label>
              <input type="file"
                class="form-control"
                id="imagen"
                name="imagen"
                accept="image/jpeg,image/jpg,image/png,image/gif">
              <div class="form-text">
                Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB
              </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-between">
              <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
              </a>
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Crear Novedad
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include_once __DIR__ . '/../src/Templates/footer.php';
?>