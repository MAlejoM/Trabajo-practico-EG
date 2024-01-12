<?php
include("header.php"); 
include_once("funciones.php");
?>
    <div class="menuGlobal">
        <div class="menuLateral">
            <?php
            include("menuLateral.php");
            if (rol($_SESSION['dni']) == 'cliente') {
                header("Location: index.php"); // Rebotar si es usuario cliente
                exit();
            }
            ?>
        </div>
        <div>   
            <div>
                <form action="servicios.php" method="GET">
                    <input type="text" name="dni" placeholder="Enter client's DNI">
                    <button type="submit">Submit</button>
                </form>
            </div>
            <div>
                <?php 
                    if(isset($_GET['dni'])){
                        $dni = $_GET['dni'];
                        $persona = persona($dni);
                        if($persona){
                            echo "<table>";
                            echo "<tr><th>Name</th><th>Surname</th><th>DNI</th><th>Link</th></tr>";
                            echo "<tr>";
                            echo "<td>".$persona['nombre']."</td>";
                            echo "<td>".$persona['apellido']."</td>";
                            echo "<td>".$persona['dni']."</td>";
                            echo "<td><a href='seleccionar_dueño.php?dni=".$persona[5]."'>Seleccionar Dueño </a></td>";
                            echo "</tr>";
                            echo "</table>";
                            
                        }else{
                            echo "No personas found for this dni";
                        }
                    }else{
                        $query = "SELECT * FROM datosusuario WHERE rol = 'cliente'";
                        $resultados = consultaSql($query);
                        $resultados = mysqli_fetch_all($resultados);
                        echo "<table>";
                        echo "<tr><th>Name</th><th>Surname</th><th>DNI</th><th>Link</th></tr>";
                        foreach($resultados as $persona){
                            echo "<tr>";
                            echo "<td>".$persona[1]."</td>";
                            echo "<td>".$persona[2]."</td>";
                            echo "<td>".$persona[5]."</td>";
                            echo "<td><a href='seleccionar_duenio.php?dni=".$persona[5]."'>Seleccionar Dueño </a></td>";
                            echo "</tr>";
                        }
                        echo "</table>"; // Add this line to close the table
                    }
                ?>
            </div>
        </div>
        
    </div>

<?php
include("footer.php");
?>