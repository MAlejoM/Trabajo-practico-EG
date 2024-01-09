<?php 
include("header.php");
include_once("funciones.php");
?>
<div>
    <div>
        <?php
        include("menuLateral.php");
        ?>
    </div>
    <div>
        <?php
        $query = "SELECT * FROM mascotas WHERE dni_cliente = ".$_SESSION['dni']; //consulta a la base de datos
        $resultados = consultaSql($query); 
        foreach ($resultados as $mascota) { //recorre los resultados de la consulta

            echo "<div class='mascota'>";
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
            <div><a href='consultarAtenciones.php?id_mascota=<?php echo $mascota['id']; ?>'>CONSULTAR ATENCIONES</a> </div>
            <?php
            
            echo "</div>";
        }
        ?>
    </div>
</div>
    


<?php
include("footer.php");
?>