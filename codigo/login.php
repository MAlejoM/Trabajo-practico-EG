<?php 
include("header.php"); ?>
<?php

include_once("funciones.php");

if (isset($_GET['error'])) {
  $error_code = $_GET['error'];

  // Ahora puedes trabajar con el código de error
  if ($error_code == 1) {
    echo "Ha ocurrido un error durante el inicio de sesión.";
    // Aquí puedes agregar más código para manejar este error específico
  }
    // Aquí puedes agregar más código para manejar este error específico
  // Puedes agregar más condiciones para manejar otros códigos de error

}

if (isset($_POST['dni']) && isset($_POST['contrasenia'])) {
  $dni = $_POST['dni'];
  $contrasenia = $_POST['contrasenia'];
  
  $query = "SELECT * FROM datosUsuario WHERE dni = '$dni' AND contrasenia = '$contrasenia'";
  $resultados = consultaSql($query);
  $cantidad = mysqli_num_rows($resultados);
  if ($cantidad == 1) {
    $usuario = mysqli_fetch_array($resultados);
    session_start();
    $_SESSION['dni'] = $usuario;
    echo "<script>alert('SE LOGUEO CORRECTAMENTE'); window.location.href='index.php'; </script>";
  } else {
    echo "<script>alert('ERROR AL LOGUEARS'); window.location.href='login.php?error=1';</script>";
  }
}

?>

<div><h2 class="inicio">Iniciar sesión</h2></div>
<div>
  <form method="post" action="login.php" class="formularios">
    <label for="username" style="border: 2px solid black; width: 85px;">dni:</label>
    <input type="number" name="dni" required><br><br>
    <label for="password" style="border: 2px solid black; width: 85px;">contraseña:</label>
    <input type="password" name="contrasenia" required><br><br>
    <input type="submit" value="Iniciar sesión" class="pulser">
  </form>
  <div><a href="/Trabajo-practico-EG/codigo/signup.php" class="crear">Crear usuario</a></div>
</div>
    
<?php 
include("footer.php"); 
?>