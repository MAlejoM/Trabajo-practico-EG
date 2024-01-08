<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'users');

function consultaSql($query)
{
  $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("error");
  $resultados = mysqli_query($connection, $query);
  mysqli_close($connection);
  return $resultados;
}

function rol($dni)
{
  $query = "SELECT * FROM datosusuario WHERE dni = '$dni'";
  $resultados = consultaSql($query);
  $rol = mysqli_fetch_assoc($resultados)['rol'];
  return $rol;
}