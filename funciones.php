<?php
define ('DB_HOST', 'localhost');
define('DB_USER', 'root');
define ('DB_PASS', 'root');
define ('DB_NAME', 'prueba');


function consultaSql($query){
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME ) or die ("error");
    $resultados = mysqli_query ($connection, $query);
    mysqli_close($connection);
    return $resultados; 
} 

?>