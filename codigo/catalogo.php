<?php include('header.php'); 
include_once("funciones.php");
if(isset($_SESSION['dni'])){
  $login = true;
    
}else{
  $login = false;
}
?>

<div >
  <?php //Si no esta logueado se muestran las opciones de catalogo y novedades
  if($login){
    echo "<a href='catalogo.php'>CATALOGO</a>";
    echo "<a href='novedades.php'>NOVEDADES</a>";
    
    
  }else{
    echo "<a href='catalogo.php'>CATALOGO</a>";
    echo "<a href='novedades.php'>NOVEDADES</a>";
  }
  ?>
  
</div>
<div>
  <?php
  include("muestreoCatalogo.php");
  ?>
</div>


<?php
include('footer.php');
?>