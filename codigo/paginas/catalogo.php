<?php include('header.php'); 
include_once("funciones.php");

?>
<div class="menuGlobal">
  <div class="menuLateral">
    <?php //Si no esta logueado se muestran las opciones de catalogo y novedades
      include("menuLateral.php");
    ?>
  </div>
  <div class="catalogo">
    <?php
    include("muestreoCatalogo.php");
    ?>
  </div>
</div>



<?php
include('footer.php');
?>