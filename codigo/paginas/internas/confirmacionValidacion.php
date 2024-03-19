<?php

include_once("../../procesos/funciones.php");
include("../generales/header.php");

if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['ingresovalidacion'])) { //si se envio el formulario

  if ($_SESSION['validacion'] == $_POST['ingresovalidacion']) { //si el codigo es correcto
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $dni = $_POST['dni'];
    $password = $_POST['password'];
    echo "Código correcto";
    $query = "INSERT INTO datosUsuario (nombre, apellido, email, dni, contrasenia, rol) VALUES ('$nombre', '$apellido', '$email', '$dni', '$password', 'cliente')"; //inserta los datos en la base de datos
    $resultados = consultaSql($query);

    if ($resultados == true) {
      ?>
      <h2>Dirijase al LOGIN tocando aca</h2>
      <a href="../generales/login.php">LOGIN</a>
      <?php
    } else {
      echo "<h1>error, no se pudo realizar la conexión con la base de datos</h1>";
      ?>
      <h2>Dirijase al LOGIN tocando aca</h2>
      <a href="../generales/login.php">LOGIN</a>
      <?php
    }
  } else {
    echo "Código incorrecto";
    ?>
    <h2>Dirijase al SIGNUP tocando aca</h2>
    <a href="../generales/signup.php">SIGNUP</a>
    <?php

  }
} else {
  echo "error";
  ?>
  <h2>Dirijase al LOGIN tocando aca</h2>
  <a href="../generales/login.php">LOGIN</a>
  <?php
}
?>