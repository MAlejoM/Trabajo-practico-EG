<?php
require_once __DIR__ . '/../src/logic/auth.logic.php';

procesar_login($_POST); // La función procesar_login se se encarga de la logica

if (isset($_SESSION['dni'])) {
  session_destroy();
}

require_once __DIR__ . '/../src/includes/header.php';
?>

<div class="main-container">
  <div class="form-container">
    <h2 class="text-center mb-3">Iniciar sesión</h2>

    <?php
    if (isset($_GET['error']) && $_GET['error'] == 1) {
      echo "<div class='alert alert-danger mb-2'>DNI o clave incorrectos.</div>";
    }
    ?>

    <form method="post" action="login.php" class="formulario">
      <label for="email">Email:</label>
      <input type="email" name="email" required class="form-control"><br>
      <label for="clave">Clave:</label>
      <input type="password" name="clave" required class="form-control"><br>
      <input type="submit" value="Iniciar" class="btn btn-success">
    </form>
  </div>
</div>
</div>
</div>

<?php
require_once __DIR__ . '/../src/includes/footer.php';
?>