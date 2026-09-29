<?php
/**
 * @var array $reviews
 */
?>

<div class="py-4 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Client Reviews</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Client Reviews & Testimonials</h1>
                <p class="text-muted small mb-0">Genuine feedback submitted by verified clients after completed salon services</p>
            </div>
            <div class="d-flex align-items-center gap-2 bg-light p-2 px-3 rounded-pill border">
                <span class="fs-4 fw-bold text-dark">4.9</span>
                <div class="text-warning">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                </div>
                <span class="small text-muted border-start ps-2">420+ Salon Visits</span>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <?php foreach ($reviews as $rev): ?>
            <div class="col-lg-6">
                <div class="card h-100 border p-4 shadow-sm" style="border-radius: var(--radius-md);">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-light border text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                <?= strtoupper(substr($rev['customer_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0"><?= e($rev['customer_name']) ?></h6>
                                <span class="text-muted" style="font-size: 0.72rem;"><?= e($rev['customer_city'] ?? 'Mumbai') ?></span>
                            </div>
                        </div>

                        <div class="text-warning">
                            <?php for ($i = 0; $i < (int)$rev['rating']; $i++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <p class="text-secondary small my-3 flex-grow-1">“<?= e($rev['review_text']) ?>”</p>

                    <div class="pt-3 border-top d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> <?= e($rev['service_name']) ?>
                        </span>
                        <span class="text-muted">Stylist: <?= e($rev['staff_name']) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
