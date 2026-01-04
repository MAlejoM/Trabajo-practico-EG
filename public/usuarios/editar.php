<?php
include_once __DIR__ . "/../../src/Templates/header.php";


use App\Modules\Usuarios\UsuarioService;

if (!UsuarioService::esAdmin()) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit();
}

$usuario_id = intval($_GET['id'] ?? 0);
if (!$usuario_id) {
    header('Location: index.php');
    exit;
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_usuario'])) {
    try {
        $_POST['activo'] = isset($_POST['activo']) ? 1 : 0;
        UsuarioService::updateAdmin($usuario_id, $_POST);

        if (isset($_POST['cliente_id'])) {
            UsuarioService::updateClienteDatos($_POST['cliente_id'], $_POST);
        }

        $mensaje = 'Usuario actualizado correctamente.';
        $tipo_mensaje = 'success';
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'danger';
    }
}

$usuario = UsuarioService::getUsuarioCompletoById($usuario_id);
if (!$usuario) {
    header('Location: index.php');
    exit;
}
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0">Editar Usuario</h1>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">Volver</a>
                </div>
                <div class="card-body text-dark">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info mb-4">
                        <strong>Tipo:</strong> <span class="badge bg-primary"><?php echo $usuario['tipo_usuario']; ?></span>
                        <?php if ($usuario['rol_nombre']): ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($usuario['rol_nombre']); ?></span>
                        <?php endif; ?>
                    </div>

                    <form method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido *</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>

                        <?php if ($usuario['tipo_usuario'] === 'Cliente'): ?>
                            <hr class="my-4">
                            <h5 class="mb-3">Datos del Cliente</h5>
                            <input type="hidden" name="cliente_id" value="<?php echo $usuario['cliente_id']; ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ciudad" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($usuario['ciudad'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="2"><?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?></textarea>
                            </div>
                        <?php endif; ?>

                        <hr class="my-4">
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="activo" name="activo" <?php echo $usuario['activo'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="activo">Usuario activo</label>
                        </div>

                        <div class="d-flex justify-content-between gap-2 mt-4">
                            <?php if ($usuario['tipo_usuario'] === 'Cliente'): ?>
                                <a href="mascotas_usuario.php?id=<?php echo $usuario_id; ?>" class="btn btn-info"><i class="fas fa-paw me-1"></i> Ver Mascotas</a>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>
                            <button type="submit" name="guardar_usuario" class="btn btn-primary px-4">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . "/../../src/Templates/footer.php";
?>