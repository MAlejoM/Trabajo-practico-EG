<?php
include_once __DIR__ . "/../src/includes/header.php";
include_once __DIR__ . "/../src/lib/funciones.php";

?>

<div class="menuGlobal">
    <div class="menuLateral">
        <?php
        include_once __DIR__ . "/../src/includes/menu_lateral.php";
        ?>
    </div>
    <div>
        <?php
        $query = "SELECT * FROM mascotas WHERE id=" . $_GET['id_mascota']; //consulta a la base de datos
        $resultados = consultaSql($query);

        foreach ($resultados as $mascota) { //recorre los resultados de la consulta

            echo "<div class='etiquetaMascota'>";
            echo "<h3>" . $mascota['nombre'] . "</h3>";
            echo "<br>";
            echo "<img src='data:image/jpeg;base64," . base64_encode($mascota['imagen']) . "' />"; //imagen de la mascota
            echo "<br>";
            echo "<h4>" . $mascota['color'] . "</h4>";
            echo "<br>";
            echo "<h4>" . $mascota['raza'] . "</h4>";
            echo "<br>";
            echo "<h4>" . $mascota['sexo'] . "</h4>";
            echo "<br>";
            echo "</div>";
        }

        ?>
    </div>
    <div class="atenciones">
        <?php


        if (isset($_GET['id_mascota'])) { //validar que se haya enviado el id de la mascota
            $id_mascota = $_GET['id_mascota'];
            include("../procesos/muestreoAtenciones.php");
        } else {
            echo "error";
        }
        ?>
    </div>
</div>
<?php
include_once __DIR__ . "/../src/includes/footer.php"; ?>