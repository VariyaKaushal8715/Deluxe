<?php
/**
 * @var string $title
 * @var string $content
 * @var array $app
 */
use Core\Auth;
$currentUser = Auth::user();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Customer Account') ?> | <?= e($app['name']) ?></title>

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Deluxe Salon CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navigation Bar -->
    <?php \Core\View::partial('navbar'); ?>

    <!-- Flash Alerts -->
    <?php \Core\View::partial('flash'); ?>

    <!-- Customer Dashboard Header -->
    <div class="py-4 bg-white border-bottom mb-4">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-light border border-2 border-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 56px; height: 56px; font-size: 1.4rem;">
                        <?= strtoupper(substr($currentUser['name'] ?? 'C', 0, 1)) ?>
                    </div>
                    <div>
                        <h1 class="h4 mb-0"><?= e($currentUser['name']) ?></h1>
                        <span class="text-muted small"><i class="bi bi-shield-check text-success me-1"></i> Verified Client &bull; <?= e($currentUser['email']) ?></span>
                    </div>
                </div>

                <div>
                    <a href="/book" class="btn btn-deluxe">
                        <i class="bi bi-plus-circle me-1"></i> New Appointment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container with Sidebar -->
    <main class="flex-grow-1">
        <div class="container pb-5">
            <div class="row g-4">
                <!-- Sidebar Navigation -->
                <div class="col-lg-3 col-md-4">
                    <div class="customer-sidebar shadow-sm">
                        <div class="text-uppercase small fw-bold text-muted mb-2 px-2">Account Management</div>
                        <a href="/customer/profile" class="customer-nav-item <?= $currentUri === '/customer/profile' ? 'active' : '' ?>">
                            <i class="bi bi-person-gear"></i>
                            <span>Profile & Address</span>
                        </a>
                        <a href="/customer/appointments" class="customer-nav-item <?= $currentUri === '/customer/appointments' ? 'active' : '' ?>">
                            <i class="bi bi-calendar2-week"></i>
                            <span>My Appointments</span>
                        </a>
                        <a href="/customer/invoices" class="customer-nav-item <?= $currentUri === '/customer/invoices' ? 'active' : '' ?>">
                            <i class="bi bi-receipt"></i>
                            <span>Billing & Receipts</span>
                        </a>

                        <hr class="my-3 text-muted">

                        <div class="text-uppercase small fw-bold text-muted mb-2 px-2">Quick Navigation</div>
                        <a href="/services" class="customer-nav-item">
                            <i class="bi bi-grid"></i>
                            <span>Explore Catalog</span>
                        </a>
                        <a href="/logout" class="customer-nav-item text-danger">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Sign Out</span>
                        </a>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="col-lg-9 col-md-8">
                    <?= $content ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php \Core\View::partial('footer'); ?>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
