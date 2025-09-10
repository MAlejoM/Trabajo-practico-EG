  <?php
    include_once("funciones.php");
    if (rol($_SESSION['rol']) == 'cliente') {
        header("Location: ../paginas/generales/index.php"); // Rebotar si es usuario cliente
        exit();
    }
    if(isset($_POST['titulo']) && isset($_POST['servicio']) && isset($_POST['descripcion']) && isset($_POST['profesional']) && isset($_POST['fecha'])){
        $titulo = $_POST['titulo'];
        $servicio = $_POST['servicio'];
        $descripcion = $_POST['descripcion'];
        $profesional = $_POST['profesional'];
        $id_mascota = $_POST['id_mascota'];
        $fecha = $_POST['fecha'];
        $query = "INSERT INTO atenciones (id_mascota, titulo, descripcion, id_servicio, id_personal, fecha_hora) VALUES ('$id_mascota','$titulo', '$descripcion', '$servicio', '$profesional', '$fecha')";
        $resultados = consultaSql($query);
        if($resultados){
            echo "<script>alert('SE CARG CORRECTAMENTE LA ATENCION'); window.location.href='../paginas/internas/servicios.php'; </script>";
        }else{
            echo "<script>alert('ERROR EN LA CARGA'); window.location.href='../paginas/internas/servicios.php'; </script>";
        }
        
    }
?>