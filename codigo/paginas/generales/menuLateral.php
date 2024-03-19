<?php
include_once("../../procesos/funciones.php");

if(isset($_SESSION['dni'])){ 
    $login = true;
    $rol = rol($_SESSION['dni']);
}else{
  $login = false;
}

if($login){ 
    
    if($rol == "admin"){
        
      echo "<a href='../internas/administrarCatalogo.php' class='btn btn-success'>ADMINISTRAR CATALOGO</a>";
        echo "<br>";
      echo "<a href='../internas/administrarNovedades.php' class='btn btn-success'>ADMINISTRAR NOVEDADES</a>";
        echo "<br>";
      echo "<a href='../internas/administrarUsuarios.php'class='btn btn-success'>ADMINISTRAR USUARIOS</a>";
        echo "<br>";
      echo "<a href='../internas/servicios.php'class='btn btn-success'>SERVICIOS</a>";
        echo "<br>";
      
      }elseif($rol == "cliente"){
      echo "<a href='../internas/catalogo.php' class='btn btn-success'>CATALOGO</a>";
        echo "<br>";
      echo "<a href='../internas/novedades.php' class='btn btn-success'>NOVEDADES</a>";
        echo "<br>";
      echo "<a href='../internas/mismascotas.php' class='btn btn-success'>MIS MASCOTAS</a>";
    }elseif($rol == "prof"){
      echo "<a href='../internas/catalogo.php'class='btn btn-success'>CATALOGO</a>";
        echo "<br>";
      echo "<a href='../internas/novedades.php'class='btn btn-success'>NOVEDADES</a>";
        echo "<br>";
      echo "<a href='../internas/servicios.php'class='btn btn-success'>SERVICIOS</a>";
        echo "<br>";
      echo "<a href='../internas/mismascotas.php'class='btn btn-success'>MIS MASCOTAS</a>";
    }  
    
    
  }else{
    
    echo "<a href='../internas/catalogo.php'class='btn btn-success'>CATALOGO</a>";
    echo "<br>";
    echo "<a href='../internas/novedades.php'class='btn btn-success'>NOVEDADES</a>";
  }
?>