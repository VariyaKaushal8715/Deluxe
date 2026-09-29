<?php
declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/database.php';
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                // Check if database needs creation
                if ($e->getCode() === 1049) {
                    self::createDatabaseIfNotExists($config);
                    self::$instance = new PDO(
                        $dsn,
                        $config['username'],
                        $config['password'],
                        $config['options']
                    );
                } else {
                    error_log('Database connection error: ' . $e->getMessage());
                    throw new RuntimeException('Database service temporarily unavailable.');
                }
            }
        }

        return self::$instance;
    }

    private static function createDatabaseIfNotExists(array $config): void
    {
        $serverDsn = sprintf('mysql:host=%s;port=%d;charset=%s', $config['host'], $config['port'], $config['charset']);
        $pdo = new PDO($serverDsn, $config['username'], $config['password'], $config['options']);
        $pdo->exec(sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE utf8mb4_unicode_ci',
            $config['dbname'],
            $config['charset']
        ));
    }
}
