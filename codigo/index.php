<?php 

include("header.php"); 
include_once("funciones.php");

if(!isset($_SESSION['dni'])){
  include("nosession.php");
}
else{
  include("session.php");
}

include("footer.php"); ?>

