<?php
declare(strict_types=1);

// Automated Functional Test Suite for Session 2
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Session.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/View.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Category.php';
require_once __DIR__ . '/../app/Models/Service.php';
require_once __DIR__ . '/../app/Models/Staff.php';
require_once __DIR__ . '/../app/Models/Review.php';

use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Review;
use Core\Auth;

echo "======================================================" . PHP_EOL;
echo "  DELUXE SALON — SESSION 2 FUNCTIONAL TEST SUITE     " . PHP_EOL;
echo "======================================================" . PHP_EOL;

$passed = 0;
$failed = 0;

function assertTest(string $description, bool $condition): void {
    global $passed, $failed;
    if ($condition) {
        echo " [PASS] " . $description . PHP_EOL;
        $passed++;
    } else {
        echo " [FAIL] " . $description . PHP_EOL;
        $failed++;
    }
}

// 1. Database Model Tests
echo PHP_EOL . "--- 1. DATABASE & MODEL TESTS ---" . PHP_EOL;
$categories = Category::getAllActive();
assertTest("Active categories retrieved (expected 5)", count($categories) === 5);

$hairCare = Category::findBySlug('hair-care');
assertTest("Category findBySlug('hair-care') returns valid category", $hairCare !== null && $hairCare['name'] === 'Hair Care & Styling');

$allServices = Service::getAllActive();
assertTest("All active services retrieved (expected >= 15)", count($allServices) >= 15);

$hairServices = Service::getAllActive(['category' => 'hair-care']);
assertTest("Filter by category 'hair-care' returns matching services", count($hairServices) > 0 && $hairServices[0]['category_slug'] === 'hair-care');

$searchHydra = Service::getAllActive(['q' => 'Hydra']);
assertTest("Search query 'Hydra' returns Hydra facial", count($searchHydra) >= 1 && str_contains(strtolower($searchHydra[0]['name']), 'hydra'));

$budgetServices = Service::getAllActive(['max_price' => 1500]);
$allUnder1500 = true;
foreach ($budgetServices as $s) {
    if ((float)$s['price'] > 1500) { $allUnder1500 = false; break; }
}
assertTest("Filter max_price <= 1500 returns only items <= 1500", count($budgetServices) > 0 && $allUnder1500);

$sortedAsc = Service::getAllActive([], 'price_asc');
assertTest("Sorting price_asc: first service price <= last service price", (float)$sortedAsc[0]['price'] <= (float)end($sortedAsc)['price']);

$hydraService = Service::findBySlug('hydra-cleanse-glow-facial');
assertTest("Service findBySlug returns valid service with treatment details", $hydraService !== null && !empty($hydraService['treatment_details']));

$related = Service::getRelated((int)$hydraService['category_id'], (int)$hydraService['id'], 3);
assertTest("Related services returned within same category", count($related) > 0);

$staffMembers = Staff::getAllActive();
assertTest("Active staff members retrieved (expected 4)", count($staffMembers) === 4);

$testimonials = Review::getPublishedTestimonials();
assertTest("Published testimonials retrieved (expected > 0)", count($testimonials) > 0);

// 2. Authentication & User Profile Tests
echo PHP_EOL . "--- 2. AUTHENTICATION & SECURITY TESTS ---" . PHP_EOL;
$customerUser = User::findByEmail('sneha.kapoor@gmail.com');
assertTest("Customer account found by email", $customerUser !== null);
assertTest("Customer password verifies correctly (Customer@123)", $customerUser !== null && Auth::verifyPassword('Customer@123', $customerUser['password_hash']));
assertTest("Incorrect password is properly rejected", $customerUser !== null && !Auth::verifyPassword('WrongPass!123', $customerUser['password_hash']));

$adminUser = User::findByEmail('admin@deluxesalon.com');
assertTest("Admin account found with role 'admin'", $adminUser !== null && $adminUser['role'] === 'admin');
assertTest("Admin password verifies correctly (Admin@123)", $adminUser !== null && Auth::verifyPassword('Admin@123', $adminUser['password_hash']));

assertTest("Duplicate email detection works", User::emailExists('sneha.kapoor@gmail.com') === true);
assertTest("Unregistered email returns false", User::emailExists('non.existent.random@example.com') === false);

// Test dynamic user registration
$testEmail = 'test.client.' . time() . '@deluxesalon.test';
$newUserId = User::create([
    'name' => 'Kavya Verma',
    'email' => $testEmail,
    'phone' => '+91 99887 76655',
    'password_hash' => Auth::hashPassword('ClientSecret@99'),
    'role' => 'customer',
    'gender' => 'female',
    'address_line' => 'Villa 14, Palm Grove',
    'area_locality' => 'Bandra',
    'city' => 'Mumbai',
    'pincode' => '400050',
]);
assertTest("New client registration creates DB row", $newUserId > 0);

$createdUser = User::findById($newUserId);
assertTest("Created user has full address fields populated", 
    $createdUser !== null && 
    $createdUser['name'] === 'Kavya Verma' && 
    $createdUser['address_line'] === 'Villa 14, Palm Grove' && 
    $createdUser['city'] === 'Mumbai'
);

User::updateProfile($newUserId, [
    'name' => 'Kavya Verma-Mehta',
    'phone' => '+91 99887 76655',
    'gender' => 'female',
    'address_line' => 'Flat 502, Sky View Towers',
    'area_locality' => 'Khar West',
    'city' => 'Mumbai',
    'state' => 'Maharashtra',
    'pincode' => '400052',
    'landmark' => 'Opp Khar Station',
]);

$updatedUser = User::findById($newUserId);
assertTest("Customer profile and address updated successfully in DB", 
    $updatedUser !== null && 
    $updatedUser['name'] === 'Kavya Verma-Mehta' && 
    $updatedUser['pincode'] === '400052' && 
    $updatedUser['landmark'] === 'Opp Khar Station'
);

// 3. HTTP Server Endpoints Verification
echo PHP_EOL . "--- 3. HTTP ENDPOINTS VERIFICATION ---" . PHP_EOL;

function testUrl(string $url): array {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);
    return ['code' => $httpCode, 'redirect' => $redirectUrl, 'body' => $response ?: ''];
}

$httpHome = testUrl('http://127.0.0.1:8000/');
assertTest("GET / returns 200 and brand title", $httpHome['code'] === 200 && str_contains($httpHome['body'], 'DELUXE'));

$httpServices = testUrl('http://127.0.0.1:8000/services');
assertTest("GET /services returns 200 and services menu", $httpServices['code'] === 200 && str_contains($httpServices['body'], 'Services Menu'));

$httpDetail = testUrl('http://127.0.0.1:8000/services/hydra-cleanse-glow-facial');
assertTest("GET /services/hydra-cleanse-glow-facial returns 200", $httpDetail['code'] === 200 && str_contains($httpDetail['body'], 'Hydra Cleanse Medical Glow Facial'));

$http404 = testUrl('http://127.0.0.1:8000/services/non-existent-service-12345');
assertTest("GET invalid service returns 404 status", $http404['code'] === 404 && str_contains($http404['body'], '404'));

$httpLogin = testUrl('http://127.0.0.1:8000/login');
assertTest("GET /login returns 200 and demo quick-fill buttons", $httpLogin['code'] === 200 && str_contains($httpLogin['body'], 'demo-fill-btn'));

$httpRegister = testUrl('http://127.0.0.1:8000/register');
assertTest("GET /register returns 200 and address foundation inputs", $httpRegister['code'] === 200 && str_contains($httpRegister['body'], 'address_line'));

$httpProtected = testUrl('http://127.0.0.1:8000/customer/profile');
assertTest("GET /customer/profile without session redirects (302) to /login", $httpProtected['code'] === 302 && str_contains($httpProtected['redirect'], '/login'));

$httpGallery = testUrl('http://127.0.0.1:8000/gallery');
assertTest("GET /gallery returns 200 and lookbook items", $httpGallery['code'] === 200 && str_contains($httpGallery['body'], 'Lookbook'));

$httpReviews = testUrl('http://127.0.0.1:8000/reviews');
assertTest("GET /reviews returns 200 and verified reviews", $httpReviews['code'] === 200 && str_contains($httpReviews['body'], 'Verified Client Love') || str_contains($httpReviews['body'], 'Client Reviews'));

$httpBook = testUrl('http://127.0.0.1:8000/book');
assertTest("GET /book returns 200 and links to service catalog", $httpBook['code'] === 200 && str_contains($httpBook['body'], 'Appointment Reservation Engine'));

echo PHP_EOL . "======================================================" . PHP_EOL;
echo "  TEST SUMMARY: {$passed} PASSED, {$failed} FAILED" . PHP_EOL;
echo "======================================================" . PHP_EOL;

if ($failed > 0) {
    exit(1);
}
