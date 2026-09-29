<?php
/**
 * @var array $items
 * @var string $activeCategory
 */
?>

<div class="py-4 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lookbook</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold mb-1">Deluxe Salon Lookbook</h1>
        <p class="text-muted small mb-0">Explore our signature hair transformations, bridal artistry, and nail designs</p>

        <!-- Categories -->
        <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
            <a href="/gallery?category=all" class="cat-pill <?= $activeCategory === 'all' ? 'active' : '' ?>">All Styles</a>
            <a href="/gallery?category=hair" class="cat-pill <?= $activeCategory === 'hair' ? 'active' : '' ?>">Hair Transformations</a>
            <a href="/gallery?category=bridal" class="cat-pill <?= $activeCategory === 'bridal' ? 'active' : '' ?>">Bridal Artistry</a>
            <a href="/gallery?category=facial" class="cat-pill <?= $activeCategory === 'facial' ? 'active' : '' ?>">Skin Glow</a>
            <a href="/gallery?category=nails" class="cat-pill <?= $activeCategory === 'nails' ? 'active' : '' ?>">Nails</a>
            <a href="/gallery?category=makeup" class="cat-pill <?= $activeCategory === 'makeup' ? 'active' : '' ?>">Party Glam</a>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <?php foreach ($items as $item): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border shadow-sm overflow-hidden" style="border-radius: var(--radius-md);">
                    <div style="height: 220px; background: linear-gradient(135deg, #2D2825 0%, #1A1817 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                        <i class="bi bi-camera text-warning fs-1 opacity-50"></i>
                        <?php if (!empty($item['is_before_after'])): ?>
                            <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3 fw-bold">Before & After</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body p-4">
                        <span class="badge bg-light text-dark border text-uppercase mb-2" style="font-size: 0.7rem;"><?= e($item['category']) ?></span>
                        <h5 class="fw-bold mb-2"><?= e($item['title']) ?></h5>
                        <p class="small text-muted mb-0"><?= e($item['description']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
