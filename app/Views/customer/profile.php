<?php
/**
 * @var array $user
 */
?>

<div class="row g-4">
    <!-- Profile & Contact Card -->
    <div class="col-lg-8">
        <div class="card border p-4 shadow-sm mb-4" style="border-radius: var(--radius-md);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="h5 fw-bold mb-0"><i class="bi bi-person-vcard text-warning me-2"></i> Personal Details & Contact</h4>
                <span class="badge bg-success-subtle text-success border border-success">Client ID #DLX-C<?= str_pad((string)$user['id'], 4, '0', STR_PAD_LEFT) ?></span>
            </div>
            <p class="text-muted small mb-4">Keep your contact and address details current for appointment reminders and home service bookings.</p>

            <form action="/customer/profile" method="POST">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label small fw-bold text-dark">Full Name *</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= e($user['name']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-bold text-dark">Email Address</label>
                        <input type="email" id="email" class="form-control bg-light" value="<?= e($user['email']) ?>" readonly title="Email cannot be changed directly for security reasons">
                        <div class="form-text small" style="font-size: 0.72rem;">Primary account identity &bull; Read-only</div>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-bold text-dark">Mobile Contact Number *</label>
                        <input type="tel" name="phone" id="phone" class="form-control" value="<?= e($user['phone']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="gender" class="form-label small fw-bold text-dark">Gender Preference</label>
                        <select name="gender" id="gender" class="form-select">
                            <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>

                    <!-- Address Foundation for Home Service -->
                    <div class="col-12 pt-3 border-top">
                        <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-house-door text-warning me-1"></i> Home Service Address Foundation</h6>
                        <p class="text-muted small mb-3">Pre-saved address for booking beauticians directly to your residence in Mumbai.</p>
                    </div>

                    <div class="col-12">
                        <label for="address_line" class="form-label small text-dark">Flat, Building, Street Address</label>
                        <input type="text" name="address_line" id="address_line" class="form-control" value="<?= e($user['address_line'] ?? '') ?>" placeholder="e.g. Flat 402, Lotus Residency">
                    </div>

                    <div class="col-md-6">
                        <label for="area_locality" class="form-label small text-dark">Area / Locality</label>
                        <input type="text" name="area_locality" id="area_locality" class="form-control" value="<?= e($user['area_locality'] ?? '') ?>" placeholder="e.g. Carter Road, Bandra West">
                    </div>

                    <div class="col-md-6">
                        <label for="landmark" class="form-label small text-dark">Landmark</label>
                        <input type="text" name="landmark" id="landmark" class="form-control" value="<?= e($user['landmark'] ?? '') ?>" placeholder="e.g. Near Cafe Coffee Day">
                    </div>

                    <div class="col-md-4 col-6">
                        <label for="city" class="form-label small text-dark">City</label>
                        <input type="text" name="city" id="city" class="form-control" value="<?= e($user['city'] ?? 'Mumbai') ?>" required>
                    </div>

                    <div class="col-md-4 col-6">
                        <label for="state" class="form-label small text-dark">State</label>
                        <input type="text" name="state" id="state" class="form-control" value="<?= e($user['state'] ?? 'Maharashtra') ?>" required>
                    </div>

                    <div class="col-md-4 col-12">
                        <label for="pincode" class="form-label small text-dark">Pincode</label>
                        <input type="text" name="pincode" id="pincode" class="form-control" value="<?= e($user['pincode'] ?? '') ?>" placeholder="e.g. 400050">
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-deluxe px-4">
                            <i class="bi bi-check2-circle me-1"></i> Save Profile Details
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Security & Password Card -->
    <div class="col-lg-4">
        <div class="card border p-4 shadow-sm mb-4" style="border-radius: var(--radius-md);">
            <h5 class="fw-bold mb-2 h6"><i class="bi bi-shield-lock text-warning me-1"></i> Security & Password</h5>
            <p class="text-muted small mb-3">Change your account password securely.</p>

            <form action="/customer/password" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="current_password" class="form-label small fw-bold text-dark">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label small fw-bold text-dark">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Min. 6 chars" required>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label small fw-bold text-dark">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                </div>

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-outline-dark btn-sm py-2">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Appointment Quick Card Placeholder (Ready for Session 3) -->
        <div class="card border p-3 bg-light shadow-sm" style="border-radius: var(--radius-md);">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-info-circle text-primary"></i>
                <span class="fw-bold small text-dark">Upcoming Appointments</span>
            </div>
            <p class="small text-muted mb-3">You can view live booking status, reschedule, and download receipts in My Appointments.</p>
            <a href="/customer/appointments" class="btn btn-sm btn-dark w-100">
                View Appointments (Session 3)
            </a>
        </div>
    </div>
</div>
