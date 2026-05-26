<?php

class Router {
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, $handler): void {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
    }

    public function post(string $path, $handler): void {
        $this->routes['POST'][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $method, string $path): void {
        $normalizedPath = $this->normalizePath($path);
        $handler = $this->routes[$method][$normalizedPath] ?? null;

        if (!$handler) {
            http_response_code(404);
            echo 'Page not found';
            return;
        }

        if (is_array($handler)) {
            $controller = new $handler[0]();
            $action = $handler[1];
            $controller->$action();
            return;
        }

        call_user_func($handler);
    }

    private function normalizePath(string $path): string {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}

?>
