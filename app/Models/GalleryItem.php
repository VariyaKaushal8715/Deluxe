<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class GalleryItem extends Model
{
    public static function getActive(int $limit = 6): array
    {
        return self::query(
            "SELECT * FROM gallery_items 
             WHERE is_active = 1 
             ORDER BY display_order ASC, id DESC
             LIMIT {$limit}"
        );
    }

    public static function getAllByCategory(?string $category = null): array
    {
        $sql = "SELECT * FROM gallery_items WHERE is_active = 1";
        $params = [];
        if ($category && $category !== 'all') {
            $sql .= " AND category = :category";
            $params['category'] = $category;
        }
        $sql .= " ORDER BY display_order ASC, id DESC";
        return self::query($sql, $params);
    }
}
