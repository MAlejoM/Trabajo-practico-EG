<?php
require_once __DIR__ . '/../../src/autoload.php';

// Si ya está logueado, redirigir al inicio
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (isset($_SESSION['usuarioId'])) {
  header('Location: ../index.php');
  exit();
}

use App\Modules\Usuarios\PasswordRecoveryService;

// Obtener token de la URL
$token = $_GET['token'] ?? '';
$validacion = PasswordRecoveryService::validarToken($token);

$mensaje = '';
$tipo = '';
$contrasena_cambiada = false;

// Procesar formulario de cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
  $token_post = $_POST['token'];
  $nueva_clave = $_POST['nueva_clave'];
  $confirmar_clave = $_POST['confirmar_clave'];

  if (empty($nueva_clave) || empty($confirmar_clave)) {
    $mensaje = 'Todos los campos son obligatorios.';
    $tipo = 'danger';
  } elseif (strlen($nueva_clave) < 8) {
    $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
    $tipo = 'danger';
  } elseif ($nueva_clave !== $confirmar_clave) {
    $mensaje = 'Las contraseñas no coinciden.';
    $tipo = 'danger';
  } else {
    $resultado = PasswordRecoveryService::resetear($token_post, $nueva_clave);
    $mensaje = $resultado['message'];
    $tipo = $resultado['success'] ? 'success' : 'danger';
    $contrasena_cambiada = $resultado['success'];
  }
}

require_once __DIR__ . '/../../src/Templates/header.php';
?>

<script>
  document.body.classList.add('login-page');
</script>

<main class="login-main py-4">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-5">
        <div class="card shadow">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <div class="mb-3">
                <i class="fas fa-lock-open fa-3x text-success"></i>
              </div>
              <h1 class="h4 mb-2">Restablecer Contraseña</h1>
              <p class="text-muted small">Crea una nueva contraseña segura</p>
            </div>

            <?php if ($mensaje): ?>
              <div class="alert alert-<?php echo $tipo; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if ($contrasena_cambiada): ?>
              <!-- Contraseña cambiada exitosamente -->
              <div class="text-center">
                <div class="mb-4">
                  <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h5 class="mb-3">¡Contraseña Actualizada!</h5>
                <p class="text-muted mb-4">
                  Tu contraseña ha sido cambiada correctamente.
                  Ya puedes iniciar sesión con tu nueva contraseña.
                </p>
                <a href="login.php" class="btn btn-success btn-lg">
                  <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                </a>
              </div>

            <?php elseif (!$validacion['valid']): ?>
              <!-- Token inválido o expirado -->
              <div class="text-center">
                <div class="mb-4">
                  <i class="fas fa-exclamation-triangle fa-4x text-danger"></i>
                </div>
                <h5 class="mb-3">Token Inválido</h5>
                <div class="alert alert-danger">
                  <?php echo htmlspecialchars($validacion['message']); ?>
                </div>
                <p class="text-muted mb-4">
                  El enlace de recuperación no es válido o ha expirado.
                </p>
                <div class="d-grid gap-2">
                  <a href="forgot_password.php" class="btn btn-primary">
                    <i class="fas fa-redo me-2"></i>Solicitar Nuevo Enlace
                  </a>
                  <a href="login.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Login
                  </a>
                </div>
              </div>

            <?php else: ?>
              <!-- Formulario de nueva contraseña -->
              <div class="alert alert-info small mb-4">
                <i class="fas fa-user me-1"></i>
                Estás restableciendo la contraseña para:
                <strong><?php echo htmlspecialchars($validacion['nombre']); ?></strong>
                (<?php echo htmlspecialchars($validacion['email']); ?>)
              </div>

              <form method="post" action="reset_password.php" id="resetForm">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="mb-3">
                  <label for="nueva_clave" class="form-label">Nueva Contraseña *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input
                      type="password"
                      class="form-control"
                      id="nueva_clave"
                      name="nueva_clave"
                      minlength="8"
                      required
                      placeholder="Mínimo 8 caracteres">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword1">
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                  <div class="form-text">
                    Mínimo 8 caracteres. Se recomienda usar letras, números y símbolos.
                  </div>
                </div>

                <div class="mb-4">
                  <label for="confirmar_clave" class="form-label">Confirmar Contraseña *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input
                      type="password"
                      class="form-control"
                      id="confirmar_clave"
                      name="confirmar_clave"
                      minlength="8"
                      required
                      placeholder="Repite la contraseña">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword2">
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                </div>

                <!-- Indicador de fortaleza -->
                <div class="mb-4">
                  <div class="small text-muted mb-1">Fortaleza de la contraseña:</div>
                  <div class="progress" style="height: 5px;">
                    <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%"></div>
                  </div>
                  <small id="strengthText" class="text-muted"></small>
                </div>

                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-check me-2"></i>Cambiar Contraseña
                  </button>
                  <a href="login.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                  </a>
                </div>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
  // Mostrar/ocultar contraseña
  document.getElementById('togglePassword1')?.addEventListener('click', function() {
    const input = document.getElementById('nueva_clave');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });

  document.getElementById('togglePassword2')?.addEventListener('click', function() {
    const input = document.getElementById('confirmar_clave');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });

  // Indicador de fortaleza de contraseña
  document.getElementById('nueva_clave')?.addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');

    let strength = 0;
    let text = '';
    let color = '';

    if (password.length >= 8) strength += 25;
    if (password.length >= 12) strength += 25;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
    if (/\d/.test(password)) strength += 15;
    if (/[^a-zA-Z\d]/.test(password)) strength += 10;

    if (strength < 40) {
      text = 'Débil';
      color = 'bg-danger';
    } else if (strength < 70) {
      text = 'Media';
      color = 'bg-warning';
    } else {
      text = 'Fuerte';
      color = 'bg-success';
    }

    strengthBar.style.width = strength + '%';
    strengthBar.className = 'progress-bar ' + color;
    strengthText.textContent = text;
  });

  // Validar que las contraseñas coincidan antes de enviar
  document.getElementById('resetForm')?.addEventListener('submit', function(e) {
    const pass1 = document.getElementById('nueva_clave').value;
    const pass2 = document.getElementById('confirmar_clave').value;

    if (pass1 !== pass2) {
      e.preventDefault();
      alert('Las contraseñas no coinciden.');
      return false;
    }
  });
</script>

<?php
require_once __DIR__ . '/../../src/Templates/footer.php';
?>
