<?php include('header.php'); 
include_once("funciones.php");

?>

<div >
  <?php //Si no esta logueado se muestran las opciones de catalogo y novedades
    include("menuLateral.php");
  ?>
  
</div>
<div>
  <?php
  include("muestreoNovedades.php");
  ?>
</div>


<?php
include('footer.php');
?>