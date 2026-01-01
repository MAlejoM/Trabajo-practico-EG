<?php 
include_once __DIR__ . "/../../src/includes/header.php";
include_once __DIR__ . "/../../src/lib/funciones.php";

// Verificar que sea admin o personal
if (!isset($_SESSION['personal_id'])) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit();
}

$cliente_id = null;
$clientes = [];
$mensaje = '';
$tipo_mensaje = '';

// Si viene de la página de mascotas de un usuario, obtener el cliente_id
if (isset($_GET['cliente_id'])) {
    $cliente_id = intval($_GET['cliente_id']);
    // Verificar que el cliente existe
    $cliente_data = get_cliente_by_id($cliente_id);
    if (!$cliente_data) {
        header('Location: ' . BASE_URL . 'public/mascota_list.php');
        exit();
    }
} else {
    // Si no viene cliente_id, obtener todos los clientes para el selector
    $clientes = get_all_clientes();
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_mascota'])) {
    $nombre = trim($_POST['nombre']);
    $raza = trim($_POST['raza']);
    $color = trim($_POST['color']);
    $fechaDeNac = $_POST['fechaDeNac'];
    $cliente_sel = isset($_POST['cliente_id']) ? intval($_POST['cliente_id']) : $cliente_id;
    
    if (empty($nombre)) {
        $mensaje = 'El nombre de la mascota es obligatorio.';
        $tipo_mensaje = 'danger';
    } elseif (empty($cliente_sel)) {
        $mensaje = 'Debe seleccionar un cliente.';
        $tipo_mensaje = 'danger';
    } else {
        $db = conectarDb();
        
        $foto_blob = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['size'] > 0) {
            $tamaño_mb = $_FILES['foto']['size'] / 1048576; // Convertir a MB
            
            if ($tamaño_mb > 2) {
                $mensaje = 'La imagen no debe pesar más de 2MB.';
                $tipo_mensaje = 'danger';
            } else {
                $foto_blob = file_get_contents($_FILES['foto']['tmp_name']);
            }
        }
        
        if (empty($mensaje)) {
            if ($foto_blob !== null) {
                $stmt = $db->prepare("
                    INSERT INTO Mascotas (clienteId, nombre, raza, color, fechaDeNac, foto, activo) 
                    VALUES (?, ?, ?, ?, ?, ?, 1)
                ");
                $stmt->bind_param("isssss", $cliente_sel, $nombre, $raza, $color, $fechaDeNac, $foto_blob);
            } else {
                $stmt = $db->prepare("
                    INSERT INTO Mascotas (clienteId, nombre, raza, color, fechaDeNac, activo) 
                    VALUES (?, ?, ?, ?, ?, 1)
                ");
                $stmt->bind_param("issss", $cliente_sel, $nombre, $raza, $color, $fechaDeNac);
            }
            
            if ($stmt->execute()) {
                $mensaje = 'Mascota registrada correctamente.';
                $tipo_mensaje = 'success';
                
                $nombre = $raza = $color = $fechaDeNac = '';
            } else {
                $mensaje = 'Error al registrar la mascota.';
                $tipo_mensaje = 'danger';
            }
            
            $stmt->close();
            $db->close();
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
          <?php include_once __DIR__ . "/../../src/includes/menu_lateral.php"; ?>
        </div>
      </div>
    </aside>
    
    <div class="col-12 col-md-8 col-lg-9">
      <div class="card">
        <div class="card-header">
          <h1 class="h4 mb-0">
            <i class="fas fa-paw me-2"></i>
            Nueva Mascota
          </h1>
        </div>
        <div class="card-body">
          <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
              <?php echo htmlspecialchars($mensaje); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>
          
          <form method="post" enctype="multipart/form-data">
            <!-- Selección de cliente (si no viene predefinido) -->
            <?php if ($cliente_id): ?>
              <input type="hidden" name="cliente_id" value="<?php echo $cliente_id; ?>">
              <div class="alert alert-info mb-3">
                <strong>Cliente seleccionado:</strong> 
                <?php 
                $db = conectarDb();
                $stmt = $db->prepare("SELECT u.nombre, u.apellido FROM Clientes c JOIN Usuarios u ON c.usuarioId = u.id WHERE c.id = ?");
                $stmt->bind_param("i", $cliente_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $cliente_info = $result->fetch_assoc();
                echo htmlspecialchars($cliente_info['nombre'] . ' ' . $cliente_info['apellido']);
                $stmt->close();
                $db->close();
                ?>
              </div>
            <?php else: ?>
              <div class="mb-3">
                <label for="cliente_id" class="form-label">Cliente (Dueño) *</label>
                <select class="form-select" id="cliente_id" name="cliente_id" required>
                  <option value="">Seleccione un cliente...</option>
                  <?php foreach ($clientes as $cliente): ?>
                    <option value="<?php echo $cliente['id']; ?>">
                      <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endif; ?>

            <!-- Datos de la mascota -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre de la Mascota *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="raza" class="form-label">Raza</label>
                <input type="text" class="form-control" id="raza" name="raza" 
                       value="<?php echo isset($raza) ? htmlspecialchars($raza) : ''; ?>" 
                       placeholder="Ej: Labrador, Siamés, etc.">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="color" class="form-label">Color</label>
                <input type="text" class="form-control" id="color" name="color" 
                       value="<?php echo isset($color) ? htmlspecialchars($color) : ''; ?>" 
                       placeholder="Ej: Negro, Blanco y marrón, etc.">
              </div>
              <div class="col-md-6 mb-3">
                <label for="fechaDeNac" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fechaDeNac" name="fechaDeNac" 
                       value="<?php echo isset($fechaDeNac) ? $fechaDeNac : ''; ?>">
              </div>
            </div>

            <!-- Foto de la mascota -->
            <div class="mb-3">
              <label for="foto" class="form-label">Foto de la Mascota</label>
              <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
              <div class="form-text">Tamaño máximo: 2MB. Formatos: JPG, PNG, GIF</div>
            </div>

            <!-- Botones -->
            <hr class="my-4">
            <div class="d-flex gap-2 flex-wrap justify-content-between">
              <div class="d-flex gap-2">
                <?php if ($cliente_id): ?>
                  <a href="<?php echo BASE_URL; ?>public/usuarios/mascotas_usuario.php?id=<?php echo $_GET['usuario_id'] ?? ''; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver
                  </a>
                <?php else: ?>
                  <a href="<?php echo BASE_URL; ?>public/mascota_list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Volver a Mascotas
                  </a>
                <?php endif; ?>
              </div>
              <button type="submit" name="guardar_mascota" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Guardar Mascota
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include_once __DIR__ . "/../../src/includes/footer.php";
?>
