<?php 
include("header.php");
include_once("funciones.php");
if (rol($_SESSION['dni']) == 'cliente') {
    header("Location: index.php"); // Rebotar si es usuario cliente
    exit();
}

?>
    <div class="menuGlobal">
        <div class="menuLateral">
            <?php
            include("menuLateral.php");
            ?>
        </div>
        <div>
            <?php
                $query = "SELECT * FROM mascotas WHERE dni_cliente = '".$_GET['dni']."'";
                $resultados = consultaSql($query);
               
                foreach ($resultados as $mascota) { //recorre los resultados de la consulta

                    echo "<div class='etiquetaMascota'>";
                    echo "<h3>".$mascota['nombre']."</h3>";
                    echo "<br>";
                    echo "<img src='data:image/jpeg;base64,".base64_encode($mascota['imagen'])."' />"; //imagen de la mascota
                    echo "<br>";
                    echo "<h4>".$mascota['color']."</h4>";
                    echo "<br>";
                    echo "<h4>".$mascota['raza']."</h4>";
                    echo "<br>";
                    echo "<h4>".$mascota['sexo']."</h4>";
                    echo "<br>";
                    ?>
                    <div><a href='registrar_atencion.php?id_mascota=<?php echo $mascota['id']; ?>'>REGISTRAR ATENCIONES</a> </div>
                    <?php
                    
                    echo "</div>";
                }
                ?>
        </div>

    </div>


<?php
include("footer.php");
?>