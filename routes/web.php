<?php
// routes/web.php

require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/SessionController.php';
require_once __DIR__ . '/../app/controllers/WellnessController.php';
require_once __DIR__ . '/../app/controllers/ForumController.php';
require_once __DIR__ . '/../app/controllers/ReportController.php';

$router = new Router();

// Auth routes
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'authenticate']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'store']);
$router->get('/logout', [AuthController::class, 'logout']);

// Dashboard
$router->get('/', [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

// User management
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/create', [UserController::class, 'create']);
$router->post('/users', [UserController::class, 'store']);
$router->get('/users/{id}', [UserController::class, 'show']);
$router->get('/users/{id}/edit', [UserController::class, 'edit']);
$router->post('/users/{id}', [UserController::class, 'update']);
$router->post('/users/{id}/delete', [UserController::class, 'delete']);

// Sessions
$router->get('/sessions', [SessionController::class, 'index']);
$router->get('/sessions/create', [SessionController::class, 'create']);
$router->post('/sessions', [SessionController::class, 'store']);
$router->get('/sessions/{id}', [SessionController::class, 'show']);

// Wellness
$router->get('/wellness', [WellnessController::class, 'index']);
$router->post('/mood', [WellnessController::class, 'storeMood']);
$router->get('/journals', [WellnessController::class, 'journals']);
$router->post('/journals', [WellnessController::class, 'storeJournal']);

// Forum
$router->get('/forum', [ForumController::class, 'index']);
$router->get('/forum/create', [ForumController::class, 'create']);
$router->post('/forum', [ForumController::class, 'store']);
$router->get('/forum/{id}', [ForumController::class, 'show']);
$router->post('/forum/{id}/comment', [ForumController::class, 'storeComment']);

// Reports
$router->get('/reports', [ReportController::class, 'index']);
$router->get('/reports/generate', [ReportController::class, 'generate']);

return $router;