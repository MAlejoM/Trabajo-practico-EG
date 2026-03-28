<?php
include_once __DIR__ . "/../../src/Templates/header.php";
include_once __DIR__ . "/../../src/Templates/pagination.php";

use App\Modules\Usuarios\UsuarioService;

// Verificar que sea administrador
if (!UsuarioService::esAdmin()) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$mostrar_inactivos = isset($_GET['inactivos']) && $_GET['inactivos'] === '1';
$filtro_rol        = $_GET['rol'] ?? 'todos';
$page              = max(1, (int)($_GET['page'] ?? 1));

// Roles disponibles para los botones de filtro (consulta ligera)
$roles_disponibles = UsuarioService::getRolesDisponibles($mostrar_inactivos);

// Listado paginado (filtro de rol resuelto en el repository)
$paginacion = UsuarioService::getAllPaginated($page, $mostrar_inactivos, $filtro_rol);
$usuarios   = $paginacion['data'];
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
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h1 class="h4 mb-0">Gestión de Usuarios</h1>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <div class="btn-group btn-group-sm" role="group">
                                <?php $inactivos_param = $mostrar_inactivos ? '&inactivos=1' : ''; ?>
                                <a href="?rol=todos<?php echo $inactivos_param; ?>" class="btn btn-outline-primary <?php echo $filtro_rol === 'todos' ? 'active' : ''; ?>">Todos</a>
                                <?php foreach ($roles_disponibles as $rol): ?>
                                    <a href="?rol=<?php echo urlencode($rol) . $inactivos_param; ?>"
                                        class="btn btn-outline-primary <?php echo strtolower($filtro_rol) === strtolower($rol) ? 'active' : ''; ?>">
                                        <?php echo ucfirst(htmlspecialchars($rol)); ?>
                                    </a>
                                <?php endforeach; ?>
                                <a href="?rol=sin_rol<?php echo $inactivos_param; ?>" class="btn btn-outline-primary <?php echo $filtro_rol === 'sin_rol' ? 'active' : ''; ?>">Sin Rol</a>
                            </div>
                            <div class="form-check form-switch ms-3 d-flex align-items-center text-dark">
                                <input class="form-check-input me-2" type="checkbox" id="mostrarInactivos"
                                    <?php echo $mostrar_inactivos ? 'checked' : ''; ?>
                                    onchange="window.location.href='?rol=<?php echo $filtro_rol; ?>&inactivos=' + (this.checked ? '1' : '0')">
                                <label class="form-check-label small" for="mostrarInactivos">Ver inactivos</label>
                            </div>
                            <a href="crear.php" class="btn btn-success btn-sm">
                                <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['creado'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>Usuario creado correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['editado'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>Usuario actualizado correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($usuarios)): ?>
                        <div class="alert alert-info">No hay usuarios registrados.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Tipo</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-dark">
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?php echo $usuario['id']; ?></td>
                                            <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $usuario['tipo_usuario'] === 'Cliente' ? 'info' : 'success'; ?>">
                                                    <?php echo $usuario['tipo_usuario']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($usuario['rol_nombre']): ?>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($usuario['rol_nombre']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $usuario['activo'] ? 'success' : 'secondary'; ?>">
                                                    <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="editar.php?id=<?php echo $usuario['id']; ?>"
                                                        class="btn btn-outline-primary"
                                                        title="Editar usuario">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($usuario['tipo_usuario'] === 'Cliente'): ?>
                                                        <a href="mascotas_usuario.php?id=<?php echo $usuario['id']; ?>"
                                                            class="btn btn-outline-info"
                                                            title="Ver mascotas">
                                                            <i class="fas fa-paw"></i>
                                                        </a>
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
                <?php
                renderPagination($paginacion['page'], $paginacion['pages'], [
                    'rol'      => $filtro_rol,
                    'inactivos' => $mostrar_inactivos ? '1' : '0',
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . "/../../src/Templates/footer.php";
?>
