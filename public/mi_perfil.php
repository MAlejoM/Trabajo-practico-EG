<?php
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/lib/funciones.php";

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

// Procesar actualización de perfil
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_perfil'])) {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
    $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
    
    // Validaciones básicas
    if (empty($nombre) || empty($apellido) || empty($email)) {
        $mensaje = '<div class="alert alert-danger">Todos los campos obligatorios deben estar completos.</div>';
    } else {
        // Actualizar datos del usuario
        $stmt = $db->prepare("UPDATE Usuarios SET nombre = ?, apellido = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $apellido, $email, $_SESSION['usuarioId']);
        
        if ($stmt->execute()) {
            // Actualizar datos específicos según el tipo de usuario
            if ($usuario['cliente_id']) {
                // Es cliente - actualizar teléfono y dirección
                $stmt = $db->prepare("UPDATE Clientes SET telefono = ?, direccion = ? WHERE usuarioId = ?");
                $stmt->bind_param("ssi", $telefono, $direccion, $_SESSION['usuarioId']);
                $stmt->execute();
            }
            // Nota: Personal no tiene campo telefono en la base de datos
            
            // Actualizar sesión
            $_SESSION['nombre'] = $nombre;
            $_SESSION['apellido'] = $apellido;
            
            $mensaje = '<div class="alert alert-success">Perfil actualizado correctamente.</div>';
            
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
            $mensaje = '<div class="alert alert-danger">Error al actualizar el perfil.</div>';
        }
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
                        <?php echo $mensaje; ?>
                        
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

                            <?php if ($usuario['cliente_id']): ?>
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" 
                                           value="<?php echo htmlspecialchars($usuario['cliente_telefono'] ?? ''); ?>">
                                </div>
                            <?php endif; ?>

                            <?php if ($usuario['cliente_id']): ?>
                                <!-- Campo adicional para clientes -->
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <textarea class="form-control" id="direccion" name="direccion" rows="2"><?php echo htmlspecialchars($usuario['cliente_direccion'] ?? ''); ?></textarea>
                                </div>
                            <?php endif; ?>

                            <!-- Información adicional solo lectura -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Usuario</label>
                                    <input type="text" class="form-control" readonly 
                                           value="<?php echo $usuario['personal_id'] ? 'Personal' : 'Cliente'; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Estado</label>
                                    <input type="text" class="form-control" readonly 
                                           value="<?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>">
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?php echo BASE_URL; ?>public/index.php" class="btn btn-secondary">Volver al inicio</a>
                                <button type="submit" name="actualizar_perfil" class="btn btn-success">Actualizar Perfil</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>