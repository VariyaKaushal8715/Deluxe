<?php
/**
 * @var array $invoices
 */
?>

<div class="card border p-4 shadow-sm mb-4" style="border-radius: var(--radius-md);">
    <h4 class="h5 fw-bold mb-3"><i class="bi bi-receipt text-warning me-2"></i> Billing & Receipts</h4>
    <p class="text-muted small mb-4">Official digital tax invoices and advance payment receipts for your salon visits.</p>

    <?php if (empty($invoices)): ?>
        <div class="text-center py-5 bg-light rounded-3 p-4">
            <i class="bi bi-receipt fs-1 text-muted mb-3 d-block"></i>
            <h5 class="fw-bold">No Invoices Issued Yet</h5>
            <p class="text-muted small mb-0">Invoices and payment receipts will be generated automatically upon booking confirmation.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small text-uppercase text-muted">
                    <tr>
                        <th>Invoice #</th>
                        <th>Booking Ref</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= e($inv['invoice_number']) ?></span></td>
                            <td><span class="small font-monospace"><?= e($inv['booking_reference']) ?></span></td>
                            <td><span class="small"><?= date('M d, Y', strtotime($inv['issued_date'])) ?></span></td>
                            <td><span class="fw-bold small"><?= currency($inv['total_amount']) ?></span></td>
                            <td><span class="badge bg-success text-capitalize"><?= e($inv['payment_status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
