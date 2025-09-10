<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
define('BASE_URL', 'http://localhost/Trabajo-practico-EG/');
?>

<!DOCTYPE html>
<html>

<head>
  <title>Veterinaria San Anton</title>
  <!-- Rutas absolutas usando la constante BASE_URL. Siempre funcionarán. -->
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>public/css/style.css?v=1">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <header>
    <div class="header-btns">
      <a href="<?php echo BASE_URL; ?>public/index.php">
        <img src="<?php echo BASE_URL; ?>public/img/Logo.jpeg" alt="Logo Veterinaria">
      </a>
      <h1>Veterinaria San Anton</h1>
      <?php if (isset($_SESSION['dni'])): ?>
        <a href="<?php echo BASE_URL; ?>public/mi_perfil.php" style="text-decoration: none;">
          <img src="<?php echo BASE_URL; ?>public/img/Perfil.jpeg" alt="Mi Perfil">
        </a>
      <?php else: ?>
        <a href="<?php echo BASE_URL; ?>public/login.php" class="btn btn-success">
          INICIAR SESION
        </a>
      <?php endif; ?>
    </div>
  </header>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>