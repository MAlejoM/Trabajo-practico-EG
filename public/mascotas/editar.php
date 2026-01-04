<?php
include_once __DIR__ . "/../../src/Templates/header.php";


use App\Modules\Mascotas\MascotaService;

if (!isset($_SESSION['personal_id'])) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$mascota_id = intval($_GET['id']);
$mascota = MascotaService::getById($mascota_id);

if (!$mascota) {
    header('Location: index.php');
    exit();
}

$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guardar_mascota'])) {
        try {
            if (MascotaService::update($mascota_id, $_POST, $_FILES)) {
                $mensaje = 'Mascota actualizada correctamente.';
                $tipo_mensaje = 'success';
                $mascota = MascotaService::getById($mascota_id); // Refrescar
            } else {
                $mensaje = 'Error al actualizar la mascota.';
                $tipo_mensaje = 'danger';
            }
        } catch (Exception $e) {
            $mensaje = $e->getMessage();
            $tipo_mensaje = 'danger';
        }
    } elseif (isset($_POST['dar_baja'])) {
        if (MascotaService::delete($mascota_id)) {
            $mensaje = 'Mascota dada de baja correctamente.';
            $tipo_mensaje = 'success';
            $mascota['activo'] = 0;
        } else {
            $mensaje = 'Error al dar de baja la mascota.';
            $tipo_mensaje = 'danger';
        }
    } elseif (isset($_POST['reactivar'])) {
        if (MascotaService::reactivate($mascota_id)) {
            $mensaje = 'Mascota reactivada correctamente.';
            $tipo_mensaje = 'success';
            $mascota['activo'] = 1;
        } else {
            $mensaje = 'Error al reactivar la mascota.';
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
                <div class="card-header">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Mascota
                    </h1>
                </div>
                <div class="card-body">
                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show">
                            <?php echo htmlspecialchars($mensaje); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Información del dueño -->
                    <div class="alert alert-info mb-4">
                        <strong>Dueño:</strong>
                        <?php echo htmlspecialchars($mascota['nombre_cliente'] . ' ' . $mascota['apellido_cliente']); ?>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <!-- Foto actual -->
                        <?php if (!empty($mascota['foto'])): ?>
                            <div class="mb-3 text-center">
                                <label class="form-label d-block">Foto Actual</label>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($mascota['foto']); ?>"
                                    alt="<?php echo htmlspecialchars($mascota['nombre']); ?>"
                                    class="img-thumbnail"
                                    style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <!-- Datos de la mascota -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?php echo htmlspecialchars($mascota['nombre']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="raza" class="form-label">Raza</label>
                                <input type="text" class="form-control" id="raza" name="raza"
                                    value="<?php echo htmlspecialchars($mascota['raza'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="text" class="form-control" id="color" name="color"
                                    value="<?php echo htmlspecialchars($mascota['color'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fechaDeNac" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fechaDeNac" name="fechaDeNac"
                                    value="<?php echo $mascota['fechaDeNac']; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fechaMuerte" class="form-label">Fecha de Fallecimiento</label>
                                <input type="date" class="form-control" id="fechaMuerte" name="fechaMuerte"
                                    value="<?php echo $mascota['fechaMuerte'] ?? ''; ?>">
                                <div class="form-text">Dejar vacío si la mascota está viva</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4 pt-2">
                                    <input type="checkbox" class="form-check-input" id="activo" name="activo"
                                        <?php echo $mascota['activo'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="activo">
                                        Mascota activa (visible en el sistema)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Nueva foto -->
                        <div class="mb-3">
                            <label for="foto" class="form-label">
                                <?php echo !empty($mascota['foto']) ? 'Cambiar Foto' : 'Agregar Foto'; ?>
                            </label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <div class="form-text">Tamaño máximo: 2MB. Formatos: JPG, PNG, GIF</div>
                        </div>

                        <!-- Botones -->
                        <hr class="my-4">
                        <div class="d-flex gap-2 flex-wrap justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver a Lista
                            </a>
                            <button type="submit" name="guardar_mascota" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>

                            <?php if ($mascota['activo']): ?>
                                <button type="submit" name="dar_baja" class="btn btn-outline-danger"
                                    onclick="return confirm('¿Está seguro que desea dar de baja a esta mascota?')">
                                    <i class="fas fa-ban me-1"></i> Dar de Baja
                                </button>
                            <?php else: ?>
                                <button type="submit" name="reactivar" class="btn btn-outline-success">
                                    <i class="fas fa-check-circle me-1"></i> Reactivar Mascota
                                </button>
                            <?php endif; ?>
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