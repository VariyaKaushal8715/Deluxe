<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\GalleryItem;

class GalleryController extends Controller
{
    public function index(): void
    {
        $category = $_GET['category'] ?? 'all';
        $items = GalleryItem::getAllByCategory($category);

        $this->render('public/gallery', [
            'title' => 'Lookbook & Style Transformations — Deluxe Salon',
            'items' => $items,
            'activeCategory' => $category,
        ]);
    }
}
