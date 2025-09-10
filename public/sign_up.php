<?php

require_once __DIR__ . '/../src/logic/auth.logic.php';

$resultado = procesar_registro($_POST);

require_once __DIR__ . '/../src/includes/header.php';
?>

<div>
  <h2>Registro de Nuevo Usuario</h2>
</div>

<div class="formLogin-signup">
  <?php
  if (!empty($resultado)):
  ?>
    <div class="alert alert-<?php echo ($resultado['status'] == 'success') ? 'success' : 'danger'; ?>">
      <?php echo htmlspecialchars($resultado['message']); ?>
    </div>
  <?php endif; ?>

  <?php
  if (empty($resultado) || $resultado['status'] == 'error'):
  ?>
    <form method="POST" action="sign_up.php">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required class="form-control" autocomplete="email">
      </div>
      <div class="form-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required class="form-control" autocomplete="new-password">
      </div>
      <div class="form-group">
        <label for="passwordDuplicada">Repetir Contraseña:</label>
        <input type="password" id="passwordDuplicada" name="passwordDuplicada" required class="form-control" autocomplete="new-password">
      </div>
      <input type="submit" value="Registrarse" class="btn btn-success">
    </form>
  <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../src/includes/footer.php';
?>