<?php

include_once __DIR__ . "/../src/includes/header.php";

include_once __DIR__ . "/../src/logic/funciones.php";
?>
<div>
  <h2>Menu principal</h2>
</div>

<div class="menuGlobal">
  <div class="menuLateral">
    <?php
    include_once __DIR__ . "/../src/includes/menu_lateral.php";
    ?>
  </div>
  <div class="menuPpal">
    <?php
    echo "<h3>BIENVENIDO A LA PAGINA DE INICIO</h3>";
    echo "<img src='public/img/bienvenida.png' alt='imagen de bienvenida'>";
    ?>
  </div>

</div>

<?php
include_once __DIR__ . "/../src/includes/footer.php"; ?>