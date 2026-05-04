<?php
$config = require __DIR__ . '/../../../config/config.php';
$baseUrl = $config['app']['base_url'];
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MindBridge - Mental Health Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="<?php echo $baseUrl; ?>?controller=dashboard&action=index">MindBridge</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <?php if ($user): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>?controller=dashboard&action=index">Dashboard</a></li>
                    <?php if ($user['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>?controller=user&action=index">Users</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>?controller=resource&action=index">Resources</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>?controller=forum&action=index">Community</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>?controller=file&action=index">Documents</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>?controller=auth&action=logout">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>?controller=auth&action=login">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>?controller=auth&action=register">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-4">
