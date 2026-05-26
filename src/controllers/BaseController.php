<?php

class BaseController {
    protected function render(string $view, array $data = []): void {
        $data['baseUrl'] = rtrim(APP_URL, '/');
        $content = $this->renderPartial($view, $data);
        $flash = $this->getFlash();
        $baseUrl = $data['baseUrl'];
        require __DIR__ . '/../views/layouts/app.php';
    }

    protected function renderPartial(string $view, array $data = []): string {
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewPath)) {
            return 'View not found';
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $viewPath;
        return ob_get_clean();
    }

    protected function redirect(string $path): void {
        $destination = rtrim(APP_URL, '/') . $path;
        header('Location: ' . $destination);
        exit;
    }

    protected function setFlash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function getFlash(): ?array {
        if (!isset($_SESSION['flash'])) {
            return null;
        }
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    protected function requireCsrfToken(): void {
        if (!isset($_POST['csrf_token']) || !Validator::verifyCSRFToken($_POST['csrf_token'])) {
            http_response_code(400);
            exit('Invalid CSRF token');
        }
    }
}

?>
