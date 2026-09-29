<?php
declare(strict_types=1);

namespace Core;

use PDO;

abstract class Model
{
    protected static function db(): PDO
    {
        return Database::getConnection();
    }

    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public static function lastInsertId(): int
    {
        return (int)self::db()->lastInsertId();
    }

    public static function beginTransaction(): void
    {
        if (!self::db()->inTransaction()) {
            self::db()->beginTransaction();
        }
    }

    public static function commit(): void
    {
        if (self::db()->inTransaction()) {
            self::db()->commit();
        }
    }

    public static function rollBack(): void
    {
        if (self::db()->inTransaction()) {
            self::db()->rollBack();
        }
    }
}
