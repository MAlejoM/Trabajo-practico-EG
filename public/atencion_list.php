<?php
include_once __DIR__ . "/../src/lib/funciones.php";
include_once __DIR__ . "/../src/includes/header.php";

// Definir BASE_URL si no está definida
if (!defined('BASE_URL')) {
  $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
  $host = $_SERVER['HTTP_HOST'];
  define('BASE_URL', $protocol . $host . "/");
}

// Verificar que sea personal autorizado
if (!isset($_SESSION['personal_id'])) {
  header('Location: ' . BASE_URL . 'public/login.php');
  exit();
}

$user_role = $_SESSION['rol'] ?? '';
$my_personal_id = $_SESSION['personal_id'] ?? null;

// Manejo de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
  $id_a_eliminar = $_POST['eliminar_id'];
  // Verificar permisos antes de eliminar (extra seguridad)
  $atencion_check = get_atencion_by_id($id_a_eliminar);
  if ($atencion_check) {
    if ($user_role === 'admin' || $atencion_check['personalId'] == $my_personal_id) {
      if (delete_atencion($id_a_eliminar)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?eliminado=1");
        exit();
      }
    }
  }
}

// Manejo de búsqueda AJAX
if (isset($_GET['ajax_search'])) {
  $termino = $_GET['q'] ?? '';
  if (!empty($termino)) {
    $atenciones = search_atenciones($termino);
  } else {
    $atenciones = get_all_atenciones();
  }

  if (empty($atenciones)) {
    echo "<tr><td colspan='7' class='text-center py-4'>No se encontraron atenciones.</td></tr>";
  } else {
    foreach ($atenciones as $atencion) {
      $fecha = date('d/m/Y H:i', strtotime($atencion['fechaHora']));
      $mascota = htmlspecialchars($atencion['nombre_mascota'] ?? 'N/A');
      $cliente = htmlspecialchars(($atencion['nombre_cliente'] ?? '') . ' ' . ($atencion['apellido_cliente'] ?? ''));
      $veterinario = htmlspecialchars(($atencion['nombre_personal'] ?? 'N/A') . ' ' . ($atencion['apellido_personal'] ?? ''));
      $titulo = htmlspecialchars($atencion['titulo'] ?? 'Sin título');
      $descripcion = htmlspecialchars($atencion['descripcion'] ?? $atencion['motivo'] ?? '');

      $puedeEditar = ($user_role === 'admin' || $atencion['personalId'] == $my_personal_id);

      echo "<tr>";
      echo "<td>{$atencion['id']}</td>";
      echo "<td class='fw-semibold'>$mascota</td>";
      echo "<td>$cliente</td>";
      echo "<td>$fecha</td>";
      echo "<td><span class='d-inline-block text-truncate' style='max-width: 150px;'>$titulo</span></td>";
      echo "<td>$veterinario</td>";
      echo "<td>
                    <div class='btn-group btn-group-sm'>
                        <button type='button' class='btn btn-outline-primary' data-bs-toggle='modal' data-bs-target='#modalDetalle{$atencion['id']}' title='Ver'>
                            <i class='fas fa-eye'></i>
                        </button>";
      if ($puedeEditar) {
        echo "  <a href='" . BASE_URL . "public/atenciones/editar_atencion.php?id={$atencion['id']}' class='btn btn-outline-secondary' title='Editar'><i class='fas fa-edit'></i></a>";
        echo "  <button type='button' class='btn btn-outline-danger' onclick='confirmarEliminacion({$atencion['id']})' title='Eliminar'><i class='fas fa-trash'></i></button>";
      }
      echo "  </div>
                    
                    <!-- Modal -->
                    <div class='modal fade' id='modalDetalle{$atencion['id']}' tabindex='-1' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title text-dark'>Detalle de Atención #{$atencion['id']}</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <div class='mb-3'>
                                        <h6 class='fw-bold'>Mascota</h6>
                                        <p>$mascota</p>
                                    </div>
                                    <div class='mb-3'>
                                        <h6 class='fw-bold'>Cliente</h6>
                                        <p>$cliente</p>
                                    </div>
                                    <div class='row mb-3'>
                                        <div class='col-6'>
                                            <h6 class='fw-bold'>Fecha y Hora</h6>
                                            <p>$fecha</p>
                                        </div>
                                        <div class='col-6'>
                                            <h6 class='fw-bold'>Veterinario</h6>
                                            <p>$veterinario</p>
                                        </div>
                                    </div>
                                    <div class='mb-3'>
                                        <h6 class='fw-bold'>Título / Motivo</h6>
                                        <p>$titulo</p>
                                    </div>
                                    <div class='mb-0'>
                                        <h6 class='fw-bold'>Descripción</h6>
                                        <p class='text-break mb-0' style='white-space: pre-wrap;'>$descripcion</p>
                                    </div>
                                </div>
                                <div class='modal-footer'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>";
      if ($puedeEditar) {
        echo "              <a href='" . BASE_URL . "public/atenciones/editar_atencion.php?id={$atencion['id']}' class='btn btn-primary'>Editar</a>";
      }
      echo "              </div>
                            </div>
                        </div>
                    </div>
                  </td>";
      echo "</tr>";
    }
  }
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
          <?php if ($user_role === 'admin'): ?>
            <a href="<?php echo BASE_URL; ?>public/atenciones/registrar_atencion.php" class="btn btn-success btn-sm">
              <i class="fas fa-plus me-1"></i>Nueva Atención
            </a>
          <?php endif; ?>
        </div>

        <div class="card-body">
          <?php if (isset($_GET['registrado'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="fas fa-check-circle me-2"></i>Atención registrada con éxito.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <?php if (isset($_GET['eliminado'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="fas fa-check-circle me-2"></i>Atención eliminada correctamente.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <!-- Buscador -->
          <div class="input-group mb-4">
            <span class="input-group-text bg-white border-end-0">
              <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Buscar por mascota o cliente..." autocomplete="off">
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Mascota</th>
                  <th>Cliente</th>
                  <th>Fecha y Hora</th>
                  <th>Título</th>
                  <th>Veterinario</th>
                  <th style="width: 100px;">Acciones</th>
                </tr>
              </thead>
              <tbody id="atencionTableBody">
                <?php if (empty($atenciones)): ?>
                  <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No hay atenciones registradas.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($atenciones as $atencion): ?>
                    <?php
                    $puedeEditar = ($user_role === 'admin' || $atencion['personalId'] == $my_personal_id);
                    $mascota = htmlspecialchars($atencion['nombre_mascota'] ?? 'N/A');
                    $cliente = htmlspecialchars(($atencion['nombre_cliente'] ?? '') . ' ' . ($atencion['apellido_cliente'] ?? ''));
                    $veterinario = htmlspecialchars(($atencion['nombre_personal'] ?? 'N/A') . ' ' . ($atencion['apellido_personal'] ?? ''));
                    $titulo = htmlspecialchars($atencion['titulo'] ?? 'Sin título');
                    $descripcion = htmlspecialchars($atencion['descripcion'] ?? $atencion['motivo'] ?? '');
                    ?>
                    <tr>
                      <td><?php echo $atencion['id']; ?></td>
                      <td class="fw-semibold"><?php echo $mascota; ?></td>
                      <td><?php echo $cliente; ?></td>
                      <td><?php echo date('d/m/Y H:i', strtotime($atencion['fechaHora'])); ?></td>
                      <td>
                        <span class="d-inline-block text-truncate" style="max-width: 150px;">
                          <?php echo $titulo; ?>
                        </span>
                      </td>
                      <td><?php echo $veterinario; ?></td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetalle<?php echo $atencion['id']; ?>" title="Ver">
                            <i class="fas fa-eye"></i>
                          </button>
                          <?php if ($puedeEditar): ?>
                            <a href="<?php echo BASE_URL; ?>public/atenciones/editar_atencion.php?id=<?php echo $atencion['id']; ?>" class="btn btn-outline-secondary" title="Editar"><i class="fas fa-edit"></i></a>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmarEliminacion(<?php echo $atencion['id']; ?>)" title="Eliminar">
                              <i class="fas fa-trash"></i>
                            </button>
                          <?php endif; ?>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="modalDetalle<?php echo $atencion['id']; ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Detalle de Atención #<?php echo $atencion['id']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="mb-3">
                                  <h6 class="fw-bold">Mascota</h6>
                                  <p><?php echo $mascota; ?></p>
                                </div>
                                <div class="mb-3">
                                  <h6 class="fw-bold">Cliente</h6>
                                  <p><?php echo $cliente; ?></p>
                                </div>
                                <div class="row mb-3">
                                  <div class="col-6">
                                    <h6 class="fw-bold">Fecha y Hora</h6>
                                    <p><?php echo date('d/m/Y H:i', strtotime($atencion['fechaHora'])); ?></p>
                                  </div>
                                  <div class="col-6">
                                    <h6 class="fw-bold">Veterinario</h6>
                                    <p><?php echo $veterinario; ?></p>
                                  </div>
                                </div>
                                <div class="mb-3">
                                  <h6 class="fw-bold">Título / Motivo</h6>
                                  <p><?php echo $titulo; ?></p>
                                </div>
                                <div class="mb-0">
                                  <h6 class="fw-bold">Descripción</h6>
                                  <p class="text-break mb-0" style="white-space: pre-wrap;"><?php echo $descripcion; ?></p>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <?php if ($puedeEditar): ?>
                                  <a href="<?php echo BASE_URL; ?>public/atenciones/editar_atencion.php?id=<?php echo $atencion['id']; ?>" class="btn btn-primary">Editar</a>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('atencionTableBody');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
      clearTimeout(debounceTimer);
      const query = this.value.trim();

      debounceTimer = setTimeout(() => {
        fetch(`atencion_list.php?ajax_search=1&q=${encodeURIComponent(query)}`)
          .then(response => response.text())
          .then(html => {
            tableBody.innerHTML = html;
          })
          .catch(error => {
            console.error('Error fetching search results:', error);
          });
      }, 300);
    });
  });

  function confirmarEliminacion(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta atención? Esta acción no se puede deshacer.')) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '';
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'eliminar_id';
      input.value = id;
      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
    }
  }
</script>

<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>