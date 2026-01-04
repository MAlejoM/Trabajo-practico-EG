<?php
include_once __DIR__ . "/../../src/Templates/header.php";


use App\Modules\Usuarios\UsuarioService;

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuarioId'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Obtener datos del usuario usando el Service
$usuario = UsuarioService::getUsuarioCompletoById($_SESSION['usuarioId']);

if (!$usuario) {
    header('Location: ../auth/logout.php');
    exit();
}

// Determinar permisos
$es_cliente = $usuario['cliente_id'] != null;
$es_personal = $usuario['personal_id'] != null;
$es_admin = isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';

// Procesar actualización de perfil (solo para personal y admin)
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {
    // Validar que tenga permisos
    if ($es_cliente) {
        $mensaje = 'Los clientes no pueden modificar sus datos desde este formulario.';
        $tipo_mensaje = 'danger';
    } else {
        try {
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $email = trim($_POST['email']);

            $datos = [
                'email' => $email,
                'nombre' => $nombre,
                'apellido' => $apellido
            ];

            UsuarioService::updateAdmin($_SESSION['usuarioId'], $datos);

            // Actualizar sesión
            $_SESSION['nombre'] = $nombre;
            $_SESSION['apellido'] = $apellido;

            $mensaje = 'Perfil actualizado correctamente.';
            $tipo_mensaje = 'success';

            // Recargar datos
            $usuario = UsuarioService::getUsuarioCompletoById($_SESSION['usuarioId']);
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    }
}

// Procesar cambio de contraseña (PARA TODOS los usuarios)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_contrasena'])) {
    $clave_actual = $_POST['clave_actual'];
    $nueva_clave = $_POST['nueva_clave'];
    $confirmar_clave = $_POST['confirmar_clave'];

    try {
        if ($nueva_clave !== $confirmar_clave) {
            throw new Exception("Las contraseñas nuevas no coinciden.");
        }

        UsuarioService::cambiarPassword($_SESSION['usuarioId'], $clave_actual, $nueva_clave);
        $mensaje = "Contraseña actualizada correctamente.";
        $tipo_mensaje = 'success';
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'danger';
    }
}
?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header">
                        <h1 class="h4 mb-0">Mi Perfil</h1>
                    </div>
                    <div class="card-body text-dark">
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                                <?php echo htmlspecialchars($mensaje); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Información básica -->
                        <div class="text-center mb-4">
                            <img src="<?php echo BASE_URL; ?>public/uploads/Perfil.jpeg" alt="Foto de perfil" width="80" height="80" class="rounded-circle mb-3 object-fit-cover">
                            <h5><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></h5>
                            <?php if ($usuario['rol_nombre']): ?>
                                <span class="badge bg-success"><?php echo htmlspecialchars($usuario['rol_nombre']); ?></span>
                            <?php else: ?>
                                <span class="badge bg-primary">Cliente</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($es_cliente): ?>
                            <!-- VISTA PARA CLIENTES -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Información:</strong> Como cliente, solo puedes cambiar tu contraseña.
                            </div>

                            <h5 class="mt-4 mb-3">Información Personal</h5>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-muted small">Nombre</label>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($usuario['nombre']); ?></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-muted small">Apellido</label>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($usuario['apellido']); ?></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Email</label>
                                <div class="fw-semibold"><?php echo htmlspecialchars($usuario['email']); ?></div>
                            </div>
                            <?php if ($usuario['telefono']): ?>
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Teléfono</label>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($usuario['telefono']); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if ($usuario['direccion']): ?>
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Dirección</label>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($usuario['direccion']); ?></div>
                                </div>
                            <?php endif; ?>

                            <hr class="my-4">
                            <h5 class="mb-3">Cambiar Contraseña</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label">Contraseña Actual *</label>
                                    <input type="password" class="form-control" name="clave_actual" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nueva Contraseña *</label>
                                    <input type="password" class="form-control" name="nueva_clave" minlength="8" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirmar Nueva Contraseña *</label>
                                    <input type="password" class="form-control" name="confirmar_clave" minlength="8" required>
                                </div>
                                <button type="submit" name="cambiar_contrasena" class="btn btn-success w-100">Cambiar Contraseña</button>
                            </form>

                        <?php else: ?>
                            <!-- VISTA PARA PERSONAL -->
                            <?php if ($es_admin): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-crown me-1"></i>
                                    <strong>Admin:</strong> Gestiona usuarios en el <a href="<?php echo BASE_URL; ?>public/usuarios/index.php" class="alert-link">módulo de usuarios</a>.
                                </div>
                            <?php endif; ?>

                            <form method="post">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre *</label>
                                        <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Apellido *</label>
                                        <input type="text" class="form-control" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                </div>
                                <button type="submit" name="actualizar_perfil" class="btn btn-success w-100">Actualizar Perfil</button>
                            </form>

                            <hr class="my-4">
                            <h5 class="mb-3">Cambiar Contraseña</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <label class="form-label">Contraseña Actual *</label>
                                    <input type="password" class="form-control" name="clave_actual" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nueva Contraseña *</label>
                                    <input type="password" class="form-control" name="nueva_clave" minlength="8" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirmar Nueva Contraseña *</label>
                                    <input type="password" class="form-control" name="confirmar_clave" minlength="8" required>
                                </div>
                                <button type="submit" name="cambiar_contrasena" class="btn btn-warning w-100">Cambiar Contraseña</button>
                            </form>
                        <?php endif; ?>

                        <hr class="my-4">
                        <div class="d-grid">
                            <a href="../index.php" class="btn btn-outline-secondary">Volver al Inicio</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once __DIR__ . "/../../src/Templates/footer.php"; ?>