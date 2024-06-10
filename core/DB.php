<?php

namespace Core;

use PDO;

class DB
{
    static protected PDO|null $instance = null;
    static public function connect(): PDO
    {
        if (is_null(self::$instance)) {
            $dsn = "mysql:host=database;port=3306;dbname=database";
            $options = [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];

            self::$instance = new PDO($dsn, "root", "password", $options);
        }

        return static::$instance;
    }
}