<?php
include_once __DIR__ . "/../src/Templates/header.php";


use App\Modules\Usuarios\UsuarioService;
use App\Modules\Mascotas\MascotaService;

if (!UsuarioService::esPersonal()) {
  header('Location: ' . BASE_URL . 'index.php');
  exit();
}

$usuario_id = intval($_GET['id'] ?? 0);
if (!$usuario_id) {
  header('Location: index.php');
  exit;
}

$usuario = UsuarioService::getUsuarioCompletoById($usuario_id);
if (!$usuario || $usuario['tipo_usuario'] !== 'Cliente') {
  header('Location: index.php');
  exit;
}

$mascotas = MascotaService::getByClienteId($usuario['cliente_id']);
?>

<div class="container py-4">
  <div class="row g-4">
    <aside class="col-md-4 col-lg-3 d-none d-md-block">
      <div class="card sticky-top" style="top: 1rem;">
        <div class="card-header fw-semibold">Menú principal</div>
        <div class="card-body d-grid gap-2">
          <?php include_once __DIR__ . "/../src/Templates/menu_lateral.php"; ?>
        </div>
      </div>
    </aside>

    <div class="col-12 col-md-8 col-lg-9">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h1 class="h4 mb-0">Mascotas de <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h1>
          <a href="<?php echo BASE_URL; ?>mascotas/crear.php?cliente_id=<?php echo $usuario['cliente_id']; ?>" class="btn btn-success btn-sm">
            <i class="fas fa-plus me-1"></i> Nueva Mascota
          </a>
        </div>
        <div class="card-body text-dark">
          <div class="alert alert-info mb-4">
            <strong>Cliente:</strong> <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?> | <strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?>
          </div>

          <?php if (empty($mascotas)): ?>
            <div class="alert alert-warning">Este cliente no tiene mascotas registradas.</div>
          <?php else: ?>
            <div class="row g-3">
              <?php foreach ($mascotas as $mascota): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                  <div class="card h-100 shadow-sm border-0">
                    <?php if (!empty($mascota['foto'])): ?>
                      <img src="data:image/jpeg;base64,<?php echo base64_encode($mascota['foto']); ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                    <?php else: ?>
                      <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                        <i class="fas fa-paw fa-3x text-muted opacity-25"></i>
                      </div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">
                      <h5 class="card-title h6 fw-bold mb-1"><?php echo htmlspecialchars($mascota['nombre']); ?></h5>
                      <p class="card-text small text-muted mb-3"><?php echo htmlspecialchars($mascota['raza'] ?? 'Sin raza'); ?></p>
                      <div class="mt-auto d-flex gap-2">
                        <a href="<?php echo BASE_URL; ?>mascotas/editar.php?id=<?php echo $mascota['id']; ?>" class="btn btn-sm btn-outline-secondary flex-fill">Editar</a>
                        <a href="<?php echo BASE_URL; ?>atenciones/atencion_list_by_mascota.php?id=<?php echo $mascota['id']; ?>" class="btn btn-sm btn-outline-primary flex-fill">Atenciones</a>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <div class="mt-4 pt-3 border-top">
            <a href="editar.php?id=<?php echo $usuario_id; ?>" class="btn btn-link text-decoration-none ps-0">
              <i class="fas fa-arrow-left me-1"></i> Volver a Usuario
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include_once __DIR__ . "/../src/Templates/footer.php";
?>