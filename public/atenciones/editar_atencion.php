<?php
include_once __DIR__ . "/../../src/lib/funciones.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Manejo de AJAX para obtener servicios filtrados por profesional (para Admins que cambian el profesional)
if (isset($_GET['ajax_servicios'])) {
    $p_id = $_GET['personal_id'] ?? 0;
    $servs = get_servicios_by_personal($p_id);
    foreach ($servs as $s) {
        echo "<option value='{$s['id']}'>" . htmlspecialchars($s['nombre']) . "</option>";
    }
    exit();
}

include_once __DIR__ . "/../../src/includes/header.php";

// Verificar que sea personal autorizado
if (!isset($_SESSION['personal_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit();
}

$id_atencion = $_GET['id'] ?? null;
if (!$id_atencion) {
    echo "<div class='container py-4'><div class='alert alert-danger'>ID de atención no especificado.</div></div>";
    include_once __DIR__ . "/../../src/includes/footer.php";
    exit;
}

$atencion = get_atencion_by_id($id_atencion);
if (!$atencion) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Atención no encontrada.</div></div>";
    include_once __DIR__ . "/../../src/includes/footer.php";
    exit;
}

$user_role = $_SESSION['rol'] ?? '';
$my_personal_id = $_SESSION['personal_id'] ?? null;

// Verificar permisos: Admin o el personal asignado
if ($user_role !== 'admin' && $atencion['personalId'] != $my_personal_id) {
    echo "<div class='container py-4'><div class='alert alert-danger'>No tienes permiso para editar esta atención.</div></div>";
    include_once __DIR__ . "/../../src/includes/footer.php";
    exit;
}

$mensaje = "";
$error = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $servicioId = $_POST['servicioId'] ?? '';

    // Si no es admin, el personalId y el estado se mantienen fijos
    $personalId = ($user_role === 'admin') ? ($_POST['personalId'] ?? $atencion['personalId']) : $atencion['personalId'];
    $estado = ($user_role === 'admin') ? ($_POST['estado'] ?? $atencion['estado']) : $atencion['estado'];

    $fechaHora = $_POST['fechaHora'] ?? '';

    if (empty($titulo) || empty($personalId) || empty($fechaHora) || empty($estado)) {
        $error = "Por favor, completa todos los campos obligatorios.";
    } else {
        $resultado = update_atencion($id_atencion, $titulo, $descripcion, $servicioId, $personalId, $fechaHora, $estado);
        if ($resultado) {
            $mensaje = "Atención actualizada correctamente.";
            // Recargar datos actualizados
            $atencion = get_atencion_by_id($id_atencion);
        } else {
            $error = "Hubo un error al actualizar la atención.";
        }
    }
}

// Obtener servicios según el profesional actualmente asignado
$servicios_filtrados = get_servicios_by_personal($atencion['personalId']);
$personal_list = get_all_personal();

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
                    <h1 class="h4 mb-0 text-dark">Editar Atención #<?php echo $id_atencion; ?></h1>
                    <a href="<?php echo BASE_URL; ?>public/atencion_list.php" class="btn btn-outline-secondary btn-sm text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">

                    <?php if ($mensaje): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $mensaje; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mascota</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($atencion['nombre_mascota']); ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cliente</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($atencion['nombre_cliente'] . ' ' . $atencion['apellido_cliente']); ?>" disabled>
                            </div>

                            <div class="col-12">
                                <label for="titulo" class="form-label fw-bold">Título / Motivo *</label>
                                <input type="text" name="titulo" id="titulo" class="form-control" value="<?php echo htmlspecialchars($atencion['titulo']); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="personalId" class="form-label fw-bold">Veterinario Asignado *</label>
                                <select name="personalId" id="personalId" class="form-select" required <?php echo ($user_role !== 'admin') ? 'disabled' : ''; ?>>
                                    <option value="">Seleccione un profesional</option>
                                    <?php foreach ($personal_list as $pers): ?>
                                        <option value="<?php echo $pers['id']; ?>" <?php echo ($pers['id'] == $atencion['personalId']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pers['nombre'] . ' ' . $pers['apellido']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($user_role !== 'admin'): ?>
                                    <input type="hidden" name="personalId" value="<?php echo $atencion['personalId']; ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label for="servicioId" class="form-label fw-bold">Servicio</label>
                                <select name="servicioId" id="servicioId" class="form-select">
                                    <option value="">Seleccione un servicio</option>
                                    <?php foreach ($servicios_filtrados as $servicio): ?>
                                        <option value="<?php echo $servicio['id']; ?>" <?php echo ($servicio['id'] == $atencion['servicioId']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($servicio['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="serviceHelp" class="form-text">Solo se muestran los servicios habilitados para el profesional seleccionado.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="fechaHora" class="form-label fw-bold">Fecha y Hora *</label>
                                <input type="datetime-local" name="fechaHora" id="fechaHora" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($atencion['fechaHora'])); ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="estado" class="form-label fw-bold">Estado *</label>
                                <select name="estado" id="estado" class="form-select" required <?php echo ($user_role !== 'admin') ? 'disabled' : ''; ?>>
                                    <option value="pendiente" <?php echo ($atencion['estado'] === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="realizada" <?php echo ($atencion['estado'] === 'realizada') ? 'selected' : ''; ?>>Realizada</option>
                                </select>
                                <?php if ($user_role !== 'admin'): ?>
                                    <input type="hidden" name="estado" value="<?php echo $atencion['estado']; ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="form-label fw-bold">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="form-control" rows="5"><?php echo htmlspecialchars($atencion['descripcion']); ?></textarea>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 bg-primary border-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const personalSelect = document.getElementById('personalId');
        const servicioSelect = document.getElementById('servicioId');

        if (personalSelect && !personalSelect.disabled) {
            personalSelect.addEventListener('change', function() {
                const personalId = this.value;
                if (personalId) {
                    fetch(`editar_atencion.php?id=<?php echo $id_atencion; ?>&ajax_servicios=1&personal_id=${personalId}`)
                        .then(response => response.text())
                        .then(html => {
                            servicioSelect.innerHTML = '<option value="">Seleccione un servicio</option>' + html;
                        })
                        .catch(error => console.error('Error fetching services:', error));
                } else {
                    servicioSelect.innerHTML = '<option value="">Seleccione un servicio</option>';
                }
            });
        }
    });
</script>

<?php
include_once __DIR__ . "/../../src/includes/footer.php";
?>