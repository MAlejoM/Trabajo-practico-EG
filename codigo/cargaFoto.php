<?php
include("header.php");
?>
<form action="procesoCarga.php" method="post" enctype="multipart/form-data">
  <input type="text" required name="nombre" placeholder="Nombre..." value="">
  <input type="file" required name="imagen">
  <input type="submit" value="aceptar">
</form>
<?php
include("footer.php");
?>