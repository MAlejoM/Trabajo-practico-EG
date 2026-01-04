<?php

namespace App\Core;

class DB
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = 'localhost';
        $user = 'root';
        $pass = ''; // La clave es vacía según la configuración del usuario
        $name = 'veterinaria_db';

        // Intentar conectar al puerto 3307 primero, luego al 3306
        $this->connection = @mysqli_connect($host, $user, $pass, $name, 3306);

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
