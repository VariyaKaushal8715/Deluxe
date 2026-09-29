<?php
/**
 * @var string $title
 * @var string $provider
 * @var string $description
 * @var array $mockUser
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card border p-4 p-md-5 shadow-sm" style="border-radius: var(--radius-lg);">
                <div class="text-center mb-4">
                    <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 text-uppercase fw-bold mb-2">
                        <i class="bi bi-code-square me-1"></i> Integration Architecture Sandbox
                    </span>
                    <h2 class="h4 fw-bold"><?= e($title) ?></h2>
                    <p class="text-muted small">Demonstration of external identity federation architecture</p>
                </div>

                <div class="p-3 mb-4 rounded-3 bg-light border">
                    <h6 class="fw-bold mb-1"><i class="bi bi-info-circle text-primary me-1"></i> Technical Protocol</h6>
                    <p class="small text-secondary mb-0"><?= e($description) ?></p>
                </div>

                <div class="card bg-white border p-3 mb-4">
                    <div class="small fw-bold text-muted text-uppercase mb-2">Simulated OAuth / OTP Payload</div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px;">
                            <?= strtoupper(substr($mockUser['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-bold text-dark small"><?= e($mockUser['name']) ?></div>
                            <div class="text-muted small"><?= e($mockUser['email']) ?> &bull; <?= e($mockUser['phone']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-secondary small mb-4">
                    <i class="bi bi-shield-check me-1"></i> In compliance with demo rules, real production financial and third-party API credentials are intentionally bypassed in this environment. Full authentication is available via standard email/password.
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/login" class="btn btn-outline-dark">
                        <i class="bi bi-arrow-left me-1"></i> Return to Sign In
                    </a>
                    <a href="/services" class="btn btn-deluxe">
                        Browse Services
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
