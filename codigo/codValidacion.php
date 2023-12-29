<?php
include("header.php");?>

<?php
$filePath = '/c:/xampp/htdocs/Trabajo-practico-EG/codigo/validacion.php';
$code = file_get_contents($filePath);
?>
<div class="codigo">
    <a><?php echo htmlspecialchars($code); ?></a>
</div>


<?php
include("footer.php");?>