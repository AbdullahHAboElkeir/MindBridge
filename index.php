<?php

/**
 * MindBridge — Front Controller
 * Entry point for all requests routed via .htaccess
 */

// ─── Bootstrap ─────────────────────────────────────────────────────────────
require_once __DIR__ . '/config/config.php';

// ─── Autoloader ────────────────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $dirs = [
        __DIR__ . '/core/',
        __DIR__ . '/app/controllers/',
        __DIR__ . '/app/models/',
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ─── Session Start ─────────────────────────────────────────────────────────
Session::start();

// ─── Uploads directory ─────────────────────────────────────────────────────
if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0775, true);
}

// ─── Launch Router ─────────────────────────────────────────────────────────
new App();
