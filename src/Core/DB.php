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
        $pass = '';
        $name = 'veterinaria_db';

        $this->connection = mysqli_connect($host, $user, $pass, $name, 3306);

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
