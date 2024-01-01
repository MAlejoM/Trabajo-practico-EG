<?php
session_start();
include_once("funciones.php");
include("|header.php");

if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['ingresovalidacion'])){
  
  if ($_SESSION['validacion']== $_POST['ingresovalidacion']) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $dni = $_POST['dni'];
    $password = $_POST['password'];
    echo "Código correcto";
    $query = "INSERT INTO datosUsuario (nombre, apellido, email, dni, contrasenia) VALUES ('$nombre', '$apellido', '$email', '$dni', '$password')";
    $resultados = consultaSql($query);
    
    if ($resultados == true) {
      ?>
      <h2>Dirijase al LOGIN tocando aca</h2>  
      <a href="login.php">LOGIN</a>
      <?php
    } else {
      echo "<h1>error, no se pudo realizar la conexión con la base de datos</h1>";
      ?>
      <h2>Dirijase al LOGIN tocando aca</h2>
      <a href="login.php">LOGIN</a>
      <?php
    }
    } else {
    echo "Código incorrecto";
    ?>
    <h2>Dirijase al SIGNUP tocando aca</h2>
    <a href="signup.php">SIGNUP</a>
    <?php

    }
  } else{
  echo "error";
  ?>
    <h2>Dirijase al LOGIN tocando aca</h2>
    <a href="login.php">LOGIN</a>
  <?php
  }
?>

           