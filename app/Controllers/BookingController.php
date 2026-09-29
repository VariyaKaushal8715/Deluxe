<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use App\Models\Service;
use App\Models\Category;

class BookingController extends Controller
{
    public function index(): void
    {
        $serviceId = (int)($_GET['service'] ?? 0);
        $selectedService = null;
        if ($serviceId > 0) {
            $selectedService = Service::findById($serviceId);
        }

        $categories = Category::getAllActive();
        $allServices = Service::getAllActive();

        $this->render('public/book_preview', [
            'title' => 'Book Appointment — Deluxe Salon',
            'selectedService' => $selectedService,
            'categories' => $categories,
            'allServices' => $allServices,
            'currentUser' => Auth::user(),
        ]);
    }
}
