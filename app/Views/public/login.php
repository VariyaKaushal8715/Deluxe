<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="auth-card">
                <div class="auth-header">
                    <a class="deluxe-brand mb-2 d-inline-block" href="/">
                        <i class="bi bi-gem text-warning"></i> YOUR <span>SALON</span>
                    </a>
                    <h2 class="h4 fw-bold">Client & Staff Sign In</h2>
                    <p class="text-muted small">Access your bookings, invoices, and personalized appointments</p>
                </div>

                <!-- Demo Account Selector Chips -->
                <div class="p-3 mb-4 rounded-3 border" style="background: var(--color-bg-subtle);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-uppercase text-muted" style="font-size: 0.72rem;">Demo Access Quick-Fill</span>
                        <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">1-Click Fill</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-xs btn-outline-dark demo-fill-btn" style="font-size: 0.76rem;" data-email="sneha.kapoor@gmail.com" data-pass="Customer@123">
                            <i class="bi bi-person me-1"></i> Customer
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark demo-fill-btn" style="font-size: 0.76rem;" data-email="ananya@deluxesalon.com" data-pass="Customer@123">
                            <i class="bi bi-scissors me-1"></i> Stylist
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-dark demo-fill-btn" style="font-size: 0.76rem;" data-email="admin@deluxesalon.com" data-pass="Admin@123">
                            <i class="bi bi-shield-lock me-1"></i> Salon Admin
                        </button>
                    </div>
                </div>

                <form action="/login" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold text-dark">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control border-start-0" placeholder="name@example.com" required autocomplete="email">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label small fw-bold text-dark mb-0">Password</label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                            <button class="btn btn-outline-secondary border-start-0 toggle-password-btn" type="button" data-target="password" title="Toggle password visibility">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-deluxe py-2 justify-content-center">
                            Sign In to Account <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center my-4 position-relative">
                    <hr class="text-muted">
                    <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 small text-muted">Or sign in with</span>
                </div>

                <!-- External Auth Sandbox Architecture Links -->
                <div class="d-grid gap-2">
                    <a href="/auth/google" class="btn btn-outline-secondary btn-sm py-2">
                        <i class="bi bi-google text-danger me-2"></i> Google Account (Sandbox Flow)
                    </a>
                    <a href="/auth/phone" class="btn btn-outline-secondary btn-sm py-2">
                        <i class="bi bi-phone text-primary me-2"></i> Mobile Number & OTP (Sandbox Flow)
                    </a>
                </div>

                <div class="text-center mt-4 pt-3 border-top small text-muted">
                    New client? <a href="/register" class="fw-bold text-dark text-decoration-underline">Create an account</a>
                </div>
            </div>
        </div>
    </div>
</div>
