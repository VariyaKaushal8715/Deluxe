<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Database;
use App\Models\Appointment;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct()
    {
        Auth::requireAuth('/login');
    }

    /**
     * Show Payment Checkout / Sandbox Page
     */
    public function checkout(): void
    {
        $appointmentId = (int)($_GET['appointment_id'] ?? 0);
        $user = Auth::user();

        $appt = Appointment::findById($appointmentId);

        if (!$appt || (int)$appt['customer_id'] !== (int)$user['id']) {
            Session::flash('error', 'Appointment not found or unauthorized access.');
            $this->redirect('/customer/appointments');
            return;
        }

        $this->render('public/payment_checkout', [
            'title' => 'Payment Checkout — Your Salon',
            'appt' => $appt,
            'currentUser' => $user
        ]);
    }

    /**
     * Handle POST submission for Payment (Full, Advance, Pay at Salon)
     */
    public function process(): void
    {
        $user = Auth::user();
        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $paymentType = trim($_POST['payment_type'] ?? 'advance');
        $paymentMethod = trim($_POST['payment_method'] ?? 'upi');
        $simulateFailure = !empty($_POST['simulate_failure']);

        if ($appointmentId <= 0) {
            Session::flash('error', 'Invalid appointment selected for payment.');
            $this->redirect('/customer/appointments');
            return;
        }

        $result = PaymentService::processPayment(
            $appointmentId,
            (int)$user['id'],
            $paymentType,
            $paymentMethod,
            $simulateFailure
        );

        if ($result['success']) {
            Session::flash('success', $result['message'] . ' Invoice Generated: ' . ($result['invoice_number'] ?? ''));
            $this->redirect('/customer/invoices/' . $appointmentId);
        } else {
            Session::flash('error', $result['message']);
            $this->redirect('/payment/checkout?appointment_id=' . $appointmentId);
        }
    }
}
