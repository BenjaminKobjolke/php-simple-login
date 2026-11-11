<?php
/**
 * Login page template, rendered by SimpleLogin::showLoginPage().
 *
 * @var array<string, string> $labels
 * @var string|null $errorMessage
 */
$e = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="<?= $e($labels['lang']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="<?= defined('SIMPLE_LOGIN_THEME_COLOR') ? htmlspecialchars(SIMPLE_LOGIN_THEME_COLOR) : '#121212' ?>">
    <title><?= $e($labels['title']) ?></title>
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
            --error-color: #cf6679;
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

        .login-error {
            color: var(--error-color);
            margin-bottom: 1rem;
        }
    </style>
    <?php if (defined('SIMPLE_LOGIN_CSS_URL')): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(SIMPLE_LOGIN_CSS_URL) ?>">
    <?php endif; ?>
</head>
<body>
    <div class="login-overlay">
        <div class="login-container">
            <div class="login-icon">🔒</div>
            <h2><?= $e($labels['heading']) ?></h2>
            <?php if ($errorMessage !== null): ?>
            <p class="login-error" role="alert"><?= $e($errorMessage) ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="password" name="simple_login_password" placeholder="<?= $e($labels['placeholder']) ?>" aria-label="<?= $e($labels['placeholder']) ?>" autocomplete="current-password" autofocus required>
                <button type="submit"><?= $e($labels['button']) ?></button>
            </form>
        </div>
    </div>
</body>
</html>
