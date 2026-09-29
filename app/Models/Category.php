<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Category extends Model
{
    public static function getAllActive(): array
    {
        return self::query(
            "SELECT c.*, COUNT(s.id) as service_count 
             FROM service_categories c
             LEFT JOIN services s ON s.category_id = c.id AND s.is_active = 1
             WHERE c.is_active = 1 
             GROUP BY c.id
             ORDER BY c.display_order ASC, c.name ASC"
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::queryOne(
            "SELECT * FROM service_categories WHERE slug = :slug AND is_active = 1 LIMIT 1",
            ['slug' => $slug]
        );
    }

    public static function findById(int $id): ?array
    {
        return self::queryOne(
            "SELECT * FROM service_categories WHERE id = :id AND is_active = 1 LIMIT 1",
            ['id' => $id]
        );
    }
}
