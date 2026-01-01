<?php
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/lib/funciones.php";

// Verificar que sea personal veterinario
if (!isset($_SESSION['personal_id'])) {
    header("Location: " . BASE_URL . "public/login.php");
    exit();
}

$fecha_hoy = date('Y-m-d');
$fecha_seleccionada = $_GET['fecha'] ?? $fecha_hoy;
$mostrar_inactivos = isset($_GET['inactivos']) && $_GET['inactivos'] === '1';
$atenciones_del_dia = get_atenciones_by_fecha($fecha_seleccionada, $mostrar_inactivos);
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
          <h1 class="h4 mb-0">Atenciones del Día</h1>
          <div class="d-flex align-items-center gap-3">
            <div class="form-check form-switch d-flex align-items-center mb-0">
              <input class="form-check-input me-2" type="checkbox" id="mostrarInactivos" 
                     <?php echo $mostrar_inactivos ? 'checked' : ''; ?>
                     onchange="window.location.href='?fecha=<?php echo $fecha_seleccionada; ?>&inactivos=' + (this.checked ? '1' : '0')">
              <label class="form-check-label small" for="mostrarInactivos">Ver todas (incluir inactivas)</label>
            </div>
            <a href="<?php echo BASE_URL; ?>public/atenciones/registrar_atencion.php" class="btn btn-success btn-sm">Nueva Atención</a>
          </div>
        </div>
        <div class="card-body">
          <form action="servicios.php" method="GET" class="mb-4">
            <div class="row g-2">
              <div class="col-auto">
                <label for="fecha" class="form-label">Fecha:</label>
              </div>
              <div class="col-auto">
                <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo $fecha_seleccionada; ?>">
              </div>
              <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Filtrar</button>
              </div>
              <div class="col-auto">
                <a href="servicios.php" class="btn btn-outline-secondary">Hoy</a>
              </div>
            </div>
          </form>
          
          <?php if (empty($atenciones_del_dia)): ?>
            <div class="alert alert-info">
              No hay atenciones programadas para el <?php echo date('d/m/Y', strtotime($fecha_seleccionada)); ?>.
            </div>
          <?php else: ?>
            <div class="mb-3">
              <h5><?php echo count($atenciones_del_dia); ?> atenciones programadas para el <?php echo date('d/m/Y', strtotime($fecha_seleccionada)); ?></h5>
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Hora</th>
                    <th>Mascota</th>
                    <th>Cliente</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Visibilidad</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($atenciones_del_dia as $atencion): ?>
                    <tr class="<?php echo $atencion['estado'] == 'completada' ? 'table-success' : ($atencion['estado'] == 'cancelada' ? 'table-danger' : ''); ?>">
                      <td><strong><?php echo date('H:i', strtotime($atencion['fecha'])); ?></strong></td>
                      <td><?php echo $atencion['nombre_mascota']; ?></td>
                      <td><?php echo $atencion['nombre_cliente'] . ' ' . $atencion['apellido_cliente']; ?></td>
                      <td><?php echo substr($atencion['motivo'], 0, 50) . (strlen($atencion['motivo']) > 50 ? '...' : ''); ?></td>
                      <td>
                        <span class="badge bg-<?php 
                          echo $atencion['estado'] == 'completada' ? 'success' : 
                               ($atencion['estado'] == 'cancelada' ? 'danger' : 'warning'); 
                        ?>">
                          <?php echo ucfirst($atencion['estado']); ?>
                        </span>
                      </td>
                      <td>
                        <span class="badge bg-<?php echo $atencion['activo'] ? 'success' : 'secondary'; ?>">
                          <?php echo $atencion['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <a href="<?php echo BASE_URL; ?>public/atenciones/ver_atencion.php?id=<?php echo $atencion['id']; ?>" class="btn btn-outline-primary">Ver</a>
                          <?php if ($atencion['estado'] != 'completada'): ?>
                            <a href="<?php echo BASE_URL; ?>public/atenciones/editar_atencion.php?id=<?php echo $atencion['id']; ?>" class="btn btn-outline-secondary">Editar</a>
                          <?php endif; ?>
                        </div>
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