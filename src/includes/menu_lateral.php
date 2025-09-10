<?php
require_once __DIR__ . '/../lib/funciones.php';

if(isset($_SESSION['dni'])){ 
    $login = true;
    $rol = rol($_SESSION['dni']);
}else{
  $login = false;
}


// Obtener el nombre del archivo actual
$currentFile = basename($_SERVER['PHP_SELF']);
// Verificar si el archivo actual es 'index.php'
if ($currentFile === 'editarMascota.php' || $currentFile === 'consultarAtenciones.php') {
  // Si es 'index.php', establecer el enlace como 'index.php'
  $link = '../';
}else if ($currentFile === 'eliminarMascota.php') {
  // Si es 'index.php', establecer el enlace como 'index.php'
  $link = '../../';
} 
else {
  // Si no es 'index.php', establecer el enlace como '../generales/index.php'
  $link = '';
}




if($login){ 
    
    if($rol == "admin"){
        
      echo "<a href=".$link."administrarCatalogo.php class='btn btn-success'>ADMINISTRAR CATALOGO</a>";
        echo "<br>";
      echo "<a href=".$link."administrarNovedades.php class='btn btn-success'>ADMINISTRAR NOVEDADES</a>";
        echo "<br>";
      echo "<a href=".$link."administrarUsuarios.php class='btn btn-success'>ADMINISTRAR USUARIOS</a>";
        echo "<br>";
      echo "<a href=".$link."servicios.php class='btn btn-success'>SERVICIOS</a>";
        echo "<br>";
      
      }elseif($rol == "cliente"){
        //es coloca el link previo a catalogo para el caso en que se este en un archivo con carpeta

      echo "<a href=".$link."catalogo.php class='btn btn-success'>CATALOGO</a>";
        echo "<br>";
      echo "<a href=".$link."novedades.php class='btn btn-success'>NOVEDADES</a>";
        echo "<br>";
      echo "<a href=".$link."misMascotas.php class='btn btn-success'>MIS MASCOTAS</a>";
    }elseif($rol == "prof"){
      echo "<a href=".$link."catalogo.php class='btn btn-success'>CATALOGO</a>";
        echo "<br>";
      echo "<a href=".$link."novedades.php class='btn btn-success'>NOVEDADES</a>";
        echo "<br>";
      echo "<a href=".$link."servicios.php class='btn btn-success'>SERVICIOS</a>";
        echo "<br>";
      echo "<a href=".$link."misMascotas.php class='btn btn-success'>MIS MASCOTAS</a>";
    }  
    
    
  }else{
    
    echo "<a href='catalogo.php'class='btn btn-success'>CATALOGO</a>";
    echo "<br>";
    echo "<a href='novedades.php'class='btn btn-success'>NOVEDADES</a>";
  }
?>