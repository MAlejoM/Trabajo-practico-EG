<?php
include_once("funciones.php");

$query = "SELECT * FROM catalogo";
$result = consultaSql( $query);
foreach ($result as $row) {
    echo "<div>";
    echo "<img src='data:image/jpeg;base64,".base64_encode($row['imagen'])."' />";
    echo "<br>";
    echo $row['nombre'];
    echo "<br>";
    echo $row['descripcion'];
    echo "<br>";
    echo $row['precio'];
    echo "<br>";
    echo "</div>";
}

?>