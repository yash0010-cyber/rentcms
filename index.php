<?php

declare(strict_types=1);

session_start();

$configPath = __DIR__ . '/config/config.php';
if (!file_exists($configPath)) {
    http_response_code(500);
    echo 'Configuration file missing. Copy config/config.example.php to config/config.php.';
    exit;
}

require_once $configPath;

$vendorAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/src/classes/' . $class . '.php',
        __DIR__ . '/src/controllers/' . $class . '.php',
        __DIR__ . '/src/models/' . $class . '.php',
        __DIR__ . '/src/helpers/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/properties', [PropertyController::class, 'index']);
$router->get('/properties/view', [PropertyController::class, 'show']);
$router->get('/owner/properties', [PropertyController::class, 'ownerIndex']);
$router->get('/owner/properties/create', [PropertyController::class, 'create']);
$router->post('/owner/properties/create', [PropertyController::class, 'store']);
$router->get('/owner/properties/edit', [PropertyController::class, 'edit']);
$router->post('/owner/properties/edit', [PropertyController::class, 'update']);

$router->get('/bookings', [BookingController::class, 'index']);
$router->post('/bookings/create', [BookingController::class, 'store']);

$router->post('/ratings/create', [RatingController::class, 'store']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'sendForgotPassword']);
$router->get('/reset-password', [AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);
$router->get('/verify-email', [AuthController::class, 'verifyEmail']);
$router->get('/dashboard', [AuthController::class, 'dashboard']);

$router->get('/admin/login', [AuthController::class, 'showAdminLogin']);
$router->post('/admin/login', [AuthController::class, 'adminLogin']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/properties', [AdminController::class, 'properties']);
$router->get('/admin/bookings', [AdminController::class, 'bookings']);
$router->get('/admin/members', [AdminController::class, 'members']);

$router->get('/owner/dashboard', [DashboardController::class, 'owner']);
$router->get('/tenant/dashboard', [DashboardController::class, 'tenant']);

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$basePath = rtrim(parse_url(APP_URL, PHP_URL_PATH) ?? '', '/');
if (!empty($basePath) && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath)) ?: '/';
}

$router->dispatch($_SERVER['REQUEST_METHOD'], $path);

?>
