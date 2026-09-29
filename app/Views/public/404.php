<div class="container py-5 text-center my-5">
    <div class="display-1 fw-bold text-muted mb-2">404</div>
    <h2 class="fw-bold mb-3"><?= e($title ?? 'Page Not Found') ?></h2>
    <p class="lead text-secondary mb-4 mx-auto" style="max-width: 500px;">
        <?= e($message ?? 'The page or beauty service you requested could not be located on Deluxe Salon & Spa.') ?>
    </p>
    <div class="d-flex justify-content-center gap-3">
        <a href="/" class="btn btn-deluxe">
            <i class="bi bi-house-door me-1"></i> Return Home
        </a>
        <a href="/services" class="btn btn-outline-dark">
            <i class="bi bi-grid me-1"></i> View Services Menu
        </a>
    </div>
</div>
