<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\Staff;
use App\Models\Review;
use App\Models\GalleryItem;

class HomeController extends Controller
{
    public function index(): void
    {
        $featuredServices = Service::getFeatured(6);
        $categories = Category::getAllActive();
        $staffMembers = Staff::getAllActive();
        $testimonials = Review::getPublishedTestimonials(4);
        $galleryPreview = GalleryItem::getActive(4);

        $this->render('public/home', [
            'title' => 'Luxury Beauty & Wellness Salon in Mumbai',
            'featuredServices' => $featuredServices,
            'categories' => $categories,
            'staffMembers' => $staffMembers,
            'testimonials' => $testimonials,
            'galleryPreview' => $galleryPreview,
        ]);
    }
}
