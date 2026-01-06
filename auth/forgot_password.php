<?php
require_once __DIR__ . '/../src/autoload.php';

// Si ya está logueado, redirigir al inicio
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (isset($_SESSION['usuarioId'])) {
  header('Location: ../index.php');
  exit();
}

$mensaje = '';
$tipo = '';
$email_enviado = false;

use App\Modules\Usuarios\PasswordRecoveryService;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);

  if (empty($email)) {
    $mensaje = 'El email es obligatorio.';
    $tipo = 'danger';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $mensaje = 'El email no es válido.';
    $tipo = 'danger';
  } else {
    $resultado = PasswordRecoveryService::solicitar($email);
    $mensaje = $resultado['message'];
    $tipo = $resultado['success'] ? 'success' : 'danger';
    $email_enviado = $resultado['success'];
  }
}

require_once __DIR__ . '/../src/Templates/header.php';
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
              <h1 class="h4 mb-2">¿Olvidaste tu contraseña?</h1>
              <p class="text-muted small">No te preocupes, te ayudaremos a recuperarla</p>
            </div>

            <?php if ($mensaje): ?>
              <div class="alert alert-<?php echo $tipo; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if (!$email_enviado): ?>
              <form method="post" action="forgot_password.php">
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
require_once __DIR__ . '/../src/Templates/footer.php';
?>