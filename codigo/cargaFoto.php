<?php
include("header.php");
?>
<form action="procesoCarga.php" method="post" enctype="multipart/form-data">
  <input type="text" required name="nombre" placeholder="Nombre..." value="">
  <input type="file" required name="imagen" accept="image/*"> <!--accept="image/*" es para que solo acepte imagenes -->

  <input type="submit" value="aceptar">
</form>
<?php
include("footer.php");
?>