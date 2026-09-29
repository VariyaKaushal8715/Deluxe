<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use Core\Database;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(): void
    {
        $reviews = Review::getPublishedTestimonials(20);

        $this->render('public/reviews', [
            'title' => 'Client Reviews & Ratings — Your Salon',
            'reviews' => $reviews,
        ]);
    }

    public function submitReview(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Please sign in to submit a review.');
            $this->redirect('/login');
            return;
        }

        $user = Auth::user();
        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $serviceId = (int)($_POST['service_id'] ?? 0);
        $staffId = (int)($_POST['staff_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 5);
        $reviewText = trim($_POST['review_text'] ?? '');

        if ($appointmentId <= 0 || empty($reviewText)) {
            Session::flash('error', 'Please enter your review comments.');
            $this->redirect('/customer/appointments');
            return;
        }

        // Check appointment eligibility
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT * FROM appointments 
             WHERE id = :id AND customer_id = :cust_id AND status = 'completed' LIMIT 1"
        );
        $stmt->execute(['id' => $appointmentId, 'cust_id' => $user['id']]);
        $appt = $stmt->fetch();

        if (!$appt) {
            Session::flash('error', 'Only completed appointments can be reviewed.');
            $this->redirect('/customer/appointments');
            return;
        }

        // Duplicate check
        $dupStmt = $db->prepare(
            "SELECT id FROM reviews WHERE appointment_id = :appt_id AND customer_id = :cust_id LIMIT 1"
        );
        $dupStmt->execute(['appt_id' => $appointmentId, 'cust_id' => $user['id']]);
        if ($dupStmt->fetch()) {
            Session::flash('error', 'You have already submitted a review for this appointment.');
            $this->redirect('/customer/invoices/' . $appointmentId);
            return;
        }

        // Insert Verified Review
        $insertStmt = $db->prepare(
            "INSERT INTO reviews (appointment_id, service_id, staff_id, customer_id, rating, review_text, moderation_status, is_verified_customer)
             VALUES (:appt_id, :srv_id, :staff_id, :cust_id, :rating, :text, 'published', 1)"
        );
        $insertStmt->execute([
            'appt_id' => $appointmentId,
            'srv_id' => $serviceId,
            'staff_id' => $staffId,
            'cust_id' => $user['id'],
            'rating' => max(1, min(5, $rating)),
            'text' => $reviewText
        ]);

        Session::flash('success', 'Thank you! Your verified customer review has been published.');
        $this->redirect('/reviews');
    }
}
