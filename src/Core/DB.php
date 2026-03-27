<?php

namespace App\Core;

class DB
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'] ?? (getenv('DB_HOST') ?: 'localhost');
        $user = $_ENV['DB_USER'] ?? (getenv('DB_USER') ?: 'root');
        $pass = $_ENV['DB_PASS'] ?? (getenv('DB_PASS') ?: '');
        $name = $_ENV['DB_NAME'] ?? (getenv('DB_NAME') ?: 'veterinaria_db');
        $port = $_ENV['DB_PORT'] ?? (getenv('DB_PORT') ?: 3306);



        try {
            $this->connection = mysqli_connect($host, $user, $pass, $name, $port);
        } catch (\mysqli_sql_exception $e) {
            $error = $e->getMessage();
            $errno = $e->getCode();
            $debugEnv = json_encode($_ENV);
            $debugGetenv = getenv('DB_HOST');
            $configFileLoaded = defined('PROJECT_ROOT') ? 'YES' : 'NO';
            die("Error de conexión a la BD: [$errno] $error | INTENTO HOST: $host | CONFIG LOADED: $configFileLoaded | ENV_DUMP: $debugEnv | GETENV: $debugGetenv");
        }

        if (!$this->connection) {
            $error = mysqli_connect_error();
            $errno = mysqli_connect_errno();
            die("Error fallback de conexión a la base de datos: [$errno] $error");
        }

        $this->connection->set_charset("utf8mb4");
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public static function getConn()
    {
        return self::getInstance()->getConnection();
    }
}
