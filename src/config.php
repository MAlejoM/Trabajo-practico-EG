<?php 
//se declaran las variables para mas seguridad y comodidad

// Rutas base del proyecto
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

// Las variables de entorno ya se cargan en autoload.php
// No se duplica aquí

define('PUBLIC_PATH', PROJECT_ROOT . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// Configuración del correo
define('MAILHOST', $_ENV['MAIL_HOST'] ?? (getenv('MAIL_HOST') ?: "smtp.gmail.com"));

define('USERNAME', $_ENV['MAIL_USER'] ?? (getenv('MAIL_USER') ?: "luhmannm0@gmail.com"));

define('PASSWORD', $_ENV['MAIL_PASS'] ?? (getenv('MAIL_PASS') ?: "qbscabxaxvjoisvt"));

define('SEND_FROM',"info@nosequeponer.com");

define('SEND_FROM_NAME',"Veterinaria San Anton");

define('REPLY_TO',"info@veterinariasananton.com");

define('REPLY_TO_NAME',"Matias");

?>