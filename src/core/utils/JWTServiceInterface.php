<?php
namespace App\core\utils;
interface JWTServiceInterface
{
    /**
     * Generates a JWT access token for the given user.
     *
     * @param mixed $user The user object for which to generate the token.
     * @return string The generated JWT token.
     */
    public function generateToken($user): string;

    /**
     * Validates a given JWT token.
     *
     * @param string $token The JWT token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    public function validateToken(string $token): bool;
}