<?php 

    include("../generales/header.php");
    include_once("../../procesos/funciones.php");
    if (rol($_SESSION['dni']) == 'cliente' || $_GET['id_mascota'] == null) {
        header("Location: index.php"); // Rebotar si es usuario cliente o si no hay id de mascota
        exit();
    }
    ?>
        <div class="menuGlobal">
            <div class="menuLateral">
                <?php
                include("../generales/menuLateral.php");
                ?>
            </div>
            <div>
                <form action='../../procesos/p_carga_atencion.php' method='post'>
                    <input type="hidden" name="id_mascota" value="<?php echo $_GET['id_mascota']; ?>">
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
    include("../generales/footer.php");
?>