# PHP Simple Login

A simple password-only login screen for PHP projects. Features a modern dark theme UI and minimal configuration.

## Installation

### Via Composer (recommended)

```bash
composer require benjaminkobjolke/php-simple-login
```

### Local Development

Add a path repository to your `composer.json`. You must also set `minimum-stability` to `dev` since the package uses dev-main:

```json
{
    "minimum-stability": "dev",
    "prefer-stable": true,
    "repositories": [
        {
            "type": "path",
            "url": "/path/to/php-simple-login"
        }
    ],
    "require": {
        "benjaminkobjolke/php-simple-login": "*"
    }
}
```

Then run `composer update`.

## Configuration

Define the required constants in your project's config file **before** calling `requireAuth()`:

```php
<?php
// In your config.php or at the top of your script

// Required: Login password ...
define('SIMPLE_LOGIN_PASSWORD', 'your-secure-password');
// ... or a hash from password_hash(), so the config doesn't hold the plain password.
// Takes precedence over SIMPLE_LOGIN_PASSWORD when both are defined.
define('SIMPLE_LOGIN_PASSWORD_HASH', '$2y$10$...');

// Optional: redirect after login (default: current request path)
define('SIMPLE_LOGIN_REDIRECT', '/dashboard.php');

// Optional: session lifetime in seconds (default: 1 year)
define('SIMPLE_LOGIN_SESSION_LIFETIME', 31536000);

// Optional: private session folder (created if missing). Recommended: in a shared session
// folder, another app's GC with a shorter lifetime deletes the session and logs the user out.
define('SIMPLE_LOGIN_SESSION_PATH', __DIR__ . '/var/sessions');

// Optional: own session cookie name. Apps on the same host otherwise share PHPSESSID.
define('SIMPLE_LOGIN_SESSION_NAME', 'MYAPP_SESSION');

// Optional: cookie path, so an app in a subfolder doesn't send its cookie to every app on the host
define('SIMPLE_LOGIN_COOKIE_PATH', '/myapp');

// Optional: extra stylesheet loaded after the built-in login styles.
// Override the login page's :root CSS variables (--bg-primary, --bg-secondary,
// --bg-card, --bg-card-hover, --text-primary, --text-secondary,
// --accent-primary, --border-color, --shadow-lg, --error-color) to theme it.
define('SIMPLE_LOGIN_CSS_URL', 'themes/my-theme.css');

// Optional: <meta name="theme-color"> of the login page (default: #121212)
define('SIMPLE_LOGIN_THEME_COLOR', '#041a3d');

// Optional: page texts. Any key left out keeps its English default.
define('SIMPLE_LOGIN_LABELS', [
    'lang' => 'de',
    'title' => 'Anmelden',
    'heading' => 'Passwort eingeben',
    'placeholder' => 'Passwort',
    'button' => 'Anmelden',
    'wrong_password' => 'Falsches Passwort',
    'locked' => 'Zu viele Versuche. Bitte in ein paar Minuten erneut versuchen.',
]);
```

## Usage

### Protect a PHP file

Add this at the top of any PHP file you want to protect:

```php
<?php
require_once 'vendor/autoload.php';
require_once 'config.php'; // Your config with SIMPLE_LOGIN_PASSWORD

use BenjaminKobjolke\SimpleLogin\SimpleLogin;

// This will show login if not authenticated
SimpleLogin::requireAuth();

// Your protected code here
echo "Welcome! You are logged in.";
```

### Check authentication without blocking

```php
<?php
require_once 'vendor/autoload.php';

use BenjaminKobjolke\SimpleLogin\SimpleLogin;

if (SimpleLogin::isAuthenticated()) {
    echo "Logged in";
} else {
    echo "Not logged in";
}
```

### Logout

Add `?logout` to any URL to log out:

```
https://example.com/dashboard.php?logout
```

Logout via GET is kept for simple links. Requests that the browser marks as cross-site
(`Sec-Fetch-Site: cross-site`, e.g. an `<img src="...?logout">` on another site) are ignored.
Older browsers don't send this header, so there another site can still log the user out.

Or programmatically:

```php
SimpleLogin::logout();
```

## Features

- Password-only authentication (no username)
- Modern dark theme login screen
- Configurable redirect after login
- 1-year session persistence by default; the cookie is renewed on each visit
- Safe session defaults: HttpOnly, SameSite=Lax, Secure on HTTPS, strict mode, new session ID after login
- Timing-safe password check, optional `password_hash()` hash
- Brute-force brake: 1 second delay per wrong password, 5-minute lock after 5 wrong tries (per session)
- Login page answers with `401` (`429` while locked) and shows "Wrong password"
- Mobile-friendly responsive design
- Zero dependencies

## Tests

```bash
composer install
composer test
```
