<?php
include_once("funciones.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['dni'])) {
    $dni = $_POST['dni'];

    // Chequear que el dni sea un entero y mayor que un millon
    if (is_numeric($dni) && $dni > 1000000) {
      $query = "SELECT * FROM datosusuario WHERE dni = '$dni'";
      $resultados = consultaSql($query);
      $cantidad = mysqli_num_rows($resultados);

      echo '$resultados';
      if ($cantidad == 0) {
        if ($_POST['password'] == $_POST['passwordDuplicada']) {
          $nombre = $_POST['nombre'];
          $apellido = $_POST['apellido'];
          $email = $_POST['email'];
          $dni = $_POST['dni'];
          $password = $_POST['password'];

          $validacion = rand(1000, 9999);


          // Enviar un mail con el código de validación
          // no lo reaclizamos pq no sabemos como hacerlo, 
          // pero se puede hacer con la función mail() de php, tenemos complicaciones con el hosting.

            // Mostrar una alerta con el codigo de validacion
            echo '<script>alert("El codigo de validacion es: \'' . $validacion . '\'");</script>';
          
          ?>
          <form method='POST' action='validacion.php'>
            <label for='validacion'>Ingrese el código de validación:</label>
            <input type='number' name='validacion' required><br><br>
            <input type='submit' value='Validar'>
          </form>

          <?php
            if (isset($_POST['validacion'])) {
            if ($_POST['validacion'] == $validacion) {
              echo "Código correcto";
              $query = "INSERT INTO datosUsuario (nombre, apellido, email, dni, contrasenia) VALUES ('$nombre', '$apellido', '$email', '$dni', '$password')";
              $resultados = consultaSql($query);

              if ($resultados == true) {
                header("Location: login.php");
                exit; // Termina la ejecución del script después de redirigir
              } else {
                echo "<h1>error, no se pudo realizar la conexión con la base de datos</h1>";
              }
            } else {
              echo "Código incorrecto";
            }
          }

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
?>
