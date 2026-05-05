<?php

/**
 * Session — static wrapper around PHP sessions
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }

        // Session timeout handling
        if (self::has('last_activity') && (time() - self::get('last_activity')) > SESSION_LIFETIME) {
            self::destroy();
            session_start();
        }

        self::set('last_activity', time());
    }

    public static function regenerate(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }
        session_regenerate_id(true);
    }

    public static function set(string $key, mixed $value): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }

        session_unset();
        session_destroy();
    }

    public static function flash(string $key, mixed $value): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    /** Is user logged in? */
    public static function isLoggedIn(): bool
    {
        return self::has('user_id');
    }

    /** Current user's role */
    public static function role(): ?string
    {
        return self::get('role');
    }

    /** Current user's ID */
    public static function userId(): ?int
    {
        return self::get('user_id');
    }
}
