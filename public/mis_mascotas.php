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
    <div class="mascotas">
        <?php
        //TODO: se comentea para evitar conflcitos, hay que reconstruir todo.
        //$query = "SELECT * FROM mascotas WHERE dni_cliente = " . $_SESSION['usuarioId'] . " AND estado = 'activo'"; //consulta a la base de datos
        //$resultados = consultaSql($query);
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
        ?>
            <div><a href='misMascotas/consultarAtenciones.php?id_mascota=<?php echo $mascota['id']; ?>'>CONSULTAR ATENCIONES</a> </div>
            <div><a href='misMascotas/editarMascota.php?id_mascota=<?php echo $mascota['id']; ?>'>EDITAR</a> </div>
        <?php

            echo "</div>";
        }
        ?>
    </div>
</div>



<?php
include_once __DIR__ . "/../src/includes/footer.php"; ?>