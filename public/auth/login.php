require_once __DIR__ . '/../../src/autoload.php';

use App\Modules\Usuarios\AuthService;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (AuthService::login($_POST['email'] ?? '', $_POST['clave'] ?? '')) {
header('Location: ../index.php');
exit;
} else {
header('Location: login.php?error=1');
exit;
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
      <div class="col-12 col-sm-8 col-md-6 col-lg-4">
        <div class="card shadow">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <img src="<?php echo BASE_URL; ?>public/uploads/Logo.jpeg" alt="Logo" width="64" height="64" class="rounded-circle mb-3">
              <h1 class="h4 mb-0">Iniciar sesión</h1>
              <p class="text-muted small">Ingresá a tu cuenta</p>
            </div>

            <?php
            if (isset($_GET['error']) && $_GET['error'] == 1) {
              echo "<div class='alert alert-danger mb-3'>
                      <strong>No se pudo iniciar sesión.</strong><br>
                      Verifique sus credenciales o contacte con administración.
                    </div>";
            }
            ?>

            <form method="post" action="login.php">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" required class="form-control" placeholder="tu@email.com">
              </div>

              <div class="mb-4">
                <label for="clave" class="form-label">Contraseña</label>
                <input type="password" name="clave" id="clave" required class="form-control" placeholder="Ingresá tu contraseña">
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg">Iniciar sesión</button>
              </div>

              <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-muted small text-decoration-none">
                  <i class="fas fa-key me-1"></i>¿Olvidaste tu contraseña?
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php
require_once __DIR__ . '/../../src/Templates/footer.php';
?>
