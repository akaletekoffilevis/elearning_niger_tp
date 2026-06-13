<?php

class Auth
{
    public static function login(int $userId): void
    {
        session_start();
        $_SESSION['user_id'] = $userId;
    }

    public static function logout(): void
    {
        session_start();
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        session_start();
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        $user = User::find($_SESSION['user_id']);
        return $user ?: null;
    }

    public static function require(): void
    {
        if (!self::check()) {
            Router::redirect('/login');
        }
    }

    public static function requireRole(string $role): void
    {
        self::require();

        $user = self::user();
        if (!$user || $user['role'] !== $role) {
            Router::redirect('/');
        }
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user && $user['role'] === 'admin';
    }
}
