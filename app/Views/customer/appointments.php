<?php
/**
 * @var array $appointments
 */
?>

<div class="card border p-4 shadow-sm mb-4" style="border-radius: var(--radius-md);">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="h5 fw-bold mb-0"><i class="bi bi-calendar-event text-warning me-2"></i> My Appointments</h4>
        <a href="/book" class="btn btn-sm btn-deluxe"><i class="bi bi-plus"></i> Book New</a>
    </div>
    <p class="text-muted small mb-4">View your scheduled salon visits, service breakdown, and appointment statuses.</p>

    <?php if (empty($appointments)): ?>
        <div class="text-center py-5 bg-light rounded-3 p-4">
            <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
            <h5 class="fw-bold">No Appointments Found</h5>
            <p class="text-muted small mb-3">You don't have any appointments scheduled yet.</p>
            <a href="/services" class="btn btn-deluxe btn-sm px-3">Browse Services & Book</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small text-uppercase text-muted">
                    <tr>
                        <th>Booking Ref</th>
                        <th>Date & Time</th>
                        <th>Treatment(s)</th>
                        <th>Stylist</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appt): ?>
                        <?php
                            $badgeClass = match($appt['status']) {
                                'completed' => 'bg-success',
                                'confirmed' => 'bg-primary',
                                'pending' => 'bg-warning text-dark',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= e($appt['booking_reference']) ?></span></td>
                            <td>
                                <div class="fw-bold small"><?= date('M d, Y', strtotime($appt['appointment_date'])) ?></div>
                                <div class="text-muted small"><?= date('h:i A', strtotime($appt['start_time'])) ?></div>
                            </td>
                            <td><span class="small fw-semibold"><?= e($appt['service_names'] ?? 'Service') ?></span></td>
                            <td><span class="small"><?= e($appt['staff_name']) ?></span></td>
                            <td><span class="fw-bold small"><?= currency($appt['total_amount']) ?></span></td>
                            <td><span class="badge <?= $badgeClass ?> text-capitalize"><?= e($appt['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
