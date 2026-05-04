<?php
$baseUrl = 'http://localhost/MindBridge/';
if (!empty($_SERVER['HTTP_HOST'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $path = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    $baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . ($path !== '/' ? $path : '') . '/';
}
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'mindbridge',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => $baseUrl,
    ],
];
