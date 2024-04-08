<?php
include_once("procesos/funciones.php");

if(isset($_SESSION['dni'])){ 
    $login = true;
    $rol = rol($_SESSION['dni']);
}else{
  $login = false;
}

if($login){ 
    
    if($rol == "admin"){
        
      echo "<a href='administrarCatalogo.php' class='btn btn-success'>ADMINISTRAR CATALOGO</a>";
        echo "<br>";
      echo "<a href='administrarNovedades.php' class='btn btn-success'>ADMINISTRAR NOVEDADES</a>";
        echo "<br>";
      echo "<a href='administrarUsuarios.php'class='btn btn-success'>ADMINISTRAR USUARIOS</a>";
        echo "<br>";
      echo "<a href='servicios.php'class='btn btn-success'>SERVICIOS</a>";
        echo "<br>";
      
      }elseif($rol == "cliente"){
      echo "<a href='catalogo.php' class='btn btn-success'>CATALOGO</a>";
        echo "<br>";
      echo "<a href='novedades.php' class='btn btn-success'>NOVEDADES</a>";
        echo "<br>";
      echo "<a href='mismascotas.php' class='btn btn-success'>MIS MASCOTAS</a>";
    }elseif($rol == "prof"){
      echo "<a href='catalogo.php'class='btn btn-success'>CATALOGO</a>";
        echo "<br>";
      echo "<a href='novedades.php'class='btn btn-success'>NOVEDADES</a>";
        echo "<br>";
      echo "<a href='servicios.php'class='btn btn-success'>SERVICIOS</a>";
        echo "<br>";
      echo "<a href='mismascotas.php'class='btn btn-success'>MIS MASCOTAS</a>";
    }  
    
    
  }else{
    
    echo "<a href='catalogo.php'class='btn btn-success'>CATALOGO</a>";
    echo "<br>";
    echo "<a href='novedades.php'class='btn btn-success'>NOVEDADES</a>";
  }
?>