<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="auth-card">
                <div class="auth-header">
                    <a class="deluxe-brand mb-2 d-inline-block" href="/">
                        <i class="bi bi-gem text-warning"></i> YOUR <span>SALON</span>
                    </a>
                    <h2 class="h4 fw-bold">Create Client Account</h2>
                    <p class="text-muted small">Register to book appointments, track visits, and unlock home service</p>
                </div>

                <form action="/register" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="name" class="form-label small fw-bold text-dark">Full Name *</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Aditi Sen" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-bold text-dark">Email Address *</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="aditi@example.com" required>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label small fw-bold text-dark">Mobile Contact Number *</label>
                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="+91 98765 43210" required>
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label small fw-bold text-dark">Password *</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control border-end-0" placeholder="Min. 6 chars" required>
                                <button class="btn btn-outline-secondary border-start-0 toggle-password-btn" type="button" data-target="password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label small fw-bold text-dark">Confirm Password *</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-end-0" placeholder="Re-enter password" required>
                                <button class="btn btn-outline-secondary border-start-0 toggle-password-btn" type="button" data-target="password_confirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Gender Preference</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="regFemale" value="female" checked>
                                    <label class="form-check-label small" for="regFemale">Female</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="regMale" value="male">
                                    <label class="form-check-label small" for="regMale">Male</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="regOther" value="other">
                                    <label class="form-check-label small" for="regOther">Other</label>
                                </div>
                            </div>
                        </div>

                        <!-- Address Foundation for Home Service -->
                        <div class="col-12 pt-2 border-top">
                            <div class="small fw-bold text-uppercase text-muted mb-2">Service Address (Optional for Home Service)</div>
                        </div>

                        <div class="col-12">
                            <label for="address_line" class="form-label small text-dark">Flat / House / Apartment / Street</label>
                            <input type="text" name="address_line" id="address_line" class="form-control" placeholder="Flat 402, Lotus Residency">
                        </div>

                        <div class="col-md-6">
                            <label for="area_locality" class="form-label small text-dark">Area / Locality</label>
                            <input type="text" name="area_locality" id="area_locality" class="form-control" placeholder="Carter Road, Bandra West">
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="city" class="form-label small text-dark">City</label>
                            <input type="text" name="city" id="city" class="form-control" value="Mumbai" required>
                        </div>

                        <div class="col-md-3 col-6">
                            <label for="pincode" class="form-label small text-dark">Pincode</label>
                            <input type="text" name="pincode" id="pincode" class="form-control" placeholder="400050">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-deluxe py-2 justify-content-center">
                            Register & Continue <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-3 border-top small text-muted">
                    Already registered? <a href="/login" class="fw-bold text-dark text-decoration-underline">Sign In here</a>
                </div>
            </div>
        </div>
    </div>
</div>
