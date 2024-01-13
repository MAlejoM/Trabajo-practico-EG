<?php
include_once("funciones.php");

$query = "SELECT * FROM catalogo";
$result = consultaSql( $query);
foreach ($result as $row) {
    echo "<div class='card'> ";
   
    echo "<img src='data:image/jpeg;base64,".base64_encode($row['imagen'])."' class='card-img-top' alt='".$row['nombre']."'  style='max-height: 200px'/>";
    echo "<div class='card-body'>";
    echo "<p class='card-text'>hola</p>";
    echo "</div>";
    echo $row['nombre'];
    echo $row['descripcion'];
    echo $row['precio'];
    echo "</div>";
}

?>