<?php
include_once __DIR__ . "/../../src/lib/funciones.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Manejo de AJAX para búsqueda de mascotas
if (isset($_GET['ajax_mascotas'])) {
    $termino = $_GET['q'] ?? '';
    $mascotas = search_mascotas($termino);
    if (empty($mascotas)) {
        echo "<div class='list-group-item text-muted small py-2'>No se encontraron mascotas.</div>";
    } else {
        foreach (array_slice($mascotas, 0, 5) as $m) {
            $cliente = htmlspecialchars(($m['nombre_cliente'] ?? '') . ' ' . ($m['apellido_cliente'] ?? ''));
            $nombreMascota = htmlspecialchars($m['nombre'] ?? '');

            $fotoData = "";
            $fotoHtml = "";
            if (!empty($m['foto'])) {
                $fotoData = "data:image/jpeg;base64," . base64_encode($m['foto']);
                $fotoHtml = "<img src='$fotoData' class='rounded-circle me-3' style='width: 35px; height: 35px; object-fit: cover;' />";
            } else {
                $fotoHtml = "<div class='bg-light rounded-circle d-flex align-items-center justify-content-center me-3' style='width: 35px; height: 35px;'><i class='fas fa-paw text-muted small'></i></div>";
            }

            echo "<button type='button' class='list-group-item list-group-item-action py-2 select-mascota' 
                    data-id='{$m['id']}' 
                    data-nombre='$nombreMascota' 
                    data-cliente='$cliente' 
                    data-foto='$fotoData'
                    data-clienteid='" . ($m['clienteId'] ?? '') . "'>
                    <div class='d-flex align-items-center'>
                        $fotoHtml
                        <div>
                            <span class='d-block fw-bold mb-0 text-dark'>$nombreMascota</span>
                            <small class='text-muted'>$cliente</small>
                        </div>
                    </div>
                  </button>";
        }
    }
    exit();
}

// Manejo de AJAX para servicios
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

$id_mascota_inicial = $_GET['id_mascota'] ?? null;
$mascota_inicial = null;
$cliente_inicial = null;

if ($id_mascota_inicial) {
    $mascota_inicial = get_mascota_by_id($id_mascota_inicial);
    if ($mascota_inicial) {
        $client_info = get_cliente_by_id($mascota_inicial['clienteId']);
        $user_info = get_usuario_by_id($client_info['usuarioId']);
        $cliente_inicial = ($user_info['nombre'] ?? '') . ' ' . ($user_info['apellido'] ?? '');
    }
}

$user_role = $_SESSION['rol'] ?? '';
$my_personal_id = $_SESSION['personal_id'] ?? null;

$mensaje = "";
$error = "";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mascotaId = $_POST['mascotaId'] ?? '';
    $clienteId = $_POST['clienteId'] ?? '';
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $servicioId = $_POST['servicioId'] ?? '';

    // Si no es admin, el personalId es el del usuario logueado
    $personalId = ($user_role === 'admin') ? ($_POST['personalId'] ?? $my_personal_id) : $my_personal_id;

    $fechaHora = $_POST['fechaHora'] ?? '';

    if (empty($mascotaId) || empty($titulo) || empty($personalId) || empty($fechaHora)) {
        $error = "Por favor, selecciona una mascota y completa todos los campos obligatorios.";
    } else {
        $resultadoId = insert_atencion($clienteId, $mascotaId, $personalId, $fechaHora, $titulo, $servicioId, $descripcion);
        if ($resultadoId) {
            header("Location: " . BASE_URL . "public/atencion_list.php?registrado=1");
            exit;
        } else {
            $error = "Hubo un error al registrar la atención.";
        }
    }
}

// Valores iniciales para servicios
$personal_por_defecto = ($user_role === 'admin') ? null : $my_personal_id;
$servicios_filtrados = $personal_por_defecto ? get_servicios_by_personal($personal_por_defecto) : [];
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
                    <h1 class="h4 mb-0">Nueva Atención</h1>
                    <a href="<?php echo BASE_URL; ?>public/atencion_list.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Buscador de Mascotas -->
                    <div class="mb-4 p-3 bg-light rounded border border-info-subtle">
                        <label class="form-label fw-bold"><i class="fas fa-paw me-2 text-info"></i>Buscar Mascota *</label>
                        <div class="position-relative">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted small"></i>
                                </span>
                                <input type="text" id="petSearch" class="form-control border-start-0" placeholder="Escriba el nombre de la mascota..." autocomplete="off">
                            </div>
                            <div id="petResults" class="list-group position-absolute w-100 mt-1 shadow-lg z-3 d-none">
                                <!-- Resultados vía AJAX -->
                            </div>
                        </div>
                        <div id="petSelectedInfo" class="mt-3 <?php echo $mascota_inicial ? '' : 'd-none'; ?>">
                            <div class="alert alert-info d-flex align-items-center mb-0 py-2 px-3">
                                <i class="fas fa-check-circle me-2 fs-5"></i>
                                <div>
                                    <span class="small text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Mascota Seleccionada:</span>
                                    <span id="selectedPetText" class="fw-semibold"><?php echo $mascota_inicial ? htmlspecialchars($mascota_inicial['nombre']) . ' (' . htmlspecialchars($cliente_inicial) . ')' : ''; ?></span>
                                </div>
                                <button type="button" id="clearPet" class="btn btn-sm btn-link text-info ms-auto p-0"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>

                    <form id="attentionForm" action="" method="POST" class="<?php echo $mascota_inicial ? '' : 'opacity-50 pointer-events-none'; ?>">
                        <!-- IDs ocultos -->
                        <input type="hidden" name="mascotaId" id="mascotaId" value="<?php echo $id_mascota_inicial ?? ''; ?>">
                        <input type="hidden" name="clienteId" id="clienteId" value="<?php echo $mascota_inicial['clienteId'] ?? ''; ?>">

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="titulo" class="form-label fw-bold">Título / Motivo *</label>
                                <input type="text" name="titulo" id="titulo" class="form-control" placeholder="Ej: Control de vacunas" required <?php echo $mascota_inicial ? '' : 'disabled'; ?>>
                            </div>

                            <div class="col-md-6">
                                <label for="personalId" class="form-label fw-bold">Veterinario Asignado *</label>
                                <select name="personalId" id="personalId" class="form-select" required <?php echo ($user_role !== 'admin') ? 'disabled' : ''; ?> <?php echo $mascota_inicial ? '' : 'disabled'; ?>>
                                    <option value="">Seleccione un profesional</option>
                                    <?php foreach ($personal_list as $pers): ?>
                                        <option value="<?php echo $pers['id']; ?>" <?php echo ($pers['id'] == $my_personal_id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pers['nombre'] . ' ' . $pers['apellido']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($user_role !== 'admin'): ?>
                                    <input type="hidden" name="personalId" value="<?php echo $my_personal_id; ?>">
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <label for="servicioId" class="form-label fw-bold">Servicio *</label>
                                <select name="servicioId" id="servicioId" class="form-select" <?php echo $mascota_inicial ? '' : 'disabled'; ?>>
                                    <option value="">Seleccione un profesional primero</option>
                                    <?php foreach ($servicios_filtrados as $servicio): ?>
                                        <option value="<?php echo $servicio['id']; ?>">
                                            <?php echo htmlspecialchars($servicio['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="serviceHelp" class="form-text">Los servicios disponibles dependen del profesional asignado.</div>
                            </div>

                            <div class="col-md-12">
                                <label for="fechaHora" class="form-label fw-bold">Fecha y Hora *</label>
                                <input type="datetime-local" name="fechaHora" id="fechaHora" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required <?php echo $mascota_inicial ? '' : 'disabled'; ?>>
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="form-label fw-bold">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="form-control" rows="5" placeholder="Detalles de la atención..." <?php echo $mascota_inicial ? '' : 'disabled'; ?>></textarea>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" id="submitBtn" class="btn btn-success px-4" <?php echo $mascota_inicial ? '' : 'disabled'; ?>>
                                    <i class="fas fa-save me-2"></i>Registrar Atención
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pointer-events-none {
        pointer-events: none;
    }

    #petResults {
        max-height: 250px;
        overflow-y: auto;
        z-index: 1050;
    }

    .select-mascota strong {
        color: #0d6efd;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const petSearch = document.getElementById('petSearch');
        const petResults = document.getElementById('petResults');
        const petSelectedInfo = document.getElementById('petSelectedInfo');
        const selectedPetText = document.getElementById('selectedPetText');
        const clearPet = document.getElementById('clearPet');
        const mascotIdInput = document.getElementById('mascotaId');
        const clienteIdInput = document.getElementById('clienteId');
        const attentionForm = document.getElementById('attentionForm');
        const formFields = attentionForm.querySelectorAll('input, select, textarea, button');

        const personalSelect = document.getElementById('personalId');
        const servicioSelect = document.getElementById('servicioId');

        let debounceTimer;

        // --- Lógica del Buscador de Mascotas ---
        petSearch.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                petResults.classList.add('d-none');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`registrar_atencion.php?ajax_mascotas=1&q=${encodeURIComponent(query)}`)
                    .then(response => response.text())
                    .then(html => {
                        petResults.innerHTML = html;
                        petResults.classList.remove('d-none');
                    })
                    .catch(error => console.error('Error pet search:', error));
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!petResults.contains(e.target) && e.target !== petSearch) {
                petResults.classList.add('d-none');
            }
        });

        petResults.addEventListener('click', function(e) {
            const btn = e.target.closest('.select-mascota');
            if (btn) {
                const id = btn.dataset.id;
                const nombre = btn.dataset.nombre;
                const cliente = btn.dataset.cliente;
                const clienteid = btn.dataset.clienteid;
                const foto = btn.dataset.foto;

                selectPet(id, nombre, cliente, clienteid, foto);
            }
        });

        function selectPet(id, nombre, cliente, clienteid, foto) {
            mascotIdInput.value = id;
            clienteIdInput.value = clienteid;

            const imgHtml = foto ?
                `<img src="${foto}" class="rounded-circle me-2" style="width: 25px; height: 25px; object-fit: cover;">` :
                `<div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 25px; height: 25px;"><i class="fas fa-paw text-info small"></i></div>`;

            selectedPetText.innerHTML = `<div class="d-flex align-items-center">${imgHtml} <span class="fw-semibold">${nombre} (${cliente})</span></div>`;
            petSelectedInfo.classList.remove('d-none');
            petResults.classList.add('d-none');
            petSearch.value = '';

            // Habilitar formulario
            attentionForm.classList.remove('opacity-50', 'pointer-events-none');
            formFields.forEach(f => f.disabled = false);
            // Respetar restricción de personal si no es admin
            if (<?php echo ($user_role === 'admin' ? 'false' : 'true'); ?>) {
                personalSelect.disabled = true;
            }
        }

        clearPet.addEventListener('click', function() {
            mascotIdInput.value = '';
            clienteIdInput.value = '';
            petSelectedInfo.classList.add('d-none');
            attentionForm.classList.add('opacity-50', 'pointer-events-none');
            formFields.forEach(f => {
                if (f.id !== 'petSearch') f.disabled = true;
            });
        });

        // --- Lógica de Servicios (igual que en edición) ---
        function updateServices(personalId) {
            if (personalId) {
                fetch(`registrar_atencion.php?ajax_servicios=1&personal_id=${personalId}`)
                    .then(response => response.text())
                    .then(html => {
                        servicioSelect.innerHTML = '<option value="">Seleccione un servicio</option>' + html;
                    })
                    .catch(error => console.error('Error fetching services:', error));
            } else {
                servicioSelect.innerHTML = '<option value="">Seleccione un profesional primero</option>';
            }
        }

        if (personalSelect.value) {
            updateServices(personalSelect.value);
        }

        personalSelect.addEventListener('change', function() {
            updateServices(this.value);
        });
    });
</script>

<?php
include_once __DIR__ . "/../../src/includes/footer.php";
?>