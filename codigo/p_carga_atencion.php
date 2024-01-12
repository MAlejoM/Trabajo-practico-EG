<?php
    include_once("funciones.php");
    if (rol($_SESSION['dni']) == 'cliente') {
        header("Location: index.php"); // Rebotar si es usuario cliente
        exit();
    }
    if(isset($_POST['titulo']) && isset($_POST['servicio']) && isset($_POST['descripcion']) && isset($_POST['profesional']) && isset($_POST['fecha'])){
        $titulo = $_POST['titulo'];
        $servicio = $_POST['servicio'];
        $descripcion = $_POST['descripcion'];
        $profesional = $_POST['profesional'];
        $fecha = $_POST['fecha'];
        $query = "INSERT INTO atenciones (titulo, descripcion, id_servicio, id_profesional, fecha) VALUES ('$titulo', '$descripcion', '$servicio', '$profesional', '$fecha')";
        $resultados = consultaSql($query);
        
    }