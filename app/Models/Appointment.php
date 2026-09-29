<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Appointment extends Model
{
    public static function findById(int $id): ?array
    {
        $appt = self::queryOne(
            "SELECT a.*, s.full_name as staff_name, s.designation as staff_designation,
                    u.name as customer_name, u.email as customer_email, u.phone as customer_phone
             FROM appointments a
             JOIN staff s ON s.id = a.staff_id
             JOIN users u ON u.id = a.customer_id
             WHERE a.id = :id LIMIT 1",
            ['id' => $id]
        );

        if ($appt) {
            $appt['services'] = self::getAppointmentServices((int)$appt['id']);
        }

        return $appt;
    }

    public static function findByReference(string $ref): ?array
    {
        $appt = self::queryOne(
            "SELECT a.*, s.full_name as staff_name, s.designation as staff_designation,
                    u.name as customer_name, u.email as customer_email, u.phone as customer_phone
             FROM appointments a
             JOIN staff s ON s.id = a.staff_id
             JOIN users u ON u.id = a.customer_id
             WHERE a.booking_reference = :ref LIMIT 1",
            ['ref' => $ref]
        );

        if ($appt) {
            $appt['services'] = self::getAppointmentServices((int)$appt['id']);
        }

        return $appt;
    }

    public static function getAppointmentServices(int $appointmentId): array
    {
        return self::query(
            "SELECT * FROM appointment_services WHERE appointment_id = :id ORDER BY id ASC",
            ['id' => $appointmentId]
        );
    }

    public static function getForCustomer(int $customerId): array
    {
        $appts = self::query(
            "SELECT a.*, s.full_name as staff_name, s.designation as staff_designation
             FROM appointments a
             JOIN staff s ON s.id = a.staff_id
             WHERE a.customer_id = :cust_id
             ORDER BY a.appointment_date DESC, a.start_time DESC",
            ['cust_id' => $customerId]
        );

        foreach ($appts as &$app) {
            $app['services'] = self::getAppointmentServices((int)$app['id']);
        }

        return $appts;
    }

    public static function cancelAppointment(int $appointmentId, int $customerId, string $reason = 'Cancelled by client'): bool
    {
        return self::execute(
            "UPDATE appointments 
             SET status = 'cancelled', cancellation_reason = :reason, cancelled_at = NOW() 
             WHERE id = :id AND customer_id = :cust_id AND status IN ('pending', 'confirmed')",
            [
                'id' => $appointmentId,
                'cust_id' => $customerId,
                'reason' => $reason
            ]
        );
    }

    public static function reschedule(int $appointmentId, int $customerId, string $newDate, string $newStartTime, int $durationMinutes): bool
    {
        $startTs = strtotime("{$newDate} {$newStartTime}");
        $endTs = $startTs + ($durationMinutes * 60);
        $newEndTime = date('H:i:s', $endTs);

        return self::execute(
            "UPDATE appointments 
             SET appointment_date = :date, start_time = :start_time, end_time = :end_time, status = 'rescheduled'
             WHERE id = :id AND customer_id = :cust_id AND status IN ('pending', 'confirmed', 'rescheduled')",
            [
                'id' => $appointmentId,
                'cust_id' => $customerId,
                'date' => $newDate,
                'start_time' => $newStartTime,
                'end_time' => $newEndTime
            ]
        );
    }

    public static function getAllForAdmin(?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT a.*, s.full_name as staff_name, u.name as customer_name, u.phone as customer_phone
                FROM appointments a
                JOIN staff s ON s.id = a.staff_id
                JOIN users u ON u.id = a.customer_id";
        $params = [];

        if ($startDate && $endDate) {
            $sql .= " WHERE a.appointment_date BETWEEN :start AND :end";
            $params['start'] = $startDate;
            $params['end'] = $endDate;
        }

        $sql .= " ORDER BY a.appointment_date ASC, a.start_time ASC";

        $appts = self::query($sql, $params);
        foreach ($appts as &$app) {
            $app['services'] = self::getAppointmentServices((int)$app['id']);
        }

        return $appts;
    }
}
