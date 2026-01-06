<?php
include_once __DIR__ . "/../src/Templates/header.php";

use App\Modules\Mascotas\MascotaService;

if (!isset($_SESSION['cliente_id'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$mascotas = MascotaService::getByClienteId($_SESSION['cliente_id']);
?>

<div class="container py-4">
    <div class="row g-4">
        <aside class="col-md-4 col-lg-3 d-none d-md-block">
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header fw-semibold">Menú principal</div>
                <div class="card-body d-grid gap-2">
                    <?php include_once __DIR__ . "/../src/Templates/menu_lateral.php"; ?>
                </div>
            </div>
        </aside>

        <div class="col-12 col-md-8 col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h1 class="h4 mb-0 text-dark">
                        <i class="fas fa-paw me-2 text-success"></i>
                        Mis Mascotas
                    </h1>
                </div>
                <div class="card-body text-dark">
                    <?php if (empty($mascotas)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i>
                            No tienes mascotas registradas. Contacta al administrador para agregar una.
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($mascotas as $mascota): ?>
                                <div class="col-12 col-sm-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm overflow-hidden mascota-card">
                                        <?php if (!empty($mascota['foto'])): ?>
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($mascota['foto']); ?>"
                                                class="card-img-top"
                                                style="height: 180px; object-fit: cover;"
                                                alt="<?php echo htmlspecialchars($mascota['nombre']); ?>">
                                        <?php else: ?>
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                                <i class="fas fa-paw fa-3x text-muted opacity-25"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div class="card-body d-flex flex-column text-dark">
                                            <h5 class="card-title fw-bold mb-1"><?php echo htmlspecialchars($mascota['nombre']); ?></h5>
                                            <div class="card-text small text-muted mb-3">
                                                <?php if ($mascota['raza']): ?>
                                                    <span class="d-block"><i class="fas fa-tag me-1"></i> <?php echo htmlspecialchars($mascota['raza']); ?></span>
                                                <?php endif; ?>
                                                <?php if ($mascota['color']): ?>
                                                    <span class="d-block"><i class="fas fa-palette me-1"></i> <?php echo htmlspecialchars($mascota['color']); ?></span>
                                                <?php endif; ?>
                                                <?php if ($mascota['fechaDeNac']): ?>
                                                    <?php
                                                    $fecha_nac = new DateTime($mascota['fechaDeNac']);
                                                    $hoy = new DateTime();
                                                    $edad = $fecha_nac->diff($hoy);
                                                    ?>
                                                    <span class="d-block"><i class="fas fa-birthday-cake me-1"></i> <?php echo $edad->y; ?> años</span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="mt-auto">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <span class="badge rounded-pill bg-<?php echo $mascota['activo'] ? 'success' : 'secondary'; ?>-subtle text-<?php echo $mascota['activo'] ? 'success' : 'secondary'; ?> border border-<?php echo $mascota['activo'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $mascota['activo'] ? 'Activa' : 'Inactiva'; ?>
                                                    </span>
                                                    <?php if ($mascota['fechaMuerte']): ?>
                                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger">
                                                            <i class="fas fa-cross me-1"></i> Fallecida
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-grid">
                                                    <a href="<?php echo BASE_URL; ?>atenciones/atencion_list_by_mascota.php?id_mascota=<?php echo $mascota['id']; ?>"
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-notes-medical me-1"></i> Ver Atenciones
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <hr class="my-4">
                    <div class="alert alert-light border-0 bg-light mb-0">
                        <div class="d-flex">
                            <i class="fas fa-info-circle text-primary mt-1 me-3"></i>
                            <div>
                                <p class="mb-0 small text-muted"><strong>Nota importante:</strong> Por seguridad y consistencia en la historia clínica, no se permite la edición propia de los datos de las mascotas. Para cambios en el registro, por favor contacte con nuestro personal.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .mascota-card {
        transition: transform 0.2s;
    }

    .mascota-card:hover {
        transform: translateY(-5px);
    }
</style>

<?php
include_once __DIR__ . "/../src/Templates/footer.php";
?>