<?php 
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/lib/funciones.php";

// Verificar que sea personal autorizado
if (!isset($_SESSION['personal_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit();
}

// Obtener parámetro para mostrar inactivos
$mostrar_inactivos = isset($_GET['inactivos']) && $_GET['inactivos'] === '1';
$atenciones = get_all_atenciones($mostrar_inactivos);
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
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h1 class="h4 mb-0">Gestión de Atenciones</h1>
          <div class="d-flex align-items-center gap-3">
            <div class="form-check form-switch d-flex align-items-center mb-0">
              <input class="form-check-input me-2" type="checkbox" id="mostrarInactivos" 
                     <?php echo $mostrar_inactivos ? 'checked' : ''; ?>
                     onchange="window.location.href='?inactivos=' + (this.checked ? '1' : '0')">
              <label class="form-check-label small" for="mostrarInactivos">Ver todas (incluir inactivas)</label>
            </div>
            <a href="<?php echo BASE_URL; ?>public/atenciones/registrar_atencion.php" class="btn btn-success btn-sm">Nueva Atención</a>
          </div>
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
                    <th>Visibilidad</th>
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
                        <span class="badge bg-<?php echo $atencion['activo'] ? 'success' : 'secondary'; ?>">
                          <?php echo $atencion['activo'] ? 'Activo' : 'Inactivo'; ?>
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