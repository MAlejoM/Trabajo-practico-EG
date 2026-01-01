<?php 
include_once __DIR__ . "/../../src/includes/header.php";
include_once __DIR__ . "/../../src/lib/funciones.php";

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuarioId'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ' . BASE_URL . 'public/mascota_list.php');
    exit();
}

$mascota_id = intval($_GET['id']);

// Obtener datos completos de la mascota
$db = conectarDb();
$stmt = $db->prepare("
    SELECT 
        m.id,
        m.nombre,
        m.raza,
        m.color,
        m.fechaDeNac,
        m.fechaMuerte,
        m.foto,
        m.activo,
        m.clienteId,
        c.telefono,
        c.direccion,
        c.ciudad,
        u.nombre as nombre_cliente,
        u.apellido as apellido_cliente,
        u.email as email_cliente
    FROM Mascotas m
    JOIN Clientes c ON m.clienteId = c.id
    JOIN Usuarios u ON c.usuarioId = u.id
    WHERE m.id = ?
");
$stmt->bind_param("i", $mascota_id);
$stmt->execute();
$result = $stmt->get_result();
$mascota = $result->fetch_assoc();
$stmt->close();

if (!$mascota) {
    header('Location: ' . BASE_URL . 'public/mascota_list.php');
    exit();
}

// Obtener atenciones de la mascota
$stmt = $db->prepare("
    SELECT 
        a.id,
        a.fechaHora,
        a.titulo,
        a.descripcion,
        u.nombre as nombre_personal,
        u.apellido as apellido_personal
    FROM Atenciones a
    JOIN Personal p ON a.personalId = p.id
    JOIN Usuarios u ON p.usuarioId = u.id
    WHERE a.mascotaId = ?
    ORDER BY a.fechaHora DESC
    LIMIT 5
");
$stmt->bind_param("i", $mascota_id);
$stmt->execute();
$result = $stmt->get_result();
$atenciones = [];
while ($row = $result->fetch_assoc()) {
    $atenciones[] = $row;
}
$stmt->close();
$db->close();

// Calcular edad si tiene fecha de nacimiento
$edad = null;
if ($mascota['fechaDeNac']) {
    $fecha_nac = new DateTime($mascota['fechaDeNac']);
    $fecha_actual = $mascota['fechaMuerte'] ? new DateTime($mascota['fechaMuerte']) : new DateTime();
    $edad = $fecha_nac->diff($fecha_actual);
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
        <div class="card-header d-flex justify-content-between align-items-center">
          <h1 class="h4 mb-0">
            <i class="fas fa-paw me-2"></i>
            <?php echo htmlspecialchars($mascota['nombre']); ?>
          </h1>
          <span class="badge bg-<?php echo $mascota['activo'] ? 'success' : 'secondary'; ?>">
            <?php echo $mascota['activo'] ? 'Activo' : 'Inactivo'; ?>
          </span>
        </div>
        <div class="card-body">
          <div class="row">
            <!-- Foto de la mascota -->
            <div class="col-md-4 text-center mb-4">
              <?php if (!empty($mascota['foto'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($mascota['foto']); ?>" 
                     alt="<?php echo htmlspecialchars($mascota['nombre']); ?>" 
                     class="img-fluid rounded shadow-sm" 
                     style="max-height: 300px; object-fit: cover;">
              <?php else: ?>
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                  <i class="fas fa-paw fa-5x text-muted"></i>
                </div>
              <?php endif; ?>
              
              <?php if ($mascota['fechaMuerte']): ?>
                <div class="alert alert-warning mt-3" role="alert">
                  <i class="fas fa-heart-broken me-1"></i>
                  <strong>Falleció el:</strong><br>
                  <?php echo date('d/m/Y', strtotime($mascota['fechaMuerte'])); ?>
                </div>
              <?php endif; ?>
            </div>
            
            <!-- Información de la mascota -->
            <div class="col-md-8">
              <h5 class="border-bottom pb-2 mb-3">Información de la Mascota</h5>
              
              <div class="row mb-3">
                <div class="col-sm-4 text-muted">
                  <i class="fas fa-tag me-1"></i> Nombre:
                </div>
                <div class="col-sm-8 fw-semibold">
                  <?php echo htmlspecialchars($mascota['nombre']); ?>
                </div>
              </div>
              
              <?php if ($mascota['raza']): ?>
                <div class="row mb-3">
                  <div class="col-sm-4 text-muted">
                    <i class="fas fa-dog me-1"></i> Raza:
                  </div>
                  <div class="col-sm-8">
                    <?php echo htmlspecialchars($mascota['raza']); ?>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php if ($mascota['color']): ?>
                <div class="row mb-3">
                  <div class="col-sm-4 text-muted">
                    <i class="fas fa-palette me-1"></i> Color:
                  </div>
                  <div class="col-sm-8">
                    <?php echo htmlspecialchars($mascota['color']); ?>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php if ($mascota['fechaDeNac']): ?>
                <div class="row mb-3">
                  <div class="col-sm-4 text-muted">
                    <i class="fas fa-birthday-cake me-1"></i> Fecha de Nac.:
                  </div>
                  <div class="col-sm-8">
                    <?php echo date('d/m/Y', strtotime($mascota['fechaDeNac'])); ?>
                    <?php if ($edad): ?>
                      <span class="text-muted small">
                        (<?php echo $edad->y; ?> año<?php echo $edad->y != 1 ? 's' : ''; ?> 
                        <?php echo $edad->m; ?> mes<?php echo $edad->m != 1 ? 'es' : ''; ?>)
                      </span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
              
              <h5 class="border-bottom pb-2 mb-3 mt-4">Información del Dueño</h5>
              
              <div class="row mb-3">
                <div class="col-sm-4 text-muted">
                  <i class="fas fa-user me-1"></i> Nombre:
                </div>
                <div class="col-sm-8 fw-semibold">
                  <?php echo htmlspecialchars($mascota['nombre_cliente'] . ' ' . $mascota['apellido_cliente']); ?>
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-sm-4 text-muted">
                  <i class="fas fa-envelope me-1"></i> Email:
                </div>
                <div class="col-sm-8">
                  <a href="mailto:<?php echo htmlspecialchars($mascota['email_cliente']); ?>">
                    <?php echo htmlspecialchars($mascota['email_cliente']); ?>
                  </a>
                </div>
              </div>
              
              <?php if ($mascota['telefono']): ?>
                <div class="row mb-3">
                  <div class="col-sm-4 text-muted">
                    <i class="fas fa-phone me-1"></i> Teléfono:
                  </div>
                  <div class="col-sm-8">
                    <?php echo htmlspecialchars($mascota['telefono']); ?>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php if ($mascota['direccion']): ?>
                <div class="row mb-3">
                  <div class="col-sm-4 text-muted">
                    <i class="fas fa-map-marker-alt me-1"></i> Dirección:
                  </div>
                  <div class="col-sm-8">
                    <?php echo htmlspecialchars($mascota['direccion']); ?>
                    <?php if ($mascota['ciudad']): ?>
                      <br><small class="text-muted"><?php echo htmlspecialchars($mascota['ciudad']); ?></small>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- Historial de atenciones -->
          <?php if (!empty($atenciones)): ?>
            <hr class="my-4">
            <h5 class="mb-3">
              <i class="fas fa-history me-2"></i>
              Últimas Atenciones
            </h5>
            <div class="list-group">
              <?php foreach ($atenciones as $atencion): ?>
                <div class="list-group-item">
                  <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1"><?php echo htmlspecialchars($atencion['titulo']); ?></h6>
                    <small class="text-muted">
                      <?php echo date('d/m/Y H:i', strtotime($atencion['fechaHora'])); ?>
                    </small>
                  </div>
                  <?php if ($atencion['descripcion']): ?>
                    <p class="mb-1 small"><?php echo nl2br(htmlspecialchars($atencion['descripcion'])); ?></p>
                  <?php endif; ?>
                  <small class="text-muted">
                    Atendido por: <?php echo htmlspecialchars($atencion['nombre_personal'] . ' ' . $atencion['apellido_personal']); ?>
                  </small>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <hr class="my-4">
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-1"></i>
              No hay atenciones registradas para esta mascota.
            </div>
          <?php endif; ?>
          
          <!-- Botones de acción -->
          <hr class="my-4">
          <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo BASE_URL; ?>public/mascota_list.php" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i> Volver a Lista
            </a>
            <?php if (isset($_SESSION['personal_id'])): ?>
              <a href="<?php echo BASE_URL; ?>public/mascotas/editar_mascota.php?id=<?php echo $mascota_id; ?>" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Editar Mascota
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include_once __DIR__ . "/../../src/includes/footer.php";
?>
