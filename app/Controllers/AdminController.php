<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Database;
use App\Models\Appointment;
use App\Models\Staff;
use App\Models\Service;
use App\Models\User;
use App\Services\AvailabilityService;

class AdminController extends Controller
{
    public function __construct()
    {
        Auth::requireRole('admin', '/login');
    }

    public function calendar(): void
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate = $_GET['end_date'] ?? date('Y-m-d', strtotime('+7 days'));

        $appointments = Appointment::getAllForAdmin($startDate, $endDate);
        $allStaff = Staff::getAllActive();
        $allServices = Service::getAllActive();
        $allCustomers = User::query("SELECT id, name, email, phone FROM users WHERE role = 'customer' ORDER BY name ASC");

        $this->render('admin/calendar', [
            'title' => 'Appointment Schedule & Calendar — Admin',
            'appointments' => $appointments,
            'allStaff' => $allStaff,
            'allServices' => $allServices,
            'allCustomers' => $allCustomers,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function walkInBooking(): void
    {
        $customerId = (int)($_POST['customer_id'] ?? 0);
        $staffId = (int)($_POST['staff_id'] ?? 0);
        $date = trim($_POST['appointment_date'] ?? '');
        $startTime = trim($_POST['start_time'] ?? '');
        $serviceMode = trim($_POST['service_mode'] ?? 'in_parlor');
        $notes = trim($_POST['admin_notes'] ?? 'Admin Walk-in Reservation');
        $rawServices = $_POST['services'] ?? [];
        if (is_string($rawServices)) {
            $rawServices = explode(',', $rawServices);
        }
        $serviceIds = array_map('intval', array_filter($rawServices));

        if ($customerId <= 0 || $staffId <= 0 || empty($serviceIds) || empty($date) || empty($startTime)) {
            Session::flash('error', 'Please complete all required walk-in booking details.');
            $this->redirect('/admin/calendar');
            return;
        }

        // Calculate total duration
        $totalDuration = 0;
        foreach ($serviceIds as $srvId) {
            $srv = Service::findById($srvId);
            if ($srv) {
                $totalDuration += (int)$srv['duration_minutes'];
            }
        }

        $bookingData = [
            'customer_id' => $customerId,
            'staff_id' => $staffId,
            'appointment_date' => $date,
            'start_time' => $startTime,
            'total_duration_minutes' => $totalDuration,
            'service_mode' => $serviceMode,
            'customer_notes' => $notes,
            'services' => $serviceIds
        ];

        $result = AvailabilityService::createAppointmentAtomic($bookingData);

        if ($result['success']) {
            Session::flash('success', 'Walk-in Appointment Created! Ref: ' . $result['booking_reference']);
        } else {
            Session::flash('error', $result['message']);
        }

        $this->redirect('/admin/calendar');
    }

    public function createTimeBlock(): void
    {
        $staffId = (int)($_POST['staff_id'] ?? 0);
        $date = trim($_POST['block_date'] ?? '');
        $startTime = trim($_POST['start_time'] ?? '');
        $endTime = trim($_POST['end_time'] ?? '');
        $reason = trim($_POST['reason'] ?? 'Staff Break / Maintenance');

        if ($staffId <= 0 || empty($date) || empty($startTime) || empty($endTime)) {
            Session::flash('error', 'Please fill in staff, date, start and end times for the block.');
            $this->redirect('/admin/calendar');
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare(
            "INSERT INTO staff_time_blocks (staff_id, block_date, start_time, end_time, reason)
             VALUES (:staff_id, :block_date, :start_time, :end_time, :reason)"
        );
        $stmt->execute([
            'staff_id' => $staffId,
            'block_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'reason' => $reason
        ]);

        Session::flash('success', 'Time Block added successfully! Availability updated for this staff.');
        $this->redirect('/admin/calendar');
    }
}
