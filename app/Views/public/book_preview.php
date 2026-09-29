<?php
/**
 * @var ?array $selectedService
 * @var array $categories
 * @var array $allServices
 * @var array $allStaff
 * @var ?array $currentUser
 */
?>

<div class="py-4 bg-white border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/services">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">Book Appointment</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold mb-1">Interactive Appointment Reservation Engine</h1>
        <p class="text-muted small mb-0">Select services, match available specialists, pick real-time slots, and confirm your booking instantly.</p>
    </div>
</div>

<div class="container py-5">
    <!-- Stepper Navigation -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="d-flex justify-content-between position-relative booking-stepper">
                <div class="stepper-step active" id="step-nav-1">
                    <div class="stepper-circle">1</div>
                    <div class="stepper-label">Services</div>
                </div>
                <div class="stepper-step" id="step-nav-2">
                    <div class="stepper-circle">2</div>
                    <div class="stepper-label">Staff</div>
                </div>
                <div class="stepper-step" id="step-nav-3">
                    <div class="stepper-circle">3</div>
                    <div class="stepper-label">Date & Time</div>
                </div>
                <div class="stepper-step" id="step-nav-4">
                    <div class="stepper-circle">4</div>
                    <div class="stepper-label">Service Mode</div>
                </div>
                <div class="stepper-step" id="step-nav-5">
                    <div class="stepper-circle">5</div>
                    <div class="stepper-label">Review & Confirm</div>
                </div>
            </div>
        </div>
    </div>

    <form id="booking-form" action="/book/submit" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="services" id="input-services" value="<?= $selectedService ? (int)$selectedService['id'] : '' ?>">
        <input type="hidden" name="staff_id" id="input-staff-id" value="">
        <input type="hidden" name="appointment_date" id="input-date" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="start_time" id="input-start-time" value="">
        <input type="hidden" name="service_mode" id="input-service-mode" value="in_parlor">

        <div class="row g-4">
            <!-- Left Wizard Step Cards -->
            <div class="col-lg-8">
                
                <!-- STEP 1: Select Services -->
                <div class="card border p-4 p-md-5 shadow-sm booking-step-card" id="card-step-1">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1">Step 1 of 5</span>
                            <h4 class="fw-bold mb-0">Select Treatments & Services</h4>
                        </div>
                        <span class="text-muted small">Multi-service duration supported</span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark text-uppercase">Add Service to Reservation</label>
                        <select class="form-select form-select-lg" id="service-picker">
                            <option value="">-- Choose a Service to Add --</option>
                            <?php foreach ($allServices as $s): ?>
                                <option value="<?= (int)$s['id'] ?>" 
                                        data-name="<?= e($s['name']) ?>" 
                                        data-category="<?= e($s['category_name']) ?>"
                                        data-price="<?= (float)$s['price'] ?>" 
                                        data-duration="<?= (int)$s['duration_minutes'] ?>"
                                        data-home="<?= (int)$s['is_home_service'] ?>">
                                    <?= e($s['category_name']) ?>: <?= e($s['name']) ?> — <?= currency($s['price']) ?> (<?= duration_format((int)$s['duration_minutes']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Selected Services List -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark text-uppercase">Selected Services (Cart)</label>
                        <div id="selected-services-container" class="d-flex flex-column gap-2">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-3 border-top">
                        <button type="button" class="btn btn-deluxe px-5" onclick="goToStep(2)">
                            Next: Choose Specialist <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Select Staff -->
                <div class="card border p-4 p-md-5 shadow-sm booking-step-card d-none" id="card-step-2">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1">Step 2 of 5</span>
                            <h4 class="fw-bold mb-0">Choose Your Specialist</h4>
                        </div>
                        <span class="text-muted small">Specialist matched to treatments</span>
                    </div>

                    <p class="text-muted small mb-4">Select a qualified salon professional or allow us to assign the best available expert.</p>

                    <div class="row g-3" id="staff-cards-container">
                        <!-- Populated dynamically or static fallback -->
                        <?php foreach ($allStaff as $st): ?>
                            <div class="col-md-6">
                                <div class="card border p-3 h-100 staff-select-card cursor-pointer" 
                                     data-staff-id="<?= (int)$st['id'] ?>" 
                                     onclick="selectStaff(<?= (int)$st['id'] ?>, '<?= e($st['full_name']) ?>')">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 52px; height: 52px;">
                                            <?= strtoupper(substr($st['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0"><?= e($st['full_name']) ?></h6>
                                            <div class="small text-warning fw-semibold" style="font-size: 0.78rem;"><?= e($st['designation']) ?></div>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= e($st['specialization_names'] ?? 'Multi-Specialist') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between pt-4 border-top mt-4">
                        <button type="button" class="btn btn-outline-dark px-4" onclick="goToStep(1)">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </button>
                        <button type="button" class="btn btn-deluxe px-5" onclick="goToStep(3)">
                            Next: Select Date & Time <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Select Date & Time -->
                <div class="card border p-4 p-md-5 shadow-sm booking-step-card d-none" id="card-step-3">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1">Step 3 of 5</span>
                            <h4 class="fw-bold mb-0">Select Date & Available Time Slot</h4>
                        </div>
                        <span class="text-muted small">Real availability engine</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="date-picker-input" class="form-label small fw-bold text-dark text-uppercase">Appointment Date</label>
                            <input type="date" id="date-picker-input" class="form-control form-control-lg" 
                                   min="<?= date('Y-m-d') ?>" 
                                   max="<?= date('Y-m-d', strtotime('+30 days')) ?>" 
                                   value="<?= date('Y-m-d') ?>" 
                                   onchange="fetchTimeSlots()">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="p-3 bg-light rounded-3 border w-100 small text-muted">
                                <i class="bi bi-info-circle text-warning me-1"></i> Slots respect staff schedule, breaks & active bookings.
                            </div>
                        </div>
                    </div>

                    <!-- Available Slots Grid -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark text-uppercase">Available Time Slots for Selected Date</label>
                        <div id="slots-loading" class="text-center py-4 d-none">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading availability...</span>
                            </div>
                            <div class="small text-muted mt-2">Checking staff working hours and booked slots...</div>
                        </div>
                        <div id="slots-container" class="row g-2">
                            <!-- Rendered dynamically -->
                        </div>
                    </div>

                    <div class="d-flex justify-content-between pt-4 border-top">
                        <button type="button" class="btn btn-outline-dark px-4" onclick="goToStep(2)">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </button>
                        <button type="button" class="btn btn-deluxe px-5" onclick="goToStep(4)">
                            Next: Service Mode <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 4: Service Mode & Address -->
                <div class="card border p-4 p-md-5 shadow-sm booking-step-card d-none" id="card-step-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1">Step 4 of 5</span>
                            <h4 class="fw-bold mb-0">Select Service Mode</h4>
                        </div>
                        <span class="text-muted small">In-Parlor or Home Service</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card p-3 border service-mode-card active cursor-pointer" id="mode-in-parlor" onclick="selectServiceMode('in_parlor')">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mode_radio" id="radioInParlor" value="in_parlor" checked>
                                    <label class="form-check-label fw-bold" for="radioInParlor">
                                        <i class="bi bi-building text-warning me-1"></i> In-Parlor Salon Visit
                                    </label>
                                </div>
                                <p class="small text-muted mt-2 mb-0">Visit our luxury sanctuary at Bandra West, Mumbai with dedicated private station.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card p-3 border service-mode-card cursor-pointer" id="mode-home-service" onclick="selectServiceMode('home_service')">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mode_radio" id="radioHomeService" value="home_service">
                                    <label class="form-check-label fw-bold" for="radioHomeService">
                                        <i class="bi bi-house-door text-warning me-1"></i> Doorstep Home Service
                                    </label>
                                </div>
                                <p class="small text-muted mt-2 mb-0">Professional beautician arrives at your home with sterilized kit.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Home Address Section -->
                    <div id="home-address-section" class="p-4 rounded-3 bg-light border mb-4 d-none">
                        <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt text-warning me-1"></i> Service Delivery Address</h6>
                        <div class="mb-3">
                            <label for="home_address" class="form-label small fw-bold text-dark">Full Residence Address *</label>
                            <textarea name="home_address" id="home_address" class="form-control" rows="2" placeholder="Flat / Building / Street Address, Locality, Pincode"><?= e($currentUser['address_line'] ?? '') ?> <?= e($currentUser['area_locality'] ?? '') ?> <?= e($currentUser['city'] ?? 'Mumbai') ?></textarea>
                        </div>
                        <div>
                            <label for="home_phone" class="form-label small fw-bold text-dark">Contact Phone Number *</label>
                            <input type="tel" name="home_phone" id="home_phone" class="form-control" value="<?= e($currentUser['phone'] ?? '') ?>" placeholder="+91 98765 43210">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="customer_notes" class="form-label small fw-bold text-dark text-uppercase">Appointment Notes or Special Requests (Optional)</label>
                        <input type="text" name="customer_notes" id="customer_notes" class="form-control" placeholder="e.g. Sensitive skin, prefer quiet session, etc.">
                    </div>

                    <div class="d-flex justify-content-between pt-4 border-top">
                        <button type="button" class="btn btn-outline-dark px-4" onclick="goToStep(3)">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </button>
                        <button type="button" class="btn btn-deluxe px-5" onclick="goToStep(5)">
                            Next: Review & Confirm <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 5: Review & Confirm -->
                <div class="card border p-4 p-md-5 shadow-sm booking-step-card d-none" id="card-step-5">
                    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-warning text-dark text-uppercase fw-bold mb-1">Step 5 of 5</span>
                            <h4 class="fw-bold mb-0">Review & Confirm Appointment</h4>
                        </div>
                        <span class="badge bg-success"><i class="bi bi-shield-lock me-1"></i> Atomic Conflict Guard</span>
                    </div>

                    <div class="bg-light p-4 rounded-3 border mb-4">
                        <div class="row g-3">
                            <div class="col-md-6 border-end">
                                <div class="text-uppercase small text-muted fw-bold mb-1">Reservation Date & Time</div>
                                <div class="fs-5 fw-bold text-dark" id="summary-datetime">--</div>
                                <div class="text-secondary small" id="summary-staff">Specialist: --</div>
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <div class="text-uppercase small text-muted fw-bold mb-1">Service Mode</div>
                                <div class="fs-5 fw-bold text-dark" id="summary-mode">In-Parlor Salon Visit</div>
                                <div class="text-secondary small" id="summary-address">Studio Location: Bandra West</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="text-uppercase small text-muted fw-bold mb-2">Booked Treatments</div>
                        <ul class="list-group list-group-flush border rounded-3" id="summary-services-list">
                            <!-- Items inserted dynamically -->
                        </ul>
                    </div>

                    <!-- Client Identity Check -->
                    <?php if (!$currentUser): ?>
                        <div class="alert alert-warning d-flex align-items-center justify-content-between">
                            <div>
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Sign In Required:</strong> You need an active client account to complete your booking.
                            </div>
                            <a href="/login" class="btn btn-sm btn-dark px-3">Sign In Now</a>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between pt-4 border-top">
                        <button type="button" class="btn btn-outline-dark px-4" onclick="goToStep(4)">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </button>
                        <button type="submit" class="btn btn-deluxe btn-lg px-5 justify-content-center" id="btn-submit-booking" <?= !$currentUser ? 'disabled' : '' ?>>
                            <i class="bi bi-check2-circle me-1"></i> Confirm Appointment <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Right Column: Booking Summary Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">
                    <div class="card border p-4 shadow-sm" style="border-radius: var(--radius-md);">
                        <h5 class="fw-bold mb-3"><i class="bi bi-receipt text-warning me-1"></i> Booking Summary</h5>

                        <div class="py-2 border-bottom">
                            <span class="text-muted small">Total Duration</span>
                            <div class="fw-bold text-dark fs-5" id="side-duration">0 min</div>
                        </div>

                        <div class="py-2 border-bottom">
                            <span class="text-muted small">Subtotal Amount</span>
                            <div class="fw-bold text-dark fs-4" id="side-subtotal">₹0.00</div>
                        </div>

                        <div class="py-2 border-bottom">
                            <span class="text-muted small">Advance Payable (20%)</span>
                            <div class="fw-bold text-warning fs-5" id="side-advance">₹0.00</div>
                        </div>

                        <div class="mt-3">
                            <div class="small text-muted mb-1"><i class="bi bi-person me-1"></i> Specialist</div>
                            <div class="fw-semibold text-dark small" id="side-staff-name">Any Specialist</div>
                        </div>

                        <div class="mt-2">
                            <div class="small text-muted mb-1"><i class="bi bi-calendar-event me-1"></i> Slot</div>
                            <div class="fw-semibold text-dark small" id="side-slot-display">Not Selected</div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-center">
                            <span class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-shield-check text-success me-1"></i> No double bookings guaranteed. Real-time database verification.
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- Booking Engine JS Script -->
<script>
let selectedServices = [];
let currentStep = 1;
let selectedStaffId = 0;
let selectedStaffName = 'Any Specialist';
let selectedSlotStart = '';
let selectedSlotDisplay = '';

document.addEventListener('DOMContentLoaded', () => {
    // Initial service if passed via URL
    <?php if ($selectedService): ?>
        addService({
            id: <?= (int)$selectedService['id'] ?>,
            name: "<?= e($selectedService['name']) ?>",
            category: "<?= e($selectedService['category_name']) ?>",
            price: <?= (float)$selectedService['price'] ?>,
            duration: <?= (int)$selectedService['duration_minutes'] ?>
        });
    <?php endif; ?>

    // Handle service dropdown add
    document.getElementById('service-picker').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (!opt.value) return;
        addService({
            id: parseInt(opt.value),
            name: opt.getAttribute('data-name'),
            category: opt.getAttribute('data-category'),
            price: parseFloat(opt.getAttribute('data-price')),
            duration: parseInt(opt.getAttribute('data-duration'))
        });
        this.value = '';
    });
});

function addService(srv) {
    if (selectedServices.some(s => s.id === srv.id)) return;
    selectedServices.push(srv);
    renderServicesList();
    updateSummary();
}

function removeService(id) {
    selectedServices = selectedServices.filter(s => s.id !== id);
    renderServicesList();
    updateSummary();
}

function renderServicesList() {
    const container = document.getElementById('selected-services-container');
    if (selectedServices.length === 0) {
        container.innerHTML = '<div class="text-muted small p-3 bg-light rounded text-center">No services selected yet. Choose a treatment above.</div>';
        return;
    }

    let html = '';
    selectedServices.forEach(s => {
        html += `
            <div class="p-3 rounded-3 border bg-white d-flex justify-content-between align-items-center shadow-sm">
                <div>
                    <span class="badge bg-warning text-dark text-uppercase" style="font-size: 0.65rem;">${s.category}</span>
                    <div class="fw-bold text-dark small">${s.name}</div>
                    <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> ${s.duration} mins</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="fw-bold text-dark">₹${s.price.toFixed(2)}</div>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeService(${s.id})" title="Remove service">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

function updateSummary() {
    let totalDuration = 0;
    let totalPrice = 0;
    let ids = [];

    selectedServices.forEach(s => {
        totalDuration += s.duration;
        totalPrice += s.price;
        ids.push(s.id);
    });

    document.getElementById('input-services').value = ids.join(',');
    document.getElementById('side-duration').innerText = totalDuration + ' mins';
    document.getElementById('side-subtotal').innerText = '₹' + totalPrice.toFixed(2);
    
    let advance = Math.round(totalPrice * 0.20);
    if (advance < 200 && totalPrice >= 200) advance = 200;
    if (advance > totalPrice) advance = totalPrice;
    document.getElementById('side-advance').innerText = '₹' + advance.toFixed(2);
}

function goToStep(step) {
    if (step === 2 && selectedServices.length === 0) {
        alert('Please select at least one treatment before proceeding.');
        return;
    }

    if (step === 3 && selectedStaffId === 0) {
        // Auto pick first staff if not manually clicked
        const firstCard = document.querySelector('.staff-select-card');
        if (firstCard) {
            firstCard.click();
        }
    }

    if (step === 4 && !selectedSlotStart) {
        alert('Please choose an available time slot for your appointment.');
        return;
    }

    currentStep = step;

    // Hide all step cards
    document.querySelectorAll('.booking-step-card').forEach(c => c.classList.add('d-none'));
    document.getElementById('card-step-' + step).classList.remove('d-none');

    // Update Stepper nav circles
    for (let i = 1; i <= 5; i++) {
        const nav = document.getElementById('step-nav-' + i);
        if (i < step) {
            nav.className = 'stepper-step completed';
        } else if (i === step) {
            nav.className = 'stepper-step active';
        } else {
            nav.className = 'stepper-step';
        }
    }

    if (step === 3) {
        fetchTimeSlots();
    }

    if (step === 5) {
        renderReviewSummary();
    }
}

function selectStaff(id, name) {
    selectedStaffId = id;
    selectedStaffName = name;
    document.getElementById('input-staff-id').value = id;
    document.getElementById('side-staff-name').innerText = name;

    document.querySelectorAll('.staff-select-card').forEach(c => {
        c.classList.remove('border-warning', 'bg-light');
    });

    const activeCard = document.querySelector(`[data-staff-id="${id}"]`);
    if (activeCard) {
        activeCard.classList.add('border-warning', 'bg-light');
    }
}

function fetchTimeSlots() {
    const date = document.getElementById('date-picker-input').value;
    document.getElementById('input-date').value = date;

    let totalDuration = 0;
    selectedServices.forEach(s => totalDuration += s.duration);

    if (selectedStaffId === 0 || !date) return;

    const slotsContainer = document.getElementById('slots-container');
    const loading = document.getElementById('slots-loading');

    slotsContainer.innerHTML = '';
    loading.classList.remove('d-none');

    fetch(`/api/booking/slots?staff_id=${selectedStaffId}&date=${date}&duration=${totalDuration}`)
        .then(res => res.json())
        .then(data => {
            loading.classList.add('d-none');
            if (data.success && data.slots.length > 0) {
                let html = '';
                data.slots.forEach(slot => {
                    const isSelected = selectedSlotStart === slot.start_time;
                    html += `
                        <div class="col-6 col-md-3">
                            <button type="button" class="btn btn-outline-dark w-100 py-2 slot-btn ${isSelected ? 'btn-warning text-dark active' : ''}"
                                    onclick="selectSlot('${slot.start_time}', '${slot.display}')">
                                <i class="bi bi-clock me-1"></i> ${slot.display}
                            </button>
                        </div>
                    `;
                });
                slotsContainer.innerHTML = html;
            } else {
                slotsContainer.innerHTML = `
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-3 text-warning"></i>
                        <p class="mb-0 mt-2">No available time slots for the selected date and staff duration.</p>
                        <small>Please select a different date or choose another specialist.</small>
                    </div>
                `;
            }
        })
        .catch(err => {
            loading.classList.add('d-none');
            slotsContainer.innerHTML = '<div class="col-12 text-danger small">Error checking availability slots. Please try again.</div>';
        });
}

function selectSlot(startTime, display) {
    selectedSlotStart = startTime;
    selectedSlotDisplay = display;
    document.getElementById('input-start-time').value = startTime;
    document.getElementById('side-slot-display').innerText = display;

    document.querySelectorAll('.slot-btn').forEach(b => {
        b.classList.remove('btn-warning', 'text-dark', 'active');
        b.classList.add('btn-outline-dark');
    });

    event.target.classList.remove('btn-outline-dark');
    event.target.classList.add('btn-warning', 'text-dark', 'active');
}

function selectServiceMode(mode) {
    document.getElementById('input-service-mode').value = mode;

    document.getElementById('mode-in-parlor').classList.remove('border-warning', 'bg-light');
    document.getElementById('mode-home-service').classList.remove('border-warning', 'bg-light');

    if (mode === 'home_service') {
        document.getElementById('mode-home-service').classList.add('border-warning', 'bg-light');
        document.getElementById('radioHomeService').checked = true;
        document.getElementById('home-address-section').classList.remove('d-none');
    } else {
        document.getElementById('mode-in-parlor').classList.add('border-warning', 'bg-light');
        document.getElementById('radioInParlor').checked = true;
        document.getElementById('home-address-section').classList.add('d-none');
    }
}

function renderReviewSummary() {
    const date = document.getElementById('input-date').value;
    document.getElementById('summary-datetime').innerText = date + ' at ' + selectedSlotDisplay;
    document.getElementById('summary-staff').innerText = 'Specialist: ' + selectedStaffName;

    const mode = document.getElementById('input-service-mode').value;
    if (mode === 'home_service') {
        document.getElementById('summary-mode').innerText = 'Doorstep Home Service';
        const addr = document.getElementById('home_address').value;
        document.getElementById('summary-address').innerText = 'Delivery to: ' + (addr || 'Saved Client Address');
    } else {
        document.getElementById('summary-mode').innerText = 'In-Parlor Salon Visit';
        document.getElementById('summary-address').innerText = 'Studio Location: Bandra West, Mumbai';
    }

    const list = document.getElementById('summary-services-list');
    let html = '';
    selectedServices.forEach(s => {
        html += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold small text-dark">${s.name}</span>
                    <div class="text-muted" style="font-size: 0.72rem;">Duration: ${s.duration} mins</div>
                </div>
                <span class="fw-bold text-dark">₹${s.price.toFixed(2)}</span>
            </li>
        `;
    });
    list.innerHTML = html;
}
</script>

<style>
.booking-stepper {
    margin-bottom: 2rem;
}
.booking-stepper::before {
    content: '';
    position: absolute;
    top: 18px;
    left: 40px;
    right: 40px;
    height: 2px;
    background: #EAE6E1;
    z-index: 0;
}
.stepper-step {
    position: relative;
    z-index: 1;
    text-align: center;
    background: #FAF8F5;
    padding: 0 10px;
}
.stepper-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #FFFFFF;
    border: 2px solid #EAE6E1;
    color: #7A726D;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin: 0 auto 6px;
    font-size: 0.9rem;
}
.stepper-step.active .stepper-circle {
    background: var(--color-gold);
    border-color: var(--color-gold);
    color: #FFFFFF;
}
.stepper-step.completed .stepper-circle {
    background: #151312;
    border-color: #151312;
    color: #FFFFFF;
}
.stepper-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #7A726D;
}
.stepper-step.active .stepper-label {
    color: var(--color-charcoal-900);
}
.cursor-pointer {
    cursor: pointer;
}
</style>
