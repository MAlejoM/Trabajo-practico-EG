<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include_once __DIR__ . '/../../src/includes/header.php';
require_once __DIR__ . '/../../src/logic/catalogos.logic.php';

// Verificar que el usuario sea admin
if (!isset($_SESSION['usuarioId']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header('Location: ../catalogo_list.php');
  exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
  $_SESSION['mensaje'] = 'ID de producto inválido';
  $_SESSION['tipo_mensaje'] = 'danger';
  header('Location: ../catalogo_list.php');
  exit;
}

// Obtener el producto
$producto = obtenerCatalogoPorId($id);

if (!$producto) {
  $_SESSION['mensaje'] = 'Producto no encontrado';
  $_SESSION['tipo_mensaje'] = 'danger';
  header('Location: ../catalogo_list.php');
  exit;
}

$mensaje = '';
$tipoMensaje = '';

// Obtener categorías existentes
$categorias = obtenerCategorias();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $descripcion = trim($_POST['descripcion'] ?? '');
  $precio = trim($_POST['precio'] ?? '');
  $categoria = trim($_POST['categoria'] ?? '');
  $stock = trim($_POST['stock'] ?? '0');

  // Validaciones
  if (empty($nombre)) {
    $mensaje = 'El nombre del producto es obligatorio';
    $tipoMensaje = 'danger';
  } elseif (!empty($precio) && !is_numeric($precio)) {
    $mensaje = 'El precio debe ser un número válido';
    $tipoMensaje = 'danger';
  } elseif (!is_numeric($stock) || $stock < 0) {
    $mensaje = 'El stock debe ser un número positivo';
    $tipoMensaje = 'danger';
  } else {
    // Procesar nueva imagen si se subió
    $nombreImagen = $producto['imagen']; // Mantener la imagen actual por defecto
    // Procesar imagen si se subió una nueva
    $imagenBlob = null; // Por defecto, no se actualiza la imagen
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

    // Si no hubo error, modificar el producto
    if ($tipoMensaje !== 'danger') {
      $resultado = modificarCatalogo($id, $nombre, $descripcion, $precio, $imagenBlob, $categoria, $stock);

      if ($resultado) {
        $_SESSION['mensaje'] = 'Producto actualizado exitosamente';
        $_SESSION['tipo_mensaje'] = 'success';
        header('Location: ../catalogo_list.php');
        exit;
      } else {
        $mensaje = 'Error al actualizar el producto';
        $tipoMensaje = 'danger';
      }
    }
  }

  // Actualizar datos para mostrar en el formulario
  $producto['nombre'] = $nombre;
  $producto['descripcion'] = $descripcion;
  $producto['precio'] = $precio;
  $producto['categoria'] = $categoria;
  $producto['stock'] = $stock;
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
            <i class="fas fa-edit me-2"></i>
            Editar Producto
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
              <label for="nombre" class="form-label">Nombre del Producto *</label>
              <input type="text"
                class="form-control"
                id="nombre"
                name="nombre"
                required
                maxlength="200"
                value="<?php echo htmlspecialchars($producto['nombre']); ?>">
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number"
                  class="form-control"
                  id="precio"
                  name="precio"
                  step="0.01"
                  min="0"
                  value="<?php echo htmlspecialchars($producto['precio']); ?>">
              </div>

              <div class="col-md-6 mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number"
                  class="form-control"
                  id="stock"
                  name="stock"
                  min="0"
                  value="<?php echo htmlspecialchars($producto['stock']); ?>">
              </div>
            </div>

            <div class="mb-3">
              <label for="categoria" class="form-label">Categoría</label>
              <input type="text"
                class="form-control"
                id="categoria"
                name="categoria"
                list="categorias"
                maxlength="100"
                value="<?php echo htmlspecialchars($producto['categoria']); ?>">
              <datalist id="categorias">
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?php echo htmlspecialchars($cat); ?>">
                  <?php endforeach; ?>
              </datalist>
              <div class="form-text">
                Seleccione una categoría existente o escriba una nueva
              </div>
            </div>

            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea class="form-control"
                id="descripcion"
                name="descripcion"
                rows="4"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>

            <div class="mb-3">
              <label for="imagen" class="form-label">Imagen</label>
              <?php if ($producto['imagen']): ?>
                <div class="mb-2">
                  <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['imagen']); ?>"
                    alt="Imagen actual"
                    class="img-thumbnail"
                    style="max-width: 200px;">
                  <p class="text-muted small mb-0">Imagen actual</p>
                </div>
              <?php endif; ?>
              <input type="file"
                class="form-control"
                id="imagen"
                name="imagen"
                accept="image/jpeg,image/jpg,image/png,image/gif">
              <div class="form-text">
                <?php echo $producto['imagen'] ? 'Seleccione una nueva imagen para reemplazar la actual. ' : ''; ?>
                Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB
              </div>
            </div>

            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-between">
              <a href="../catalogo_list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
              </a>
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Guardar Cambios
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