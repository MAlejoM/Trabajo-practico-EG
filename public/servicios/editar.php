<?php
include_once __DIR__ . "/../../src/includes/header.php";
include_once __DIR__ . "/../../src/lib/funciones.php";
include_once __DIR__ . "/../../src/logic/servicios.logic.php";

if (!verificar_es_admin()) {
    header("Location: " . BASE_URL . "public/index.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

$servicio = get_servicio_by_id($id);
if (!$servicio) {
    header("Location: index.php");
    exit();
}

$error = null;
$exito = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guardar_cambios'])) {
        $nombre = trim($_POST['nombre'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $rol_ids = $_POST['roles'] ?? []; // Array de IDs de roles

        if (empty($nombre)) {
            $error = "El nombre del servicio es obligatorio.";
        } elseif ($precio < 0) {
            $error = "El precio no puede ser negativo.";
        } else {
            // Actualizar datos básicos
            $ok_datos = actualizar_servicio($id, $nombre, $precio);

            // Actualizar roles asignados
            $ok_roles = asignar_roles_a_servicio($id, $rol_ids);

            if ($ok_datos && $ok_roles) {
                $exito = "Servicio actualizado correctamente.";
                // Refrescar datos
                $servicio = get_servicio_by_id($id);
            } else {
                $error = "Error al intentar actualizar el servicio o sus roles.";
            }
        }
    } elseif (isset($_POST['dar_baja'])) {
        if (dar_baja_servicio($id)) {
            $exito = "Servicio desactivado correctamente.";
            $servicio['activo'] = 0;
        } else {
            $error = "Error al desactivar el servicio.";
        }
    } elseif (isset($_POST['reactivar'])) {
        if (reactivar_servicio($id)) {
            $exito = "Servicio reactivado correctamente.";
            $servicio['activo'] = 1;
        } else {
            $error = "Error al reactivar el servicio.";
        }
    }
}

$all_roles = get_all_roles();
$roles_asignados = get_roles_ids_by_servicio($id);
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
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar Servicio: <?php echo htmlspecialchars($servicio['nombre']); ?>
                    </h1>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($exito): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $exito; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="editar.php?id=<?php echo $id; ?>" method="POST">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nombre" class="form-label fw-bold">Nombre del Servicio *</label>
                                <input type="text" name="nombre" id="nombre" class="form-control"
                                    value="<?php echo htmlspecialchars($servicio['nombre']); ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="precio" class="form-label fw-bold">Precio ($) *</label>
                                <input type="number" step="0.01" name="precio" id="precio" class="form-control"
                                    value="<?php echo htmlspecialchars($servicio['precio']); ?>" required>
                            </div>

                            <hr class="my-4">

                            <div class="col-12">
                                <h5 class="mb-3">Roles con acceso</h5>
                                <p class="small text-muted mb-3">Selecciona los roles que podrán gestionar o visualizar este servicio.</p>

                                <div class="row row-cols-1 row-cols-sm-2 g-2">
                                    <?php foreach ($all_roles as $rol): ?>
                                        <div class="col">
                                            <div class="card h-100 border-light hover-light">
                                                <div class="card-body p-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="roles[]"
                                                            value="<?php echo $rol['id']; ?>"
                                                            id="rol_<?php echo $rol['id']; ?>"
                                                            <?php echo in_array($rol['id'], $roles_asignados) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label d-block cursor-pointer" for="rol_<?php echo $rol['id']; ?>">
                                                            <?php echo ucfirst(htmlspecialchars($rol['nombre'])); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <hr class="my-4">
                                <div class="d-flex gap-2 flex-wrap justify-content-between">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Volver a Lista
                                    </a>
                                    <div class="d-flex gap-2">
                                        <?php if ($servicio['activo']): ?>
                                            <button type="submit" name="dar_baja" class="btn btn-outline-danger"
                                                onclick="return confirm('¿Está seguro que desea desactivar este servicio?')">
                                                <i class="fas fa-ban me-1"></i> Desactivar
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="reactivar" class="btn btn-outline-success">
                                                <i class="fas fa-check-circle me-1"></i> Reactivar
                                            </button>
                                        <?php endif; ?>
                                        <button type="submit" name="guardar_cambios" class="btn btn-success">
                                            <i class="fas fa-save me-1"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-light:hover {
        background-color: #f8f9fa !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>

<?php include_once __DIR__ . "/../../src/includes/footer.php"; ?>