<?php
use Core\Session;
$flashes = Session::getFlashes();
?>
<?php if (!empty($flashes)): ?>
    <div class="container mt-3">
        <?php foreach ($flashes as $type => $messages): ?>
            <?php 
                $bsClass = match($type) {
                    'success' => 'alert-success border-success-subtle',
                    'danger', 'error' => 'alert-danger border-danger-subtle',
                    'warning' => 'alert-warning border-warning-subtle',
                    default => 'alert-info border-info-subtle',
                };
                $iconClass = match($type) {
                    'success' => 'bi-check-circle-fill text-success',
                    'danger', 'error' => 'bi-exclamation-triangle-fill text-danger',
                    'warning' => 'bi-exclamation-circle-fill text-warning',
                    default => 'bi-info-circle-fill text-info',
                };
            ?>
            <?php foreach ($messages as $msg): ?>
                <div class="alert <?= $bsClass ?> alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                    <i class="bi <?= $iconClass ?> fs-5"></i>
                    <div><?= $msg ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
