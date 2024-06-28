<?php

namespace App\Utils;

use Exception;

class CsrfProtection {

    /**
     * Generate a CSRF token
     * @return string
     * @throws Exception
     */
    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Superglobals::setSession('csrfToken', $token);
        return $token;
    }

    /**
     * Check if the CSRF token is valid
     * @param string $token
     * @return bool
     */
    public static function checkToken(string $token): bool
    {
        $csrfToken = Superglobals::getSession('csrfToken');
        return isset($csrfToken) && hash_equals($csrfToken, $token);
    }

    /**
     * Unset the CSRF token
     * @return void
     */
    public static function unsetToken(): void
    {
        Superglobals::unsetSession('csrfToken');
    }

}