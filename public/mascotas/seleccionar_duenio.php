<?php
include_once __DIR__ . "/../../src/Templates/header.php";

use App\Modules\Mascotas\MascotaService;
use App\Core\DB;

$dni = $_GET['dni'] ?? '';
$mascotas = MascotaService::getByClienteDni($dni);

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
                    <h1 class="h4 mb-0">Mascotas del Cliente (DNI: <?php echo htmlspecialchars($dni); ?>)</h1>
                </div>
                <div class="card-body text-dark">
                    <?php if (empty($mascotas)): ?>
                        <div class="alert alert-info">No se encontraron mascotas para este cliente.</div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($mascotas as $mascota): ?>
                                <div class="col-sm-6 col-xl-4">
                                    <div class="card h-100 shadow-sm">
                                        <?php if ($mascota['imagen']): ?>
                                            <img src="data:image/jpeg;base64,<?php echo base64_encode($mascota['imagen']); ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light text-muted d-flex align-items-center justify-content-center" style="height: 180px;">
                                                <i class="fas fa-paw fa-3x opacity-25"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="card-body d-flex flex-column text-dark">
                                            <h5 class="card-title h6 fw-bold mb-1"><?php echo htmlspecialchars($mascota['nombre']); ?></h5>
                                            <p class="small text-muted mb-3">
                                                Raza: <?php echo htmlspecialchars($mascota['raza']); ?><br>
                                                Sexo: <?php echo htmlspecialchars($mascota['sexo']); ?>
                                            </p>
                                            <div class="btn-group btn-group-sm mt-auto">
                                                <a href='../atenciones/atencion_list_by_mascota.php?id_mascota=<?php echo $mascota['id']; ?>' class="btn btn-outline-primary">Atenciones</a>
                                                <a href='../atenciones/crear.php?id_mascota=<?php echo $mascota['id']; ?>' class="btn btn-outline-success">Registrar</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . "/../../src/Templates/footer.php"; ?>
