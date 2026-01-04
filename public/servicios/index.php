<?php
include_once __DIR__ . "/../../src/Templates/header.php";
// Aún necesario para otras cosas? Posiblemente sí, auth, menús.
// include_once __DIR__ . "/../../src/logic/servicios.logic.php"; // REMOVED

use App\Modules\Servicios\ServicioService;
use App\Modules\Usuarios\UsuarioService;

// Solo administradores pueden gestionar servicios
if (!UsuarioService::esAdmin()) {
    header("Location: " . BASE_URL . "public/index.php");
    exit();
}

// Obtener parámetro para mostrar inactivos
$mostrar_inactivos = isset($_GET['inactivos']) && $_GET['inactivos'] === '1';

// Manejo de búsqueda AJAX
if (isset($_GET['ajax_search'])) {
    $termino = $_GET['q'] ?? '';
    $servicios = ServicioService::getAll($mostrar_inactivos);
    if (!empty($termino)) {
        $servicios = array_filter($servicios, function ($s) use ($termino) {
            return strpos(strtolower($s['nombre']), strtolower($termino)) !== false;
        });
    }

    if (empty($servicios)) {
        echo "<tr><td colspan='5' class='text-center py-4'>No se encontraron servicios.</td></tr>";
    } else {
        foreach ($servicios as $s) {
            $badgeClass = $s['activo'] == 1 ? 'success' : 'secondary';
            $estado = $s['activo'] == 1 ? 'Activo' : 'Inactivo';
            $nombre = htmlspecialchars($s['nombre']);
            $precio = number_format($s['precio'], 2, ',', '.');
            $id = $s['id'];
            $mutedClass = !$s['activo'] ? 'text-muted' : '';

            echo "<tr class='$mutedClass'>";
            echo "<td>$id</td>";
            echo "<td><strong>$nombre</strong></td>";
            echo "<td>$$precio</td>";
            echo "<td><span class='badge bg-$badgeClass'>$estado</span></td>";
            echo "<td>
                    <div class='btn-group btn-group-sm'>
                        <a href='editar.php?id=$id' class='btn btn-outline-secondary' title='Editar'><i class='fas fa-edit'></i></a>";
            if ($s['activo']) {
                echo "<a href='?accion=baja&id=$id&inactivos=" . ($mostrar_inactivos ? '1' : '0') . "' 
                         class='btn btn-outline-danger' 
                         onclick=\"return confirm('¿Está seguro de desactivar este servicio?')\" title='Desactivar'>
                         <i class='fas fa-toggle-on'></i></a>";
            } else {
                echo "<a href='?accion=reactivar&id=$id&inactivos=" . ($mostrar_inactivos ? '1' : '0') . "' 
                         class='btn btn-outline-success' title='Reactivar'>
                         <i class='fas fa-toggle-off'></i></a>";
            }
            echo "</div></td></tr>";
        }
    }
    exit();
}

// Manejo de acciones (baja/reactivación)
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['accion'] === 'baja') {
        ServicioService::delete($id);
    } elseif ($_GET['accion'] === 'reactivar') {
        ServicioService::reactivate($id);
    }
    header("Location: index.php?inactivos=" . ($mostrar_inactivos ? '1' : '0'));
    exit();
}

$servicios = ServicioService::getAll($mostrar_inactivos);
?>

<div class="container py-4">
    <div class="row g-4">
        <aside class="col-md-4 col-lg-3 d-none d-md-block">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header fw-semibold">Menú principal</div>
                <div class="card-body d-grid gap-2">
                    <?php include_once __DIR__ . "/../../src/Templates/menu_lateral.php"; ?>
                </div>
            </div>
        </aside>

        <div class="col-12 col-md-8 col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h1 class="h4 mb-0">Gestión de Servicios</h1>
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check form-switch d-flex align-items-center mb-0">
                            <input class="form-check-input me-2" type="checkbox" id="mostrarInactivos"
                                <?php echo $mostrar_inactivos ? 'checked' : ''; ?>
                                onchange="window.location.href='?inactivos=' + (this.checked ? '1' : '0')">
                            <label class="form-check-label small" for="mostrarInactivos">Ver inactivos</label>
                        </div>
                        <a href="crear.php" class="btn btn-success btn-sm">Nuevo Servicio</a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Buscador -->
                    <div class="input-group mb-4">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Buscar por nombre..." autocomplete="off">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Servicio</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="servicioTableBody">
                                <?php if (empty($servicios)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No se encontraron servicios registrados.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($servicios as $s): ?>
                                        <tr class="<?php echo !$s['activo'] ? 'text-muted' : ''; ?>">
                                            <td><?php echo $s['id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($s['nombre']); ?></strong></td>
                                            <td>$<?php echo number_format($s['precio'], 2, ',', '.'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $s['activo'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $s['activo'] ? 'Activo' : 'Inactivo'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="editar.php?id=<?php echo $s['id']; ?>" class="btn btn-outline-secondary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($s['activo']): ?>
                                                        <a href="?accion=baja&id=<?php echo $s['id']; ?>&inactivos=<?php echo $mostrar_inactivos ? '1' : '0'; ?>"
                                                            class="btn btn-outline-danger"
                                                            onclick="return confirm('¿Está seguro de desactivar este servicio?')" title="Desactivar">
                                                            <i class="fas fa-toggle-on"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="?accion=reactivar&id=<?php echo $s['id']; ?>&inactivos=<?php echo $mostrar_inactivos ? '1' : '0'; ?>"
                                                            class="btn btn-outline-success" title="Reactivar">
                                                            <i class="fas fa-toggle-off"></i>
                                                        </a>
                                                    <?php endif; ?>
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
        const tableBody = document.getElementById('servicioTableBody');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            debounceTimer = setTimeout(() => {
                fetch(`index.php?ajax_search=1&q=${encodeURIComponent(query)}&inactivos=<?php echo $mostrar_inactivos ? '1' : '0'; ?>`)
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

<?php include_once __DIR__ . "/../../src/Templates/footer.php"; ?>