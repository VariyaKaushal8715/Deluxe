<?php
/**
 * @var array $service
 */
$iconMap = [
    'hair-care' => 'bi-scissors',
    'facials-skin' => 'bi-stars',
    'nails-pedicure' => 'bi-hand-index-thumb',
    'bridal-makeup' => 'bi-heart',
    'spa-wellness' => 'bi-droplet',
];
$catSlug = $service['category_slug'] ?? '';
$cardIcon = $iconMap[$catSlug] ?? 'bi-sparkles';
?>
<div class="col-lg-4 col-md-6 mb-4">
    <div class="service-card">
        <div class="service-card-img-wrap">
            <div class="service-card-img-placeholder">
                <i class="bi <?= $cardIcon ?>"></i>
            </div>

            <?php if (!empty($service['is_popular'])): ?>
                <span class="badge-popular"><i class="bi bi-fire me-1"></i> Popular</span>
            <?php endif; ?>

            <?php if (!empty($service['is_home_service'])): ?>
                <span class="badge-home-service"><i class="bi bi-house-door me-1"></i> Home Service</span>
            <?php endif; ?>
        </div>

        <div class="service-card-body">
            <div class="service-category-tag"><?= e($service['category_name'] ?? 'Treatment') ?></div>
            <h3 class="service-title">
                <a href="/services/<?= e($service['slug']) ?>"><?= e($service['name']) ?></a>
            </h3>
            <p class="service-desc"><?= e(mb_strimwidth($service['description'], 0, 110, '...')) ?></p>

            <div class="service-meta">
                <div>
                    <div class="service-price"><?= currency($service['price']) ?></div>
                    <div class="service-duration"><i class="bi bi-clock"></i> <?= duration_format((int)$service['duration_minutes']) ?></div>
                </div>
                <div class="d-flex gap-1">
                    <a href="/services/<?= e($service['slug']) ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="View service details">
                        Details
                    </a>
                    <a href="/book?service=<?= (int)$service['id'] ?>" class="btn btn-sm btn-deluxe rounded-pill px-3" title="Book this service">
                        Book
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
