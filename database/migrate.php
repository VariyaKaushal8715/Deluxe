<?php
declare(strict_types=1);

echo "=== DELUXE SALON DATABASE MIGRATOR & SEEDER ===" . PHP_EOL;

try {
    $dbConfig = require __DIR__ . '/../config/database.php';
    
    // Connect to server (without db first to ensure db exists)
    $serverDsn = sprintf('mysql:host=%s;port=%d;charset=%s', $dbConfig['host'], $dbConfig['port'], $dbConfig['charset']);
    $pdo = new PDO($serverDsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    
    echo "1. Creating database `{$dbConfig['dbname']}` if not exists..." . PHP_EOL;
    $pdo->exec(sprintf(
        'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE utf8mb4_unicode_ci',
        $dbConfig['dbname'],
        $dbConfig['charset']
    ));
    $pdo->exec("USE `{$dbConfig['dbname']}`");

    echo "2. Applying schema (database/schema.sql)..." . PHP_EOL;
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schemaSql);
    echo "   -> Schema applied successfully." . PHP_EOL;

    echo "3. Applying seed data (database/seeds.sql)..." . PHP_EOL;
    $seedsSql = file_get_contents(__DIR__ . '/seeds.sql');
    
    // Parse statements by semicolon outside of strings
    $lines = explode("\n", $seedsSql);
    $query = '';
    $statementIndex = 0;
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '--')) {
            continue;
        }
        $query .= $line . "\n";
        if (str_ends_with($trimmed, ';')) {
            $statementIndex++;
            try {
                $pdo->exec($query);
            } catch (Throwable $e) {
                echo "ERROR on statement #{$statementIndex}:" . PHP_EOL;
                echo substr($query, 0, 200) . "..." . PHP_EOL;
                throw $e;
            }
            $query = '';
        }
    }
    echo "   -> Seed data applied successfully." . PHP_EOL;

    // Verify row counts
    $counts = [
        'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'service_categories' => $pdo->query("SELECT COUNT(*) FROM service_categories")->fetchColumn(),
        'services' => $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn(),
        'staff' => $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn(),
        'reviews' => $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn(),
        'inventory_items' => $pdo->query("SELECT COUNT(*) FROM inventory_items")->fetchColumn(),
    ];

    echo PHP_EOL . "=== VERIFICATION SUMMARY ===" . PHP_EOL;
    foreach ($counts as $table => $count) {
        echo sprintf(" - %-20s: %d records" . PHP_EOL, $table, $count);
    }
    echo "SUCCESS: Database migrated and seeded." . PHP_EOL;

} catch (Throwable $e) {
    echo "ERROR during migration: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
