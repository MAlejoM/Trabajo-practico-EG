<?php
session_start();
session_destroy(); //destruyo la session e inicio una nueva, ya que al entrar en el menu de login se supone que no hay ninguna session iniciada
include_once __DIR__ . "/../src/includes/header.php";
?>
<div>
  <h2>Registro</h2>
</div>
<div>
  <form method="POST" action="validacion.php" class="formLogin-signup">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" required><br><br>

    <label for="apellido">apellido:</label>
    <input type="text" name="apellido" required><br><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br><br>

    <label for="dni">dni:</label>
    <input type="number" name="dni" required><br><br>

    <label for="password">Contraseña:</label>
    <input type="text" name="password" required><br><br>

    <label for="password">Contraseña nuevamente:</label>
    <input type="text" name="passwordDuplicada" required><br><br>

    <input type="submit" value="Registrarse" class="btn btn-success">
  </form>
</div>
<?php
include_once __DIR__ . "/../src/includes/footer.php"; ?>