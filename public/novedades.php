<?php include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/lib/funciones.php";

?>

<div class="menuGlobal">
  <div class="menuLateral">
    <?php //Si no esta logueado se muestran las opciones de catalogo y novedades
    include_once __DIR__ . "/../src/includes/menu_lateral.php";
    ?>
  </div>

  <div>
    <?php
    include_once __DIR__ . "../muestreo_novedades.php";
    ?>
  </div>
</div>



<?php
include_once __DIR__ . "/../src/includes/footer.php";
?>