<?php

// Autoload de Composer (PHPMailer, etc.)
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
}

spl_autoload_register(function ($class) {
    // Namespace base
    $prefix = 'App\\';
    // Directorio base donde están las clases (src/)
    $base_dir = __DIR__ . '/';

    // Verificar si la clase usa el prefijo
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Obtener el nombre relativo de la clase
    $relative_class = substr($class, $len);

    // Reemplazar separadores de namespace con separadores de directorio
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Si el archivo existe, requerirlo
    if (file_exists($file)) {
        require $file;
    }
});

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

// Cargar variables de entorno si el archivo existe
if (file_exists(PROJECT_ROOT . '/.env')) {
    $dotenv = new \App\Core\DotEnv(PROJECT_ROOT . '/.env');
    $dotenv->load();
}
