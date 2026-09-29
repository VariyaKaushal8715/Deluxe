<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use App\Models\Appointment;
use App\Models\Review;
use Core\Database;

class InvoiceController extends Controller
{
    public function __construct()
    {
        Auth::requireAuth('/login');
    }

    /**
     * Display printable digital invoice / receipt for an appointment
     */
    public function show(string $idOrRef): void
    {
        $user = Auth::user();

        if (is_numeric($idOrRef)) {
            $appt = Appointment::findById((int)$idOrRef);
        } else {
            $appt = Appointment::findByReference($idOrRef);
        }

        if (!$appt || ((int)$appt['customer_id'] !== (int)$user['id'] && !Auth::isAdmin())) {
            Session::flash('error', 'Invoice record not found or unauthorized access.');
            $this->redirect('/customer/invoices');
            return;
        }

        // Fetch invoice record
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM invoices WHERE appointment_id = :appt_id LIMIT 1");
        $stmt->execute(['appt_id' => $appt['id']]);
        $invoice = $stmt->fetch();

        // Fetch invoice line items
        $items = [];
        if ($invoice) {
            $itemStmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = :inv_id ORDER BY id ASC");
            $itemStmt->execute(['inv_id' => $invoice['id']]);
            $items = $itemStmt->fetchAll();
        }

        // Fetch payment history
        $payStmt = $db->prepare("SELECT * FROM payments WHERE appointment_id = :appt_id ORDER BY id ASC");
        $payStmt->execute(['appt_id' => $appt['id']]);
        $payments = $payStmt->fetchAll();

        // Check review eligibility
        $existingReview = null;
        if ($appt['status'] === 'completed') {
            $revStmt = $db->prepare("SELECT * FROM reviews WHERE appointment_id = :appt_id AND customer_id = :cust_id LIMIT 1");
            $revStmt->execute(['appt_id' => $appt['id'], 'cust_id' => $user['id']]);
            $existingReview = $revStmt->fetch();
        }

        $this->render('customer/invoice_detail', [
            'title' => 'Invoice ' . ($invoice['invoice_number'] ?? $appt['booking_reference']) . ' — Your Salon',
            'appt' => $appt,
            'invoice' => $invoice,
            'items' => $items,
            'payments' => $payments,
            'existingReview' => $existingReview,
            'currentUser' => $user
        ]);
    }
}
