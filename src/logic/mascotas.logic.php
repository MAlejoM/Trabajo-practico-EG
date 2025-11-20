<?php

subir_foto_mascota();

include_once __DIR__ . "/../includes/header.php"; ?>
<form action="procesoCarga.php" method="post" enctype="multipart/form-data">
  <input type="text" required name="nombre" placeholder="Nombre..." value="">
  <input type="file" required name="imagen" accept="image/*"> <!--accept="image/*" es para que solo acepte imagenes -->

  <input type="submit" value="aceptar">
</form>
<?php
include_once __DIR__ . "/../includes/footer.php";
?>

eliminar_mascota($id);

<?php
include_once __DIR__ . "/../includes/header.php";
include_once __DIR__ . "/../lib/funciones.php";
$id_mascota = $_GET['id_mascota'];
$query = "SELECT * FROM mascotas WHERE id = " . $id_mascota; //validar que la mascota exista
$resultados =  consultaSql($query);
$resultados = mysqli_fetch_array($resultados);
if ($resultados) {

  if (isset($resultados['dni_cliente']) && $resultados['dni_cliente'] == $_SESSION['usuarioId']) {
    echo '<a href="' . BASE_URL . 'public/procesos/p_carga_mascota.php?eliminar=1&id_mascota=' . $id_mascota . '">Eliminar mascota</a>';
    echo '<a href="' . BASE_URL . 'public/procesos/p_carga_mascota.php?eliminar=2&id_mascota=' . $id_mascota . '">Fallecio</a>';
    echo '<a href="' . BASE_URL . 'public/mis_mascotas.php">Cancelar</a>';
  } else {
    echo "error, no es su mascota";
  }
} else {
  echo "error, no hay mascotas con ese id";
}


?>

<?php
include_once __DIR__ . "/../includes/footer.php";
?>

guardar_mascota($datos);

<?php
include_once __DIR__ . "/../lib/funciones.php";
session_start();
if (isset($_GET['eliminar'])) {
  if ($_GET['eliminar'] == 1) {
    $query = "UPDATE mascotas SET estado = 'eliminado' WHERE id = " . $_GET['id_mascota'];
    $resultado = consultaSql($query);
    if ($resultado) {
      echo "<script>alert('SE ELIMINO CORRECTAMENTE'); window.location.href='../misMascotas.php'; </script>";
    } else {
      echo "<script>alert('ERROR EN LA ELIMINACION'); window.location.href='../misMascotas.php'; </script>";
    }
  } elseif ($_GET['eliminar'] == 2) {

    $query = "UPDATE mascotas SET estado = 'fallecido', fecha_mue = '" . date('Y-m-d') . "' WHERE id = " . $_GET['id_mascota'];
    $resultado = consultaSql($query);
    if ($resultado) {
      echo "<script>alert('SE ELIMINO CORRECTAMENTE'); window.location.href='../misMascotas.php'; </script>";
    } else {
      echo "<script>alert('ERROR EN LA ELIMINACION'); window.location.href='../misMascotas.php'; </script>";
    }
  }
}



$nombre = $_POST["nombre"];
$color = $_POST["color"];
$raza = $_POST["raza"];
$sexo = $_POST["sexo"];
$tamaño = 0;
$dni_cliente = $_SESSION['usuarioId'];
$hay_imagen = !empty($_FILES['imagen']['tmp_name']);
if ($hay_imagen) {
  $imagen = addslashes(file_get_contents($_FILES['imagen']['tmp_name']));
  $tamaño = $_FILES['imagen']['size'] / 1048576; // Convertir a megabytes
}

if ($tamaño <= 1) { //comprobar que la imagen o no este en el post o que  pese menos de un mb
  if ($hay_imagen) {
    $query = "UPDATE mascotas SET nombre = '$nombre', imagen = '$imagen', raza = '$raza', sexo = '$sexo', color = '$color' WHERE id = " . $_POST['id_mascota'];
  } else {
    $query = "UPDATE mascotas SET nombre = '$nombre', raza = '$raza', sexo = '$sexo', color = '$color' WHERE id = " . $_POST['id_mascota'];
  }
  $resultado = consultaSql($query);
  if ($resultado) {
    echo "<script>alert('SE ACTUALIZO CORRECTAMENTE'); window.location.href='../misMascotas.php'; </script>";
  } else {
    echo "<script>alert('ERROR EN LA CARGA'); window.location.href='../misMascotas.php'; </script>";
  }
} else {
  echo "<script>alert('ERROR EN LA CARGA, EL ARCHIVO PESA MAS DE 1MB'); window.location.href='../misMascotas.php'; </script>";
}
?>