<?php
/**
 * @var array $appointments
 * @var array $allStaff
 * @var array $allServices
 * @var array $allCustomers
 * @var string $startDate
 * @var string $endDate
 */
?>

<div class="py-4 bg-white border-bottom mb-4">
    <div class="container-fluid px-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Admin Portal</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold mb-0"><i class="bi bi-calendar-range text-warning me-2"></i> Salon Master Schedule & Calendar</h1>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-deluxe" data-bs-toggle="modal" data-bs-target="#walkInModal">
                    <i class="bi bi-plus-circle me-1"></i> New Walk-In Booking
                </button>
                <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#timeBlockModal">
                    <i class="bi bi-slash-circle me-1"></i> Add Time Block / Break
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 pb-5">
    <!-- Filter Date Bar -->
    <div class="card border p-3 shadow-sm mb-4" style="border-radius: var(--radius-md);">
        <form method="GET" action="/admin/calendar" class="row g-3 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">From Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= e($startDate) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-dark mb-1">To Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= e($endDate) ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-dark btn-sm w-100 py-2">
                    <i class="bi bi-filter me-1"></i> Filter Schedule
                </button>
            </div>
            <div class="col-md-3 d-flex align-items-end justify-content-end">
                <span class="badge bg-warning text-dark px-3 py-2 fw-bold">
                    Total Bookings: <?= count($appointments) ?>
                </span>
            </div>
        </form>
    </div>

    <!-- Calendar Table Grid -->
    <div class="card border shadow-sm" style="border-radius: var(--radius-lg); overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="py-3">Reference</th>
                        <th scope="col" class="py-3">Client</th>
                        <th scope="col" class="py-3">Treatments</th>
                        <th scope="col" class="py-3">Specialist</th>
                        <th scope="col" class="py-3">Date & Time</th>
                        <th scope="col" class="py-3">Mode</th>
                        <th scope="col" class="py-3">Total</th>
                        <th scope="col" class="py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-2 text-warning d-block mb-2"></i>
                                No scheduled appointments found for this date range.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $app): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark"><?= e($app['booking_reference']) ?></span>
                                    <div class="text-muted" style="font-size: 0.72rem;"><?= date('M d, H:i', strtotime($app['created_at'])) ?></div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($app['customer_name']) ?></div>
                                    <div class="text-muted small"><?= e($app['customer_phone']) ?></div>
                                </td>
                                <td>
                                    <?php if (!empty($app['services'])): ?>
                                        <?php foreach ($app['services'] as $srv): ?>
                                            <span class="badge bg-light text-dark border me-1 mb-1"><?= e($srv['service_name']) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">Standard Treatment</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= e($app['staff_name']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= date('M d, Y', strtotime($app['appointment_date'])) ?></div>
                                    <div class="text-secondary small"><i class="bi bi-clock me-1"></i> <?= date('g:i A', strtotime($app['start_time'])) ?> - <?= date('g:i A', strtotime($app['end_time'])) ?></div>
                                </td>
                                <td>
                                    <?php if ($app['service_mode'] === 'home_service'): ?>
                                        <span class="badge bg-info-subtle text-info border border-info px-2 py-1"><i class="bi bi-house-door me-1"></i> Home Service</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-dark border px-2 py-1"><i class="bi bi-building me-1"></i> In-Parlor</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= currency($app['total_amount']) ?></div>
                                    <div class="text-muted" style="font-size: 0.72rem;">Adv: <?= currency($app['advance_paid']) ?></div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusBadges = [
                                        'confirmed' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'rescheduled' => 'bg-info text-dark',
                                        'completed' => 'bg-primary',
                                        'cancelled' => 'bg-danger',
                                    ];
                                    $badge = $statusBadges[$app['status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $badge ?> text-uppercase px-2 py-1" style="font-size: 0.7rem;"><?= e($app['status']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal 1: Walk-In Booking -->
<div class="modal fade" id="walkInModal" tabindex="-1" aria-labelledby="walkInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/admin/walkin" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="walkInModalLabel"><i class="bi bi-plus-circle me-1 text-warning"></i> Create Walk-In Reservation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label small fw-bold text-dark">Select Client *</label>
                            <select name="customer_id" id="customer_id" class="form-select" required>
                                <option value="">-- Choose Client --</option>
                                <?php foreach ($allCustomers as $cust): ?>
                                    <option value="<?= (int)$cust['id'] ?>"><?= e($cust['name']) ?> (<?= e($cust['phone']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="admin_staff_id" class="form-label small fw-bold text-dark">Assign Specialist *</label>
                            <select name="staff_id" id="admin_staff_id" class="form-select" required>
                                <option value="">-- Choose Staff --</option>
                                <?php foreach ($allStaff as $st): ?>
                                    <option value="<?= (int)$st['id'] ?>"><?= e($st['full_name']) ?> (<?= e($st['designation']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="admin_services" class="form-label small fw-bold text-dark">Select Services *</label>
                            <select name="services[]" id="admin_services" class="form-select" multiple required style="height: 120px;">
                                <?php foreach ($allServices as $srv): ?>
                                    <option value="<?= (int)$srv['id'] ?>">
                                        <?= e($srv['category_name']) ?>: <?= e($srv['name']) ?> — <?= currency($srv['price']) ?> (<?= duration_format((int)$srv['duration_minutes']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text small">Hold Ctrl / Cmd to select multiple services.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="admin_date" class="form-label small fw-bold text-dark">Date *</label>
                            <input type="date" name="appointment_date" id="admin_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="admin_start_time" class="form-label small fw-bold text-dark">Start Time *</label>
                            <input type="time" name="start_time" id="admin_start_time" class="form-control" value="10:00" required>
                        </div>

                        <div class="col-md-6">
                            <label for="admin_mode" class="form-label small fw-bold text-dark">Service Mode</label>
                            <select name="service_mode" id="admin_mode" class="form-select">
                                <option value="in_parlor">In-Parlor Salon Visit</option>
                                <option value="home_service">Doorstep Home Service</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="admin_notes" class="form-label small fw-bold text-dark">Admin Notes</label>
                            <input type="text" name="admin_notes" id="admin_notes" class="form-control" value="Walk-in Reservation">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-deluxe px-4">Create Walk-In Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Add Time Block / Break -->
<div class="modal fade" id="timeBlockModal" tabindex="-1" aria-labelledby="timeBlockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/time-block" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold" id="timeBlockModalLabel"><i class="bi bi-slash-circle me-1 text-warning"></i> Add Staff Time Block / Break</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="block_staff_id" class="form-label small fw-bold text-dark">Staff Member *</label>
                        <select name="staff_id" id="block_staff_id" class="form-select" required>
                            <option value="">-- Choose Staff --</option>
                            <?php foreach ($allStaff as $st): ?>
                                <option value="<?= (int)$st['id'] ?>"><?= e($st['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="block_date" class="form-label small fw-bold text-dark">Block Date *</label>
                        <input type="date" name="block_date" id="block_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label for="block_start_time" class="form-label small fw-bold text-dark">Start Time *</label>
                            <input type="time" name="start_time" id="block_start_time" class="form-control" value="13:00" required>
                        </div>
                        <div class="col-6">
                            <label for="block_end_time" class="form-label small fw-bold text-dark">End Time *</label>
                            <input type="time" name="end_time" id="block_end_time" class="form-control" value="14:00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label small fw-bold text-dark">Reason / Description</label>
                        <input type="text" name="reason" id="reason" class="form-control" placeholder="e.g. Personal Break, Maintenance, Emergency">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-deluxe px-4">Block Time Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>
