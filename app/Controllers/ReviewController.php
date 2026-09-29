<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(): void
    {
        $reviews = Review::getPublishedTestimonials(20);

        $this->render('public/reviews', [
            'title' => 'Client Reviews & Ratings — Deluxe Salon',
            'reviews' => $reviews,
        ]);
    }
}
