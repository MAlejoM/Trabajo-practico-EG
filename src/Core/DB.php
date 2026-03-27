<?php

namespace App\Core;

class DB
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $name = getenv('DB_NAME') ?: 'veterinaria_db';
        $port = getenv('DB_PORT') ?: 3306;


        $this->connection = mysqli_connect($host, $user, $pass, $name, $port);

        if (!$this->connection) {
            $error = mysqli_connect_error();
            $errno = mysqli_connect_errno();
            die("Error de conexión a la base de datos: [$errno] $error");
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
