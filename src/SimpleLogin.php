<?php

declare(strict_types=1);

namespace BenjaminKobjolke\SimpleLogin;

/**
 * Simple Login - Main Class
 * Provides password-only authentication for PHP projects
 */
final class SimpleLogin
{
    private const DEFAULT_LABELS = [
        'lang' => 'en',
        'title' => 'Login',
        'heading' => 'Enter Password',
        'placeholder' => 'Password',
        'button' => 'Login',
        'wrong_password' => 'Wrong password',
        'locked' => 'Too many attempts. Try again in a few minutes.',
    ];

    /**
     * Guard method - require authentication to continue
     * Shows login page if not authenticated, otherwise continues execution
     *
     * Requires SIMPLE_LOGIN_PASSWORD or SIMPLE_LOGIN_PASSWORD_HASH to be defined before calling.
     */
    public static function requireAuth(): void
    {
        Session::start();

        // Handle logout request; ignore cross-site requests (e.g. <img src="...?logout"> on another site)
        if (isset($_GET['logout']) && ($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '') !== 'cross-site') {
            Session::logout();
            header('Location: ' . self::currentPath());
            exit;
        }

        $error = null;
        $password = $_POST['simple_login_password'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_string($password)) {
            if (Session::login($password)) {
                $redirect = defined('SIMPLE_LOGIN_REDIRECT')
                    ? SIMPLE_LOGIN_REDIRECT
                    : self::currentPath();
                header('Location: ' . $redirect);
                exit;
            }
            $error = 'wrong_password';
        }

        // If not authenticated, show login page and exit
        if (!Session::isAuthenticated()) {
            if (Session::isLocked()) {
                self::showLoginPage(429, 'locked');
            } else {
                self::showLoginPage(401, $error);
            }
            exit;
        }
    }

    /**
     * Check if user is authenticated (without blocking)
     */
    public static function isAuthenticated(): bool
    {
        return Session::isAuthenticated();
    }

    /**
     * Log out the user
     */
    public static function logout(): void
    {
        Session::logout();
    }

    /**
     * Current request path without query, limited to a local path so it can't become an open redirect
     */
    private static function currentPath(): string
    {
        $path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        return is_string($path) && preg_match('#^/(?![/\\\\])#', $path) === 1 ? $path : '/';
    }

    /**
     * Render the login page
     */
    private static function showLoginPage(int $status, ?string $error): void
    {
        http_response_code($status);
        $labels = array_merge(
            self::DEFAULT_LABELS,
            defined('SIMPLE_LOGIN_LABELS') ? SIMPLE_LOGIN_LABELS : []
        );
        $errorMessage = $error === null ? null : $labels[$error];
        require __DIR__ . '/../templates/login.php';
    }
}
