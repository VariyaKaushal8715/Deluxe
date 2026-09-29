<?php
/**
 * @var array $services
 * @var array $categories
 * @var array $filters
 * @var string $sortBy
 * @var ?array $activeCategory
 * @var int $totalCount
 */
?>

<div class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Services Menu</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold mb-0">
                    <?= $activeCategory ? e($activeCategory['name']) : 'All Salon & Wellness Services' ?>
                </h1>
                <p class="text-muted small mb-0">Showing <?= $totalCount ?> beauty and grooming treatments</p>
            </div>

            <!-- Search Bar -->
            <form action="/services" method="GET" class="d-flex gap-2" style="max-width: 380px; width: 100%;">
                <?php if (!empty($filters['category'])): ?>
                    <input type="hidden" name="category" value="<?= e($filters['category']) ?>">
                <?php endif; ?>
                <div class="input-group">
                    <input type="text" name="q" class="form-control rounded-start-pill" placeholder="Search facial, haircut, massage..." value="<?= e($filters['q']) ?>">
                    <button class="btn btn-dark rounded-end-pill px-3" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Category Pills -->
        <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
            <a href="/services<?= !empty($filters['q']) ? '?q=' . urlencode($filters['q']) : '' ?>" class="cat-pill <?= empty($filters['category']) ? 'active' : '' ?>">
                <span>All Treatments</span>
            </a>
            <?php foreach ($categories as $cat): ?>
                <?php 
                    $catUrl = '/services?category=' . urlencode($cat['slug']);
                    if (!empty($filters['q'])) $catUrl .= '&q=' . urlencode($filters['q']);
                ?>
                <a href="<?= $catUrl ?>" class="cat-pill <?= ($filters['category'] ?? '') === $cat['slug'] ? 'active' : '' ?>">
                    <span><?= e($cat['name']) ?></span>
                    <span class="badge rounded-pill"><?= (int)$cat['service_count'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <!-- Filter Controls Sidebar / Offcanvas -->
        <div class="col-lg-3">
            <div class="card border p-3 shadow-sm" style="border-radius: var(--radius-md);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 h6"><i class="bi bi-sliders me-1"></i> Filter Menu</h5>
                    <a href="/services" class="small text-danger text-decoration-none">Clear All</a>
                </div>

                <form action="/services" method="GET" id="filterForm">
                    <!-- Preserve search if present -->
                    <?php if (!empty($filters['q'])): ?>
                        <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
                    <?php endif; ?>

                    <!-- Category Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Category</label>
                        <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= e($cat['slug']) ?>" <?= ($filters['category'] ?? '') === $cat['slug'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Gender Suitability</label>
                        <div class="d-flex gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderAll" value="" <?= empty($filters['gender']) ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label small" for="genderAll">All</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female" <?= ($filters['gender'] ?? '') === 'female' ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label small" for="genderFemale">Women</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male" <?= ($filters['gender'] ?? '') === 'male' ? 'checked' : '' ?> onchange="this.form.submit()">
                                <label class="form-check-label small" for="genderMale">Men</label>
                            </div>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Max Budget (₹)</label>
                        <select name="max_price" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Any Price</option>
                            <option value="1000" <?= ($filters['max_price'] ?? '') === '1000' ? 'selected' : '' ?>>Under ₹1,000</option>
                            <option value="2000" <?= ($filters['max_price'] ?? '') === '2000' ? 'selected' : '' ?>>Under ₹2,000</option>
                            <option value="4000" <?= ($filters['max_price'] ?? '') === '4000' ? 'selected' : '' ?>>Under ₹4,000</option>
                            <option value="8000" <?= ($filters['max_price'] ?? '') === '8000' ? 'selected' : '' ?>>Under ₹8,000</option>
                        </select>
                    </div>

                    <!-- Duration Filter -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Max Duration</label>
                        <select name="max_duration" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Any Duration</option>
                            <option value="45" <?= ($filters['max_duration'] ?? '') === '45' ? 'selected' : '' ?>>Up to 45 mins</option>
                            <option value="60" <?= ($filters['max_duration'] ?? '') === '60' ? 'selected' : '' ?>>Up to 60 mins (1 hr)</option>
                            <option value="90" <?= ($filters['max_duration'] ?? '') === '90' ? 'selected' : '' ?>>Up to 90 mins</option>
                        </select>
                    </div>

                    <!-- Checkbox Preferences -->
                    <div class="mb-3 pt-2 border-top">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="popular" value="1" id="filterPopular" <?= ($filters['popular'] ?? '') === '1' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="form-check-label small fw-semibold" for="filterPopular">
                                <i class="bi bi-fire text-warning me-1"></i> Popular Only
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="home_service" value="1" id="filterHome" <?= ($filters['home_service'] ?? '') === '1' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <label class="form-check-label small fw-semibold" for="filterHome">
                                <i class="bi bi-house-door text-warning me-1"></i> Home Service Available
                            </label>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button type="submit" class="btn btn-sm btn-dark rounded-pill">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Service Cards Grid -->
        <div class="col-lg-9">
            <!-- Sorting & Meta Header -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="small text-muted">
                    Showing <span class="fw-bold text-dark"><?= count($services) ?></span> treatments
                    <?php if (!empty($filters['q'])): ?>
                        for "<strong><?= e($filters['q']) ?></strong>"
                    <?php endif; ?>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <label for="sortSelector" class="small text-muted text-nowrap">Sort by:</label>
                    <select id="sortSelector" class="form-select form-select-sm" style="width: auto;" onchange="
                        const url = new URL(window.location.href);
                        url.searchParams.set('sort', this.value);
                        window.location.href = url.toString();
                    ">
                        <option value="popular" <?= $sortBy === 'popular' ? 'selected' : '' ?>>Most Popular</option>
                        <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="duration_asc" <?= $sortBy === 'duration_asc' ? 'selected' : '' ?>>Duration: Shortest</option>
                        <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Newest Added</option>
                    </select>
                </div>
            </div>

            <!-- Service Cards -->
            <?php if (empty($services)): ?>
                <div class="text-center py-5 bg-white rounded-3 border p-5">
                    <div class="text-muted mb-3 fs-1"><i class="bi bi-search"></i></div>
                    <h4 class="fw-bold">No Treatments Found</h4>
                    <p class="text-muted mb-4">We couldn't find any beauty or wellness treatments matching your active criteria.</p>
                    <a href="/services" class="btn btn-deluxe rounded-pill">
                        <i class="bi bi-arrow-repeat"></i> Reset All Filters
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($services as $service): ?>
                        <?php \Core\View::partial('service_card', ['service' => $service]); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
