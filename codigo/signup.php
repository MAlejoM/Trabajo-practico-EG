<?php
  include("header.php"); 
?>
<head>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<div><h2 class="registro">Registro</h2></div>
  <div>
    <form method="POST" action="validacion.php" class="formularios">
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

      <input type="submit" value="Registrarse" class="pulser">
    </form>
  </div>
<?php
  include("footer.php"); 
?>