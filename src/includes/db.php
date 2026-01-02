<?php

/**
 * Archivo de conexión a la base de datos
 * Configura la conexión PDO a MySQL
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'veterinaria_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
  // Crear conexión PDO
  $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
  $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];

  $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
  // En desarrollo, mostrar error detallado
  if (defined('DEV_MODE') && DEV_MODE) {
    throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
  } else {
    // En producción, mostrar mensaje genérico
    throw new Exception("Error al conectar con la base de datos. Por favor, contacte al administrador.");
  }
}
