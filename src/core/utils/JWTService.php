<?php
namespace App\core\utils;
use App\core\utils\JWTServiceInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService implements JWTServiceInterface
{
    private string $secretKey = "aca66cbd4187750ba5eb599a6561cb56";

    public function generateToken($user): string
    {
        $payload = [
            'iss' => 'persuratankauman.com',
            'aud' => 'persuratankauman.com',
            'iat' => time(),
            'exp' => time() + 60,
            'email' => $user->getEmail()
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token): bool
    {
        try {
            JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return true;
        } catch (\Exception $e) {
            return false;
            // throw new Exception($e->getMessage());
        }
    }
}
