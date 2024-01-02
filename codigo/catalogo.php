<?php include('header.php'); 
include_once("funciones.php");
if(isset($_SESSION['dni'])){
  $login = true;
    
}else{
  $login = false;
}
?>

<div>
  <?php
  if($login){
    
  }else{
    echo "<a href='catalogo.php'>CATALOGO</a>";
    echo "<a href='novedades.php'>NOVEDADES</a>";
  }
  ?>
  
</div>
<div>

</div>


<?php
include('footer.php');
?>