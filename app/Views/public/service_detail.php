<?php
/**
 * @var array $service
 * @var array $relatedServices
 * @var array $reviews
 */
?>

<div class="py-4 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/services">Services Menu</a></li>
                <li class="breadcrumb-item"><a href="/services?category=<?= e($service['category_slug']) ?>"><?= e($service['category_name']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= e($service['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Service Main Content -->
        <div class="col-lg-8">
            <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm mb-4">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase">
                        <?= e($service['category_name']) ?>
                    </span>
                    <?php if (!empty($service['is_popular'])): ?>
                        <span class="badge bg-danger px-3 py-2 fw-bold"><i class="bi bi-fire me-1"></i> Highly Popular</span>
                    <?php endif; ?>
                    <?php if (!empty($service['is_home_service'])): ?>
                        <span class="badge bg-dark px-3 py-2"><i class="bi bi-house-door me-1"></i> Home Service Eligible</span>
                    <?php endif; ?>
                </div>

                <h1 class="display-6 fw-bold mb-3"><?= e($service['name']) ?></h1>
                
                <div class="d-flex flex-wrap align-items-center gap-4 py-3 border-top border-bottom my-4">
                    <div>
                        <div class="small text-muted text-uppercase">Price</div>
                        <div class="fs-2 fw-bold text-dark"><?= currency($service['price']) ?></div>
                    </div>
                    <div class="border-start ps-4">
                        <div class="small text-muted text-uppercase">Duration</div>
                        <div class="fs-4 fw-semibold text-secondary"><i class="bi bi-clock me-1 text-warning"></i> <?= duration_format((int)$service['duration_minutes']) ?></div>
                    </div>
                    <div class="border-start ps-4">
                        <div class="small text-muted text-uppercase">Gender Suitability</div>
                        <div class="fs-5 fw-semibold text-capitalize text-secondary">
                            <i class="bi bi-person me-1 text-warning"></i> <?= e($service['gender_target']) === 'all' ? 'Unisex (All)' : e($service['gender_target']) ?>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="h5 fw-bold mb-3">About This Treatment</h4>
                    <p class="text-secondary lead fs-6" style="line-height: 1.8;">
                        <?= nl2br(e($service['description'])) ?>
                    </p>
                </div>

                <?php if (!empty($service['treatment_details'])): ?>
                    <div class="p-4 rounded-3 mb-4" style="background: var(--color-gold-light); border: 1px solid var(--color-gold-border);">
                        <h5 class="fw-bold mb-2 text-dark"><i class="bi bi-check2-all text-warning me-1"></i> Included Treatment Protocol</h5>
                        <p class="mb-0 text-secondary small">
                            <?= e($service['treatment_details']) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-3 pt-3 border-top">
                    <a href="/book?service=<?= (int)$service['id'] ?>" class="btn btn-deluxe btn-lg px-5">
                        <i class="bi bi-calendar-check me-1"></i> Book This Service
                    </a>
                    <a href="/services" class="btn btn-outline-dark btn-lg px-4">
                        Explore Other Services
                    </a>
                </div>
            </div>

            <!-- Client Reviews for this Service -->
            <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="h5 fw-bold mb-0">Verified Client Experiences (<?= count($reviews) ?>)</h4>
                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i> 100% Verified Bookings
                    </span>
                </div>

                <?php if (empty($reviews)): ?>
                    <p class="text-muted mb-0 small">No verified reviews submitted for this specific treatment yet.</p>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($reviews as $rev): ?>
                            <div class="col-12 border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="fw-bold small"><?= e($rev['customer_name']) ?></div>
                                    <div class="text-warning small">
                                        <?php for ($i = 0; $i < (int)$rev['rating']; $i++): ?>
                                            <i class="bi bi-star-fill"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-secondary small mb-1">“<?= e($rev['review_text']) ?>”</p>
                                <span class="text-muted" style="font-size: 0.72rem;">Stylist: <?= e($rev['staff_name']) ?> &bull; <?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar / Booking Box & Related -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px;">
                <div class="card border p-4 shadow-sm mb-4" style="border-radius: var(--radius-md);">
                    <h5 class="fw-bold mb-3">Quick Reservation</h5>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                        <span class="text-muted">Treatment Fee</span>
                        <span class="fw-bold fs-5 text-dark"><?= currency($service['price']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                        <span class="text-muted">Estimated Time</span>
                        <span class="fw-bold text-dark"><?= duration_format((int)$service['duration_minutes']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                        <span class="text-muted">Salon Location</span>
                        <span class="fw-semibold text-dark">Bandra West, Mumbai</span>
                    </div>

                    <div class="d-grid mt-4">
                        <a href="/book?service=<?= (int)$service['id'] ?>" class="btn btn-deluxe py-3 justify-content-center">
                            Continue to Staff & Date <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="text-center mt-2">
                        <span class="text-muted" style="font-size: 0.75rem;">Instant confirmation &bull; No advance required to browse</span>
                    </div>
                </div>

                <!-- Related Services -->
                <?php if (!empty($relatedServices)): ?>
                    <div class="card border p-4 shadow-sm" style="border-radius: var(--radius-md);">
                        <h5 class="fw-bold mb-3 h6">Complementary Treatments</h5>
                        <div class="list-group list-group-flush">
                            <?php foreach ($relatedServices as $rel): ?>
                                <a href="/services/<?= e($rel['slug']) ?>" class="list-group-item list-group-item-action px-0 py-2 border-bottom text-decoration-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="fw-semibold small text-dark"><?= e($rel['name']) ?></div>
                                        <div class="fw-bold text-dark small"><?= currency($rel['price']) ?></div>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;"><?= duration_format((int)$rel['duration_minutes']) ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
