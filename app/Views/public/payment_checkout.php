<?php
/**
 * @var array $appt
 * @var array $currentUser
 */
?>

<div class="py-4 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/customer/appointments">My Appointments</a></li>
                <li class="breadcrumb-item active" aria-current="page">Payment Checkout</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold mb-1">Sandbox Payment Checkout</h1>
        <p class="text-muted small mb-0">Complete payment for booking reference <strong><?= e($appt['booking_reference']) ?></strong></p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-7">
            <div class="card border p-4 p-md-5 shadow-sm" style="border-radius: var(--radius-lg);">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1">Demo Sandbox</span>
                        <h4 class="fw-bold mb-0">Select Payment Option</h4>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i> Simulated Bank API
                    </span>
                </div>

                <!-- Booking Summary Box -->
                <div class="bg-light p-4 rounded-3 border mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-bold text-uppercase text-muted">Booking Summary</span>
                        <span class="badge bg-dark font-monospace"><?= e($appt['booking_reference']) ?></span>
                    </div>
                    <div class="fw-bold text-dark fs-5 mb-1">
                        <?= date('M d, Y', strtotime($appt['appointment_date'])) ?> at <?= date('g:i A', strtotime($appt['start_time'])) ?>
                    </div>
                    <div class="text-secondary small mb-3">Specialist: <?= e($appt['staff_name']) ?></div>

                    <div class="border-top pt-2">
                        <div class="d-flex justify-content-between align-items-center py-1 small">
                            <span class="text-muted">Total Treatment Amount</span>
                            <span class="fw-bold text-dark fs-5"><?= currency($appt['total_amount']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-1 small">
                            <span class="text-muted">Minimum Advance Payable (20%)</span>
                            <span class="fw-bold text-warning fs-5"><?= currency($appt['advance_paid'] > 0 ? $appt['advance_paid'] : round($appt['total_amount'] * 0.20, 2)) ?></span>
                        </div>
                    </div>
                </div>

                <form action="/payment/process" method="POST" id="payment-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="appointment_id" value="<?= (int)$appt['id'] ?>">

                    <!-- 1. Payment Amount Selection -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark text-uppercase">1. Choose Payment Type</label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card p-3 border payment-type-card active cursor-pointer" onclick="selectPayType('advance')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_type" id="payTypeAdvance" value="advance" checked>
                                        <label class="form-check-label fw-bold small" for="payTypeAdvance">
                                            Pay Advance
                                        </label>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Token amount to confirm slot</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card p-3 border payment-type-card cursor-pointer" onclick="selectPayType('full')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_type" id="payTypeFull" value="full">
                                        <label class="form-check-label fw-bold small" for="payTypeFull">
                                            Pay Full Amount
                                        </label>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">100% upfront settlement</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card p-3 border payment-type-card cursor-pointer" onclick="selectPayType('pay_at_salon')">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_type" id="payTypeSalon" value="pay_at_salon">
                                        <label class="form-check-label fw-bold small" for="payTypeSalon">
                                            Pay at Salon
                                        </label>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Pay upon appointment arrival</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Payment Method Selection -->
                    <div class="mb-4" id="method-section">
                        <label class="form-label small fw-bold text-dark text-uppercase">2. Choose Payment Gateway Method</label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card p-3 border text-center cursor-pointer method-card active" onclick="selectMethod('upi')">
                                    <input type="radio" name="payment_method" id="methodUpi" value="upi" class="d-none" checked>
                                    <i class="bi bi-qr-code-scan fs-3 text-warning mb-1"></i>
                                    <div class="fw-bold small">BHIM / UPI</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">GPay, PhonePe, Paytm</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card p-3 border text-center cursor-pointer method-card" onclick="selectMethod('card')">
                                    <input type="radio" name="payment_method" id="methodCard" value="card" class="d-none">
                                    <i class="bi bi-credit-card-2-front fs-3 text-warning mb-1"></i>
                                    <div class="fw-bold small">Credit / Debit Card</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">Visa, Mastercard, RuPay</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card p-3 border text-center cursor-pointer method-card" onclick="selectMethod('net_banking')">
                                    <input type="radio" name="payment_method" id="methodNet" value="net_banking" class="d-none">
                                    <i class="bi bi-bank fs-3 text-warning mb-1"></i>
                                    <div class="fw-bold small">Net Banking</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">All Major Indian Banks</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sandbox Failure Simulation Checkbox -->
                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="simulate_failure" id="simulate_failure" value="1">
                            <label class="form-check-label small text-muted" for="simulate_failure">
                                <i class="bi bi-bug text-danger me-1"></i> <strong>Simulate Gateway Failure:</strong> Check this to test payment retry & error handling flow.
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-deluxe btn-lg py-3 justify-content-center" id="btn-pay-submit">
                            Complete Demo Payment <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
function selectPayType(type) {
    document.querySelectorAll('.payment-type-card').forEach(c => c.classList.remove('active', 'border-warning', 'bg-light'));
    
    if (type === 'advance') {
        document.getElementById('payTypeAdvance').checked = true;
        document.getElementById('payTypeAdvance').closest('.payment-type-card').classList.add('active', 'border-warning', 'bg-light');
        document.getElementById('method-section').classList.remove('d-none');
    } else if (type === 'full') {
        document.getElementById('payTypeFull').checked = true;
        document.getElementById('payTypeFull').closest('.payment-type-card').classList.add('active', 'border-warning', 'bg-light');
        document.getElementById('method-section').classList.remove('d-none');
    } else {
        document.getElementById('payTypeSalon').checked = true;
        document.getElementById('payTypeSalon').closest('.payment-type-card').classList.add('active', 'border-warning', 'bg-light');
        document.getElementById('method-section').classList.add('d-none');
    }
}

function selectMethod(method) {
    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('active', 'border-warning', 'bg-light'));
    if (method === 'upi') {
        document.getElementById('methodUpi').checked = true;
        document.getElementById('methodUpi').closest('.method-card').classList.add('active', 'border-warning', 'bg-light');
    } else if (method === 'card') {
        document.getElementById('methodCard').checked = true;
        document.getElementById('methodCard').closest('.method-card').classList.add('active', 'border-warning', 'bg-light');
    } else {
        document.getElementById('methodNet').checked = true;
        document.getElementById('methodNet').closest('.method-card').classList.add('active', 'border-warning', 'bg-light');
    }
}
</script>
