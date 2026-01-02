<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once __DIR__ . '/../../src/includes/header.php';
require_once __DIR__ . '/../../src/logic/novedades.logic.php';

// Verificar que el usuario sea admin
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header('Location: ../novedad_list.php');
  exit;
}

$mensaje = '';
$tipoMensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $titulo = trim($_POST['titulo'] ?? '');
  $contenido = trim($_POST['contenido'] ?? '');
  $usuarioId = $_SESSION['usuarioId'];

  // Validaciones
  if (empty($titulo)) {
    $mensaje = 'El título es obligatorio';
    $tipoMensaje = 'danger';
  } elseif (empty($contenido)) {
    $mensaje = 'El contenido es obligatorio';
    $tipoMensaje = 'danger';
  } else {
    // Procesar imagen si se subió
    $imagenBlob = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
      // Validar tipo de archivo
      $tipoArchivo = $_FILES['imagen']['type'];
      $extensionesPermitidas = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

      if (!in_array($tipoArchivo, $extensionesPermitidas)) {
        $mensaje = 'Formato de imagen no permitido. Use JPG, PNG o GIF';
        $tipoMensaje = 'danger';
      } elseif ($_FILES['imagen']['size'] > 5000000) { // 5MB
        $mensaje = 'La imagen es demasiado grande (máximo 5MB)';
        $tipoMensaje = 'danger';
      } else {
        // Leer el archivo como datos binarios
        $imagenBlob = file_get_contents($_FILES['imagen']['tmp_name']);
      }
    }

    // Si no hubo error, crear la novedad
    if ($tipoMensaje !== 'danger') {
      $resultado = crearNovedad($titulo, $contenido, $imagenBlob, $usuarioId);

      if ($resultado) {
        $_SESSION['mensaje'] = 'Novedad creada exitosamente';
        $_SESSION['tipo_mensaje'] = 'success';
        header('Location: ../novedad_list.php');
        exit;
      } else {
        $mensaje = 'Error al crear la novedad';
        $tipoMensaje = 'danger';
      }
    }
  }
}
?>

<div class="container py-4">
  <div class="row g-4">
    <aside class="col-md-4 col-lg-3 d-none d-md-block">
      <div class="card sticky-top" style="top: 1rem;">
        <div class="card-header fw-semibold">Menú principal</div>
        <div class="card-body d-grid gap-2">
          <?php include_once __DIR__ . '/../../src/includes/menu_lateral.php'; ?>
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
              <a href="../novedad_list.php" class="btn btn-secondary">
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
include_once __DIR__ . '/../../src/includes/footer.php';
?>