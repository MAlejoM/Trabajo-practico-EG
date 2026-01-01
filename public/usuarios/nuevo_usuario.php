<?php 
include_once __DIR__ . "/../../src/includes/header.php";
include_once __DIR__ . "/../../src/lib/funciones.php";

if (!isset($_SESSION['personal_id']) || !verificar_es_admin()) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

// Obtener roles para el selector
$db = conectarDb();
$stmt = $db->prepare("SELECT id, nombre FROM Roles ORDER BY nombre ASC");
$stmt->execute();
$result = $stmt->get_result();
$roles = [];
while ($row = $result->fetch_assoc()) {
    $roles[] = $row;
}
$stmt->close();
$db->close();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    $email = trim($_POST['email']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $clave = $_POST['clave'];
    $confirmar_clave = $_POST['confirmar_clave'];
    $tipo = $_POST['tipo']; 
    $rol_id = isset($_POST['rol_id']) ? intval($_POST['rol_id']) : null;
    
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
    $ciudad = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : '';
    
    if (empty($email) || empty($nombre) || empty($apellido) || empty($clave)) {
        $mensaje = 'Todos los campos obligatorios deben estar completos.';
        $tipo_mensaje = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El email no es válido.';
        $tipo_mensaje = 'danger';
    } elseif (strlen($clave) < 6) {
        $mensaje = 'La contraseña debe tener al menos 6 caracteres.';
        $tipo_mensaje = 'danger';
    } elseif ($clave !== $confirmar_clave) {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipo_mensaje = 'danger';
    } elseif ($tipo === 'personal' && empty($rol_id)) {
        $mensaje = 'Debe seleccionar un rol para el personal.';
        $tipo_mensaje = 'danger';
    } else {
        $db = conectarDb();
        
        $stmt = $db->prepare("SELECT id FROM Usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $mensaje = 'El email ya está registrado.';
            $tipo_mensaje = 'danger';
            $stmt->close();
        } else {
            $stmt->close();
            
            // Hash
            $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
            
            $db->begin_transaction();
            
            try {
                $stmt = $db->prepare("INSERT INTO Usuarios (email, clave, nombre, apellido, activo) VALUES (?, ?, ?, ?, 1)");
                $stmt->bind_param("ssss", $email, $clave_hash, $nombre, $apellido);
                $stmt->execute();
                $usuario_id = $db->insert_id;
                $stmt->close();
                
                if ($tipo === 'cliente') {
                    // Insertar cliente
                    $stmt = $db->prepare("INSERT INTO Clientes (id, usuarioId, telefono, direccion, ciudad) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("iisss", $usuario_id, $usuario_id, $telefono, $direccion, $ciudad);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    // Insertar personal
                    $stmt = $db->prepare("INSERT INTO Personal (id, usuarioId, rolId, activo) VALUES (?, ?, ?, 1)");
                    $stmt->bind_param("iii", $usuario_id, $usuario_id, $rol_id);
                    $stmt->execute();
                    $stmt->close();
                }
                
                $db->commit();
                $mensaje = 'Usuario creado correctamente.';
                $tipo_mensaje = 'success';
                
                // Limpiar formulario
                $email = $nombre = $apellido = $telefono = $direccion = $ciudad = '';
            } catch (Exception $e) {
                $db->rollback();
                $mensaje = 'Error al crear el usuario: ' . $e->getMessage();
                $tipo_mensaje = 'danger';
            }
        }
        
        $db->close();
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
            <i class="fas fa-user-plus me-2"></i>
            Nuevo Usuario
          </h1>
        </div>
        <div class="card-body">
          <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
              <?php echo htmlspecialchars($mensaje); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>
          
          <form method="post">
            <!-- Tipo de usuario -->
            <div class="mb-4">
              <label class="form-label fw-semibold">Tipo de Usuario *</label>
              <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="tipo" id="tipo_cliente" value="cliente" checked>
                <label class="btn btn-outline-primary" for="tipo_cliente">
                  <i class="fas fa-user me-1"></i> Cliente
                </label>
                
                <input type="radio" class="btn-check" name="tipo" id="tipo_personal" value="personal">
                <label class="btn btn-outline-success" for="tipo_personal">
                  <i class="fas fa-user-tie me-1"></i> Personal
                </label>
              </div>
            </div>

            <!-- Datos básicos -->
            <h5 class="border-bottom pb-2 mb-3">Datos Básicos</h5>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="apellido" class="form-label">Apellido *</label>
                <input type="text" class="form-control" id="apellido" name="apellido" 
                       value="<?php echo isset($apellido) ? htmlspecialchars($apellido) : ''; ?>" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email *</label>
              <input type="email" class="form-control" id="email" name="email" 
                     value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="clave" class="form-label">Contraseña * (mínimo 6 caracteres)</label>
                <input type="password" class="form-control" id="clave" name="clave" minlength="6" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="confirmar_clave" class="form-label">Confirmar Contraseña *</label>
                <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" minlength="6" required>
              </div>
            </div>

            <!-- Campos para Personal (ocultos por defecto) -->
            <div id="campos_personal" style="display: none;">
              <h5 class="border-bottom pb-2 mb-3">Datos del Personal</h5>
              <div class="mb-3">
                <label for="rol_id" class="form-label">Rol *</label>
                <select class="form-select" id="rol_id" name="rol_id">
                  <option value="">Seleccione un rol...</option>
                  <?php foreach ($roles as $rol): ?>
                    <option value="<?php echo $rol['id']; ?>"><?php echo htmlspecialchars($rol['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Campos para Cliente (visibles por defecto) -->
            <div id="campos_cliente">
              <h5 class="border-bottom pb-2 mb-3">Datos del Cliente</h5>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="telefono" class="form-label">Teléfono</label>
                  <input type="tel" class="form-control" id="telefono" name="telefono" 
                         value="<?php echo isset($telefono) ? htmlspecialchars($telefono) : ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="ciudad" class="form-label">Ciudad</label>
                  <input type="text" class="form-control" id="ciudad" name="ciudad" 
                         value="<?php echo isset($ciudad) ? htmlspecialchars($ciudad) : ''; ?>">
                </div>
              </div>
              <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <textarea class="form-control" id="direccion" name="direccion" rows="2"><?php echo isset($direccion) ? htmlspecialchars($direccion) : ''; ?></textarea>
              </div>
            </div>

            <!-- Botones -->
            <hr class="my-4">
            <div class="d-flex gap-2 justify-content-between">
              <a href="<?php echo BASE_URL; ?>public/usuarios/usuario_list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
              </a>
              <button type="submit" name="crear_usuario" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Crear Usuario
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Toggle entre campos de cliente y personal
document.querySelectorAll('input[name="tipo"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const camposCliente = document.getElementById('campos_cliente');
    const camposPersonal = document.getElementById('campos_personal');
    const rolSelect = document.getElementById('rol_id');
    
    if (this.value === 'cliente') {
      camposCliente.style.display = 'block';
      camposPersonal.style.display = 'none';
      rolSelect.removeAttribute('required');
    } else {
      camposCliente.style.display = 'none';
      camposPersonal.style.display = 'block';
      rolSelect.setAttribute('required', 'required');
    }
  });
});
</script>

<?php
include_once __DIR__ . "/../../src/includes/footer.php";
?>
