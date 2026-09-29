<?php
/**
 * @var ?array $selectedService
 * @var array $categories
 * @var array $allServices
 * @var ?array $currentUser
 */
?>

<div class="py-4 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/services">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">Appointment Booking</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold mb-1">Appointment Reservation Engine</h1>
        <p class="text-muted small mb-0">Select your bespoke treatments, preferred beautician, and available time slot</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card border p-4 p-md-5 shadow-sm" style="border-radius: var(--radius-lg);">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div>
                        <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1">Step 1 of 5</span>
                        <h4 class="fw-bold mb-0">Selected Treatment(s)</h4>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i> Foundation Verified
                    </span>
                </div>

                <?php if ($selectedService): ?>
                    <div class="p-3 rounded-3 mb-4 border d-flex justify-content-between align-items-center" style="background: var(--color-gold-light); border-color: var(--color-gold-border) !important;">
                        <div>
                            <span class="badge bg-warning text-dark text-uppercase small mb-1"><?= e($selectedService['category_name']) ?></span>
                            <h5 class="fw-bold mb-1 text-dark"><?= e($selectedService['name']) ?></h5>
                            <span class="text-secondary small"><i class="bi bi-clock me-1"></i> <?= duration_format((int)$selectedService['duration_minutes']) ?></span>
                        </div>
                        <div class="text-end">
                            <div class="fs-4 fw-bold text-dark"><?= currency($selectedService['price']) ?></div>
                            <span class="badge bg-dark text-light small"><i class="bi bi-check-circle me-1"></i> Primary Treatment</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>Select a primary service from our menu to start your reservation.</div>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-dark text-uppercase">Choose or Change Service</label>
                    <select class="form-select form-select-lg" onchange="window.location.href = '/book?service=' + this.value">
                        <option value="">-- Choose a Beauty or Wellness Service --</option>
                        <?php foreach ($allServices as $s): ?>
                            <option value="<?= (int)$s['id'] ?>" <?= ($selectedService && (int)$selectedService['id'] === (int)$s['id']) ? 'selected' : '' ?>>
                                <?= e($s['category_name']) ?>: <?= e($s['name']) ?> — <?= currency($s['price']) ?> (<?= duration_format((int)$s['duration_minutes']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Client Status -->
                <div class="p-3 rounded-3 bg-light border mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small fw-bold text-muted text-uppercase">Client Identity</div>
                            <?php if ($currentUser): ?>
                                <div class="fw-bold text-dark"><?= e($currentUser['name']) ?> (<?= e($currentUser['email']) ?>)</div>
                                <span class="text-muted small"><i class="bi bi-geo-alt"></i> <?= e($currentUser['address_line'] ?? 'Bandra West, Mumbai') ?></span>
                            <?php else: ?>
                                <div class="text-secondary small">You are currently browsing as a guest.</div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if ($currentUser): ?>
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Authenticated</span>
                            <?php else: ?>
                                <a href="/login" class="btn btn-sm btn-outline-dark">Sign In First</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Session 3 Activation Banner -->
                <div class="p-4 rounded-3 border" style="background: linear-gradient(135deg, #FAF8F5 0%, #F5EFE6 100%);">
                    <div class="d-flex align-items-center gap-2 text-warning mb-2 fw-bold">
                        <i class="bi bi-gear-wide-connected fs-4"></i>
                        <span>Ready for Session 3 Activation</span>
                    </div>
                    <p class="small text-muted mb-3">
                        The Session 2 Foundation (Database models, User RBAC, Service Catalog, and Address Architecture) is fully connected. In <strong>Session 3</strong>, the interactive Multi-Step Stepper (Multi-service duration calculation, Staff roster matching, Real-time available slot engine, and Double-booking conflict guards) will be activated on this exact endpoint.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="/services" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Explore More Services
                        </a>
                        <?php if ($selectedService): ?>
                            <a href="/services/<?= e($selectedService['slug']) ?>" class="btn btn-deluxe btn-sm rounded-pill px-3">
                                View Service Details
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
