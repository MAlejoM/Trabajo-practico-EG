<?php
include_once __DIR__ . "/../../src/includes/header.php";
include_once __DIR__ . "/../../src/lib/funciones.php";
include_once __DIR__ . "/../../src/logic/servicios.logic.php";

if (!verificar_es_admin()) {
    header("Location: " . BASE_URL . "public/index.php");
    exit();
}

$error = null;
$exito = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $rol_ids = $_POST['roles'] ?? []; // Array de IDs de roles

    if (empty($nombre)) {
        $error = "El nombre del servicio es obligatorio.";
    } elseif ($precio < 0) {
        $error = "El precio no puede ser negativo.";
    } else {
        $nuevo_id = insertar_servicio($nombre, $precio);
        if ($nuevo_id) {
            // Asignar roles seleccionados
            asignar_roles_a_servicio($nuevo_id, $rol_ids);

            header("Location: index.php?exito=creado");
            exit();
        } else {
            $error = "Error al intentar crear el servicio.";
        }
    }
}

$all_roles = get_all_roles();
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
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h1 class="h4 mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Nuevo Servicio
                    </h1>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="crear.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nombre" class="form-label fw-bold">Nombre del Servicio *</label>
                                <input type="text" name="nombre" id="nombre" class="form-control"
                                    placeholder="Ej: Peluquería Canina" required>
                            </div>
                            <div class="col-md-4">
                                <label for="precio" class="form-label fw-bold">Precio ($) *</label>
                                <input type="number" step="0.01" name="precio" id="precio" class="form-control"
                                    placeholder="0.00" required>
                            </div>

                            <hr class="my-4">

                            <div class="col-12">
                                <h5 class="mb-3">Roles con acceso</h5>
                                <p class="small text-muted mb-3">Selecciona los roles que podrán gestionar o visualizar este servicio desde el inicio.</p>

                                <div class="row row-cols-1 row-cols-sm-2 g-2">
                                    <?php foreach ($all_roles as $rol): ?>
                                        <div class="col">
                                            <div class="card h-100 border-light hover-light">
                                                <div class="card-body p-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="roles[]"
                                                            value="<?php echo $rol['id']; ?>"
                                                            id="rol_<?php echo $rol['id']; ?>">
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
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Guardar Servicio
                                    </button>
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