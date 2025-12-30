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

// Obtener datos del usuario
$usuario = get_usuario_completo_by_id($usuario_id);

if (!$usuario) {
    header('Location: ' . BASE_URL . 'public/usuarios/usuario_list.php');
    exit();
}

// Verificar que sea un cliente
if ($usuario['tipo_usuario'] !== 'Cliente') {
    header('Location: ' . BASE_URL . 'public/usuarios/editar_usuario.php?id=' . $usuario_id);
    exit();
}

// Obtener mascotas
$mascotas = get_mascotas_by_cliente_id($usuario['cliente_id']);
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
        <div class="card-header d-flex justify-content-between align-items-center">
          <h1 class="h4 mb-0">Mascotas de <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h1>
          <a href="<?php echo BASE_URL; ?>public/mascotas/nueva_mascota.php?cliente_id=<?php echo $usuario['cliente_id']; ?>" class="btn btn-success btn-sm">
            <i class="fas fa-plus me-1"></i> Nueva Mascota
          </a>
        </div>
        <div class="card-body">
          <!-- Información del cliente -->
          <div class="alert alert-info mb-4">
            <strong>Cliente:</strong> <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?>
            <br>
            <strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?>
            <?php if ($usuario['telefono']): ?>
              <br><strong>Teléfono:</strong> <?php echo htmlspecialchars($usuario['telefono']); ?>
            <?php endif; ?>
          </div>

          <?php if (empty($mascotas)): ?>
            <div class="alert alert-warning">
              <i class="fas fa-info-circle me-1"></i>
              Este cliente no tiene mascotas registradas.
            </div>
          <?php else: ?>
            <div class="row g-3">
              <?php foreach ($mascotas as $mascota): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                  <div class="card h-100">
                    <?php if (!empty($mascota['foto'])): ?>
                      <img src="data:image/jpeg;base64,<?php echo base64_encode($mascota['foto']); ?>" 
                           class="card-img-top" 
                           style="height: 200px; object-fit: cover;" 
                           alt="<?php echo htmlspecialchars($mascota['nombre']); ?>">
                    <?php else: ?>
                      <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-paw fa-3x text-muted"></i>
                      </div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                      <h5 class="card-title"><?php echo htmlspecialchars($mascota['nombre']); ?></h5>
                      <p class="card-text small text-muted mb-2">
                        <?php if ($mascota['raza']): ?>
                          <strong>Raza:</strong> <?php echo htmlspecialchars($mascota['raza']); ?><br>
                        <?php endif; ?>
                        <?php if ($mascota['color']): ?>
                          <strong>Color:</strong> <?php echo htmlspecialchars($mascota['color']); ?><br>
                        <?php endif; ?>
                        <?php if ($mascota['fechaDeNac']): ?>
                          <strong>Fecha Nac:</strong> <?php echo date('d/m/Y', strtotime($mascota['fechaDeNac'])); ?><br>
                        <?php endif; ?>
                        <?php if ($mascota['fechaMuerte']): ?>
                          <strong>Fecha Muerte:</strong> <?php echo date('d/m/Y', strtotime($mascota['fechaMuerte'])); ?><br>
                        <?php endif; ?>
                      </p>
                      
                      <div class="mt-auto">
                        <span class="badge bg-<?php echo $mascota['activo'] ? 'success' : 'secondary'; ?> mb-2">
                          <?php echo $mascota['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                        <div class="d-flex gap-1">
                          <a href="<?php echo BASE_URL; ?>public/mascotas/ver_mascota.php?id=<?php echo $mascota['id']; ?>" 
                             class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="fas fa-eye me-1"></i> Ver
                          </a>
                          <a href="<?php echo BASE_URL; ?>public/mascotas/editar_mascota.php?id=<?php echo $mascota['id']; ?>" 
                             class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="fas fa-edit me-1"></i> Editar
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Botón de regreso -->
          <div class="mt-4">
            <a href="<?php echo BASE_URL; ?>public/usuarios/editar_usuario.php?id=<?php echo $usuario_id; ?>" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i> Volver a Editar Usuario
            </a>
            <a href="<?php echo BASE_URL; ?>public/usuarios/usuario_list.php" class="btn btn-outline-secondary">
              <i class="fas fa-list me-1"></i> Lista de Usuarios
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include_once __DIR__ . "/../../src/includes/footer.php";
?>
