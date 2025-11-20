<?php 
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/lib/funciones.php";

// Verificar que sea personal autorizado
if (!isset($_SESSION['personal_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit();
}

$atenciones = get_all_atenciones();
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
          <h1 class="h4 mb-0">Gestión de Atenciones</h1>
          <a href="<?php echo BASE_URL; ?>public/atenciones/registrar_atencion.php" class="btn btn-success">Nueva Atención</a>
        </div>
        <div class="card-body">
          <?php if (empty($atenciones)): ?>
            <div class="alert alert-info">No hay atenciones registradas.</div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Mascota</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($atenciones as $atencion): ?>
                    <tr>
                      <td><?php echo $atencion['id']; ?></td>
                      <td><?php echo $atencion['nombre_mascota']; ?></td>
                      <td><?php echo $atencion['nombre_cliente'] . ' ' . $atencion['apellido_cliente']; ?></td>
                      <td><?php echo date('d/m/Y H:i', strtotime($atencion['fecha'])); ?></td>
                      <td><?php echo substr($atencion['motivo'], 0, 50) . (strlen($atencion['motivo']) > 50 ? '...' : ''); ?></td>
                      <td>
                        <span class="badge bg-<?php echo $atencion['estado'] == 'completada' ? 'success' : 'warning'; ?>">
                          <?php echo ucfirst($atencion['estado']); ?>
                        </span>
                      </td>
                      <td>
                        <a href="<?php echo BASE_URL; ?>public/atenciones/ver_atencion.php?id=<?php echo $atencion['id']; ?>" class="btn btn-sm btn-outline-primary">Ver</a>
                        <a href="<?php echo BASE_URL; ?>public/atenciones/editar_atencion.php?id=<?php echo $atencion['id']; ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
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