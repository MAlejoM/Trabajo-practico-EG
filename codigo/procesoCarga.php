<?php
  include("funciones.php");
  $nombre = $_POST["nombre"];
  $imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
  
  $query = "INSERT INTO catalogo (nombre, imagen) VALUES ('$nombre', '$imagen')";
  $resultado = consultaSql($query);
  if($resultado){
    echo "se ha cargado la imagen";
  }else{
    echo "no se ha cargado la imagen";
  }
?>