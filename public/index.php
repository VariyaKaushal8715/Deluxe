<?php
declare(strict_types=1);

// Deluxe Salon & Spa — Front Controller

// Autoloader for Core and App namespaces
spl_autoload_register(function (string $class) {
    // Project root directory
    $baseDir = __DIR__ . '/../';

    // Normalize namespace to file path
    $classPath = str_replace('\\', '/', $class);

    // Map Core\ to core/ and App\ to app/
    if (str_starts_with($classPath, 'Core/')) {
        $file = $baseDir . 'core/' . substr($classPath, 5) . '.php';
    } elseif (str_starts_with($classPath, 'App/')) {
        $file = $baseDir . 'app/' . substr($classPath, 4) . '.php';
    } else {
        $file = $baseDir . $classPath . '.php';
    }

    if (file_exists($file)) {
        require_once $file;
    }
});

// Require global view helpers
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/View.php';

use Core\Session;
use Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ServiceController;
use App\Controllers\AuthController;
use App\Controllers\CustomerController;
use App\Controllers\BookingController;
use App\Controllers\GalleryController;
use App\Controllers\ReviewController;
use App\Controllers\AdminController;

// Start secure session
Session::start();

// Initialize Router
$router = new Router();

// Public Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/services', [ServiceController::class, 'index']);
$router->get('/services/{slug}', [ServiceController::class, 'show']);
$router->get('/gallery', [GalleryController::class, 'index']);
$router->get('/reviews', [ReviewController::class, 'index']);

// Booking Engine Routes
$router->get('/book', [BookingController::class, 'index']);
$router->get('/api/booking/staff', [BookingController::class, 'getStaffForServices']);
$router->get('/api/booking/slots', [BookingController::class, 'getAvailableSlots']);
$router->post('/book/submit', [BookingController::class, 'submitBooking']);

// Authentication Routes
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'handleLogin']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'handleRegister']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/auth/google', [AuthController::class, 'googleAuthSandbox']);
$router->get('/auth/phone', [AuthController::class, 'phoneOtpSandbox']);

// Customer Portal Protected Routes
$router->get('/customer/profile', [CustomerController::class, 'profile']);
$router->post('/customer/profile', [CustomerController::class, 'updateProfile']);
$router->post('/customer/password', [CustomerController::class, 'updatePassword']);
$router->get('/customer/appointments', [CustomerController::class, 'appointments']);
$router->post('/customer/appointments/cancel', [CustomerController::class, 'cancelAppointment']);
$router->post('/customer/appointments/reschedule', [CustomerController::class, 'rescheduleAppointment']);
$router->get('/customer/invoices', [CustomerController::class, 'invoices']);

// Admin Calendar & Scheduling Routes
$router->get('/admin/calendar', [AdminController::class, 'calendar']);
$router->post('/admin/walkin', [AdminController::class, 'walkInBooking']);
$router->post('/admin/time-block', [AdminController::class, 'createTimeBlock']);

// Dispatch Request
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);
