<?php
include("../header.php");
include_once("../procesos/funciones.php");

?>

<div class="menuGlobal">
    <div class="menuLateral">
        <?php
        include("../menuLateral.php");
        ?>
    </div>
    <div>
        <?php
        $id_mascota = $_GET['id_mascota']; 
        $query = "SELECT * FROM mascotas WHERE id = ".$id_mascota; //validar que la mascota exista
        $resultados =  consultaSql($query); 
        $resultados = mysqli_fetch_array($resultados);
        if($resultados){  
            if(isset($resultados['dni_cliente'])&&$resultados['dni_cliente'] == $_SESSION['dni']){   //validar que la mascota sea del cliente
                $query = "SELECT * FROM mascotas WHERE id = ".$id_mascota;
                $resultados = consultaSql($query);
                $resultados = mysqli_fetch_array($resultados);
                if($resultados){
                    echo "<form action='../procesos/p_carga_mascota.php' method='post' enctype='multipart/form-data'>";
                    echo "<input type='hidden' name='id_mascota' value='".$id_mascota."'>";
                    echo "<label for='nombre'>Nombre</label>";
                    echo "<input type='text' name='nombre' value='".$resultados['nombre']."'>";
                    echo "<br>";
                    echo "<label for='color'>Color</label>";
                    echo "<input type='text' name='color' value='".$resultados['color']."'>";
                    echo "<br>";
                    echo "<label for='raza'>Raza</label>";
                    echo "<input type='text' name='raza' value='".$resultados['raza']."'>";
                    echo "<br>";
                    echo "<label for='sexo'>Sexo</label>";
                    echo "<input type='text' name='sexo' value='".$resultados['sexo']."'>";
                    echo "<br>";
                    echo "<label for='imagen'>Imagen</label>";
                    echo "<img src='data:image/jpeg;base64,".base64_encode($resultados['imagen'])."' />"; //imagen de la mascota
                    echo "<input type=\"file\"  name=\"imagen\" accept=\"image/*\">";
                    echo "<br>";
                    echo "<input type='submit' value='Editar'>";
                    echo "</form>";

                    echo "<div><a href='editarMascota/eliminarMascota.php?id_mascota=".$resultados["id"]."'>Eliminar</a></div>";
                }else{
                    echo "error, no es su mascota";
                }
            }else{
                echo "error, no hay mascotas con ese id";
            }
        }
        ?>
    </div>
</div>


<?php
include("../footer.php");  
?>