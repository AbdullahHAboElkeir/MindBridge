<?php

// ─── Application ───────────────────────────────────────────────
define('APP_NAME',    'MindBridge');
define('APP_VERSION', '1.0.0');

// BASE_URL: adjust if your folder differs.
// Project lives at htdocs/MindBridge/MindBridge → URL is /MindBridge/MindBridge
define('BASE_URL',    'http://localhost/MindBridge-main/');
define('BASE_PATH',   dirname(__DIR__));          // = htdocs/MindBridge/MindBridge

// ─── Database ──────────────────────────────────────────────────
define('DB_HOST',     'localhost');
define('DB_NAME',     'mindbridge');
define('DB_USER',     'root');
define('DB_PASS',     '');
define('DB_CHARSET',  'utf8mb4');

// ─── Session ───────────────────────────────────────────────────
define('SESSION_LIFETIME', 3600);   // 1 hour

// ─── Uploads ───────────────────────────────────────────────────
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('UPLOAD_URL',  BASE_URL  . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB

// ─── Pagination ────────────────────────────────────────────────
define('ITEMS_PER_PAGE', 10);

// ─── Crisis Keywords ───────────────────────────────────────────
define('CRISIS_KEYWORDS', [
    'suicide', 'suicidal', 'kill myself', 'end my life', 'want to die',
    'self harm', 'self-harm', 'hurt myself', 'overdose', 'no reason to live',
    'hopeless', 'worthless', 'can\'t go on', 'don\'t want to live'
]);

// ─── Mood Levels ───────────────────────────────────────────────
define('MOOD_LABELS', [
    1 => 'Very Low', 2 => 'Low', 3 => 'Somewhat Low',
    4 => 'Below Average', 5 => 'Average',
    6 => 'Above Average', 7 => 'Good',
    8 => 'Very Good', 9 => 'Great', 10 => 'Excellent'
]);

// ─── Error Reporting (turn off in production) ──────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1);
