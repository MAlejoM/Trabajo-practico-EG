<?php
include_once __DIR__ . "/../src/lib/funciones.php";

// Definir BASE_URL si no está definida (necesaria para redirecciones y enlaces AJAX)
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

// Obtener parámetro para mostrar inactivos
$mostrar_inactivos = isset($_GET['inactivos']) && $_GET['inactivos'] === '1';

// Manejo de búsqueda AJAX - Debe ser antes de incluir el header.php para evitar output HTML extra
if (isset($_GET['ajax_search'])) {
  $termino = $_GET['q'] ?? '';
  if (!empty($termino)) {
    $mascotas = search_mascotas($termino);
  } else {
    $mascotas = get_all_mascotas();
  }

  if (empty($mascotas)) {
    echo "<tr><td colspan='7' class='text-center'>No se encontraron mascotas.</td></tr>";
  } else {
    foreach ($mascotas as $mascota) {
      $badgeClass = $mascota['activo'] == 1 ? 'success' : 'secondary';
      $estado = $mascota['activo'] == 1 ? 'Activo' : 'Inactivo';
      $nombreMascota = htmlspecialchars($mascota['nombre']);
      $raza = htmlspecialchars($mascota['raza']);
      $color = htmlspecialchars($mascota['color']);
      $dueno = htmlspecialchars($mascota['nombre_cliente'] . ' ' . $mascota['apellido_cliente']);
      $id = $mascota['id'];

      echo "<tr>";
      echo "<td>";
      if (!empty($mascota['foto'])) {
        echo "<img src='data:image/jpeg;base64," . base64_encode($mascota['foto']) . "' class='rounded' style='width: 40px; height: 40px; object-fit: cover;' />";
      } else {
        echo "<div class='bg-light rounded d-flex align-items-center justify-content-center' style='width: 40px; height: 40px;'><i class='fas fa-paw text-muted'></i></div>";
      }
      echo "</td>";
      echo "<td class='fw-semibold'>$nombreMascota</td>";
      echo "<td>$raza</td>";
      echo "<td>$color</td>";
      echo "<td>$dueno</td>";
      echo "<td><span class='badge bg-$badgeClass'>$estado</span></td>";
      echo "<td>
                    <div class='btn-group btn-group-sm'>
                        <a href='" . BASE_URL . "public/mascotas/editar_mascota.php?id=$id' class='btn btn-outline-secondary' title='Editar'><i class='fas fa-edit'></i></a>
                        <a href='" . BASE_URL . "public/atenciones/atencion_list_by_mascota.php?id=$id' class='btn btn-outline-info' title='Atenciones'><i class='fas fa-notes-medical'></i></a>
                    </div>
                  </td>";
      echo "</tr>";
    }
  }
  exit(); // Detener ejecución para AJAX
}

include_once __DIR__ . "/../src/includes/header.php";

$mascotas = get_all_mascotas($mostrar_inactivos);
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
          <h1 class="h4 mb-0">Gestión de Mascotas</h1>
          <div class="d-flex align-items-center gap-3">
            <div class="form-check form-switch d-flex align-items-center mb-0">
              <input class="form-check-input me-2" type="checkbox" id="mostrarInactivos" 
                     <?php echo $mostrar_inactivos ? 'checked' : ''; ?>
                     onchange="window.location.href='?inactivos=' + (this.checked ? '1' : '0')">
              <label class="form-check-label small" for="mostrarInactivos">Ver todas (incluir inactivas)</label>
            </div>
            <a href="<?php echo BASE_URL; ?>public/mascotas/nueva_mascota.php" class="btn btn-success btn-sm">Nueva Mascota</a>
          </div>
        </div>

        <div class="card-body">
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
                  <th style="width: 60px;">Foto</th>
                  <th>Nombre</th>
                  <th>Raza</th>
                  <th>Color</th>
                  <th>Dueño</th>
                  <th>Estado</th>
                  <th style="width: 100px;">Acciones</th>
                </tr>
              </thead>
              <tbody id="mascotaTableBody">
                <?php if (empty($mascotas)): ?>
                  <tr>
                    <td colspan="7" class="text-center py-4">No hay mascotas registradas.</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($mascotas as $mascota): ?>
                    <tr>
                      <td>
                        <?php if (!empty($mascota['foto'])): ?>
                          <img src="data:image/jpeg;base64,<?php echo base64_encode($mascota['foto']); ?>" class="rounded" style="width: 40px; height: 40px; object-fit: cover;" alt="<?php echo $mascota['nombre']; ?>" />
                        <?php else: ?>
                          <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-paw text-muted"></i>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td class="fw-semibold"><?php echo htmlspecialchars($mascota['nombre']); ?></td>
                      <td><?php echo htmlspecialchars($mascota['raza']); ?></td>
                      <td><?php echo htmlspecialchars($mascota['color']); ?></td>
                      <td><?php echo htmlspecialchars($mascota['nombre_cliente'] . ' ' . $mascota['apellido_cliente']); ?></td>
                      <td>
                        <span class="badge bg-<?php echo $mascota['activo'] == 1 ? 'success' : 'secondary'; ?>">
                          <?php echo $mascota['activo'] == 1 ? 'Activo' : 'Inactivo'; ?>
                        </span>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <a href="<?php echo BASE_URL; ?>public/mascotas/editar_mascota.php?id=<?php echo $mascota['id']; ?>" class="btn btn-outline-secondary" title="Editar"><i class="fas fa-edit"></i></a>
                          <a href="<?php echo BASE_URL; ?>public/atenciones/atencion_list_by_mascota.php?id=<?php echo $mascota['id']; ?>" class="btn btn-outline-info" title="Atenciones"><i class="fas fa-notes-medical"></i></a>
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
  //Para poder buscar dinamicamente
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('mascotaTableBody');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
      clearTimeout(debounceTimer);
      const query = this.value.trim();

      debounceTimer = setTimeout(() => {
        fetch(`mascota_list.php?ajax_search=1&q=${encodeURIComponent(query)}`)
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
</script>

<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>