<?php
namespace App\core\modules\auth;

use App\core\model\User;
use App\core\model\Auth;
use App\core\utils\JWTServiceInterface;
use App\core\modules\auth\dto\LoginResponseDTO;
use App\core\modules\user\UserUseCase;
use Exception;

class AuthUseCase
{
    private AuthRepoIntr $authRepository;
    private UserUseCase $userUseCase;
    private JWTServiceInterface $jwtService;

    public function __construct(AuthRepoIntr $authRepository, JWTServiceInterface $jwtService, UserUseCase $userUseCase)
    {
        $this->authRepository = $authRepository;
        $this->jwtService = $jwtService;
        $this->userUseCase = $userUseCase;
    }

    public function login(string $email, string $password): LoginResponseDTO
    {
        try {

            $user = new User(null, null, $email, null);
            $refreshToken = bin2hex(random_bytes(32));
            $newRefreshTokenExpires = time() + (60 * 60 * 24 * 30);

            $storedUser = $this->userUseCase->getUserByEmail($user->getEmail());
            if (!$storedUser || !password_verify($password, $storedUser->getPasswordHash())) {
                return new LoginResponseDTO(false, 'Invalid Credentials');
            }

            $existRefreshToken = $this->authRepository->findRefreshTokenByUserId($storedUser->getId());
            $accessToken = $this->jwtService->generateToken($storedUser);
            if ($existRefreshToken && $existRefreshToken->getExpiresAt() > time()) {
                return new LoginResponseDTO(true, 'Login berhasil!', $accessToken, $existRefreshToken->getRefreshToken());
            }

            if($existRefreshToken && $existRefreshToken->getExpiresAt() < time()){
                $this->authRepository->updateRefreshToken($existRefreshToken, $refreshToken, $newRefreshTokenExpires);
                return new LoginResponseDTO(true, 'Login berhasil!', $accessToken);
            }

            $accessToken = $this->jwtService->generateToken($storedUser);

            $auth = new Auth($storedUser->getId(), $refreshToken, $newRefreshTokenExpires);
            $this->authRepository->saveRefreshToken($auth);

            return new LoginResponseDTO(true, 'Login Berhasil', $accessToken, $refreshToken);

        } catch (Exception $e) {

            return new LoginResponseDTO(false, 'An error occurred during login: ' . $e->getMessage());
        }
    }

    public function refreshRefreshToken(){

    }

    public function refreshAccessToken(string $refreshToken): ?string
    {
        try {

            $auth = $this->authRepository->findRefreshToken($refreshToken);
            if (!$auth || $auth->getExpiresAt() < time()) {
                return null;
            }

            $user = $this->userUseCase->getUserById($auth->getUserId());
            $newAccessToken = $this->jwtService->generateToken($user);

            return $newAccessToken;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function logout(string $refreshToken): bool
    {
        try {
            return $this->authRepository->revokeRefreshToken($refreshToken);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
