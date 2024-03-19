<?php include('../generales/header.php'); 
include_once("../../procesos/funciones.php");

?>

<div class="menuGlobal">
  <div class="menuLateral">
  <?php //Si no esta logueado se muestran las opciones de catalogo y novedades
    include("../generales/menuLateral.php");
  ?>
  </div>
 
  <div>
  <?php
  include("../../procesos/muestreoNovedades.php");
  ?>
</div>
</div>



<?php
include('../generales/footer.php');
?>