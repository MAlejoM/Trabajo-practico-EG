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
                <form action='p_carga_atencion.php' method='post'>
                    <input type="text" name="titulo" id="titulo" placeholder="Ingrese el título" required>
                    <br>
                    <label for="servicio">Seleccione un servicio:</label>
                    <select name="servicio" id="servicio" required>
                        <?php
                            $query = "SELECT * FROM servicios";
                            $resultados = consultaSql($query);
                            foreach ($resultados as $servicio) { //recorre los resultados de la consulta
                                echo "<option value='".$servicio['id']."'>".$servicio['nombre']."</option>";
                            }
                        ?>  
                    </select>
                    <br>
                    <textarea name="descripcion" id="descripcion" placeholder="Ingrese la descripción" required></textarea>
                    <br>
                    <label for="profesional">Seleccione un profesional:</label>
                    <select name="profesional" id="profesional" required>
                        <?php
                            $query = "SELECT * FROM datosusuario WHERE rol = 'prof'";
                            $resultados = consultaSql($query);
                            foreach ($resultados as $profesional) {
                                echo "<option value='".$profesional['id']."'>".$profesional['nombre']."  ".$profesional['apellido']."</option>";
                            }
                        ?>  
                    </select>
                    <br>
                    <label for="fecha">Seleccione una fecha:</label>
                    <input type="date" name="fecha" id="fecha" required>
                    <br>
                    <button type="submit">Enviar</button>
                </form>
            </div>
        </div>


<?php
    include("footer.php");
?>