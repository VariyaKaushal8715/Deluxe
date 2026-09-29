<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Staff extends Model
{
    public static function getAllActive(): array
    {
        return self::query(
            "SELECT s.*, GROUP_CONCAT(c.name SEPARATOR ', ') as specialization_names
             FROM staff s
             LEFT JOIN staff_specializations sp ON sp.staff_id = s.id
             LEFT JOIN service_categories c ON c.id = sp.category_id
             WHERE s.is_active = 1
             GROUP BY s.id
             ORDER BY s.id ASC"
        );
    }

    public static function findById(int $id): ?array
    {
        return self::queryOne(
            "SELECT * FROM staff WHERE id = :id AND is_active = 1 LIMIT 1",
            ['id' => $id]
        );
    }

    public static function getByCategory(int $categoryId): array
    {
        return self::query(
            "SELECT s.* 
             FROM staff s
             JOIN staff_specializations sp ON sp.staff_id = s.id
             WHERE sp.category_id = :cat_id AND s.is_active = 1
             ORDER BY s.full_name ASC",
            ['cat_id' => $categoryId]
        );
    }
}
