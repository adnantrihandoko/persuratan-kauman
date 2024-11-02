<?php
namespace App\core\modules\auth;

use App\core\model\Auth;
use App\core\modules\user\UserRepoIntr;
use App\core\modules\auth\AuthDataAccess;

class AuthRepository implements AuthRepoIntr
{
    private $authDataAccess;

    public function __construct(AuthDataAccess $authDataAccess)
    {
        $this->authDataAccess = $authDataAccess;
    }

    public function saveRefreshToken(Auth $auth): bool
    {
        return $this->authDataAccess->insertRefreshToken($auth);
    }

    public function findRefreshToken(string $refreshToken): ?Auth
    {
        return $this->authDataAccess->getRefreshToken($refreshToken);
    }

    public function findRefreshTokenByUserId(int $userId): Auth|null{
        return $this->authDataAccess->getRefreshTokenByUserId($userId);
    }

    public function updateRefreshToken(Auth $oldRefreshToken, string $newRefreshToken, int $newExpires): Auth|null{
        return $this->authDataAccess->updateRefreshToken($oldRefreshToken, $newRefreshToken, $newExpires);
    }

    public function revokeRefreshToken(string $refreshToken): bool
    {
        return $this->authDataAccess->deleteRefreshToken($refreshToken);
    }
}
