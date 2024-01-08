<!DOCTYPE html>
<html>
<head>
  <title>Veterinaria San Anton</title>
  <link rel="stylesheet" type="text/css" href="public/style.css?v=0">
</head>
<body>
  <header>       
    <div class="header-btns">
      <a href="index.php">
        <img src="public/img/Logo.jpeg" alt="Descripción de la imagen">
      </a>
      <h1>Veterinaria San Anton</h1>
      <?php 
      session_start();
      if(isset($_SESSION['dni'])){
        ?>
      <a href="miPerfil.php">
        <img src="public/img/Perfil.jpeg" alt="Descripción de la imagen">
      </a>
      <?php }else{ ?>
      <a href="login.php">
        INICIAR SESION
      </a>
      <?php } ?>
    </div>
          
  </header>