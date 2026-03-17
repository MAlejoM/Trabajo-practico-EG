<?php


$is_logged_in = isset($_SESSION['usuarioId']);
$user_role = $_SESSION['rol'] ?? null;
$is_personal = isset($_SESSION['personal_id']);
$is_cliente = isset($_SESSION['cliente_id']);

?>

<div class="menuLateral">
  <?php if ($is_logged_in): ?>
    <?php if ($is_personal): ?>
      <?php if ($user_role == "admin"): ?>
        <a href="<?php echo BASE_URL; ?>catalogos/index.php" class='btn btn-success mb-2 w-100'>ADMINISTRAR CATÁLOGO</a>
        <a href="<?php echo BASE_URL; ?>novedades/index.php" class='btn btn-success mb-2 w-100'>ADMINISTRAR NOVEDADES</a>
        <a href="<?php echo BASE_URL; ?>usuarios/index.php" class='btn btn-success mb-2 w-100'>USUARIOS</a>
        <a href="<?php echo BASE_URL; ?>servicios/index.php" class='btn btn-success mb-2 w-100'>SERVICIOS</a>
        <a href="<?php echo BASE_URL; ?>mascotas/index.php" class='btn btn-success mb-2 w-100'>MASCOTAS</a>
        <a href="<?php echo BASE_URL; ?>atenciones/index.php" class='btn btn-success mb-2 w-100'>ATENCIONES</a>

      <?php else: ?>
        <a href="<?php echo BASE_URL; ?>catalogos/index.php" class='btn btn-success mb-2 w-100'>CATÁLOGO</a>
        <a href="<?php echo BASE_URL; ?>novedades/index.php" class='btn btn-success mb-2 w-100'>NOVEDADES</a>
        <a href="<?php echo BASE_URL; ?>mascotas/index.php" class='btn btn-success mb-2 w-100'>MASCOTAS</a>
        <a href="<?php echo BASE_URL; ?>atenciones/index.php" class='btn btn-success mb-2 w-100'>ATENCIONES</a>
      <?php endif; ?>

    <?php elseif ($is_cliente): ?>
      <a href="<?php echo BASE_URL; ?>catalogos/index.php" class='btn btn-success mb-2 w-100'>CATÁLOGO</a>
      <a href="<?php echo BASE_URL; ?>novedades/index.php" class='btn btn-success mb-2 w-100'>NOVEDADES</a>
      <a href="<?php echo BASE_URL; ?>mascotas/mis_mascotas.php" class='btn btn-success mb-2 w-100'>MIS MASCOTAS</a>
    <?php endif; ?>
  <?php else:
  ?>
    <a href='<?php echo BASE_URL; ?>catalogos/index.php' class='btn btn-success mb-2 w-100'>CATÁLOGO</a>
    <a href='<?php echo BASE_URL; ?>novedades/index.php' class='btn btn-success mb-2 w-100'>NOVEDADES</a>
  <?php endif; ?>
</div>