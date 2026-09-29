<?php
declare(strict_types=1);

namespace Core;

class View
{
    public static function render(string $viewPath, array $data = [], string $layout = 'main'): void
    {
        // Extract data variables into local scope
        extract($data);

        // Global app configuration available in views
        $app = require __DIR__ . '/../config/app.php';
        $currentUser = Auth::user();
        $isLoggedIn = Auth::check();
        $flashes = Session::getFlashes();
        $csrfToken = Session::generateCsrfToken();

        // Capture view content buffer
        ob_start();
        $fullViewFile = __DIR__ . '/../app/Views/' . ltrim($viewPath, '/') . '.php';
        if (!file_exists($fullViewFile)) {
            ob_end_clean();
            http_response_code(500);
            echo "View file not found: {$viewPath}";
            return;
        }
        require $fullViewFile;
        $content = ob_get_clean();

        // Render layout
        if ($layout) {
            $fullLayoutFile = __DIR__ . '/../app/Views/layouts/' . $layout . '.php';
            if (file_exists($fullLayoutFile)) {
                require $fullLayoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    public static function partial(string $partialPath, array $data = []): void
    {
        extract($data);
        $app = require __DIR__ . '/../config/app.php';
        $currentUser = Auth::user();
        $isLoggedIn = Auth::check();
        $csrfToken = Session::generateCsrfToken();

        $file = __DIR__ . '/../app/Views/partials/' . ltrim($partialPath, '/') . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
}

// Global View Helpers
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
        $token = Session::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
