<?php
namespace App\core\modules\auth\dto;

class LoginResponseDTO
{
    private bool $success;
    private string $message;
    private ?string $accessToken;
    private ?string $refreshToken;

    public function __construct(bool $success, string $message, ?string $accessToken = null, ?string $refreshToken = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }
}
