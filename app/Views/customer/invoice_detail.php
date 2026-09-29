<?php
/**
 * @var array $appt
 * @var ?array $invoice
 * @var array $items
 * @var array $payments
 * @var ?array $existingReview
 * @var array $currentUser
 */
?>

<div class="py-4 bg-white border-bottom d-print-none">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="/customer/invoices">Invoices</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= e($invoice['invoice_number'] ?? $appt['booking_reference']) ?></li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold mb-0">Tax Invoice & Digital Receipt</h1>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print / Save PDF
                </button>
                <a href="/customer/appointments" class="btn btn-deluxe">
                    <i class="bi bi-arrow-left me-1"></i> My Appointments
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Invoice Document Paper -->
            <div class="card border p-4 p-md-5 shadow-sm bg-white rounded-4 invoice-paper">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div>
                        <div class="deluxe-brand fs-3 fw-bold mb-1">
                            <i class="bi bi-gem text-warning"></i> YOUR <span>SALON</span>
                        </div>
                        <div class="text-secondary small">Luxury Beauty & Holistic Wellness</div>
                        <div class="text-muted small">104 Elegance Boulevard, Bandra West, Mumbai 400050</div>
                        <div class="text-muted small">Phone: +91 98765 43210 &bull; GSTIN: 27AAAAA0000A1Z5</div>
                    </div>

                    <div class="text-end">
                        <span class="badge bg-dark fs-6 font-monospace mb-2"><?= e($invoice['invoice_number'] ?? ('YS-INV-' . date('Y') . '-' . $appt['id'])) ?></span>
                        <div class="small fw-bold text-dark">Date: <?= date('M d, Y', strtotime($invoice['issued_date'] ?? $appt['created_at'])) ?></div>
                        <div class="small text-muted font-monospace">Booking Ref: <?= e($appt['booking_reference']) ?></div>
                    </div>
                </div>

                <!-- Bill To & Appointment Info -->
                <div class="row g-4 mb-4 pb-4 border-bottom">
                    <div class="col-md-6 border-end">
                        <div class="text-uppercase small fw-bold text-muted mb-2">Billed To (Client)</div>
                        <div class="fw-bold text-dark fs-5"><?= e($appt['customer_name']) ?></div>
                        <div class="text-secondary small"><?= e($appt['customer_email']) ?></div>
                        <div class="text-secondary small">Phone: <?= e($appt['customer_phone']) ?></div>
                        <?php if ($appt['service_mode'] === 'home_service' && !empty($appt['home_address'])): ?>
                            <div class="text-muted small mt-2">
                                <strong>Home Service Address:</strong> <?= e($appt['home_address']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 ps-md-4">
                        <div class="text-uppercase small fw-bold text-muted mb-2">Appointment Session</div>
                        <div class="fw-bold text-dark fs-5"><?= date('M d, Y', strtotime($appt['appointment_date'])) ?></div>
                        <div class="text-secondary small"><i class="bi bi-clock me-1"></i> <?= date('g:i A', strtotime($appt['start_time'])) ?> - <?= date('g:i A', strtotime($appt['end_time'])) ?></div>
                        <div class="text-secondary small">Specialist: <strong><?= e($appt['staff_name']) ?></strong> (<?= e($appt['staff_designation']) ?>)</div>
                        <div class="text-secondary small">Mode: <strong><?= $appt['service_mode'] === 'home_service' ? 'Doorstep Home Service' : 'In-Parlor Visit' ?></strong></div>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light small text-uppercase text-muted">
                            <tr>
                                <th>#</th>
                                <th>Treatment / Item Description</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $idx => $item): ?>
                                    <tr>
                                        <td><?= $idx + 1 ?></td>
                                        <td class="fw-semibold text-dark"><?= e($item['item_description']) ?></td>
                                        <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                        <td class="text-end"><?= currency($item['unit_price']) ?></td>
                                        <td class="text-end fw-bold"><?= currency($item['total_price']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php elseif (!empty($appt['services'])): ?>
                                <?php foreach ($appt['services'] as $idx => $srv): ?>
                                    <tr>
                                        <td><?= $idx + 1 ?></td>
                                        <td class="fw-semibold text-dark"><?= e($srv['service_name']) ?> (<?= duration_format((int)$srv['duration_minutes']) ?>)</td>
                                        <td class="text-center">1</td>
                                        <td class="text-end"><?= currency($srv['price']) ?></td>
                                        <td class="text-end fw-bold"><?= currency($srv['price']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Totals -->
                <div class="row justify-content-end mb-4">
                    <div class="col-md-5">
                        <div class="card p-3 border bg-light">
                            <div class="d-flex justify-content-between align-items-center py-1 small">
                                <span class="text-muted">Treatment Subtotal</span>
                                <span class="fw-bold text-dark"><?= currency($appt['subtotal']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1 small">
                                <span class="text-muted">GST Tax (0% Demo)</span>
                                <span class="fw-bold text-dark">₹0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1 small border-bottom pb-2">
                                <span class="text-muted">Discount</span>
                                <span class="fw-bold text-dark">-<?= currency($appt['discount_amount']) ?></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="fw-bold text-dark">Total Invoice Amount</span>
                                <span class="fw-bold text-dark fs-5"><?= currency($appt['total_amount']) ?></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center py-1 small text-success">
                                <span>Advance Paid / Settled</span>
                                <span class="fw-bold"><?= currency($appt['advance_paid']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1 small text-danger fw-bold">
                                <span>Balance Payable at Salon</span>
                                <span><?= currency($appt['balance_due']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Audit Trail -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-clock-history text-warning me-1"></i> Payment Transaction History</h6>
                    <?php if (empty($payments)): ?>
                        <div class="p-3 bg-light rounded border text-muted small">No payment transactions recorded yet. (Pay at Salon selected).</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered small mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pay Ref</th>
                                        <th>Method</th>
                                        <th>Txn ID</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $p): ?>
                                        <tr>
                                            <td class="font-monospace"><?= e($p['payment_reference']) ?></td>
                                            <td class="text-uppercase"><?= e($p['payment_method']) ?></td>
                                            <td class="font-monospace text-muted"><?= e($p['transaction_id'] ?? 'N/A') ?></td>
                                            <td class="fw-bold"><?= currency($p['amount']) ?></td>
                                            <td>
                                                <span class="badge <?= $p['payment_status'] === 'paid' ? 'bg-success' : 'bg-warning text-dark' ?> text-uppercase">
                                                    <?= e($p['payment_status']) ?>
                                                </span>
                                            </td>
                                            <td><?= e($p['paid_at'] ? date('M d, H:i', strtotime($p['paid_at'])) : date('M d, H:i', strtotime($p['created_at']))) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Verified Review Section for Completed Appointment -->
                <?php if ($appt['status'] === 'completed'): ?>
                    <div class="p-4 rounded-3 border bg-warning-subtle d-print-none mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="bi bi-patch-check-fill text-success me-1"></i> Verified Completed Visit</h6>
                                <p class="small text-muted mb-0">As a verified client, your feedback helps others discover great styling services.</p>
                            </div>
                            <?php if ($existingReview): ?>
                                <span class="badge bg-success px-3 py-2"><i class="bi bi-check2-circle me-1"></i> Review Submitted</span>
                            <?php else: ?>
                                <button type="button" class="btn btn-dark btn-sm px-3" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                    <i class="bi bi-star me-1"></i> Write Verified Review
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Footer Note -->
                <div class="mt-4 pt-3 border-top text-center text-muted small">
                    Thank you for choosing <strong>Your Salon</strong>. For invoice queries, please contact contact@yoursalon.com.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Write Verified Review -->
<?php if ($appt['status'] === 'completed' && !$existingReview): ?>
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/reviews/submit" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="appointment_id" value="<?= (int)$appt['id'] ?>">
                <input type="hidden" name="staff_id" value="<?= (int)$appt['staff_id'] ?>">
                <input type="hidden" name="service_id" value="<?= (int)($appt['services'][0]['service_id'] ?? 1) ?>">

                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title h6 fw-bold" id="reviewModalLabel"><i class="bi bi-star-fill text-warning me-1"></i> Submit Verified Review</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Overall Treatment Rating *</label>
                        <select name="rating" class="form-select" required>
                            <option value="5" selected>★★★★★ (5 Stars — Exceptional)</option>
                            <option value="4">★★★★☆ (4 Stars — Very Good)</option>
                            <option value="3">★★★☆☆ (3 Stars — Average)</option>
                            <option value="2">★★☆☆☆ (2 Stars — Poor)</option>
                            <option value="1">★☆☆☆☆ (1 Star — Terrible)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="review_text" class="form-label small fw-bold text-dark">Your Review Feedback *</label>
                        <textarea name="review_text" id="review_text" class="form-control" rows="4" placeholder="Share details about your hair cut, facial glow, or beautician service..." required></textarea>
                    </div>

                    <div class="p-3 bg-light rounded border text-muted small">
                        <i class="bi bi-shield-check text-success me-1"></i> Review will be marked as <strong>Verified Customer</strong> because it is linked to completed booking #<?= e($appt['booking_reference']) ?>.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-deluxe btn-sm px-4">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
