<?php
declare(strict_types=1);

namespace Core;

abstract class Controller
{
    protected function render(string $viewPath, array $data = [], string $layout = 'main'): void
    {
        View::render($viewPath, $data, $layout);
    }

    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function validateCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!Session::validateCsrfToken($token)) {
            Session::setFlash('danger', 'Security validation expired. Please try submitting again.');
            return false;
        }
        return true;
    }

    protected function getPost(): array
    {
        return array_map(function ($val) {
            return is_string($val) ? trim($val) : $val;
        }, $_POST);
    }

    protected function getQuery(): array
    {
        return array_map(function ($val) {
            return is_string($val) ? trim($val) : $val;
        }, $_GET);
    }
}
