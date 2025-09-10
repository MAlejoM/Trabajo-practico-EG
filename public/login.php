<?php
require_once __DIR__ . '/../src/logic/auth.logic.php';

procesar_login($_POST); // La función procesar_login se se encarga de la logica

if (isset($_SESSION['dni'])) {
  session_destroy();
}

require_once __DIR__ . '/../src/includes/header.php';
?>

<div>
  <h2>Iniciar sesión</h2>
</div>
<div class="formLogin-signup">
  <?php
  if (isset($_GET['error']) && $_GET['error'] == 1) {
    echo "<div class='alert alert-danger'>DNI o contraseña incorrectos.</div>";
  }
  ?>
  <form method="post" action="login.php" class="formulario">
    <label for="dni">Dni:</label>
    <input type="number" name="dni" required class="form-control"><br>
    <label for="contrasenia">Contraseña:</label>
    <input type="password" name="contrasenia" required class="form-control"><br>
    <input type="submit" value="Iniciar" class="btn btn-success">
  </form>
  <div>
    <a href="signup.php" class="btn btn-info">Crear usuario</a>
  </div>
</div>

<?php
require_once __DIR__ . '/../src/includes/footer.php';
?>