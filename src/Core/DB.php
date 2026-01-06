<?php

namespace App\Core;

class DB
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        // Usar constantes definidas en config.php
        $this->connection = @mysqli_connect(\DB_HOST, \DB_USER, \DB_PASS, \DB_NAME, 3306);

        if (!$this->connection) {
            throw new \Exception("Error de conexión a la base de datos: " . mysqli_connect_error());
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
