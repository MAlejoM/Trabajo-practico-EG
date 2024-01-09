<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'users');

function consultaSql($query)
{ //Funcion para realizar consultas a la base de datos
  $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die("error");
  $resultados = mysqli_query($connection, $query);
  mysqli_close($connection);
  return $resultados;
}

function rol($dni)
{ //Funcion para obtener el rol de un usuario
  $query = "SELECT * FROM datosusuario WHERE dni = '$dni'";
  $resultados = consultaSql($query);
  $rol = mysqli_fetch_assoc($resultados)['rol'];
  return $rol;
}

function nombre($id)
{ //Funcion para obtener el nombre de un usuario
  $query = "SELECT * FROM datosusuario WHERE id = '$id'";
  $resultados = consultaSql($query);
  $nombre = mysqli_fetch_assoc($resultados)['nombre'];
  return $nombre;
}



function servicio($id)
{ //Funcion para obtener los datos de un servicio
  $query = "SELECT * FROM servicios WHERE id = '$id'";
  $resultados = consultaSql($query);
  $resultados = mysqli_fetch_array($resultados);
  return $resultados;
}