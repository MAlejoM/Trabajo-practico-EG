<?php

/**
 * DEBUG VERSION - Forgot Password con debugging habilitado
 * Este archivo reemplaza temporalmente forgot_password.php para mostrar errores detallados
 */

// Habilitar display de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<!-- DEBUG: Iniciando forgot_password.php -->\n";

try {
  echo "<!-- DEBUG: Requiriendo password_recovery.logic.php -->\n";
  require_once __DIR__ . '/../src/logic/password_recovery.logic.php';
  echo "<!-- DEBUG: password_recovery.logic.php loaded OK -->\n";
} catch (Exception $e) {
  die("<h1>Error cargando password_recovery.logic.php</h1><pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>");
}

// Si ya está logueado, redirigir al inicio
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (isset($_SESSION['usuarioId'])) {
  header('Location: index.php');
  exit();
}

$mensaje = '';
$tipo = '';
$email_enviado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo "<!-- DEBUG: POST recibido -->\n";
  $email = trim($_POST['email']);
  echo "<!-- DEBUG: Email = " . htmlspecialchars($email) . " -->\n";

  if (empty($email)) {
    $mensaje = 'El email es obligatorio.';
    $tipo = 'danger';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $mensaje = 'El email no es válido.';
    $tipo = 'danger';
  } else {
    echo "<!-- DEBUG: Llamando a solicitar_recuperacion() -->\n";

    try {
      $resultado = solicitar_recuperacion($email);
      echo "<!-- DEBUG: solicitar_recuperacion() retornó -->\n";
      echo "<!-- DEBUG: Resultado = " . json_encode($resultado) . " -->\n";

      $mensaje = $resultado['message'];
      $tipo = $resultado['success'] ? 'success' : 'danger';
      $email_enviado = $resultado['success'];
    } catch (Exception $e) {
      echo "<div style='background:red;color:white;padding:20px;margin:20px;'>";
      echo "<h2>ERROR CAPTURADO:</h2>";
      echo "<h3>Mensaje: " . htmlspecialchars($e->getMessage()) . "</h3>";
      echo "<pre>Trace:\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
      echo "</div>";

      $mensaje = 'Error: ' . $e->getMessage();
      $tipo = 'danger';
    }
  }
}

echo "<!-- DEBUG: Requiriendo header.php -->\n";
require_once __DIR__ . '/../src/includes/header.php';
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
                <i class="fas fa-key fa-3x text-primary"></i>
              </div>
              <h1 class="h4 mb-2">¿Olvidaste tu contraseña? (DEBUG)</h1>
              <p class="text-muted small">No te preocupes, te ayudaremos a recuperarla</p>
            </div>

            <?php if ($mensaje): ?>
              <div class="alert alert-<?php echo $tipo; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if (!$email_enviado): ?>
              <form method="post" action="forgot_password_debug.php">
                <div class="mb-4">
                  <label for="email" class="form-label">Email</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input
                      type="email"
                      name="email"
                      id="email"
                      required
                      class="form-control"
                      placeholder="tu@email.com"
                      value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                  </div>
                  <div class="form-text">
                    Ingresa el email asociado a tu cuenta
                  </div>
                </div>

                <div class="alert alert-info small mb-4">
                  <i class="fas fa-info-circle me-1"></i>
                  Te enviaremos un enlace para que puedas crear una nueva contraseña.
                  El enlace será válido por <strong>1 hora</strong>.
                </div>

                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-2"></i>Enviar Instrucciones
                  </button>
                  <a href="login.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Login
                  </a>
                </div>
              </form>
            <?php else: ?>
              <div class="text-center">
                <div class="mb-4">
                  <i class="fas fa-envelope-open-text fa-4x text-success"></i>
                </div>
                <h5 class="mb-3">¡Email enviado!</h5>
                <p class="text-muted mb-4">
                  Revisa tu bandeja de entrada. Si el email existe en nuestro sistema,
                  recibirás instrucciones para recuperar tu contraseña.
                </p>
                <div class="alert alert-warning small">
                  <i class="fas fa-clock me-1"></i>
                  Si no ves el email en unos minutos, revisa tu carpeta de spam.
                </div>
                <a href="login.php" class="btn btn-primary">
                  <i class="fas fa-sign-in-alt me-2"></i>Ir al Login
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="text-center mt-3">
          <small class="text-muted">
            ¿Necesitas ayuda?
            <a href="mailto:<?php echo REPLY_TO; ?>" class="text-decoration-none">Contacta con nosotros</a>
          </small>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
require_once __DIR__ . '/../src/includes/footer.php';
?>