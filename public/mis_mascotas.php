<?php 
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/lib/funciones.php";

if (!isset($_SESSION['cliente_id'])) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit();
}

$mascotas = get_mascotas_by_cliente_id($_SESSION['cliente_id']);
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
        <div class="card-header">
          <h1 class="h4 mb-0">
            <i class="fas fa-paw me-2"></i>
            Mis Mascotas
          </h1>
        </div>
        <div class="card-body">
          <?php if (empty($mascotas)): ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-1"></i>
              No tienes mascotas registradas. Contacta al administrador para agregar una.
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
                          <?php
                          // Calculo d edad
                          $fecha_nac = new DateTime($mascota['fechaDeNac']);
                          $hoy = new DateTime();
                          $edad = $fecha_nac->diff($hoy);
                          ?>
                          <strong>Edad:</strong> <?php echo $edad->y; ?> año<?php echo $edad->y != 1 ? 's' : ''; ?> 
                          <?php echo $edad->m; ?> mes<?php echo $edad->m != 1 ? 'es' : ''; ?><br>
                        <?php endif; ?>
                        <?php if ($mascota['fechaMuerte']): ?>
                          <strong class="text-danger">Falleció:</strong> <?php echo date('d/m/Y', strtotime($mascota['fechaMuerte'])); ?><br>
                        <?php endif; ?>
                      </p>
                      
                      <div class="mt-auto">
                        <span class="badge bg-<?php echo $mascota['activo'] ? 'success' : 'secondary'; ?> mb-2">
                          <?php echo $mascota['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                        <?php if ($mascota['fechaMuerte']): ?>
                          <span class="badge bg-warning mb-2">
                            <i class="fas fa-heart-broken me-1"></i> Fallecido
                          </span>
                        <?php endif; ?>
                        <div class="d-flex gap-1">
                          <a href="<?php echo BASE_URL; ?>public/mascotas/ver_mascota.php?id=<?php echo $mascota['id']; ?>" 
                             class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="fas fa-eye me-1"></i> Ver Detalles
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          
          <hr class="my-4">
          <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-1"></i>
            <strong>Nota:</strong> Para agregar o editar mascotas, contacta con el personal de la veterinaria.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>
