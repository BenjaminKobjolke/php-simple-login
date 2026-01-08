<?php
namespace BenjaminKobjolke\SimpleLogin;

/**
 * Simple Login - Main Class
 * Provides password-only authentication for PHP projects
 */
class SimpleLogin
{
    /**
     * Guard method - require authentication to continue
     * Shows login page if not authenticated, otherwise continues execution
     *
     * Requires SIMPLE_LOGIN_PASSWORD constant to be defined before calling.
     */
    public static function requireAuth(): void
    {
        Session::start();

        // Handle logout request
        if (isset($_GET['logout'])) {
            Session::logout();
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
            exit;
        }

        // Handle login form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simple_login_password'])) {
            if (Session::login($_POST['simple_login_password'])) {
                $redirect = defined('SIMPLE_LOGIN_REDIRECT')
                    ? SIMPLE_LOGIN_REDIRECT
                    : $_SERVER['PHP_SELF'];
                header('Location: ' . $redirect);
                exit;
            }
        }

        // If not authenticated, show login page and exit
        if (!Session::isAuthenticated()) {
            self::showLoginPage();
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
     * Render the login page
     */
    private static function showLoginPage(): void
    {
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#121212">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --bg-card: #2a2a2a;
            --bg-card-hover: #353535;
            --text-primary: #e0e0e0;
            --text-secondary: #b0b0b0;
            --accent-primary: #bb86fc;
            --border-color: #404040;
            --shadow-lg: 0 8px 16px rgba(0, 0, 0, 0.6);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .login-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .login-container {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 3rem 2rem;
            box-shadow: var(--shadow-lg);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .login-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .login-container h2 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .login-container input {
            padding: 1rem 1.5rem;
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .login-container input:focus {
            border-color: var(--accent-primary);
            background: var(--bg-card-hover);
        }

        .login-container button {
            padding: 1rem 2rem;
            background: var(--accent-primary);
            border: none;
            border-radius: 12px;
            color: #000;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 48px;
        }

        .login-container button:hover {
            background: #d0a3ff;
            transform: scale(1.02);
        }

        .login-container button:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <div class="login-overlay">
        <div class="login-container">
            <div class="login-icon">🔒</div>
            <h2>Enter Password</h2>
            <form method="POST" action="">
                <input type="password" name="simple_login_password" placeholder="Password" autocomplete="off" autofocus required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
        <?php
    }
}
