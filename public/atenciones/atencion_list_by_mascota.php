<?php
include_once __DIR__ . "/../../src/includes/header.php";
include_once __DIR__ . "/../../src/lib/funciones.php";


$mascota_id = $_GET['id'] ?? null;

if (!$mascota_id) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Mascota no especificada.</div></div>";
    include_once __DIR__ . "/../../src/includes/footer.php";
    exit;
}

$mascota = get_mascota_by_id($mascota_id);

if (!$mascota) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Mascota no encontrada.</div></div>";
    include_once __DIR__ . "/../../src/includes/footer.php";
    exit;
}


if ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'personal' && $mascota['clienteId'] != $_SESSION['cliente_id']) {
    echo "<div class='container py-4'><div class='alert alert-danger'>No tienes permiso para ver esta mascota.</div></div>";
    include_once __DIR__ . "/../../src/includes/footer.php";
    exit;
}

$atenciones = get_atenciones_by_mascota($mascota_id);

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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0">Atenciones de <?php echo $mascota['nombre']; ?></h1>
                    <?php
                    $volver_url = isset($_SESSION['cliente_id']) ? "public/mis_mascotas.php" : "public/mascota_list.php";
                    ?>
                    <a href="<?php echo BASE_URL . $volver_url; ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <?php if (!empty($mascota['foto'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($mascota['foto']); ?>"
                                    class="rounded-circle me-3"
                                    style="width: 60px; height: 60px; object-fit: cover;"
                                    alt="<?php echo $mascota['nombre']; ?>">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3"
                                    style="width: 60px; height: 60px;">
                                    <i class="fas fa-paw text-white fa-lg"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h5 class="mb-0"><?php echo $mascota['nombre']; ?></h5>
                                <small class="text-muted"><?php echo $mascota['raza'] ?? 'Raza no especificada'; ?></small>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($atenciones)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No hay atenciones registradas para esta mascota.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha Realización</th>
                                        <th>Título</th>
                                        <th>Motivo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($atenciones as $atencion): ?>
                                        <?php
                                        // Lógica de estado
                                        $tieneDescripcion = !empty($atencion['descripcion']);
                                        $estadoCalculado = $tieneDescripcion ? 'Atendida' : 'Programada';
                                        $badgeClass = $tieneDescripcion ? 'success' : 'warning';

                                        // Fechas
                                        $fechaRealizacion = $atencion['fecha_realizacion'] ?? null;
                                        $fechaRegistro = $atencion['fechaHora'] ?? null;
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $fechaRealizacion ? date('d/m/Y', strtotime($fechaRealizacion)) : '<span class="text-muted">-</span>'; ?>
                                            </td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($atencion['titulo'] ?? 'Sin título'); ?></td>
                                            <td>
                                                <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                                    <?php echo htmlspecialchars($atencion['motivo'] ?? $atencion['descripcion'] ?? ''); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $badgeClass; ?>">
                                                    <?php echo $estadoCalculado; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDetalle<?php echo $atencion['id']; ?>">
                                                    <i class="fas fa-eye me-1"></i>Ver
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="modalDetalle<?php echo $atencion['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Detalle de Atención</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <h6 class="fw-bold">Título</h6>
                                                                    <p><?php echo htmlspecialchars($atencion['titulo'] ?? '-'); ?></p>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <h6 class="fw-bold">Estado</h6>
                                                                    <span class="badge bg-<?php echo $badgeClass; ?>"><?php echo $estadoCalculado; ?></span>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-6">
                                                                        <h6 class="fw-bold">Fecha Realización</h6>
                                                                        <p><?php echo $fechaRealizacion ? date('d/m/Y', strtotime($fechaRealizacion)) : '-'; ?></p>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <h6 class="fw-bold">Fecha Registro</h6>
                                                                        <p><?php echo $fechaRegistro ? date('d/m/Y H:i', strtotime($fechaRegistro)) : '-'; ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <h6 class="fw-bold">Descripción / Motivo</h6>
                                                                    <p class="text-break"><?php echo nl2br(htmlspecialchars($atencion['descripcion'] ?? $atencion['motivo'] ?? '')); ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . "/../../src/includes/footer.php";
?>