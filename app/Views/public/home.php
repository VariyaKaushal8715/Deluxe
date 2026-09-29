<?php
/**
 * @var array $featuredServices
 * @var array $categories
 * @var array $staffMembers
 * @var array $testimonials
 * @var array $galleryPreview
 */
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container position-relative z-1">
        <div class="row align-items-center g-5">
            <!-- Left Hero Content -->
            <div class="col-lg-6 hero-content-fade">
                <span class="hero-label-pill mb-3">
                    <i class="bi bi-sparkles text-warning me-1"></i> Premium Beauty & Wellness Experience
                </span>
                
                <h1 class="hero-title mb-3">
                    Beauty, Designed <br>Around You.
                </h1>
                
                <p class="hero-lead mb-4 pe-lg-4">
                    Discover personalized hair styling, clinical facial treatments, and holistic wellness — crafted by dedicated specialists for your ultimate comfort.
                </p>

                <!-- Hero CTAs -->
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                    <a href="/book" class="btn btn-deluxe btn-lg hero-cta-primary px-4 py-3">
                        Book Appointment <i class="bi bi-arrow-right ms-2 fs-6"></i>
                    </a>
                    <a href="/services" class="btn btn-deluxe-outline btn-lg hero-cta-secondary px-4 py-3">
                        Explore Services
                    </a>
                </div>

                <!-- Trust Bar -->
                <div class="d-flex flex-wrap align-items-center gap-4 pt-3 border-top border-secondary-subtle text-muted small">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check2-circle text-warning fs-5"></i>
                        <span>Flexible Booking</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check2-circle text-warning fs-5"></i>
                        <span>Professional Services</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check2-circle text-warning fs-5"></i>
                        <span>In-Salon & Home Service</span>
                    </div>
                </div>
            </div>

            <!-- Right Visual Composition -->
            <div class="col-lg-6 hero-image-reveal">
                <div class="hero-visual-wrapper">
                    <!-- Main Editorial Image -->
                    <div class="hero-image-frame">
                        <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80" 
                             alt="Luxury Salon Experience" 
                             class="hero-img-main img-fluid">
                    </div>

                    <!-- Subtle Floating Badge -->
                    <div class="hero-floating-card shadow-lg p-3 rounded-4 bg-white border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi bi-calendar-check-fill fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark small mb-0">Instant Slot Confirmation</div>
                                <div class="text-muted" style="font-size: 0.76rem;">Select stylist & preferred time</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Bar -->
<section class="py-5 bg-white border-bottom scroll-reveal">
    <div class="container">
        <div class="text-center mb-4">
            <span class="text-uppercase small fw-bold text-warning letter-spacing-1">Explore By Category</span>
            <h2 class="h3 fw-bold">Curated Beauty & Wellness Specialties</h2>
        </div>

        <div class="row g-3 justify-content-center">
            <?php 
            $catIcons = [
                'hair-care' => 'bi-scissors',
                'facials-skin' => 'bi-stars',
                'nails-pedicure' => 'bi-hand-index-thumb',
                'bridal-makeup' => 'bi-heart',
                'spa-wellness' => 'bi-droplet',
            ];
            foreach ($categories as $cat): 
                $icon = $catIcons[$cat['slug']] ?? 'bi-sparkles';
            ?>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="/services?category=<?= e($cat['slug']) ?>" class="card h-100 text-center p-3 border text-decoration-none shadow-sm transition-all" style="border-radius: var(--radius-md);">
                        <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle" style="width: 52px; height: 52px; background: var(--color-gold-light); color: var(--color-gold-dark); font-size: 1.5rem;">
                            <i class="bi <?= $icon ?>"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1 small"><?= e($cat['name']) ?></h6>
                        <span class="text-muted" style="font-size: 0.75rem;"><?= (int)$cat['service_count'] ?> Treatments</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Treatments Section -->
<section class="py-5 scroll-reveal">
    <div class="container">
        <div class="d-flex flex-wrap align-items-end justify-content-between mb-4">
            <div>
                <span class="text-uppercase small fw-bold text-warning letter-spacing-1">Popular Selections</span>
                <h2 class="fw-bold mb-1">Signature Salon Treatments</h2>
                <p class="text-muted mb-0">Our most celebrated services, curated by master beauticians.</p>
            </div>
            <a href="/services" class="btn btn-outline-dark rounded-pill px-4 mt-3 mt-md-0">
                View Complete Menu <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row">
            <?php foreach ($featuredServices as $service): ?>
                <?php \Core\View::partial('service_card', ['service' => $service]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5 bg-white border-top border-bottom scroll-reveal">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="text-uppercase small fw-bold text-warning letter-spacing-1">The Service Standard</span>
                <h2 class="fw-bold mb-3">A Standard of Care That Truly Elevates You</h2>
                <p class="text-muted mb-4">
                    At Your Salon, we reject rushed assembly-line services. Every appointment is allocated generous time, ensuring uninterrupted personalized care from dedicated specialists in a quiet, sanitized atmosphere.
                </p>

                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="d-flex gap-3">
                            <div class="fs-3 text-warning"><i class="bi bi-clock-history"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Guaranteed Time Slots</h6>
                                <p class="small text-muted mb-0">No endless waiting chair delays. Your beautician is ready the minute you arrive.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="d-flex gap-3">
                            <div class="fs-3 text-warning"><i class="bi bi-shield-check"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Clinical Hygiene</h6>
                                <p class="small text-muted mb-0">Medical-grade sterilization for all tools and single-use disposable kits.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="d-flex gap-3">
                            <div class="fs-3 text-warning"><i class="bi bi-house-heart"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Home Service Care</h6>
                                <p class="small text-muted mb-0">Bring the complete salon luxury experience to your home in select areas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="d-flex gap-3">
                            <div class="fs-3 text-warning"><i class="bi bi-award"></i></div>
                            <div>
                                <h6 class="fw-bold mb-1">Certified Specialists</h6>
                                <p class="small text-muted mb-0">Internationally certified hair stylists and aesthetic dermatologists.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-3">
                    <?php foreach ($staffMembers as $staff): ?>
                        <div class="col-sm-6">
                            <div class="card border p-3 h-100 shadow-sm" style="border-radius: var(--radius-md);">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold" style="width: 46px; height: 46px; font-size: 1.1rem;">
                                        <?= strtoupper(substr($staff['full_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0"><?= e($staff['full_name']) ?></h6>
                                        <span class="small text-warning fw-semibold" style="font-size: 0.76rem;"><?= e($staff['designation']) ?></span>
                                    </div>
                                </div>
                                <p class="small text-muted mb-0"><?= e(mb_strimwidth($staff['bio'], 0, 85, '...')) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Client Testimonials -->
<section class="py-5 bg-light scroll-reveal">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="text-uppercase small fw-bold text-warning letter-spacing-1">Verified Client Love</span>
            <h2 class="fw-bold mb-1">Real Stories from Regular Clients</h2>
            <p class="text-muted">Every review is tied to an actual completed salon booking.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($testimonials as $review): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm p-4" style="border-radius: var(--radius-md);">
                        <div class="text-warning mb-2">
                            <?php for ($i = 0; $i < (int)$review['rating']; $i++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="small text-secondary mb-3 flex-grow-1">“<?= e($review['review_text']) ?>”</p>
                        <div class="pt-3 border-top">
                            <div class="fw-bold small text-dark"><?= e($review['customer_name']) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-patch-check-fill text-success"></i> <?= e($review['service_name']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5" style="background: linear-gradient(135deg, #1A1817 0%, #292422 100%);">
    <div class="container text-center py-4">
        <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-3">Reserve Your Experience</span>
        <h2 class="display-6 fw-bold text-white mb-3">Ready for Your Personalized Beauty Session?</h2>
        <p class="lead text-secondary mb-4 mx-auto" style="max-width: 600px;">
            Book online in less than 2 minutes. Choose your favorite beautician, exact time slot, and preferred service mode.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="/book" class="btn btn-deluxe btn-lg px-4">
                <i class="bi bi-calendar-check"></i> Book Now
            </a>
            <a href="/services" class="btn btn-deluxe-outline text-white border-light btn-lg px-4">
                View All Services
            </a>
        </div>
    </div>
</section>
