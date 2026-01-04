<?php
include_once __DIR__ . "/../../src/Templates/header.php";


use App\Modules\Catalogos\CatalogoService;

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header('Location: ' . BASE_URL . 'public/index.php');
  exit();
}

$id = intval($_GET['id'] ?? 0);
$producto = CatalogoService::getById($id);
if (!$producto) {
  header('Location: index.php');
  exit;
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
  try {
    $datos = $_POST;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
      $datos['imagen'] = file_get_contents($_FILES['imagen']['tmp_name']);
    } else {
      $datos['imagen'] = null; // No actualizar imagen si no se sube una nueva
    }

    CatalogoService::update($id, $datos);
    header('Location: index.php?editado=1');
    exit;
  } catch (Exception $e) {
    $mensaje = $e->getMessage();
    $tipo_mensaje = 'danger';
  }
}
?>

<div class="container py-4">
  <div class="row g-4">
    <aside class="col-md-4 col-lg-3 d-none d-md-block">
      <div class="card sticky-top" style="top: 1rem;">
        <div class="card-header fw-semibold">Menú principal</div>
        <div class="card-body d-grid gap-2">
          <?php include_once __DIR__ . "/../../src/Templates/menu_lateral.php"; ?>
        </div>
      </div>
    </aside>

    <div class="col-12 col-md-8 col-lg-9">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h1 class="h4 mb-0">Editar Producto</h1>
          <a href="index.php" class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>
        <div class="card-body text-dark">
          <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label class="form-label">Nombre *</label>
              <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Categoría</label>
                <input type="text" name="categoria" class="form-control" list="categorias-existentes" value="<?php echo htmlspecialchars($producto['categoria']); ?>">
                <datalist id="categorias-existentes">
                  <?php foreach (CatalogoService::getCategorias() as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>">
                    <?php endforeach; ?>
                </datalist>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Precio ($) *</label>
                <input type="number" name="precio" step="0.01" class="form-control" value="<?php echo $producto['precio']; ?>" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Stock *</label>
                <input type="number" name="stock" class="form-control" value="<?php echo $producto['stock']; ?>" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Imagen (dejar en blanco para mantener la actual)</label>
              <input type="file" name="imagen" class="form-control" accept="image/*">
              <?php if ($producto['imagen']): ?>
                <div class="mt-2 text-muted small">Ya tiene una imagen cargada.</div>
              <?php endif; ?>
            </div>
            <div class="text-end">
              <button type="submit" name="editar" class="btn btn-primary px-4">Guardar Cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once __DIR__ . "/../../src/Templates/footer.php"; ?>