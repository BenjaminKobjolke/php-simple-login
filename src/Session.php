<?php
namespace BenjaminKobjolke\SimpleLogin;

/**
 * Session Management Class
 * Handles user session, authentication state
 */
class Session
{
    /**
     * Start the session
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $lifetime = defined('SIMPLE_LOGIN_SESSION_LIFETIME')
                ? SIMPLE_LOGIN_SESSION_LIFETIME
                : 31536000; // Default: 365 days

            ini_set('session.gc_maxlifetime', $lifetime);
            ini_set('session.cookie_lifetime', $lifetime);

            session_start();
        }
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(): bool
    {
        self::start();
        return isset($_SESSION['simple_login_authenticated'])
            && $_SESSION['simple_login_authenticated'] === true;
    }

    /**
     * Log in the user
     */
    public static function login(string $password): bool
    {
        if (!defined('SIMPLE_LOGIN_PASSWORD')) {
            return false;
        }

        if ($password === SIMPLE_LOGIN_PASSWORD) {
            self::start();
            $_SESSION['simple_login_authenticated'] = true;
            return true;
        }
        return false;
    }

    /**
     * Log out the user
     */
    public static function logout(): void
    {
        self::start();
        unset($_SESSION['simple_login_authenticated']);
        session_destroy();
    }
}
