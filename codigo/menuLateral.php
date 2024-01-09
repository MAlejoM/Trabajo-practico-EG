<?php
include_once("funciones.php");

if(isset($_SESSION['dni'])){ 
    $login = true;
    $rol = rol($_SESSION['dni']);
}else{
  $login = false;
}

if($login){ 
    
    if($rol == "admin"){
        
      echo "<a href='administrarCatalogo.php'>ADMINISTRAR CATALOGO</a>";
        echo "<br>";
      echo "<a href='administrarNovedades.php'>ADMINISTRAR NOVEDADES</a>";
        echo "<br>";
      echo "<a href='administrarUsuarios.php'>ADMINISTRAR USUARIOS</a>";
        echo "<br>";
      echo "<a href='servicios.php'>SERVICIOS</a>";
        echo "<br>";
      
      }elseif($rol == "cliente"){
      echo "<a href='catalogo.php'>CATALOGO</a>";
        echo "<br>";
      echo "<a href='novedades.php'>NOVEDADES</a>";
        echo "<br>";
      echo "<a href='mismascotas.php'>MIS MASCOTAS</a>";
    }elseif($rol == "prof"){
      echo "<a href='catalogo.php'>CATALOGO</a>";
        echo "<br>";
      echo "<a href='novedades.php'>NOVEDADES</a>";
        echo "<br>";
      echo "<a href='servicios.php'>SERVICIOS</a>";
        echo "<br>";
      echo "<a href='mismascotas.php'>MIS MASCOTAS</a>";
    }  
    
    
  }else{
    
    echo "<a href='catalogo.php'>CATALOGO</a>";
    echo "<br>";
    echo "<a href='novedades.php'>NOVEDADES</a>";
  }
?>