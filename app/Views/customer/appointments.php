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
    <p class="text-muted small mb-4">Manage your scheduled salon visits, reschedule time slots, or cancel upcoming bookings.</p>

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
                        <th>Specialist</th>
                        <th>Mode</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appt): ?>
                        <?php
                            $badgeClass = match($appt['status']) {
                                'completed' => 'bg-success',
                                'confirmed' => 'bg-primary',
                                'pending' => 'bg-warning text-dark',
                                'rescheduled' => 'bg-info text-dark',
                                'cancelled' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                            $canManage = in_array($appt['status'], ['pending', 'confirmed', 'rescheduled']);
                        ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= e($appt['booking_reference']) ?></span></td>
                            <td>
                                <div class="fw-bold small"><?= date('M d, Y', strtotime($appt['appointment_date'])) ?></div>
                                <div class="text-muted small"><?= date('g:i A', strtotime($appt['start_time'])) ?> - <?= date('g:i A', strtotime($appt['end_time'])) ?></div>
                            </td>
                            <td><span class="small fw-semibold"><?= e($appt['service_names'] ?? 'Treatment') ?></span></td>
                            <td><span class="small"><?= e($appt['staff_name']) ?></span></td>
                            <td>
                                <?php if ($appt['service_mode'] === 'home_service'): ?>
                                    <span class="badge bg-info-subtle text-info border border-info" style="font-size: 0.7rem;"><i class="bi bi-house-door"></i> Home</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border" style="font-size: 0.7rem;"><i class="bi bi-building"></i> Salon</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-bold small"><?= currency($appt['total_amount']) ?></div>
                                <div class="text-muted" style="font-size: 0.7rem;">Adv: <?= currency($appt['advance_paid']) ?></div>
                            </td>
                            <td><span class="badge <?= $badgeClass ?> text-capitalize"><?= e($appt['status']) ?></span></td>
                            <td class="text-end">
                                <?php if ($canManage): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Manage
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#rescheduleModal<?= (int)$appt['id'] ?>">
                                                    <i class="bi bi-calendar-range me-2 text-primary"></i> Reschedule
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#cancelModal<?= (int)$appt['id'] ?>">
                                                    <i class="bi bi-x-circle me-2"></i> Cancel Appointment
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <!-- Reschedule Modal -->
                                    <div class="modal fade text-start" id="rescheduleModal<?= (int)$appt['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="/customer/appointments/reschedule" method="POST">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="appointment_id" value="<?= (int)$appt['id'] ?>">
                                                    <div class="modal-header bg-dark text-white">
                                                        <h5 class="modal-title h6 fw-bold">Reschedule Booking #<?= e($appt['booking_reference']) ?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark">New Appointment Date *</label>
                                                            <input type="date" name="new_date" class="form-control" min="<?= date('Y-m-d') ?>" value="<?= e($appt['appointment_date']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark">New Preferred Start Time *</label>
                                                            <input type="time" name="new_time" class="form-control" value="<?= e($appt['start_time']) ?>" required>
                                                        </div>
                                                        <div class="p-3 bg-light rounded border text-muted small">
                                                            <i class="bi bi-info-circle text-warning me-1"></i> Rescheduling checks real-time availability for specialist <strong><?= e($appt['staff_name']) ?></strong>.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-deluxe btn-sm">Confirm Reschedule</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cancel Modal -->
                                    <div class="modal fade text-start" id="cancelModal<?= (int)$appt['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="/customer/appointments/cancel" method="POST">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="appointment_id" value="<?= (int)$appt['id'] ?>">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title h6 fw-bold">Cancel Appointment #<?= e($appt['booking_reference']) ?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <p class="text-dark small mb-3">Are you sure you want to cancel this appointment reservation? The time slot will be released back to the schedule.</p>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold text-dark">Reason for Cancellation (Optional)</label>
                                                            <input type="text" name="reason" class="form-control" placeholder="e.g. Schedule conflict, feeling unwell">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Keep Booking</button>
                                                        <button type="submit" class="btn btn-danger btn-sm">Confirm Cancellation</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">--</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
