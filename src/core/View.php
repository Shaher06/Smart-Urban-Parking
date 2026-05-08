<?php

class View
{
    private string $viewPath;

    public function __construct()
    {
        $this->viewPath = BASE_PATH . '/views';
    }

    public function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $file = $this->viewPath . '/' . $template . '.php';
        if (!file_exists($file)) {
            include $this->viewPath . '/errors/404.php';
            return;
        }
        include $file;
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}