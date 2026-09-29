<?php
declare(strict_types=1);

namespace App\Services;

use Core\Database;
use PDO;

class AvailabilityService
{
    /**
     * Get available time slots for a staff member on a specific date for a given total duration.
     *
     * @param int $staffId
     * @param string $date YYYY-MM-DD
     * @param int $durationMinutes Total required booking duration in minutes
     * @param int $slotIntervalMinutes Grid step in minutes (e.g. 15 or 30 mins)
     * @return array Array of slot arrays e.g. [['start' => '10:00', 'end' => '11:15', 'display' => '10:00 AM'], ...]
     */
    public static function getAvailableSlots(
        int $staffId,
        string $date,
        int $durationMinutes,
        int $slotIntervalMinutes = 30
    ): array {
        if ($durationMinutes <= 0 || empty($date)) {
            return [];
        }

        // 1. Check if date is in the past
        $today = date('Y-m-d');
        if ($date < $today) {
            return [];
        }

        // 2. Check if salon is closed on this holiday
        $db = Database::getConnection();
        $holidayStmt = $db->prepare("SELECT is_closed FROM salon_holidays WHERE holiday_date = :date LIMIT 1");
        $holidayStmt->execute(['date' => $date]);
        $holiday = $holidayStmt->fetch();
        if ($holiday && (int)$holiday['is_closed'] === 1) {
            return [];
        }

        // 3. Get Staff Working Hours for the day of week (0=Sunday ... 6=Saturday)
        $dayOfWeek = (int)date('w', strtotime($date));
        $hoursStmt = $db->prepare(
            "SELECT start_time, end_time, is_day_off 
             FROM staff_working_hours 
             WHERE staff_id = :staff_id AND day_of_week = :dow 
             LIMIT 1"
        );
        $hoursStmt->execute(['staff_id' => $staffId, 'dow' => $dayOfWeek]);
        $workingHours = $hoursStmt->fetch();

        if (!$workingHours || (int)$workingHours['is_day_off'] === 1) {
            return [];
        }

        $workStartTs = strtotime("{$date} {$workingHours['start_time']}");
        $workEndTs   = strtotime("{$date} {$workingHours['end_time']}");

        // 4. Fetch Staff Daily Breaks for this day of week
        $breaksStmt = $db->prepare(
            "SELECT break_start, break_end 
             FROM staff_breaks 
             WHERE staff_id = :staff_id AND day_of_week = :dow"
        );
        $breaksStmt->execute(['staff_id' => $staffId, 'dow' => $dayOfWeek]);
        $breaks = $breaksStmt->fetchAll();

        $blockedRanges = [];
        foreach ($breaks as $b) {
            $blockedRanges[] = [
                'start' => strtotime("{$date} {$b['break_start']}"),
                'end'   => strtotime("{$date} {$b['break_end']}")
            ];
        }

        // 5. Fetch Staff Specific Time Blocks / Leaves for this date
        $blocksStmt = $db->prepare(
            "SELECT start_time, end_time 
             FROM staff_time_blocks 
             WHERE staff_id = :staff_id AND block_date = :date"
        );
        $blocksStmt->execute(['staff_id' => $staffId, 'date' => $date]);
        $timeBlocks = $blocksStmt->fetchAll();

        foreach ($timeBlocks as $tb) {
            $blockedRanges[] = [
                'start' => strtotime("{$date} {$tb['start_time']}"),
                'end'   => strtotime("{$date} {$tb['end_time']}")
            ];
        }

        // 6. Fetch Existing Active Appointments for staff on this date
        $apptsStmt = $db->prepare(
            "SELECT start_time, end_time 
             FROM appointments 
             WHERE staff_id = :staff_id 
               AND appointment_date = :date 
               AND status NOT IN ('cancelled', 'no_show')"
        );
        $apptsStmt->execute(['staff_id' => $staffId, 'date' => $date]);
        $existingAppts = $apptsStmt->fetchAll();

        foreach ($existingAppts as $app) {
            $blockedRanges[] = [
                'start' => strtotime("{$date} {$app['start_time']}"),
                'end'   => strtotime("{$date} {$app['end_time']}")
            ];
        }

        // 7. Calculate Candidate Slots
        $durationSec = $durationMinutes * 60;
        $stepSec = $slotIntervalMinutes * 60;
        $nowTs = time();

        $availableSlots = [];
        for ($slotStart = $workStartTs; ($slotStart + $durationSec) <= $workEndTs; $slotStart += $stepSec) {
            $slotEnd = $slotStart + $durationSec;

            // If the date is today, slot must start after current time + 15 min buffer
            if ($date === $today && $slotStart <= ($nowTs + 15 * 60)) {
                continue;
            }

            // Check overlap with all blocked ranges (Breaks, Blocks, Existing Appts)
            $isOverlap = false;
            foreach ($blockedRanges as $range) {
                // Overlap occurs if slotStart < rangeEnd AND slotEnd > rangeStart
                if ($slotStart < $range['end'] && $slotEnd > $range['start']) {
                    $isOverlap = true;
                    break;
                }
            }

            if (!$isOverlap) {
                $availableSlots[] = [
                    'start_time' => date('H:i:s', $slotStart),
                    'end_time' => date('H:i:s', $slotEnd),
                    'display' => date('g:i A', $slotStart),
                    'display_end' => date('g:i A', $slotEnd),
                ];
            }
        }

        return $availableSlots;
    }

    /**
     * Atomically check availability and create appointment inside a DB transaction to prevent race conditions.
     *
     * @param array $bookingData
     * @return array ['success' => bool, 'message' => string, 'booking_reference' => ?string, 'appointment_id' => ?int]
     */
    public static function createAppointmentAtomic(array $bookingData): array
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $staffId = (int)$bookingData['staff_id'];
            $customerId = (int)$bookingData['customer_id'];
            $date = $bookingData['appointment_date'];
            $startTime = $bookingData['start_time'];
            $durationMinutes = (int)$bookingData['total_duration_minutes'];
            $serviceMode = $bookingData['service_mode'] ?? 'in_parlor';
            $homeAddress = $bookingData['home_address'] ?? null;
            $homePhone = $bookingData['home_contact_phone'] ?? null;
            $customerNotes = $bookingData['customer_notes'] ?? null;
            $services = $bookingData['services'] ?? [];

            if (empty($services)) {
                $db->rollBack();
                return ['success' => false, 'message' => 'No services selected for booking.'];
            }

            $startTs = strtotime("{$date} {$startTime}");
            $endTs = $startTs + ($durationMinutes * 60);
            $endTime = date('H:i:s', $endTs);

            // 1. Lock staff appointments for the date to prevent concurrent double-booking
            $checkStmt = $db->prepare(
                "SELECT id, start_time, end_time 
                 FROM appointments 
                 WHERE staff_id = :staff_id 
                   AND appointment_date = :date 
                   AND status NOT IN ('cancelled', 'no_show')
                   AND (
                        (start_time < :end_time AND end_time > :start_time)
                   )
                 FOR UPDATE"
            );
            $checkStmt->execute([
                'staff_id' => $staffId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime
            ]);
            $conflicts = $checkStmt->fetchAll();

            if (!empty($conflicts)) {
                $db->rollBack();
                return [
                    'success' => false,
                    'message' => 'This time slot is no longer available. Please select a different time slot.'
                ];
            }

            // 2. Check breaks conflict
            $dayOfWeek = (int)date('w', strtotime($date));
            $breakStmt = $db->prepare(
                "SELECT id FROM staff_breaks 
                 WHERE staff_id = :staff_id AND day_of_week = :dow 
                   AND (break_start < :end_time AND break_end > :start_time)"
            );
            $breakStmt->execute([
                'staff_id' => $staffId,
                'dow' => $dayOfWeek,
                'start_time' => $startTime,
                'end_time' => $endTime
            ]);
            if ($breakStmt->fetch()) {
                $db->rollBack();
                return ['success' => false, 'message' => 'The selected slot overlaps with a staff break.'];
            }

            // 3. Recalculate price server-side from DB to guarantee price integrity
            $subtotal = 0.00;
            $serviceItems = [];
            foreach ($services as $srvId) {
                $srvStmt = $db->prepare("SELECT * FROM services WHERE id = :id AND is_active = 1");
                $srvStmt->execute(['id' => (int)$srvId]);
                $srv = $srvStmt->fetch();
                if (!$srv) {
                    $db->rollBack();
                    return ['success' => false, 'message' => 'Invalid or inactive service selected.'];
                }
                $subtotal += (float)$srv['price'];
                $serviceItems[] = $srv;
            }

            // Calculate advance payable (e.g. 20% or min ₹200)
            $advanceRate = 0.20;
            $advancePaid = round($subtotal * $advanceRate, 2);
            if ($advancePaid < 200.00 && $subtotal >= 200.00) {
                $advancePaid = 200.00;
            }
            if ($advancePaid > $subtotal) {
                $advancePaid = $subtotal;
            }
            $balanceDue = $subtotal - $advancePaid;

            // 4. Generate unique human-friendly booking reference: YS-YYYYMMDD-XXXX
            $randomHex = strtoupper(bin2hex(random_bytes(2)));
            $bookingRef = 'YS-' . date('Ymd', strtotime($date)) . '-' . $randomHex;

            // 5. Insert Appointment
            $insertApptStmt = $db->prepare(
                "INSERT INTO appointments (
                    booking_reference, customer_id, staff_id, appointment_date, start_time, end_time,
                    total_duration_minutes, service_mode, home_address, home_contact_phone,
                    status, subtotal, tax_amount, discount_amount, advance_paid, balance_due, total_amount, customer_notes
                ) VALUES (
                    :ref, :cust_id, :staff_id, :app_date, :start_time, :end_time,
                    :duration, :mode, :address, :phone,
                    'confirmed', :subtotal, 0.00, 0.00, :advance, :balance, :total, :notes
                )"
            );
            $insertApptStmt->execute([
                'ref' => $bookingRef,
                'cust_id' => $customerId,
                'staff_id' => $staffId,
                'app_date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration' => $durationMinutes,
                'mode' => $serviceMode,
                'address' => $homeAddress,
                'phone' => $homePhone,
                'subtotal' => $subtotal,
                'advance' => $advancePaid,
                'balance' => $balanceDue,
                'total' => $subtotal,
                'notes' => $customerNotes
            ]);

            $appointmentId = (int)$db->lastInsertId();

            // 6. Insert Appointment Services line items
            $insertSrvStmt = $db->prepare(
                "INSERT INTO appointment_services (appointment_id, service_id, service_name, duration_minutes, price)
                 VALUES (:appt_id, :srv_id, :srv_name, :duration, :price)"
            );
            foreach ($serviceItems as $item) {
                $insertSrvStmt->execute([
                    'appt_id' => $appointmentId,
                    'srv_id' => $item['id'],
                    'srv_name' => $item['name'],
                    'duration' => $item['duration_minutes'],
                    'price' => $item['price']
                ]);
            }

            $db->commit();

            return [
                'success' => true,
                'message' => 'Appointment booked successfully!',
                'booking_reference' => $bookingRef,
                'appointment_id' => $appointmentId
            ];

        } catch (\Throwable $e) {
            $db->rollBack();
            return [
                'success' => false,
                'message' => 'An error occurred while creating your appointment. Please try again. (' . $e->getMessage() . ')'
            ];
        }
    }
}
