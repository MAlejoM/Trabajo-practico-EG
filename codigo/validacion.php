<?php
include_once("procesos/funciones.php");
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

          
          $_SESSION['validacion'] = $validacion;

          // Enviar un mail con el código de validación
            
          $envio = sendMail($email, "Codigo de validacion", "Su codigo de validacion es: " . $validacion);
          if($envio == "ok"){
            echo '<script>alert("El codigo de validacion fue enviado correctamente a su mail");</script>';
          // Mostrar una alerta con el codigo de validacion
          }else{
            echo '<script>alert("El codigo de validacion no pudo ser enviado");</script>';
          }
          
          ?>
          <head>
            <link rel="stylesheet" type="text/css" href="style.css">
          </head>
          <div><h2 class="validacion">Validacion de codigo</h2></div>
          <form method='POST' action='confirmacionValidacion.php' class="formulario">
            
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
        echo "<div><a href='signup.php' class='crear'>Iniciar sesion</a></div>";
      }
    } else {
      echo "<h1>error, el dni debe ser un entero mayor que un millón</h1>";
    }
  }
}
include("footer.php");
?>
