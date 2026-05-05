<?php
include_once __DIR__ . "/../../src/Templates/header.php";
// Para menús y roles

use App\Modules\Atenciones\AtencionService;
use App\Modules\Mascotas\MascotaService;
use App\Core\SessionHandler;

$user_role      = SessionHandler::getRol() ?? '';
$my_personal_id = SessionHandler::getPersonalId();

$mascota_id = $_GET['id'] ?? null;

if (!$mascota_id) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Mascota no especificada.</div></div>";
    include_once __DIR__ . "/../../src/Templates/footer.php";
    exit;
}

$mascota = MascotaService::getById($mascota_id);

if (!$mascota) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Mascota no encontrada.</div></div>";
    include_once __DIR__ . "/../../src/Templates/footer.php";
    exit;
}


if (!SessionHandler::esPersonal() && (!SessionHandler::esCliente() || $mascota['clienteId'] != SessionHandler::getClienteId())) {
    echo "<div class='container py-4'><div class='alert alert-danger'>No tienes permiso para ver esta mascota.</div></div>";
    include_once __DIR__ . "/../../src/Templates/footer.php";
    exit;
}

$atenciones = AtencionService::getByMascotaId($mascota_id);

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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0">Atenciones de <?php echo htmlspecialchars($mascota['nombre']); ?></h1>
                    <?php
                    $volver_url = SessionHandler::esCliente() ? "mascotas/mis_mascotas.php" : "mascotas/index.php";
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
                                    alt="Mascota - <?php echo htmlspecialchars($mascota['nombre'] . ' - ' . $mascota['raza']); ?>"
                                    title="<?php echo htmlspecialchars($mascota['nombre'] . ' - ' . $mascota['raza']); ?>"
                                    role="img">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3"
                                    style="width: 60px; height: 60px;">
                                    <i class="fas fa-paw text-white fa-lg" aria-hidden="true"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h2 class="h5 mb-0"><?php echo htmlspecialchars($mascota['nombre']); ?></h2>
                                <small class="text-muted"><?php echo htmlspecialchars($mascota['raza'] ?? 'Raza no especificada'); ?></small>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($atenciones)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2" aria-hidden="true"></i>No hay atenciones registradas para esta mascota.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Título</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($atenciones as $atencion): ?>
                                        <?php
                                        $estado = $atencion['estado'] ?? 'pendiente';
                                        $badgeClass = ($estado === 'realizada') ? 'success' : 'warning';
                                        $fechaHora = $atencion['fechaHora'] ?? null;
                                        $fechaHoraTs = $fechaHora ? (new DateTime($fechaHora, new DateTimeZone('America/Argentina/Buenos_Aires')))->getTimestamp() : 0;
                                        $ahora = (new DateTime('now', new DateTimeZone('America/Argentina/Buenos_Aires')))->getTimestamp();
                                        $puedeCompletar = ($estado === 'pendiente' && $ahora >= $fechaHoraTs);
                                        $tooltipCompletar = '';
                                        if ($estado === 'pendiente') {
                                            if ($user_role !== 'admin' && $atencion['personalId'] != $my_personal_id) {
                                                $tooltipCompletar = 'No tienes permiso para completar esta atención';
                                            } elseif ($ahora < $fechaHoraTs) {
                                                $tooltipCompletar = 'Solo se puede marcar como realizada una vez pasado el horario del turno';
                                            }
                                        } else {
                                            $tooltipCompletar = 'La atención ya está completada';
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <?php echo $fechaHora ? date('d/m/Y H:i', strtotime($fechaHora)) : '<span class="text-muted">-</span>'; ?>
                                            </td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($atencion['titulo'] ?? 'Sin título'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $badgeClass; ?>">
                                                    <?php echo ucfirst($estado); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalDetalle<?php echo $atencion['id']; ?>">
                                                    <i class="fas fa-eye me-1"></i>Ver
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success <?php echo !$puedeCompletar ? 'disabled' : ''; ?>"
                                                    onclick="<?php echo $puedeCompletar ? "completarAtencion({$atencion['id']})" : ''; ?>"
                                                    title="<?php echo htmlspecialchars($tooltipCompletar); ?>"
                                                    style="<?php echo !$puedeCompletar ? 'opacity: 0.5; cursor: not-allowed; pointer-events: auto;' : ''; ?>">
                                                    <i class="fas fa-check me-1"></i>Completar
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="modalDetalle<?php echo $atencion['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h2 class="h5 modal-title">Detalle de Atención</h2>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <h3 class="h6 fw-bold">Título</h3>
                                                                    <p><?php echo htmlspecialchars($atencion['titulo'] ?? '-'); ?></p>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <h3 class="h6 fw-bold">Estado</h3>
                                                                    <span class="badge bg-<?php echo $badgeClass; ?>"><?php echo ucfirst($estado); ?></span>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <h3 class="h6 fw-bold">Fecha y Hora</h3>
                                                                    <p><?php echo $fechaHora ? date('d/m/Y H:i', strtotime($fechaHora)) : '-'; ?></p>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <h3 class="h6 fw-bold">Descripción</h3>
                                                                    <p class="text-break" style="white-space: pre-wrap;"><?php echo htmlspecialchars($atencion['descripcion'] ?? ''); ?></p>
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

<script>
function completarAtencion(id) {
    confirmarAccion('¿Deseas marcar esta atención como realizada?', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'completar_id';
        input.value = id;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }, { titulo: 'Completar atención', btnTexto: 'Confirmar', btnClase: 'btn-success' });
}
</script>

<?php
include_once __DIR__ . "/../../src/Templates/footer.php";
?>