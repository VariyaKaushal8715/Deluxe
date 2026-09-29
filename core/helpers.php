<?php
declare(strict_types=1);

// Global View and Template Helper Functions (Global Namespace)

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('currency')) {
    function currency(float|int|string $amount): string
    {
        return '₹' . number_format((float)$amount, 0);
    }
}

if (!function_exists('duration_format')) {
    function duration_format(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} mins";
        }
        $hours = floor($minutes / 60);
        $rem = $minutes % 60;
        return $rem > 0 ? "{$hours}h {$rem}m" : "{$hours}h";
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = \Core\Session::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
