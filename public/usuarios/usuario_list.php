<?php 
include_once __DIR__ . "/../../src/includes/header.php";
include_once __DIR__ . "/../../src/lib/funciones.php";
include_once __DIR__ . "/../../src/logic/usuarios.logic.php";

// Verificar que sea administrador
if (!isset($_SESSION['personal_id']) || !verificar_es_admin()) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit();
}

// Obtener todos los usuarios
$usuarios = get_all_usuarios();

// Obtener lista de roles únicos para los filtros
$db = conectarDb();
$stmt = $db->prepare("SELECT DISTINCT nombre FROM Roles ORDER BY nombre ASC");
$stmt->execute();
$result = $stmt->get_result();
$roles_disponibles = [];
while ($row = $result->fetch_assoc()) {
    $roles_disponibles[] = $row['nombre'];
}
$stmt->close();
$db->close();

// Filtrar por rol si se especifica
$filtro_rol = isset($_GET['rol']) ? $_GET['rol'] : 'todos';
if ($filtro_rol !== 'todos') {
    $usuarios = array_filter($usuarios, function($u) use ($filtro_rol) {
        // Comparar el rol del usuario con el filtro
        if ($filtro_rol === 'sin_rol') {
            return empty($u['rol_nombre']);
        }
        return strtolower($u['rol_nombre']) === strtolower($filtro_rol);
    });
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
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h1 class="h4 mb-0">Gestión de Usuarios</h1>
            <div class="btn-group btn-group-sm" role="group">
              <a href="?rol=todos" class="btn btn-outline-primary <?php echo $filtro_rol === 'todos' ? 'active' : ''; ?>">Todos</a>
              <?php foreach ($roles_disponibles as $rol): ?>
                <a href="?rol=<?php echo urlencode($rol); ?>" 
                   class="btn btn-outline-primary <?php echo strtolower($filtro_rol) === strtolower($rol) ? 'active' : ''; ?>">
                  <?php echo ucfirst(htmlspecialchars($rol)); ?>
                </a>
              <?php endforeach; ?>
              <a href="?rol=sin_rol" class="btn btn-outline-primary <?php echo $filtro_rol === 'sin_rol' ? 'active' : ''; ?>">Sin Rol</a>
            </div>
          </div>
        </div>
        <div class="card-body">
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
                <tbody>
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
                        <span class="badge bg-<?php echo $usuario['activo'] ? 'success' : 'danger'; ?>">
                          <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" 
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
      </div>
    </div>
  </div>
</div>

<?php
include_once __DIR__ . "/../../src/includes/footer.php";
?>
