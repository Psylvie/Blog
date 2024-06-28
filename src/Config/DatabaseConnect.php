<?php

namespace App\Config;

use PDO;
use PDOException;

require_once 'MySql.php';

class DatabaseConnect
{
    public static function connect(): PDO
    {
        try {
            $mysqlClient = new PDO(
                sprintf('mysql:host=%s;dbname=%s;port=%s;charset=utf8mb4',
                    MYSQL_HOST,
                    MYSQL_DBNAME,
                    MYSQL_PORT),
                MYSQL_USER,
                MYSQL_PASSWORD
            );
            $mysqlClient->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $mysqlClient;

        } catch (PDOException $e) {
            error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
            throw $e;
        }
    }
}
