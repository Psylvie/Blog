<?php

namespace App\Utils;

class Superglobals
{
    public static function getSessionData(): array
    {
        return $_SESSION ?? [];
    }
    public static function getSession(string $key): ?string
    {
        return $_SESSION[$key] ?? null;
    }

    public static function setSession(string $key, string $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function unsetSession(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function getPost(string $key): ?string
    {
        return $_POST[$key] ?? null;
    }

    public static function getGet(string $key): ?string
    {
        return $_GET[$key] ?? null;
    }

    public static function getFiles(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public static function getServer(string $key): ?string
    {
        return $_SERVER[$key] ?? null;
    }

    public static function setFlashMessage(string $type, string $message): void
    {
        $_SESSION['flash_type'] = $type;
        $_SESSION['flash_message'] = $message;
    }

    public static function getFlashMessage(): array
    {
        $flash = [];
        if (isset($_SESSION['flash_type']) && isset($_SESSION['flash_message'])) {
            $flash['type'] = $_SESSION['flash_type'];
            $flash['message'] = $_SESSION['flash_message'];
            unset($_SESSION['flash_type'], $_SESSION['flash_message']);
        }
        return $flash;
    }
    public static function getCookie(string $key): ?string
    {
        return $_COOKIE[$key] ?? null;
    }
}
