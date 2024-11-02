<?php
namespace App\core\model;

class Auth
{
    private $userId;
    private $refreshToken;
    private $expiresAt;

    public function __construct($userId, $refreshToken, $expiresAt)
    {
        $this->userId = $userId;
        $this->refreshToken = $refreshToken;
        $this->expiresAt = $expiresAt;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
