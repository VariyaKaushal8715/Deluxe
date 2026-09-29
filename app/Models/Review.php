<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Review extends Model
{
    public static function getPublishedTestimonials(int $limit = 4): array
    {
        return self::query(
            "SELECT r.*, u.name as customer_name, u.city as customer_city, s.name as service_name, st.full_name as staff_name
             FROM reviews r
             JOIN users u ON u.id = r.customer_id
             JOIN services s ON s.id = r.service_id
             JOIN staff st ON st.id = r.staff_id
             WHERE r.moderation_status = 'published'
             ORDER BY r.created_at DESC
             LIMIT {$limit}"
        );
    }

    public static function getByService(int $serviceId, int $limit = 5): array
    {
        return self::query(
            "SELECT r.*, u.name as customer_name, st.full_name as staff_name
             FROM reviews r
             JOIN users u ON u.id = r.customer_id
             JOIN staff st ON st.id = r.staff_id
             WHERE r.service_id = :service_id AND r.moderation_status = 'published'
             ORDER BY r.created_at DESC
             LIMIT {$limit}",
            ['service_id' => $serviceId]
        );
    }
}
