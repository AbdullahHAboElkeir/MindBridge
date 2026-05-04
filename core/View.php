<?php
class View
{
    public function render(string $path, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = __DIR__ . '/../app/views/' . $path . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($viewFile);
            exit;
        }
        include __DIR__ . '/../app/views/layout/header.php';
        include $viewFile;
        include __DIR__ . '/../app/views/layout/footer.php';
    }
}
