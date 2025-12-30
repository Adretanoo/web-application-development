<?php
declare(strict_types=1);

namespace Acer\Pr7\config;

use PDO;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $db   = 'task_manager';
            $user = 'postgres';
            $pass = '2007';
            $port = 5432;

            $dsn = "pgsql:host=$host;port=$port;dbname=$db";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            self::$instance = new PDO($dsn, $user, $pass, $options);
        }

        return self::$instance;
    }
}