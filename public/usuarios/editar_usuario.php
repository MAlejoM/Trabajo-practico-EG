<?php 
include_once __DIR__ . "/../../src/includes/header.php";
include_once __DIR__ . "/../../src/lib/funciones.php";
include_once __DIR__ . "/../../src/logic/usuarios.logic.php";

// Verificar que sea administrador
if (!isset($_SESSION['personal_id']) || !verificar_es_admin()) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit();
}

// Verificar que se proporcionó un ID
if (!isset($_GET['id'])) {
    header('Location: ' . BASE_URL . 'public/usuarios/usuario_list.php');
    exit();
}

$usuario_id = intval($_GET['id']);
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_usuario'])) {
    $datos = [
        'email' => trim($_POST['email']),
        'nombre' => trim($_POST['nombre']),
        'apellido' => trim($_POST['apellido']),
        'activo' => isset($_POST['activo']) ? 1 : 0
    ];
    
    // Validaciones
    if (empty($datos['email']) || empty($datos['nombre']) || empty($datos['apellido'])) {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipo_mensaje = 'danger';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El email no es válido.';
        $tipo_mensaje = 'danger';
    } else {
        // Intentar actualizar
        $resultado = update_usuario_admin($usuario_id, $datos);
        
        if ($resultado) {
            $mensaje = 'Usuario actualizado correctamente.';
            $tipo_mensaje = 'success';
            
            // Si hay datos de cliente, también actualizarlos
            if (isset($_POST['telefono']) && isset($_POST['cliente_id'])) {
                $datos_cliente = [
                    'telefono' => trim($_POST['telefono']),
                    'direccion' => trim($_POST['direccion']),
                    'ciudad' => trim($_POST['ciudad'])
                ];
                update_cliente_datos(intval($_POST['cliente_id']), $datos_cliente);
            }
        } else {
            $mensaje = 'Error al actualizar el usuario. Es posible que el email ya esté en uso.';
            $tipo_mensaje = 'danger';
        }
    }
}

// Obtener datos del usuario
$usuario = get_usuario_completo_by_id($usuario_id);

if (!$usuario) {
    header('Location: ' . BASE_URL . 'public/usuarios/usuario_list.php');
    exit();
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
          <h1 class="h4 mb-0">Editar Usuario</h1>
        </div>
        <div class="card-body">
          <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
              <?php echo htmlspecialchars($mensaje); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>
          
          <!-- Información del usuario -->
          <div class="alert alert-info mb-4">
            <strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
            <br>
            <strong>Tipo:</strong> 
            <span class="badge bg-<?php echo $usuario['tipo_usuario'] === 'Cliente' ? 'info' : 'success'; ?>">
              <?php echo $usuario['tipo_usuario']; ?>
            </span>
            <?php if ($usuario['rol_nombre']): ?>
              <span class="badge bg-secondary"><?php echo htmlspecialchars($usuario['rol_nombre']); ?></span>
            <?php endif; ?>
          </div>
          
          <!-- Formulario de edición -->
          <form method="post">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="apellido" class="form-label">Apellido *</label>
                <input type="text" class="form-control" id="apellido" name="apellido" 
                       value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
              </div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email *</label>
              <input type="email" class="form-control" id="email" name="email" 
                     value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
            </div>

            <!-- Campos específicos para clientes -->
            <?php if ($usuario['tipo_usuario'] === 'Cliente'): ?>
              <hr class="my-4">
              <h5 class="mb-3">Datos del Cliente</h5>
              <input type="hidden" name="cliente_id" value="<?php echo $usuario['cliente_id']; ?>">
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="telefono" class="form-label">Teléfono</label>
                  <input type="tel" class="form-control" id="telefono" name="telefono" 
                         value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="ciudad" class="form-label">Ciudad</label>
                  <input type="text" class="form-control" id="ciudad" name="ciudad" 
                         value="<?php echo htmlspecialchars($usuario['ciudad'] ?? ''); ?>">
                </div>
              </div>
              
              <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <textarea class="form-control" id="direccion" name="direccion" rows="2"><?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?></textarea>
              </div>
            <?php endif; ?>

            <!-- Estado activo -->
            <hr class="my-4">
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="activo" name="activo" 
                     <?php echo $usuario['activo'] ? 'checked' : ''; ?>>
              <label class="form-check-label" for="activo">
                Usuario activo (puede iniciar sesión)
              </label>
            </div>

            <!-- Botones de acción -->
            <div class="d-flex gap-2 flex-wrap justify-content-between mt-4">
              <div class="d-flex gap-2">
                <a href="<?php echo BASE_URL; ?>public/usuarios/usuario_list.php" class="btn btn-secondary">
                  <i class="fas fa-arrow-left me-1"></i> Volver
                </a>
                <?php if ($usuario['tipo_usuario'] === 'Cliente'): ?>
                  <a href="<?php echo BASE_URL; ?>public/usuarios/mascotas_usuario.php?id=<?php echo $usuario_id; ?>" class="btn btn-info">
                    <i class="fas fa-paw me-1"></i> Ver Mascotas
                  </a>
                <?php endif; ?>
              </div>
              <button type="submit" name="guardar_usuario" class="btn btn-success">
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
include_once __DIR__ . "/../../src/includes/footer.php";
?>
