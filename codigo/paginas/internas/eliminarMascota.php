<?php
include("../generales/header.php");
include_once("../../procesos/funciones.php");
$id_mascota = $_GET['id_mascota']; 
$query = "SELECT * FROM mascotas WHERE id = ".$id_mascota; //validar que la mascota exista
$resultados =  consultaSql($query); 
$resultados = mysqli_fetch_array($resultados);
if($resultados){  

    if(isset($resultados['dni_cliente']) && $resultados['dni_cliente'] == $_SESSION['dni']){
        echo '<a href="procesoCarga.php?eliminar=1&id_mascota=' . $id_mascota . '">Eliminar mascota</a>';
        echo '<a href="procesoCarga.php?eliminar=2&id_mascota=' . $id_mascota . '">Fallecio</a>';
        echo '<a href="misMascotas.php">Cancelar</a>';
    } else {
        echo "error, no es su mascota";
    }
} else {
    echo "error, no hay mascotas con ese id";
}


?>

<?php
include("../generales/footer.php");
?>