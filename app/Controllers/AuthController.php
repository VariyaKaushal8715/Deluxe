<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? '/admin/dashboard' : '/customer/profile');
            return;
        }

        $this->render('public/login', [
            'title' => 'Sign In — Deluxe Salon & Spa'
        ]);
    }

    public function handleLogin(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/login');
            return;
        }

        $post = $this->getPost();
        $email = strtolower(trim($post['email'] ?? ''));
        $password = $post['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::setFlash('danger', 'Please provide both your registered email and password.');
            $this->redirect('/login');
            return;
        }

        $user = User::findByEmail($email);

        if (!$user || !Auth::verifyPassword($password, $user['password_hash'])) {
            Session::setFlash('danger', 'Invalid email or password credentials. Please verify and try again.');
            $this->redirect('/login');
            return;
        }

        if ($user['status'] !== 'active') {
            Session::setFlash('danger', 'Your account is currently inactive or suspended. Please contact salon reception.');
            $this->redirect('/login');
            return;
        }

        // Successfully authenticated
        Auth::login($user);
        Session::setFlash('success', "Welcome back, {$user['name']}!");

        // Redirect based on user role
        if ($user['role'] === Auth::ROLE_ADMIN) {
            $this->redirect('/admin/dashboard');
        } elseif ($user['role'] === Auth::ROLE_STAFF) {
            $this->redirect('/admin/calendar');
        } else {
            $this->redirect('/customer/profile');
        }
    }

    public function register(): void
    {
        if (Auth::check()) {
            $this->redirect('/customer/profile');
            return;
        }

        $this->render('public/register', [
            'title' => 'Create Client Account — Deluxe Salon & Spa'
        ]);
    }

    public function handleRegister(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/register');
            return;
        }

        $post = $this->getPost();
        $name = trim($post['name'] ?? '');
        $email = strtolower(trim($post['email'] ?? ''));
        $phone = trim($post['phone'] ?? '');
        $password = $post['password'] ?? '';
        $confirmPassword = $post['password_confirmation'] ?? '';
        $gender = $post['gender'] ?? 'female';
        $addressLine = trim($post['address_line'] ?? '');
        $areaLocality = trim($post['area_locality'] ?? '');
        $city = trim($post['city'] ?? 'Mumbai');
        $pincode = trim($post['pincode'] ?? '');

        // Validation
        $errors = [];
        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Please enter your full name (at least 2 characters).';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif (User::emailExists($email)) {
            $errors[] = 'An account with this email address already exists. Please sign in instead.';
        }
        if (empty($phone) || strlen($phone) < 10) {
            $errors[] = 'Please enter a valid 10-digit mobile phone number.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long.';
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match. Please verify.';
        }

        if (!empty($errors)) {
            Session::setFlash('danger', implode('<br>', $errors));
            $this->redirect('/register');
            return;
        }

        $userId = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password_hash' => Auth::hashPassword($password),
            'role' => Auth::ROLE_CUSTOMER,
            'gender' => in_array($gender, ['female', 'male', 'other']) ? $gender : 'female',
            'address_line' => $addressLine ?: null,
            'area_locality' => $areaLocality ?: null,
            'city' => $city ?: 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => $pincode ?: null,
            'landmark' => trim($post['landmark'] ?? '') ?: null,
        ]);

        $createdUser = User::findById($userId);
        Auth::login($createdUser);

        Session::setFlash('success', 'Your client account was created successfully! Welcome to Deluxe Salon.');
        $this->redirect('/customer/profile');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::setFlash('info', 'You have been signed out safely. We look forward to seeing you soon.');
        $this->redirect('/');
    }

    // Google OAuth Sandbox Architecture
    public function googleAuthSandbox(): void
    {
        $this->render('public/auth_sandbox', [
            'title' => 'Google One-Tap / OAuth Architecture',
            'provider' => 'Google',
            'description' => 'In a live production environment, this route initiates the OAuth 2.0 redirect to accounts.google.com with Client ID and state token.',
            'mockUser' => [
                'name' => 'Aditi Sen',
                'email' => 'aditi.sen.demo@gmail.com',
                'phone' => '+91 98333 44555'
            ]
        ]);
    }

    // Phone OTP Sandbox Architecture
    public function phoneOtpSandbox(): void
    {
        $this->render('public/auth_sandbox', [
            'title' => 'Phone OTP Authentication Architecture',
            'provider' => 'SMS OTP Gateway',
            'description' => 'In a live production environment, this route integrates with SMS providers (such as Twilio or Fast2SMS) using a 6-digit cryptographic OTP verification step.',
            'mockUser' => [
                'name' => 'Karan Malhotra',
                'email' => 'karan.demo@gmail.com',
                'phone' => '+91 98111 22334'
            ]
        ]);
    }
}
