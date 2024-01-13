<?php
include_once("funciones.php");

$query = "SELECT * FROM catalogo";
$result = consultaSql( $query);
foreach ($result as $row) {
    echo "<div class='card'>
    <img src='data:image/jpeg;base64'".base64_encode($row['imagen'])." class='card-img-top' alt='...'/>
    <div class='card-body'>
      <p class='card-text'>hola</p>
    </div>";
    echo $row['nombre'];
    echo $row['descripcion'];
    echo $row['precio'];
    echo "</div>";
}

?>