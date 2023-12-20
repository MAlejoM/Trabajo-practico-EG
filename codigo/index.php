<?php include("header.php"); ?>

<?php 

include_once("funciones.php");
 

echo "Conexion exitosa";
session_start();
$_SESSION['nombre_insertado'] = false; 
?>

<form action="muestreo.php" method="POST">
    <input type="text" name="nombre">
    <input type="submit" value="Enviar">
</form>

<?php include("footer.php"); ?>


