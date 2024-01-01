<?php include("header.php"); ?>

<?php 
include_once("funciones.php");

  if (isset($_POST['nombre'])) {
    $nombre = $_POST['nombre'];
    session_start();

    // Verificar si el nombre ya ha sido insertado
    if (!($_SESSION['nombre_insertado'])) {
      $query = "INSERT INTO tabla1 (id, nombre) VALUES (NULL, '$nombre')";
      $result = consultaSql($query);

      // Marcar el nombre como insertado en la sesión
      $_SESSION['nombre_insertado'] = true;
      
    } else {
      echo "El nombre ya ha sido insertado previamente.";
      echo "<br>";
    }

    echo "Se ha insertado el nombre: $nombre";
    echo "<br>";
    $_POST['nombre'] = "";

    $query = "SELECT * FROM tabla1";
    $result = consultaSql($query);
    foreach ($result as $row) {
      echo $row['nombre'];
      echo "<br>";
    }
  }
   
?>

<?php include("footer.php"); ?>
