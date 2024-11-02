<?php
namespace App\core\modules\auth;

use App\core\model\Auth;

interface AuthRepoIntr
{
    public function saveRefreshToken(Auth $auth): bool;
    public function findRefreshToken(string $refreshToken): ?Auth;
    public function revokeRefreshToken(string $refreshToken): bool;
    public function findRefreshTokenByUserId(int $userId): Auth|null;
    public function updateRefreshToken(Auth $auth, string $newRefreshToken, int $newExpires): Auth|null;
}
