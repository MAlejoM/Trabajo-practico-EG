<?php
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/lib/funciones.php";
include_once __DIR__ . "/../src/logic/usuarios.logic.php";

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuarioId'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit();
}

// Obtener datos del usuario
$db = conectarDb();
$stmt = $db->prepare("
    SELECT 
        u.id,
        u.email,
        u.nombre,
        u.apellido,
        u.activo,
        p.id as personal_id,
        c.id as cliente_id,
        c.telefono as cliente_telefono,
        c.direccion as cliente_direccion,
        r.nombre as rol_nombre
    FROM Usuarios u
    LEFT JOIN Personal p ON p.usuarioId = u.id
    LEFT JOIN Clientes c ON c.usuarioId = u.id
    LEFT JOIN Roles r ON p.rolId = r.id
    WHERE u.id = ?
");
$stmt->bind_param("i", $_SESSION['usuarioId']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    header('Location: ' . BASE_URL . 'public/logout.php');
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
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $email = trim($_POST['email']);

        // Validaciones básicas
        if (empty($nombre) || empty($apellido) || empty($email)) {
            $mensaje = 'Todos los campos obligatorios deben estar completos.';
            $tipo_mensaje = 'danger';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = 'El email no es válido.';
            $tipo_mensaje = 'danger';
        } else {
            // Usar función de lógica para actualizar
            $datos = [
                'email' => $email,
                'nombre' => $nombre,
                'apellido' => $apellido
            ];

            $resultado = update_usuario_personal($_SESSION['usuarioId'], $datos);

            if ($resultado) {
                // Actualizar sesión
                $_SESSION['nombre'] = $nombre;
                $_SESSION['apellido'] = $apellido;

                $mensaje = 'Perfil actualizado correctamente.';
                $tipo_mensaje = 'success';

                // Recargar datos del usuario
                $stmt = $db->prepare("
                    SELECT 
                        u.id,
                        u.email,
                        u.nombre,
                        u.apellido,
                        u.activo,
                        p.id as personal_id,
                        c.id as cliente_id,
                        c.telefono as cliente_telefono,
                        c.direccion as cliente_direccion,
                        r.nombre as rol_nombre
                    FROM Usuarios u
                    LEFT JOIN Personal p ON p.usuarioId = u.id
                    LEFT JOIN Clientes c ON c.usuarioId = u.id
                    LEFT JOIN Roles r ON p.rolId = r.id
                    WHERE u.id = ?
                ");
                $stmt->bind_param("i", $_SESSION['usuarioId']);
                $stmt->execute();
                $result = $stmt->get_result();
                $usuario = $result->fetch_assoc();
            } else {
                $mensaje = 'Error al actualizar el perfil. Es posible que el email ya esté en uso.';
                $tipo_mensaje = 'danger';
            }
        }
    }
}

// Procesar cambio de contraseña (PARA TODOS los usuarios)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_contrasena'])) {
    $clave_actual = $_POST['clave_actual'];
    $nueva_clave = $_POST['nueva_clave'];
    $confirmar_clave = $_POST['confirmar_clave'];

    // Validaciones mejoradas
    if (empty($clave_actual) || empty($nueva_clave) || empty($confirmar_clave)) {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipo_mensaje = 'danger';
    } elseif (strlen($nueva_clave) < 8) {
        $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
        $tipo_mensaje = 'danger';
    } elseif ($nueva_clave !== $confirmar_clave) {
        $mensaje = 'Las contraseñas nuevas no coinciden.';
        $tipo_mensaje = 'danger';
    } else {
        $resultado = cambiar_contrasena($_SESSION['usuarioId'], $clave_actual, $nueva_clave);
        $mensaje = $resultado['mensaje'];
        $tipo_mensaje = $resultado['success'] ? 'success' : 'danger';
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
                    <div class="card-body">
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
                            <!-- VISTA PARA CLIENTES: Solo cambio de contraseña -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Información:</strong> Como cliente, solo puedes cambiar tu contraseña.
                                Para modificar otros datos, contacta al administrador.
                            </div>

                            <!-- Datos de solo lectura -->
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
                            <?php if ($usuario['cliente_telefono']): ?>
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Teléfono</label>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($usuario['cliente_telefono']); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if ($usuario['cliente_direccion']): ?>
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Dirección</label>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($usuario['cliente_direccion']); ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Formulario de cambio de contraseña -->
                            <hr class="my-4">
                            <h5 class="mb-3">Cambiar Contraseña</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="clave_actual" class="form-label">Contraseña Actual *</label>
                                    <input type="password" class="form-control" id="clave_actual" name="clave_actual" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nueva_clave" class="form-label">Nueva Contraseña * (mínimo 8 caracteres)</label>
                                    <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" minlength="8" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmar_clave" class="form-label">Confirmar Nueva Contraseña *</label>
                                    <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" minlength="8" required>
                                </div>
                                <button type="submit" name="cambiar_contrasena" class="btn btn-success w-100">
                                    <i class="fas fa-key me-1"></i> Cambiar Contraseña
                                </button>
                            </form>

                        <?php else: ?>
                            <!-- VISTA PARA PERSONAL: Edición de datos propios -->
                            <?php if ($es_admin): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-crown me-1"></i>
                                    <strong>Administrador:</strong> Puedes gestionar todos los usuarios desde el
                                    <a href="<?php echo BASE_URL; ?>public/usuarios/usuario_list.php" class="alert-link">módulo de usuarios</a>.
                                </div>
                            <?php endif; ?>

                            <!-- Formulario de edición -->
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

                                <!-- Información adicional solo lectura -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipo de Usuario</label>
                                        <input type="text" class="form-control" readonly value="Personal">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado</label>
                                        <input type="text" class="form-control" readonly value="<?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>">
                                    </div>
                                </div>

                                <button type="submit" name="actualizar_perfil" class="btn btn-success w-100">
                                    <i class="fas fa-save me-1"></i> Actualizar Perfil
                                </button>
                            </form>

                            <!-- Sección de Cambio de Contraseña para Personal/Admin -->
                            <hr class="my-4">
                            <h5 class="mb-3">Cambiar Contraseña</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="clave_actual" class="form-label">Contraseña Actual *</label>
                                    <input type="password" class="form-control" id="clave_actual" name="clave_actual" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nueva_clave" class="form-label">Nueva Contraseña * (mínimo 8 caracteres)</label>
                                    <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" minlength="8" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmar_clave" class="form-label">Confirmar Nueva Contraseña *</label>
                                    <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" minlength="8" required>
                                </div>
                                <button type="submit" name="cambiar_contrasena" class="btn btn-warning w-100">
                                    <i class="fas fa-key me-1"></i> Cambiar Contraseña
                                </button>
                            </form>
                        <?php endif; ?>

                        <hr class="my-4">
                        <div class="d-grid">
                            <a href="<?php echo BASE_URL; ?>public/index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>