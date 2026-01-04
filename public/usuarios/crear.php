<?php
include_once __DIR__ . "/../../src/Templates/header.php";


use App\Modules\Usuarios\UsuarioService;

if (!UsuarioService::esAdmin()) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

// Obtener roles para el selector (Legacy for now)
$db = \App\Core\DB::getConn();
$roles = $db->query("SELECT id, nombre FROM Roles ORDER BY nombre ASC")->fetch_all(MYSQLI_ASSOC);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    if ($_POST['clave'] !== $_POST['confirmar_clave']) {
        $mensaje = 'Las contraseñas no coinciden.';
        $tipo_mensaje = 'danger';
    } else {
        try {
            UsuarioService::create($_POST);
            header("Location: index.php?creado=1");
            exit;
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
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
                    <h1 class="h4 mb-0"><i class="fas fa-user-plus me-2"></i>Nuevo Usuario</h1>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">Volver</a>
                </div>
                <div class="card-body text-dark">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tipo de Usuario *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="tipo" id="tipo_cliente" value="cliente" checked>
                                <label class="btn btn-outline-primary" for="tipo_cliente"><i class="fas fa-user me-1"></i> Cliente</label>
                                <input type="radio" class="btn-check" name="tipo" id="tipo_personal" value="personal">
                                <label class="btn btn-outline-success" for="tipo_personal"><i class="fas fa-user-tie me-1"></i> Personal</label>
                            </div>
                        </div>

                        <h5 class="border-bottom pb-2 mb-3">Datos Básicos</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellido" class="form-label">Apellido *</label>
                                <input type="text" class="form-control" id="apellido" name="apellido" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="clave" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="clave" name="clave" minlength="6" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirmar_clave" class="form-label">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" minlength="6" required>
                            </div>
                        </div>

                        <div id="campos_personal" style="display: none;">
                            <h5 class="border-bottom pb-2 mb-3">Datos del Personal</h5>
                            <div class="mb-3">
                                <label for="rol_id" class="form-label">Rol *</label>
                                <select class="form-select" id="rol_id" name="rol_id">
                                    <option value="">Seleccione un rol...</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?php echo $rol['id']; ?>"><?php echo htmlspecialchars($rol['nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div id="campos_cliente">
                            <h5 class="border-bottom pb-2 mb-3">Datos del Cliente</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ciudad" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" name="ciudad">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="text-end">
                            <button type="submit" name="crear_usuario" class="btn btn-success px-4">
                                <i class="fas fa-save me-1"></i> Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="tipo"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('campos_cliente').style.display = (this.value === 'cliente' ? 'block' : 'none');
            document.getElementById('campos_personal').style.display = (this.value === 'personal' ? 'block' : 'none');
            document.getElementById('rol_id').required = (this.value === 'personal');
        });
    });
</script>

<?php
include_once __DIR__ . "/../../src/Templates/footer.php";
?>