<?php include_once __DIR__ . "/../src/includes/header.php";
include_once("procesos/funciones.php");

?>
<div class="menuGlobal">
  <div class="menuLateral">
    <?php
    include_once __DIR__ . "/../src/includes/menu_lateral.php";
    ?>
  </div>
  <div class="catalogo">
    <?php
    include_once __DIR__ . "/../src/muestreo_catalogo.php";
    ?>
  </div>
</div>



<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>