<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use App\Models\Service;
use App\Models\Category;
use App\Models\Staff;
use App\Models\Appointment;
use App\Services\AvailabilityService;

class BookingController extends Controller
{
    public function index(): void
    {
        $serviceId = (int)($_GET['service'] ?? 0);
        $selectedService = null;
        if ($serviceId > 0) {
            $selectedService = Service::findById($serviceId);
        }

        $categories = Category::getAllActive();
        $allServices = Service::getAllActive();
        $allStaff = Staff::getAllActive();

        $this->render('public/book_preview', [
            'title' => 'Book Appointment — Your Salon',
            'selectedService' => $selectedService,
            'categories' => $categories,
            'allServices' => $allServices,
            'allStaff' => $allStaff,
            'currentUser' => Auth::user(),
        ]);
    }

    /**
     * AJAX endpoint to fetch available staff based on selected service IDs
     */
    public function getStaffForServices(): void
    {
        header('Content-Type: application/json');
        
        $serviceIds = $_GET['service_ids'] ?? [];
        if (!is_array($serviceIds)) {
            $serviceIds = explode(',', (string)$serviceIds);
        }
        $serviceIds = array_map('intval', array_filter($serviceIds));

        if (empty($serviceIds)) {
            echo json_encode(['success' => false, 'message' => 'No services selected', 'staff' => []]);
            return;
        }

        // Find categories for all selected services
        $db = \Core\Database::getConnection();
        $inQuery = implode(',', array_fill(0, count($serviceIds), '?'));
        $stmt = $db->prepare("SELECT DISTINCT category_id FROM services WHERE id IN ({$inQuery})");
        $stmt->execute($serviceIds);
        $catIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Find staff that specialize in ALL these categories (or all staff if no match)
        $allStaff = Staff::getAllActive();
        $eligibleStaff = [];

        foreach ($allStaff as $st) {
            $stSpecStmt = $db->prepare("SELECT category_id FROM staff_specializations WHERE staff_id = :st_id");
            $stSpecStmt->execute(['st_id' => $st['id']]);
            $stCatIds = $stSpecStmt->fetchAll(\PDO::FETCH_COLUMN);

            // Check if staff covers at least one selected category
            $intersection = array_intersect($catIds, $stCatIds);
            if (!empty($intersection) || empty($stCatIds)) {
                $eligibleStaff[] = $st;
            }
        }

        if (empty($eligibleStaff)) {
            $eligibleStaff = $allStaff; // Fallback to all staff if strict match is empty
        }

        echo json_encode(['success' => true, 'staff' => $eligibleStaff]);
    }

    /**
     * AJAX endpoint to calculate real available slots
     */
    public function getAvailableSlots(): void
    {
        header('Content-Type: application/json');

        $staffId = (int)($_GET['staff_id'] ?? 0);
        $date = trim($_GET['date'] ?? '');
        $durationMinutes = (int)($_GET['duration'] ?? 30);

        if ($staffId <= 0 || empty($date)) {
            echo json_encode(['success' => false, 'message' => 'Invalid staff or date.', 'slots' => []]);
            return;
        }

        $slots = AvailabilityService::getAvailableSlots($staffId, $date, $durationMinutes);

        echo json_encode([
            'success' => true,
            'staff_id' => $staffId,
            'date' => $date,
            'duration' => $durationMinutes,
            'slots' => $slots
        ]);
    }

    /**
     * POST handler for submitting a new appointment
     */
    public function submitBooking(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please sign in to confirm your appointment reservation.');
            $this->redirect('/login');
            return;
        }

        $user = Auth::user();
        $staffId = (int)($_POST['staff_id'] ?? 0);
        $date = trim($_POST['appointment_date'] ?? '');
        $startTime = trim($_POST['start_time'] ?? '');
        $serviceMode = trim($_POST['service_mode'] ?? 'in_parlor');
        $homeAddress = trim($_POST['home_address'] ?? '');
        $homePhone = trim($_POST['home_phone'] ?? $user['phone']);
        $notes = trim($_POST['customer_notes'] ?? '');
        
        $rawServices = $_POST['services'] ?? [];
        if (is_string($rawServices)) {
            $rawServices = explode(',', $rawServices);
        }
        $serviceIds = array_map('intval', array_filter($rawServices));

        if (empty($serviceIds) || $staffId <= 0 || empty($date) || empty($startTime)) {
            Session::flash('error', 'Please fill in all required booking fields (Services, Staff, Date, Time).');
            $this->redirect('/book');
            return;
        }

        if ($serviceMode === 'home_service' && empty($homeAddress)) {
            Session::flash('error', 'Please provide a valid delivery address for Home Service mode.');
            $this->redirect('/book');
            return;
        }

        // Calculate total duration server-side
        $totalDuration = 0;
        foreach ($serviceIds as $srvId) {
            $srv = Service::findById($srvId);
            if ($srv) {
                $totalDuration += (int)$srv['duration_minutes'];
            }
        }

        $bookingData = [
            'customer_id' => $user['id'],
            'staff_id' => $staffId,
            'appointment_date' => $date,
            'start_time' => $startTime,
            'total_duration_minutes' => $totalDuration,
            'service_mode' => $serviceMode,
            'home_address' => $homeAddress,
            'home_contact_phone' => $homePhone,
            'customer_notes' => $notes,
            'services' => $serviceIds
        ];

        $result = AvailabilityService::createAppointmentAtomic($bookingData);

        if ($result['success']) {
            Session::flash('success', 'Appointment Reserved! Booking Reference: ' . $result['booking_reference']);
            $this->redirect('/customer/appointments');
        } else {
            Session::flash('error', $result['message']);
            $this->redirect('/book');
        }
    }
}

