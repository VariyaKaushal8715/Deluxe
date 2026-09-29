<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use App\Models\User;

class CustomerController extends Controller
{
    public function __construct()
    {
        Auth::requireAuth('/login');
    }

    public function profile(): void
    {
        $userId = Auth::id();
        $user = User::findById($userId);

        if (!$user) {
            Auth::logout();
            $this->redirect('/login');
            return;
        }

        $this->render('customer/profile', [
            'title' => 'My Profile & Address — Deluxe Salon',
            'user' => $user
        ], 'customer');
    }

    public function updateProfile(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/customer/profile');
            return;
        }

        $userId = Auth::id();
        $post = $this->getPost();

        $name = trim($post['name'] ?? '');
        $phone = trim($post['phone'] ?? '');
        $gender = $post['gender'] ?? 'female';
        $addressLine = trim($post['address_line'] ?? '');
        $areaLocality = trim($post['area_locality'] ?? '');
        $city = trim($post['city'] ?? 'Mumbai');
        $state = trim($post['state'] ?? 'Maharashtra');
        $pincode = trim($post['pincode'] ?? '');
        $landmark = trim($post['landmark'] ?? '');

        // Validation
        if (empty($name) || strlen($name) < 2) {
            Session::setFlash('danger', 'Please enter your valid full name.');
            $this->redirect('/customer/profile');
            return;
        }

        if (empty($phone) || strlen($phone) < 10) {
            Session::setFlash('danger', 'Please enter a valid 10-digit mobile contact number.');
            $this->redirect('/customer/profile');
            return;
        }

        User::updateProfile($userId, [
            'name' => $name,
            'phone' => $phone,
            'gender' => in_array($gender, ['female', 'male', 'other']) ? $gender : 'female',
            'address_line' => $addressLine ?: null,
            'area_locality' => $areaLocality ?: null,
            'city' => $city ?: 'Mumbai',
            'state' => $state ?: 'Maharashtra',
            'pincode' => $pincode ?: null,
            'landmark' => $landmark ?: null,
        ]);

        Auth::updateUserSession([
            'name' => $name,
            'phone' => $phone,
            'address_line' => $addressLine,
            'city' => $city,
            'pincode' => $pincode,
        ]);

        Session::setFlash('success', 'Your personal details and service address have been updated successfully.');
        $this->redirect('/customer/profile');
    }

    public function updatePassword(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/customer/profile');
            return;
        }

        $userId = Auth::id();
        $post = $this->getPost();
        $currentPassword = $post['current_password'] ?? '';
        $newPassword = $post['new_password'] ?? '';
        $confirmPassword = $post['confirm_password'] ?? '';

        $user = User::findByEmail(Auth::user()['email']);

        if (!$user || !Auth::verifyPassword($currentPassword, $user['password_hash'])) {
            Session::setFlash('danger', 'Your current password was entered incorrectly.');
            $this->redirect('/customer/profile');
            return;
        }

        if (strlen($newPassword) < 6) {
            Session::setFlash('danger', 'Your new password must be at least 6 characters in length.');
            $this->redirect('/customer/profile');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            Session::setFlash('danger', 'The new password and confirmation password do not match.');
            $this->redirect('/customer/profile');
            return;
        }

        User::updatePassword($userId, Auth::hashPassword($newPassword));

        Session::setFlash('success', 'Your password has been changed securely.');
        $this->redirect('/customer/profile');
    }

    public function appointments(): void
    {
        $userId = Auth::id();
        $appointments = \Core\Model::query(
            "SELECT a.*, s.full_name as staff_name, 
                    GROUP_CONCAT(asrv.service_name SEPARATOR ', ') as service_names
             FROM appointments a
             JOIN staff s ON s.id = a.staff_id
             LEFT JOIN appointment_services asrv ON asrv.appointment_id = a.id
             WHERE a.customer_id = :cust_id
             GROUP BY a.id
             ORDER BY a.appointment_date DESC, a.start_time DESC",
            ['cust_id' => $userId]
        );

        $this->render('customer/appointments', [
            'title' => 'My Appointments — Deluxe Salon',
            'appointments' => $appointments
        ], 'customer');
    }

    public function invoices(): void
    {
        $userId = Auth::id();
        $invoices = \Core\Model::query(
            "SELECT i.*, a.booking_reference
             FROM invoices i
             JOIN appointments a ON a.id = i.appointment_id
             WHERE i.customer_id = :cust_id
             ORDER BY i.issued_date DESC",
            ['cust_id' => $userId]
        );

        $this->render('customer/invoices', [
            'title' => 'My Invoices & Receipts — Deluxe Salon',
            'invoices' => $invoices
        ], 'customer');
    }
}
