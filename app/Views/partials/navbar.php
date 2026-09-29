<?php
use Core\Auth;
$isLoggedIn = Auth::check();
$currentUser = Auth::user();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<nav class="navbar navbar-expand-lg deluxe-navbar sticky-top">
    <div class="container">
        <a class="deluxe-brand" href="/">
            <i class="bi bi-gem text-warning"></i> YOUR <span>SALON</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#deluxeNav" aria-controls="deluxeNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="deluxeNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $currentUri === '/' ? 'active' : '' ?>" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentUri, '/services') ? 'active' : '' ?>" href="/services">Services & Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentUri === '/gallery' ? 'active' : '' ?>" href="/gallery">Lookbook</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentUri === '/reviews' ? 'active' : '' ?>" href="/reviews">Reviews</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <!-- Book Appointment Primary CTA -->
                <a href="/book" class="btn btn-deluxe">
                    <i class="bi bi-calendar2-check"></i> Book Appointment
                </a>

                <?php if ($isLoggedIn): ?>
                    <div class="dropdown">
                        <button class="btn btn-deluxe-outline dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span><?= e($currentUser['name'] ?? 'Account') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><h6 class="dropdown-header text-uppercase small text-muted">Client Portal</h6></li>
                            <li><a class="dropdown-item" href="/customer/profile"><i class="bi bi-person me-2"></i> My Profile & Address</a></li>
                            <li><a class="dropdown-item" href="/customer/appointments"><i class="bi bi-calendar-event me-2"></i> My Appointments</a></li>
                            <li><a class="dropdown-item" href="/customer/invoices"><i class="bi bi-receipt me-2"></i> Invoices & Receipts</a></li>
                            <?php if (Auth::isAdmin()): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-warning" href="/admin/dashboard"><i class="bi bi-speedometer2 me-2"></i> Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn btn-deluxe-outline">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
