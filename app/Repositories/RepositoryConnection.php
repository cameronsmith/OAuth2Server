<?php namespace App\Repositories;

use PDO;

class RepositoryConnection extends PDO
{
    public static function getConnectionInstance(array $config) {
        $pdo = new static(
            getenv('DB_ADAPTER') . ':' . $config['file']
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}