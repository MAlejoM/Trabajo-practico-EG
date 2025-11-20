<?php 
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/lib/funciones.php";

// Verificar que sea personal autorizado
if (!isset($_SESSION['personal_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit();
}

$mascotas = get_all_mascotas();
?>

<div class="container py-4">
  <div class="row g-4">
    <aside class="col-md-4 col-lg-3 d-none d-md-block">
      <div class="card sticky-top" style="top: 1rem;">
        <div class="card-header fw-semibold">Menú principal</div>
        <div class="card-body d-grid gap-2">
          <?php include_once __DIR__ . "/../src/includes/menu_lateral.php"; ?>
        </div>
      </div>
    </aside>
    <div class="col-12 col-md-8 col-lg-9">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h1 class="h4 mb-0">Gestión de Mascotas</h1>
          <a href="<?php echo BASE_URL; ?>public/mascotas/nueva_mascota.php" class="btn btn-success">Nueva Mascota</a>
        </div>
        <div class="card-body">
          <?php if (empty($mascotas)): ?>
            <div class="alert alert-info">No hay mascotas registradas.</div>
          <?php else: ?>
            <div class="row g-3">
              <?php foreach ($mascotas as $mascota): ?>
                <div class="col-12 col-sm-6 col-lg-4">
                  <div class="card h-100">
                    <?php if (!empty($mascota['foto'])): ?>
                      <img src="data:image/jpeg;base64,<?php echo base64_encode($mascota['foto']); ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?php echo $mascota['nombre']; ?>" />
                    <?php else: ?>
                      <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <span class="text-muted">Sin imagen</span>
                      </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                      <h5 class="card-title"><?php echo $mascota['nombre']; ?></h5>
                      <p class="card-text small text-muted mb-2">
                        <strong>Raza:</strong> <?php echo $mascota['raza']; ?><br>
                        <strong>Color:</strong> <?php echo $mascota['color']; ?><br>
                        <strong>Fecha Nac:</strong> <?php echo date('d/m/Y', strtotime($mascota['fechaDeNac'])); ?><br>
                        <strong>Dueño:</strong> <?php echo $mascota['nombre_cliente'] . ' ' . $mascota['apellido_cliente']; ?>
                      </p>
                      <div class="mt-auto">
                        <span class="badge bg-<?php echo $mascota['activo'] == 1 ? 'success' : 'secondary'; ?> mb-2">
                          <?php echo $mascota['activo'] == 1 ? 'Activo' : 'Inactivo'; ?>
                        </span>
                        <div class="d-flex gap-1">
                          <a href="<?php echo BASE_URL; ?>public/mascotas/ver_mascota.php?id=<?php echo $mascota['id']; ?>" class="btn btn-sm btn-outline-primary flex-fill">Ver</a>
                          <a href="<?php echo BASE_URL; ?>public/mascotas/editar_mascota.php?id=<?php echo $mascota['id']; ?>" class="btn btn-sm btn-outline-secondary flex-fill">Editar</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>