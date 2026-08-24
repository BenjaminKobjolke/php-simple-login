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

// Required: Login password
define('SIMPLE_LOGIN_PASSWORD', 'your-secure-password');

// Optional: redirect after login (default: same page)
define('SIMPLE_LOGIN_REDIRECT', '/dashboard.php');

// Optional: session lifetime in seconds (default: 1 year)
define('SIMPLE_LOGIN_SESSION_LIFETIME', 31536000);

// Optional: extra stylesheet loaded after the built-in login styles.
// Override the login page's :root CSS variables (--bg-primary, --bg-secondary,
// --bg-card, --bg-card-hover, --text-primary, --text-secondary,
// --accent-primary, --border-color, --shadow-lg) to theme it.
define('SIMPLE_LOGIN_CSS_URL', 'themes/my-theme.css');

// Optional: <meta name="theme-color"> of the login page (default: #121212)
define('SIMPLE_LOGIN_THEME_COLOR', '#041a3d');
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

Or programmatically:

```php
SimpleLogin::logout();
```

## Features

- Password-only authentication (no username)
- Modern dark theme login screen
- Configurable redirect after login
- 1-year session persistence by default
- Mobile-friendly responsive design
- Zero dependencies
