<?php
include("header.php"); 
include_once("funciones.php");
if(isset($_GET['id_mascota'])){ //validar que se haya enviado el id de la mascota
    $id_mascota = $_GET['id_mascota']; 
    $query = "SELECT * FROM mascotas WHERE id = ".$id_mascota; //validar que la mascota exista
    $resultados =  consultaSql($query); 
    if($resultados){  
        $resultados = mysqli_fetch_array($resultados); 
        if($resultados['dni_cliente'] == $_SESSION['dni']){   //validar que la mascota sea del cliente
            $query = "SELECT * FROM atenciones WHERE id_mascota = ".$id_mascota; //validar que la mascota tenga atenciones
            $resultados = consultaSql($query); 
            if($resultados){
                foreach ($resultados as $atencion) 
                    {                                   //mostrar las atenciones de la mascota
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
                    echo "</div>";
                    }
                }else{
                    echo "error, no hay atenciones para esa mascota";
                }
            }else{
                echo "error, no es su mascota";
            }
        }else{
            echo "error, no hay mascotas con ese id";
        }
    }    
        else{
            echo "error";
        }
?>