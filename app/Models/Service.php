<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Service extends Model
{
    public static function getAllActive(array $filters = [], string $sortBy = 'popular'): array
    {
        $sql = "SELECT s.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
                FROM services s
                JOIN service_categories c ON c.id = s.category_id AND c.is_active = 1
                WHERE s.is_active = 1";
        $params = [];

        // Search Keyword
        if (!empty($filters['q'])) {
            $sql .= " AND (s.name LIKE :search1 OR s.description LIKE :search2 OR s.treatment_details LIKE :search3 OR c.name LIKE :search4)";
            $searchTerm = '%' . trim($filters['q']) . '%';
            $params['search1'] = $searchTerm;
            $params['search2'] = $searchTerm;
            $params['search3'] = $searchTerm;
            $params['search4'] = $searchTerm;
        }

        // Category filter (slug or id)
        if (!empty($filters['category'])) {
            if (is_numeric($filters['category'])) {
                $sql .= " AND s.category_id = :cat_id";
                $params['cat_id'] = (int)$filters['category'];
            } else {
                $sql .= " AND c.slug = :cat_slug";
                $params['cat_slug'] = trim($filters['category']);
            }
        }

        // Gender filter
        if (!empty($filters['gender']) && in_array($filters['gender'], ['female', 'male', 'all'], true)) {
            if ($filters['gender'] === 'female') {
                $sql .= " AND s.gender_target IN ('female', 'all')";
            } elseif ($filters['gender'] === 'male') {
                $sql .= " AND s.gender_target IN ('male', 'all')";
            }
        }

        // Home service filter
        if (isset($filters['home_service']) && $filters['home_service'] !== '') {
            $sql .= " AND s.is_home_service = :is_home";
            $params['is_home'] = (int)$filters['home_service'];
        }

        // Popular filter
        if (isset($filters['popular']) && (int)$filters['popular'] === 1) {
            $sql .= " AND s.is_popular = 1";
        }

        // Price range
        if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
            $sql .= " AND s.price >= :min_price";
            $params['min_price'] = (float)$filters['min_price'];
        }
        if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
            $sql .= " AND s.price <= :max_price";
            $params['max_price'] = (float)$filters['max_price'];
        }

        // Duration filter
        if (!empty($filters['max_duration']) && is_numeric($filters['max_duration'])) {
            $sql .= " AND s.duration_minutes <= :max_duration";
            $params['max_duration'] = (int)$filters['max_duration'];
        }

        // Sorting
        switch ($sortBy) {
            case 'price_asc':
                $sql .= " ORDER BY s.price ASC, s.name ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY s.price DESC, s.name ASC";
                break;
            case 'duration_asc':
                $sql .= " ORDER BY s.duration_minutes ASC, s.price ASC";
                break;
            case 'newest':
                $sql .= " ORDER BY s.id DESC";
                break;
            case 'popular':
            default:
                $sql .= " ORDER BY s.is_popular DESC, s.id ASC";
                break;
        }

        return self::query($sql, $params);
    }

    public static function getFeatured(int $limit = 6): array
    {
        return self::query(
            "SELECT s.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
             FROM services s
             JOIN service_categories c ON c.id = s.category_id AND c.is_active = 1
             WHERE s.is_active = 1 AND s.is_popular = 1
             ORDER BY s.id ASC
             LIMIT {$limit}"
        );
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::queryOne(
            "SELECT s.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
             FROM services s
             JOIN service_categories c ON c.id = s.category_id
             WHERE s.slug = :slug AND s.is_active = 1
             LIMIT 1",
            ['slug' => $slug]
        );
    }

    public static function findById(int $id): ?array
    {
        return self::queryOne(
            "SELECT s.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
             FROM services s
             JOIN service_categories c ON c.id = s.category_id
             WHERE s.id = :id AND s.is_active = 1
             LIMIT 1",
            ['id' => $id]
        );
    }

    public static function getRelated(int $categoryId, int $currentServiceId, int $limit = 3): array
    {
        return self::query(
            "SELECT s.*, c.name as category_name, c.slug as category_slug
             FROM services s
             JOIN service_categories c ON c.id = s.category_id
             WHERE s.category_id = :cat_id AND s.id != :cur_id AND s.is_active = 1
             ORDER BY s.is_popular DESC, s.price ASC
             LIMIT {$limit}",
            ['cat_id' => $categoryId, 'cur_id' => $currentServiceId]
        );
    }
}
