<?php
$baseUrl = $baseUrl ?? rtrim(APP_URL, '/');
$title = $title ?? APP_NAME;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= htmlspecialchars($baseUrl); ?>/public/assets/css/app.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= htmlspecialchars($baseUrl); ?>/">Site name</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($baseUrl); ?>/">Homepage</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($baseUrl); ?>/properties">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($baseUrl); ?>/properties">Contact</a></li>
            </ul>
            <div class="d-flex gap-3 align-items-center">
                <?php if (Auth::isLoggedIn()): ?>
                    <a class="text-decoration-none" href="<?= htmlspecialchars($baseUrl); ?>/dashboard">Dashboard</a>
                    <a class="btn btn-outline-primary" href="<?= htmlspecialchars($baseUrl); ?>/logout">Logout</a>
                <?php else: ?>
                    <a class="text-decoration-none" href="<?= htmlspecialchars($baseUrl); ?>/login">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main>
    <?php if (!empty($flash)): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= htmlspecialchars($flash['type']); ?>">
                <?= htmlspecialchars($flash['message']); ?>
            </div>
        </div>
    <?php endif; ?>

    <?= $content; ?>
</main>

<footer class="footer bg-white border-top mt-5">
    <div class="container py-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
        <span class="text-muted">© <?= date('Y'); ?> RentCMS. All rights reserved.</span>
        <div class="d-flex gap-3">
            <a href="#" class="text-muted text-decoration-none">Privacy</a>
            <a href="#" class="text-muted text-decoration-none">Terms</a>
            <a href="#" class="text-muted text-decoration-none">Support</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= htmlspecialchars($baseUrl); ?>/public/assets/js/app.js"></script>
</body>
</html>
