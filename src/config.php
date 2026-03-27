<?php 
//se declaran las variables para mas seguridad y comodidad

// Rutas base del proyecto
define('PROJECT_ROOT', dirname(__DIR__));

// Cargar variables de entorno si el archivo existe
if (file_exists(PROJECT_ROOT . '/.env')) {
    $dotenv = new \App\Core\DotEnv(PROJECT_ROOT . '/.env');
    $dotenv->load();
}

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