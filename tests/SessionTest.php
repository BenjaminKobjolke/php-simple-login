<?php

declare(strict_types=1);

namespace BenjaminKobjolke\SimpleLogin\Tests;

use BenjaminKobjolke\SimpleLogin\Session;
use BenjaminKobjolke\SimpleLogin\SimpleLogin;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class SessionTest extends TestCase
{
    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }

    public function testLoginWithCorrectPasswordAuthenticates(): void
    {
        self::assertTrue(Session::login('secret'));
        self::assertTrue(Session::isAuthenticated());
    }

    public function testLoginWithWrongPasswordFails(): void
    {
        self::assertFalse(Session::login('wrong'));
        self::assertFalse(Session::isAuthenticated());
    }

    public function testLoginRegeneratesSessionId(): void
    {
        Session::start();
        $before = session_id();

        Session::login('secret');

        self::assertNotSame($before, session_id());
    }

    public function testLocksAfterTooManyWrongPasswords(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Session::login('wrong');
        }

        self::assertTrue(Session::isLocked());
        self::assertFalse(Session::login('secret'));
        self::assertFalse(Session::isAuthenticated());
    }

    public function testLogoutEndsAuthentication(): void
    {
        Session::login('secret');
        Session::logout();

        self::assertFalse(Session::isAuthenticated());
    }

    #[RunInSeparateProcess]
    public function testPasswordHashTakesPrecedenceOverPlainPassword(): void
    {
        define('SIMPLE_LOGIN_PASSWORD_HASH', password_hash('hashed-secret', PASSWORD_DEFAULT));

        self::assertFalse(Session::login('secret'));
        self::assertTrue(Session::login('hashed-secret'));
    }

    public function testCurrentPathRejectsNonLocalTargets(): void
    {
        $currentPath = new ReflectionMethod(SimpleLogin::class, 'currentPath');
        $cases = [
            '/app/index.php?logout' => '/app/index.php',
            '//evil.example/x' => '/',
            '/\\evil.example' => '/',
            'http://evil.example/' => '/',
        ];

        foreach ($cases as $uri => $expected) {
            $_SERVER['REQUEST_URI'] = $uri;
            self::assertSame($expected, $currentPath->invoke(null), $uri);
        }
    }
}
