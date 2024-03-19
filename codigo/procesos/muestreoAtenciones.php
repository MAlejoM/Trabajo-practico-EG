<?php


        $query = "SELECT * FROM mascotas WHERE id = ".$id_mascota; //validar que la mascota exista
        $resultados =  consultaSql($query); 
        $resultados = mysqli_fetch_array($resultados);

        if($resultados){  

            if($_SESSION['rol']!='cliente'||(isset($resultados['dni_cliente'])&&$resultados['dni_cliente'] == $_SESSION['dni'])){   //validar que la mascota sea del cliente y en el caso de no ser cliente, este pude acceder a todas las mascotas
                $query = "SELECT * FROM atenciones WHERE id_mascota = ".$id_mascota; //validar que la mascota tenga atenciones
                $resultados = consultaSql($query); 
                $resultados = mysqli_fetch_all($resultados, MYSQLI_ASSOC); //crea un arreglo con los datos de la consulta a la BD
            
                // Get the length of the array
                if($resultados){
                    foreach ($resultados as $atencion) 
                        {                                 
                        echo "<div class='atencion'>"; 
                        echo "<h3>".$atencion['titulo']."</h3>"; 
                        echo "<br>";
                        echo "<h4>".$atencion['fecha_hora']."</h4>";
                        echo "<br>";
                        echo "<h4>".$atencion['descripcion']."</h4>";
                        echo "<br>";
                        echo "<h4> Realizado por:".nombre($atencion['id_personal'])."</h4>"; //funcion que devuelve el nombre del personal
                        echo "<br>";
                        echo "<h4> Servicio:".servicio($atencion['id_servicio'])['nombre']."</h4>"; //funcion que devuelve el nombre del servicio
                        echo "<br>";
                        echo "<h4> Tipo de Servicio:".servicio($atencion['id_servicio'])['tipo']."</h4>"; //funcion que devuelve el tipo del servicio
                        if($_SESSION['rol']!='cliente'){echo "<a href='editarAtencion.php?id_atencion=".$atencion['id']."'>Editar atención</a>";} //validar que el usuario no sea cliente para poder editar atenciones
                        echo "</div>";
                        }
                    }
                    else{
                        echo "error, no hay atenciones para esa mascota";
                    }
                }else{
                    echo "error, no es su mascota";
                }
            }else{
                echo "error, no existe la mascota";
            }



?>