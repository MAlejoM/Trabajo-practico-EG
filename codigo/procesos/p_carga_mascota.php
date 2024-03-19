<?php
  include("funciones.php");
  session_start();
  if(isset($_GET['eliminar'])){
    if($_GET['eliminar']==1){
      $query = "UPDATE mascotas SET estado = 'eliminado' WHERE id = ".$_GET['id_mascota'];
      $resultado = consultaSql($query);
      if($resultado){
        echo "<script>alert('SE ELIMINO CORRECTAMENTE'); window.location.href='misMascotas.php'; </script>";
      }else{
        echo "<script>alert('ERROR EN LA ELIMINACION'); window.location.href='misMascotas.php'; </script>";
      }
      }elseif($_GET['eliminar']==2){
  
        $query = "UPDATE mascotas SET estado = 'fallecido', fecha_mue = '" . date('Y-m-d') . "' WHERE id = " . $_GET['id_mascota'];
        $resultado = consultaSql($query);
        if($resultado){
          echo "<script>alert('SE ELIMINO CORRECTAMENTE'); window.location.href='misMascotas.php'; </script>";
        }else{
          echo "<script>alert('ERROR EN LA ELIMINACION'); window.location.href='misMascotas.php'; </script>";
        }
      }
  }
  
  

  $nombre = $_POST["nombre"];
  $color = $_POST["color"];
  $raza = $_POST["raza"];
  $sexo = $_POST["sexo"];
  $tamaño = 0;
  $dni_cliente = $_SESSION['dni']; 
  $hay_imagen = !empty($_FILES['imagen']['tmp_name']);
  if($hay_imagen){
    $imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name'])); 
    $tamaño = $_FILES['imagen']['size'] / 1048576; // Convertir a megabytes
  }
  
  if ($tamaño <= 1) { //comprobar que la imagen o no este en el post o que  pese menos de un mb
    if($hay_imagen){
      $query = "UPDATE mascotas SET nombre = '$nombre', imagen = '$imagen', raza = '$raza', sexo = '$sexo', color = '$color' WHERE id = ".$_POST['id_mascota'];
    }else{
      $query = "UPDATE mascotas SET nombre = '$nombre', raza = '$raza', sexo = '$sexo', color = '$color' WHERE id = ".$_POST['id_mascota'];
    }
    $resultado = consultaSql($query);
    if($resultado){
      echo "<script>alert('SE ACTUALIZO CORRECTAMENTE'); window.location.href='misMascotas.php'; </script>";
    }else{
      echo "<script>alert('ERROR EN LA CARGA'); window.location.href='misMascotas.php'; </script>";
    }
  } else {
    echo "<script>alert('ERROR EN LA CARGA, EL ARCHIVO PESA MAS DE 1MB'); window.location.href='misMascotas.php'; </script>";
  }
?>