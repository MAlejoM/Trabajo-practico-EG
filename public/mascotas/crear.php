<?php
include_once __DIR__ . "/../../src/Templates/header.php";

use App\Modules\Mascotas\MascotaService;
use App\Modules\Usuarios\UsuarioService;

// Verificar que sea admin o personal
if (!UsuarioService::esPersonal()) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit();
}

$cliente_id = null;
$clientes = [];
$mensaje = '';
$tipo_mensaje = '';

// Si viene de la página de mascotas de un usuario, obtener el cliente_id
if (isset($_GET['cliente_id'])) {
    $cliente_id = intval($_GET['cliente_id']);
    // Verificar que el cliente existe con func antigua por ahora
    $cliente_data = UsuarioService::getClienteCompletoById($cliente_id);
    if (!$cliente_data) {
        header('Location: index.php');
        exit();
    }
} else {
    // Si no viene cliente_id, obtener todos los clientes para el selector
    $clientes = UsuarioService::getAllClientes();
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_mascota'])) {

    $_POST['cliente_id'] = isset($_POST['cliente_id']) ? $_POST['cliente_id'] : $cliente_id;

    try {
        $exito = MascotaService::create($_POST, $_FILES);
        if ($exito) {
            $mensaje = 'Mascota registrada correctamente.';
            $tipo_mensaje = 'success';
            // Limpiar form
            $nombre = $raza = $color = $fechaDeNac = '';
        } else {
            $mensaje = 'Error al registrar la mascota.';
            $tipo_mensaje = 'danger';
        }
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
        $tipo_mensaje = 'danger';
        // Mantener valores
        $nombre = $_POST['nombre'] ?? '';
        $raza = $_POST['raza'] ?? '';
        $color = $_POST['color'] ?? '';
        $fechaDeNac = $_POST['fechaDeNac'] ?? '';
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
                <div class="card-header">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-paw me-2"></i>
                        Nueva Mascota
                    </h1>
                </div>
                <div class="card-body">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data">
                        <!-- Selección de cliente (si no viene predefinido) -->
                        <?php if ($cliente_id): ?>
                            <input type="hidden" name="cliente_id" value="<?php echo $cliente_id; ?>">
                            <div class="alert alert-info mb-3">
                                <strong>Cliente seleccionado:</strong>
                                <?php
                                // Legacy
                                echo htmlspecialchars($cliente_data['nombre'] ?? '' . ' ' . $cliente_data['apellido'] ?? 'Cliente');
                                ?>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label for="cliente_id" class="form-label">Cliente (Dueño) *</label>
                                <select class="form-select" id="cliente_id" name="cliente_id" required>
                                    <option value="">Seleccione un cliente...</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?php echo $cliente['id']; ?>" <?php if (isset($_POST['cliente_id']) && $_POST['cliente_id'] == $cliente['id']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- Datos de la mascota -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre de la Mascota *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="raza" class="form-label">Raza</label>
                                <input type="text" class="form-control" id="raza" name="raza"
                                    value="<?php echo htmlspecialchars($raza ?? ''); ?>"
                                    placeholder="Ej: Labrador, Siamés, etc.">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color"
                                    value="<?php echo htmlspecialchars($color ?? ''); ?>"
                                    placeholder="Ej: Negro, Blanco y marrón, etc.">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fechaDeNac" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fechaDeNac" name="fechaDeNac"
                                    value="<?php echo $fechaDeNac ?? ''; ?>">
                            </div>
                        </div>

                        <!-- Foto de la mascota -->
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto de la Mascota</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <div class="form-text">Tamaño máximo: 2MB. Formatos: JPG, PNG, GIF</div>
                        </div>

                        <!-- Botones -->
                        <hr class="my-4">
                        <div class="d-flex gap-2 flex-wrap justify-content-between">
                            <div class="d-flex gap-2">
                                <?php if ($cliente_id): ?>
                                    <a href="<?php echo BASE_URL; ?>public/usuarios/mascotas_usuario.php?id=<?php echo $_GET['usuario_id'] ?? ''; ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Volver
                                    </a>
                                <?php else: ?>
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Volver a Mascotas
                                    </a>
                                <?php endif; ?>
                            </div>
                            <button type="submit" name="guardar_mascota" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Guardar Mascota
                            </button>
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