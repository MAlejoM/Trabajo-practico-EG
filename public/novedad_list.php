<?php
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/logic/novedades.logic.php";

// Verificar si el usuario es admin (para mostrar botones de administración)
$esAdmin = isset($_SESSION['usuarioId']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';

// Obtener todas las novedades
$novedades = obtenerNovedades();

// Mensajes de éxito/error
$mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : null;
$tipoMensaje = isset($_SESSION['tipo_mensaje']) ? $_SESSION['tipo_mensaje'] : null;
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

// Preprocesar novedades para base64 si tienen imagen (para que el JSON sea válido y manejable en JS)
$novedades_js = array_map(function($n) {
    if (!empty($n['imagen'])) {
        $n['imagen'] = base64_encode($n['imagen']);
    }
    return $n;
}, $novedades);
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
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1 class="h4 mb-0">Novedades</h1>
            <div class="d-flex gap-2 align-items-center flex-wrap">
              <?php if ($esAdmin): ?>
                <a href="novedades/crear.php" class="btn btn-success btn-sm">
                  <i class="fas fa-plus me-1"></i> Nueva Novedad
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="card-body">
          <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipoMensaje === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
              <?php echo htmlspecialchars($mensaje); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <?php if (empty($novedades)): ?>
            <div class="alert alert-info">No hay novedades publicadas aún.</div>
          <?php else: ?>
            <div class="row g-4">
              <?php foreach ($novedades as $novedad): ?>
                <div class="col-sm-6 col-xl-4">
                  <div class="card h-100">
                    <?php if ($novedad['imagen']): ?>
                      <img src="data:image/jpeg;base64,<?php echo base64_encode($novedad['imagen']); ?>"
                        class="card-img-top"
                        alt="<?php echo htmlspecialchars($novedad['titulo']); ?>"
                        style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                      <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-newspaper fa-3x opacity-50"></i>
                      </div>
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">
                      <h5 class="card-title"><?php echo htmlspecialchars($novedad['titulo']); ?></h5>

                      <p class="text-muted small mb-2">
                        <i class="fas fa-calendar me-1"></i>
                        <?php echo date('d/m/Y H:i', strtotime($novedad['fechaPublicacion'])); ?>
                        <?php if (isset($novedad['autorNombre'])): ?>
                          <br>
                          <i class="fas fa-user me-1"></i>
                          <?php echo htmlspecialchars($novedad['autorNombre'] . ' ' . $novedad['autorApellido']); ?>
                        <?php endif; ?>
                      </p>

                      <p class="card-text flex-grow-1">
                        <?php
                        $contenido = strip_tags($novedad['contenido']);
                        echo htmlspecialchars(substr($contenido, 0, 120)) . (strlen($contenido) > 120 ? '...' : '');
                        ?>
                      </p>

                      <div class="btn-group btn-group-sm mt-auto">
                        <button class="btn btn-outline-primary" onclick="verDetalle(<?php echo $novedad['id']; ?>)">
                          <i class="fas fa-eye me-1"></i>Ver más
                        </button>

                        <?php if ($esAdmin): ?>
                          <a href="novedades/editar.php?id=<?php echo $novedad['id']; ?>" class="btn btn-outline-warning">
                            <i class="fas fa-edit"></i>
                          </a>
                          <button class="btn btn-outline-danger" onclick="confirmarEliminar(<?php echo $novedad['id']; ?>)">
                            <i class="fas fa-trash"></i>
                          </button>
                        <?php endif; ?>
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

<!-- Modal para ver detalle -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detalleModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalContenido"></div>
    </div>
  </div>
</div>

<script>
  const novedades = <?php echo json_encode($novedades_js); ?>;

  function verDetalle(id) {
    const novedad = novedades.find(n => n.id == id);
    if (!novedad) return;

    document.getElementById('detalleModalLabel').textContent = novedad.titulo;

    let contenidoHTML = '';

    if (novedad.imagen) {
      contenidoHTML += `<img src="data:image/jpeg;base64,${novedad.imagen}" alt="${novedad.titulo}" class="img-fluid rounded mb-3 w-100">`;
    }

    contenidoHTML += `
      <p class="text-muted small mb-3">
        <i class="fas fa-calendar me-1"></i> ${formatearFecha(novedad.fechaPublicacion)}`;

    if (novedad.autorNombre) {
      contenidoHTML += `<br><i class="fas fa-user me-1"></i> ${novedad.autorNombre} ${novedad.autorApellido}`;
    }

    contenidoHTML += `
      </p>
      <div style="white-space: pre-wrap;">${novedad.contenido}</div>
    `;

    document.getElementById('modalContenido').innerHTML = contenidoHTML;

    const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
    modal.show();
  }

  function formatearFecha(fechaStr) {
    const fecha = new Date(fechaStr);
    return fecha.toLocaleDateString('es-AR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  function confirmarEliminar(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta novedad? Esta acción no se puede deshacer.')) {
      window.location.href = 'novedades/eliminar.php?id=' + id;
    }
  }
</script>

<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>