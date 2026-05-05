<?php

/**
 * Middleware — RBAC route protection
 */
class Middleware
{
    /**
     * Require the user to be logged in.
     * Redirects to login if not authenticated.
     */
    public static function requireAuth(): void
    {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Please log in to access that page.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Require a specific role (or array of roles).
     * @param string|array $roles
     */
    public static function requireRole(string|array $roles): void
    {
        self::requireAuth();

        $roles = (array) $roles;
        if (!in_array(Session::role(), $roles, true)) {
            http_response_code(403);
            $view = BASE_PATH . '/app/views/errors/403.php';
            if (file_exists($view)) {
                require_once $view;
            } else {
                // Fallback: redirect to dashboard with flash error
                Session::flash('error', 'You do not have permission to access that page.');
                header('Location: ' . BASE_URL . '/dashboard');
            }
            exit;
        }
    }

    /** Shorthand helpers */
    public static function requirePatient(): void   { self::requireRole('patient'); }
    public static function requireTherapist(): void { self::requireRole('therapist'); }
    public static function requireAdmin(): void     { self::requireRole('admin'); }

    public static function requirePatientOrTherapist(): void
    {
        self::requireRole(['patient', 'therapist']);
    }

    public static function requireAnyRole(): void
    {
        self::requireRole(['patient', 'therapist', 'admin']);
    }

    /**
     * Redirect already-logged-in users away from guest pages (login, register).
     */
    public static function guestOnly(): void
    {
        if (Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }
}
