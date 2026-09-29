<?php
declare(strict_types=1);

namespace App\Services;

use Core\Database;
use App\Models\Appointment;
use App\Models\Service;
use Exception;

class PaymentService
{
    /**
     * Process a simulated/sandbox payment and atomically create Payment & Invoice records.
     *
     * @param int $appointmentId
     * @param int $customerId
     * @param string $paymentType 'full', 'advance', or 'pay_at_salon'
     * @param string $paymentMethod 'upi', 'card', 'net_banking', 'pay_at_salon'
     * @param bool $simulateFailure Set to true to test payment failure retry flow
     * @return array ['success' => bool, 'message' => string, 'payment_reference' => ?string, 'invoice_number' => ?string]
     */
    public static function processPayment(
        int $appointmentId,
        int $customerId,
        string $paymentType,
        string $paymentMethod,
        bool $simulateFailure = false
    ): array {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // 1. Fetch appointment details with row lock
            $stmt = $db->prepare("SELECT * FROM appointments WHERE id = :id AND customer_id = :cust_id FOR UPDATE");
            $stmt->execute(['id' => $appointmentId, 'cust_id' => $customerId]);
            $appt = $stmt->fetch();

            if (!$appt) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Appointment record not found or unauthorized access.'];
            }

            $totalAmount = (float)$appt['total_amount'];

            // Calculate payment amount based on paymentType
            if ($paymentType === 'full') {
                $payAmount = $totalAmount;
            } elseif ($paymentType === 'advance') {
                $payAmount = (float)$appt['advance_paid'];
                if ($payAmount <= 0) {
                    $payAmount = round($totalAmount * 0.20, 2);
                    if ($payAmount < 200.00 && $totalAmount >= 200.00) $payAmount = 200.00;
                    if ($payAmount > $totalAmount) $payAmount = $totalAmount;
                }
            } else {
                // Pay at Salon
                $paymentType = 'advance';
                $payAmount = 0.00;
            }

            // Generate Payment Reference: YS-PAY-YYYYMMDD-XXXX
            $randomHex = strtoupper(bin2hex(random_bytes(2)));
            $payRef = 'YS-PAY-' . date('Ymd') . '-' . $randomHex;

            // Simulated Failure handling
            if ($simulateFailure && $paymentMethod !== 'pay_at_salon') {
                $failedStmt = $db->prepare(
                    "INSERT INTO payments (
                        payment_reference, appointment_id, customer_id, amount, payment_type, payment_method, payment_status, gateway_note
                    ) VALUES (
                        :ref, :appt_id, :cust_id, :amount, :type, :method, 'failed', 'Simulated Bank Sandbox Failure'
                    )"
                );
                $failedStmt->execute([
                    'ref' => $payRef,
                    'appt_id' => $appointmentId,
                    'cust_id' => $customerId,
                    'amount' => $payAmount,
                    'type' => $paymentType,
                    'method' => $paymentMethod
                ]);

                $db->commit();
                return [
                    'success' => false,
                    'message' => 'Demo Payment Failed: Simulated bank transaction decline. You can retry with another method.'
                ];
            }

            // Determine status
            $paymentStatus = ($paymentMethod === 'pay_at_salon') ? 'pending' : 'paid';
            $paidAt = ($paymentStatus === 'paid') ? date('Y-m-d H:i:s') : null;
            $txnId = ($paymentStatus === 'paid') ? ('TXN-DEMO-' . time() . rand(100, 999)) : null;

            // 2. Insert Payment Record
            $payStmt = $db->prepare(
                "INSERT INTO payments (
                    payment_reference, appointment_id, customer_id, amount, payment_type, payment_method, payment_status, transaction_id, gateway_note, paid_at
                ) VALUES (
                    :ref, :appt_id, :cust_id, :amount, :type, :method, :status, :txn_id, :note, :paid_at
                )"
            );
            $payStmt->execute([
                'ref' => $payRef,
                'appt_id' => $appointmentId,
                'cust_id' => $customerId,
                'amount' => $payAmount,
                'type' => $paymentType,
                'method' => $paymentMethod,
                'status' => $paymentStatus,
                'txn_id' => $txnId,
                'note' => 'Demo Sandbox Payment Engine',
                'paid_at' => $paidAt
            ]);

            // 3. Update Appointment Status & Amounts
            $advancePaid = ($paymentStatus === 'paid') ? $payAmount : (float)$appt['advance_paid'];
            $balanceDue = max(0.00, $totalAmount - $advancePaid);
            $apptStatus = ($paymentStatus === 'paid' || $paymentMethod === 'pay_at_salon') ? 'confirmed' : 'pending';

            $updateApptStmt = $db->prepare(
                "UPDATE appointments 
                 SET advance_paid = :adv, balance_due = :bal, status = :status 
                 WHERE id = :id"
            );
            $updateApptStmt->execute([
                'adv' => $advancePaid,
                'bal' => $balanceDue,
                'status' => $apptStatus,
                'id' => $appointmentId
            ]);

            // 4. Generate or Update Invoice
            $invCheck = $db->prepare("SELECT id, invoice_number FROM invoices WHERE appointment_id = :appt_id");
            $invCheck->execute(['appt_id' => $appointmentId]);
            $existingInv = $invCheck->fetch();

            if ($existingInv) {
                $invoiceNum = $existingInv['invoice_number'];
                $invId = (int)$existingInv['id'];

                $invStatus = ($balanceDue <= 0.00) ? 'paid' : (($advancePaid > 0) ? 'partially_paid' : 'unpaid');
                $updateInv = $db->prepare(
                    "UPDATE invoices 
                     SET advance_paid = :adv, balance_amount = :bal, payment_status = :status 
                     WHERE id = :id"
                );
                $updateInv->execute([
                    'adv' => $advancePaid,
                    'bal' => $balanceDue,
                    'status' => $invStatus,
                    'id' => $invId
                ]);
            } else {
                // Generate Invoice Number: YS-INV-YYYY-XXXXX
                $invoiceNum = 'YS-INV-' . date('Y') . '-' . str_pad((string)$appointmentId, 5, '0', STR_PAD_LEFT);
                $invStatus = ($balanceDue <= 0.00) ? 'paid' : (($advancePaid > 0) ? 'partially_paid' : 'unpaid');

                $insertInv = $db->prepare(
                    "INSERT INTO invoices (
                        invoice_number, appointment_id, customer_id, subtotal, tax_rate, tax_amount, discount_amount, advance_paid, balance_amount, total_amount, payment_status, issued_date
                    ) VALUES (
                        :inv_num, :appt_id, :cust_id, :subtotal, 0.00, 0.00, 0.00, :adv, :bal, :total, :status, :issued_date
                    )"
                );
                $insertInv->execute([
                    'inv_num' => $invoiceNum,
                    'appt_id' => $appointmentId,
                    'cust_id' => $customerId,
                    'subtotal' => $totalAmount,
                    'adv' => $advancePaid,
                    'bal' => $balanceDue,
                    'total' => $totalAmount,
                    'status' => $invStatus,
                    'issued_date' => date('Y-m-d')
                ]);
                $invId = (int)$db->lastInsertId();

                // Insert Invoice Line Items from appointment services
                $srvStmt = $db->prepare("SELECT * FROM appointment_services WHERE appointment_id = :appt_id");
                $srvStmt->execute(['appt_id' => $appointmentId]);
                $srvItems = $srvStmt->fetchAll();

                $insertItem = $db->prepare(
                    "INSERT INTO invoice_items (invoice_id, item_description, item_type, quantity, unit_price, total_price)
                     VALUES (:inv_id, :desc, 'service', 1, :price, :price)"
                );
                foreach ($srvItems as $item) {
                    $insertItem->execute([
                        'inv_id' => $invId,
                        'desc' => $item['service_name'],
                        'price' => $item['price']
                    ]);
                }
            }

            $db->commit();

            return [
                'success' => true,
                'message' => ($paymentMethod === 'pay_at_salon') ? 'Appointment reserved for Pay-At-Salon!' : 'Demo Payment Processed Successfully!',
                'payment_reference' => $payRef,
                'invoice_number' => $invoiceNum,
                'payment_status' => $paymentStatus
            ];

        } catch (\Throwable $e) {
            $db->rollBack();
            return [
                'success' => false,
                'message' => 'Payment processing error: ' . $e->getMessage()
            ];
        }
    }
}
