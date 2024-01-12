<?php 


include("header.php"); 
include_once("funciones.php");
?>
<div><h2>Menu principal</h2></div>

<div class="menuGlobal">
  <div class="menuLateral">
    <?php
    include("menuLateral.php");
    ?>
  </div>
  <div class="menuPpal">
  <?php 
  echo "<h3>BIENVENIDO A LA PAGINA DE INICIO</h3>"; 
  echo "<img src='./img/bienvenida.png' alt='imagen de bienvenida'>"; 
  ?>
  </div>
  
</div>

<?php
include("footer.php"); ?>

