<?php

require_once __DIR__ . '/../lib/funciones.php';


$is_logged_in = isset($_SESSION['personal_id']);
$user_role = $_SESSION['rol'] ?? null;

?>

<div class="menuLateral">
  <?php if ($is_logged_in): ?>
    <?php if ($user_role == "admin"): ?>
      <a href="<?php echo BASE_URL; ?>public/admin/catalogo.php" class='btn btn-success'>ADMINISTRAR CATALOGO</a>
      <a href="<?php echo BASE_URL; ?>public/admin/novedades.php" class='btn btn-success'>ADMINISTRAR NOVEDADES</a>
      <a href="<?php echo BASE_URL; ?>public/admin/usuarios.php" class='btn btn-success'>ADMINISTRAR USUARIOS</a>
      <a href="<?php echo BASE_URL; ?>public/servicios.php" class='btn btn-success'>SERVICIOS</a>

    <?php elseif ($user_role == "cliente"): ?>
      <a href="<?php echo BASE_URL; ?>public/catalogo.php" class='btn btn-success'>CATALOGO</a>
      <a href="<?php echo BASE_URL; ?>public/novedades.php" class='btn btn-success'>NOVEDADES</a>
      <a href="<?php echo BASE_URL; ?>public/mis_mascotas.php" class='btn btn-success'>MIS MASCOTAS</a>

    <?php elseif ($user_role == "prof"): ?>
      <a href="<?php echo BASE_URL; ?>public/catalogo.php" class='btn btn-success'>CATALOGO</a>
      <a href="<?php echo BASE_URL; ?>public/novedades.php" class='btn btn-success'>NOVEDADES</a>
      <a href="<?php echo BASE_URL; ?>public/servicios.php" class='btn btn-success'>SERVICIOS</a>

      //Un profesional también debería ver sus mascotas o las de clientes??
  <a href="<?php echo BASE_URL; ?>public/mis_mascotas.php" class='btn btn-success'>MIS MASCOTAS</a>
    <?php endif; ?>
  <?php else: 
  ?>
    <a href='<?php echo BASE_URL; ?>public/catalogo.php' class='btn btn-success'>CATALOGO</a>
    <a href='<?php echo BASE_URL; ?>public/novedades.php' class='btn btn-success'>NOVEDADES</a>
  <?php endif; ?>
</div>