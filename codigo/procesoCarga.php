<?php
  include("funciones.php");
  $nombre = $_POST["nombre"];
  $imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
  $tamaño = $_FILES['imagen']['size'] / 1048576; // Convertir a megabytes
  
  if ($tamaño <= 1) {
    $query = "INSERT INTO catalogo (nombre, imagen) VALUES ('$nombre', '$imagen')";
    $resultado = consultaSql($query);
    if($resultado){
      echo "se ha cargado la imagen";
    }else{
      echo "no se ha cargado la imagen";
    }
  } else {
    echo "El archivo debe pesar menos de 1MB";
  }
?>