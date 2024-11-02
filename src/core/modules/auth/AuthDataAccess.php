<?php
namespace App\core\modules\auth;

use App\core\model\Auth;
use Exception;
use PDO;

class AuthDataAccess
{
    private PDO $pdo;

    public function __construct(PDO $connection)
    {
        $this->pdo = $connection;
        $this->initializeDatabase();
    }

    private function initializeDatabase(): void
    {
        try {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS refresh_tokens (user_id INTEGER, refresh_token TEXT, expires_at INTEGER)");
        } catch (Exception $e) {
            throw new Exception("Failed to initialize database: " . $e->getMessage());
        }
    }

    public function insertRefreshToken(Auth $auth): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO refresh_tokens (user_id, refresh_token, expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([
            $auth->getUserId(),
            $auth->getRefreshToken(),
            $auth->getExpiresAt()
        ]);
    }

    public function updateRefreshToken(Auth $auth, string $newRefreshToken, int $newExpires): Auth|null
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE refresh_tokens SET refresh_token = ?, expires_at = ? WHERE user_id=? AND refresh_token = ?");
            $stmt->execute([
                $newRefreshToken,
                $newExpires,
                $auth->getUserId(),
                $auth->getRefreshToken(),
            ]);
            $data = $this->getRefreshTokenByUserId($auth->getUserId());
            return $data ?: $data;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function getRefreshTokenByUserId(int $userId): Auth|null
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM refresh_tokens WHERE user_id=?");
            $stmt->execute([$userId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? new Auth($data['user_id'], $data['refresh_token'], $data['expires_at']) : null;

        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function getRefreshToken(string $refreshToken): ?Auth
    {
        $stmt = $this->pdo->prepare("SELECT * FROM refresh_tokens WHERE refresh_token = ?");
        $stmt->execute([$refreshToken]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new Auth(
                $result['user_id'],
                $result['refresh_token'],
                $result['expires_at']
            );
        }

        return null;
    }

    public function deleteRefreshToken(string $refreshToken): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM refresh_tokens WHERE refresh_token = ?");
        return $stmt->execute([$refreshToken]);
    }
}
