<!DOCTYPE html>
<html>
<head>
  <title>Veterinaria San Anton</title>

  <?php
      // Obtener el nombre del archivo actual
      $currentFile = basename($_SERVER['PHP_SELF']);
      // Verificar si el archivo actual es 'index.php'
      if ($currentFile === 'editarMascota.php' || $currentFile === 'consultarAtenciones.php') {
        // Si es 'index.php', establecer el enlace como 'index.php'
        $link = '../';
      }else if ($currentFile === 'eliminarMascota.php') {
        // Si es 'index.php', establecer el enlace como 'index.php'
        $link = '../../';
      } 
      else {
        // Si no es 'index.php', establecer el enlace como '../generales/index.php'
        $link = '';
      }
      
  ?>
  <link rel="stylesheet" type="text/css" href="<?php echo $link; ?>public/style.css?v=0">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
  <header>       
    <div class="header-btns">
      
      <a href="<?php echo $link; ?>index.php">
        <img src="<?php echo $link; ?>public/img/Logo.jpeg" alt="Descripción de la imagen">
      </a>
      <h1>Veterinaria San Anton</h1>
      <?php 
      session_start();
      if(isset($_SESSION['dni'])){
        ?>
      <a href="miPerfil.php" style="text-decoration: none; border-bottom: 2px solid rgb(0, 0, 0);">
        <img src="<?php echo $link; ?>public/img/Perfil.jpeg" alt="Descripción de la imagen">
      </a>
      <?php }else{ ?>
      <a href="<?php echo $link; ?>login.php" class="btn btn-success" style="text-decoration: none; border-bottom: 2px solid rgb(0, 0, 0); ">
        INICIAR SESION
      </a>
      <?php } ?>
    </div>
          
  </header>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>  
</body>