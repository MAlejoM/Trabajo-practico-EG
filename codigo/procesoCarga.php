<?php
  include("funciones.php");
  session_start();
  $nombre = $_POST["nombre"];
  $dni_cliente = $_SESSION['dni']; 
  $imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name'])); 
  $tamaño = $_FILES['imagen']['size'] / 1048576; // Convertir a megabytes
  
  if ($tamaño <= 1) {
    $query = "INSERT INTO mascotas (nombre, imagen, dni_cliente, raza, sexo, color) VALUES ('$nombre', '$imagen' ,'$dni_cliente', 'Border Collie', 'macho', 'negro' )"; 
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