<?php
declare(strict_types=1);

namespace Core;

class Auth
{
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_STAFF = 'staff';
    public const ROLE_ADMIN = 'admin';

    public static function check(): bool
    {
        return Session::has('user_id');
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function role(): ?string
    {
        $user = self::user();
        return $user['role'] ?? null;
    }

    public static function isCustomer(): bool
    {
        return self::role() === self::ROLE_CUSTOMER;
    }

    public static function isAdmin(): bool
    {
        return self::role() === self::ROLE_ADMIN;
    }

    public static function isStaff(): bool
    {
        return self::role() === self::ROLE_STAFF;
    }

    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user_id', (int)$user['id']);
        Session::set('user', [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'role' => $user['role'],
            'profile_image' => $user['profile_image'] ?? null,
            'address_line' => $user['address_line'] ?? null,
            'city' => $user['city'] ?? null,
            'pincode' => $user['pincode'] ?? null,
        ]);
    }

    public static function updateUserSession(array $updatedFields): void
    {
        $user = Session::get('user', []);
        foreach ($updatedFields as $key => $value) {
            $user[$key] = $value;
        }
        Session::set('user', $user);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function requireAuth(string $redirect = '/login'): void
    {
        if (!self::check()) {
            Session::setFlash('warning', 'Please sign in to access your account.');
            header("Location: {$redirect}");
            exit;
        }
    }

    public static function requireRole(string|array $roles, string $redirect = '/'): void
    {
        self::requireAuth();
        $roles = (array)$roles;
        if (!in_array(self::role(), $roles, true)) {
            Session::setFlash('danger', 'Access restricted. You do not have permission for this section.');
            header("Location: {$redirect}");
            exit;
        }
    }

    public static function requireCustomer(): void
    {
        self::requireRole(self::ROLE_CUSTOMER, '/');
    }

    public static function requireAdmin(): void
    {
        self::requireRole(self::ROLE_ADMIN, '/login');
    }
}
