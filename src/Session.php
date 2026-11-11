<?php

declare(strict_types=1);

namespace BenjaminKobjolke\SimpleLogin;

/**
 * Session Management Class
 * Handles user session, authentication state
 */
final class Session
{
    private const AUTH_KEY = 'simple_login_authenticated';
    private const FAILURES_KEY = 'simple_login_failures';
    private const LOCKED_UNTIL_KEY = 'simple_login_locked_until';
    private const MAX_FAILURES = 5;
    private const LOCK_SECONDS = 300;
    private const FAILURE_DELAY_SECONDS = 1;

    /**
     * Start the session with safe cookie defaults
     */
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        $lifetime = self::lifetime();
        ini_set('session.gc_maxlifetime', (string) $lifetime);
        ini_set('session.cookie_lifetime', (string) $lifetime);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        if (self::isHttps()) {
            ini_set('session.cookie_secure', '1');
        }

        // Private folder: another app's GC with a shorter gc_maxlifetime would delete our session files.
        if (defined('SIMPLE_LOGIN_SESSION_PATH')) {
            if (!is_dir(SIMPLE_LOGIN_SESSION_PATH)) {
                mkdir(SIMPLE_LOGIN_SESSION_PATH, 0700, true);
            }
            session_save_path(SIMPLE_LOGIN_SESSION_PATH);
        }
        if (defined('SIMPLE_LOGIN_SESSION_NAME')) {
            session_name(SIMPLE_LOGIN_SESSION_NAME);
        }
        if (defined('SIMPLE_LOGIN_COOKIE_PATH')) {
            ini_set('session.cookie_path', SIMPLE_LOGIN_COOKIE_PATH);
        }

        session_start();

        if (self::authenticatedInSession()) {
            self::renewCookie($lifetime);
        }
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(): bool
    {
        self::start();
        return self::authenticatedInSession();
    }

    /**
     * Check if login is locked after too many wrong passwords
     */
    public static function isLocked(): bool
    {
        self::start();
        return ($_SESSION[self::LOCKED_UNTIL_KEY] ?? 0) > time();
    }

    /**
     * Log in the user
     */
    public static function login(string $password): bool
    {
        self::start();
        if (self::isLocked()) {
            return false;
        }

        if (!self::passwordMatches($password)) {
            $failures = ($_SESSION[self::FAILURES_KEY] ?? 0) + 1;
            if ($failures >= self::MAX_FAILURES) {
                $_SESSION[self::LOCKED_UNTIL_KEY] = time() + self::LOCK_SECONDS;
                $failures = 0;
            }
            $_SESSION[self::FAILURES_KEY] = $failures;
            sleep(self::FAILURE_DELAY_SECONDS);
            return false;
        }

        // New ID prevents session fixation.
        session_regenerate_id(true);
        unset($_SESSION[self::FAILURES_KEY], $_SESSION[self::LOCKED_UNTIL_KEY]);
        $_SESSION[self::AUTH_KEY] = true;
        return true;
    }

    /**
     * Log out the user
     */
    public static function logout(): void
    {
        self::start();
        unset($_SESSION[self::AUTH_KEY]);
        session_destroy();
    }

    private static function passwordMatches(string $password): bool
    {
        if (defined('SIMPLE_LOGIN_PASSWORD_HASH')) {
            return password_verify($password, SIMPLE_LOGIN_PASSWORD_HASH);
        }
        if (defined('SIMPLE_LOGIN_PASSWORD')) {
            return hash_equals((string) SIMPLE_LOGIN_PASSWORD, $password);
        }
        return false;
    }

    private static function authenticatedInSession(): bool
    {
        return ($_SESSION[self::AUTH_KEY] ?? false) === true;
    }

    private static function lifetime(): int
    {
        return defined('SIMPLE_LOGIN_SESSION_LIFETIME')
            ? (int) SIMPLE_LOGIN_SESSION_LIFETIME
            : 31536000; // Default: 365 days
    }

    private static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (string) ($_SERVER['SERVER_PORT'] ?? '') === '443';
    }

    /**
     * PHP sends the cookie only when the session is created, and browsers cap its lifetime at
     * 400 days. Re-sending it on each visit makes the login slide.
     */
    private static function renewCookie(int $lifetime): void
    {
        if (!ini_get('session.use_cookies') || headers_sent()) {
            return;
        }
        $params = session_get_cookie_params();
        setcookie(session_name(), session_id(), [
            'expires' => time() + $lifetime,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'],
        ]);
    }
}
