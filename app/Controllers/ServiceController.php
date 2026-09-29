<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\Review;

class ServiceController extends Controller
{
    public function index(): void
    {
        $query = $this->getQuery();

        $filters = [
            'q' => $query['q'] ?? '',
            'category' => $query['category'] ?? '',
            'gender' => $query['gender'] ?? '',
            'home_service' => $query['home_service'] ?? '',
            'popular' => $query['popular'] ?? '',
            'min_price' => $query['min_price'] ?? '',
            'max_price' => $query['max_price'] ?? '',
            'max_duration' => $query['max_duration'] ?? '',
        ];

        $sortBy = $query['sort'] ?? 'popular';

        $services = Service::getAllActive($filters, $sortBy);
        $categories = Category::getAllActive();

        // Selected category details if filtered
        $activeCategory = null;
        if (!empty($filters['category'])) {
            $activeCategory = Category::findBySlug($filters['category']);
        }

        $this->render('public/services', [
            'title' => $activeCategory ? "{$activeCategory['name']} — Deluxe Salon Services" : 'All Salon & Wellness Services — Deluxe Salon',
            'services' => $services,
            'categories' => $categories,
            'filters' => $filters,
            'sortBy' => $sortBy,
            'activeCategory' => $activeCategory,
            'totalCount' => count($services),
        ]);
    }

    public function show(string $slug): void
    {
        $service = Service::findBySlug($slug);

        if (!$service) {
            http_response_code(404);
            $this->render('public/404', [
                'title' => 'Service Not Found',
                'message' => 'The beauty or wellness treatment you requested could not be found or is currently inactive.'
            ]);
            return;
        }

        $relatedServices = Service::getRelated((int)$service['category_id'], (int)$service['id'], 3);
        $reviews = Review::getByService((int)$service['id'], 4);

        $this->render('public/service_detail', [
            'title' => "{$service['name']} — Deluxe Salon & Spa",
            'service' => $service,
            'relatedServices' => $relatedServices,
            'reviews' => $reviews,
        ]);
    }
}
