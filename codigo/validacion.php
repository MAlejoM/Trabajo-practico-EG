<?php
include_once("funciones.php");
include("header.php");
  
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['dni'])) {
    $dni = $_POST['dni'];

    // Chequear que el dni sea un entero y mayor que un millon
    if (is_numeric($dni) && $dni > 1000000) {
      $query = "SELECT * FROM datosusuario WHERE dni = '$dni'";
      $resultados = consultaSql($query);
      $cantidad = mysqli_num_rows($resultados);

      if ($cantidad == 0) {
        if ($_POST['password'] == $_POST['passwordDuplicada']) {
          $nombre = $_POST['nombre'];
          $apellido = $_POST['apellido'];
          $email = $_POST['email'];
          $dni = $_POST['dni'];
          $password = $_POST['password'];

          $validacion = rand(1000, 9999);

          session_start();
          $_SESSION['validacion'] = $validacion;

          // Enviar un mail con el código de validación
          // no lo reaclizamos pq no sabemos como hacerlo, 
          // pero se puede hacer con la función mail() de php, tenemos complicaciones con el hosting.

          // Mostrar una alerta con el codigo de validacion
          echo '<script>alert("El codigo de validacion es: \'' . $validacion . '\'");</script>';
          ?>
          <head>
            <link rel="stylesheet" type="text/css" href="style.css">
          </head>
          <div><h2 class="validacion">Validacion de codigo</h2></div>
          <form method='POST' action='confirmacionValidacion.php' class="formularios">
            
            <input type='hidden' name='nombre' value='<?php echo $nombre; ?>'>
            <input type='hidden' name='apellido' value='<?php echo $apellido; ?>'>
            <input type='hidden' name='email' value='<?php echo $email; ?>'>
            <input type='hidden' name='dni' value='<?php echo $dni; ?>'>
            <input type='hidden' name='password' value='<?php echo $password; ?>'>
            <label for='validacion'>Ingrese el código de validación:</label>
            <input type='number' name='ingresovalidacion' required><br><br>
            <input type='submit' value='Validar' class='pulser'>
          </form>

          <?php
            
          

        } else {
          echo "<h1>error, las 2 contraseñas son distintas</h1>";
        }
      } else {
        echo "<h1>error, el dni ingresado ya está registrado</h1>";
      }
    } else {
      echo "<h1>error, el dni debe ser un entero mayor que un millón</h1>";
    }
  }
}
include("footer.php")
?>
