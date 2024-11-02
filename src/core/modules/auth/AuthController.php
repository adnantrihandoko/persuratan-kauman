<?php
namespace App\core\modules\auth;

use App\core\modules\auth\AuthUseCase;
use App\core\modules\auth\dto\LoginRequestDTO;
use Exception;

class AuthController
{
    private AuthUseCase $authUseCase;

    public function __construct(AuthUseCase $authUseCase)
    {
        $this->authUseCase = $authUseCase;
    }

    public function login($requestData = null)
    {
        try {
            if ($requestData === null) {
                $requestData = json_decode(file_get_contents("php://input"), true);
            }

            if (!isset($requestData['email'], $requestData['password'])) {
                throw new Exception('Invalid input');
            }

            $loginRequest = new LoginRequestDTO($requestData['email'], $requestData['password']);

            $result = $this->authUseCase->login($loginRequest->getEmail(), $loginRequest->getPassword());

            if ($result->isSuccess()) {
                header('Content-Type: application/json');

                setcookie("a", $result->getAccessToken(), [
                    'httponly' => true,
                    'secure' => true,
                    'samesite' => 'Strict'
                ]);

                setcookie("r", $result->getRefreshToken(), [
                    'httponly' => true,
                    'secure' => true,
                    'samesite' => 'Strict'
                ]);

                $response = json_encode(['message' => $result->getMessage(), 'accessToken' => $result->getAccessToken()]);

                echo $response;

                return $response;

            } else {
                throw new Exception($result->getMessage() ?: 'Login failed');
            }

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function refreshAccessToken()
    {
        try {
            $refreshToken = $_COOKIE['r'] ?? null;
            if (!isset($refreshToken)) {
                throw new Exception('Unauthorized');
            }

            $accessToken = $this->authUseCase->refreshAccessToken($refreshToken);

            if ($accessToken) {
                header('Content-Type: application/json');
                setcookie("r", $accessToken);
                $response = json_encode(['accessToken' => $accessToken]);
                echo $response;
                return $response;
            } else {
                throw new Exception('Invalid or expired refresh token');
            }

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function logout($requestData = null)
    {
        try {

            if ($requestData === null) {
                $requestData = json_decode(file_get_contents("php://input"), true);
            }

            if (!isset($requestData['refreshToken'])) {
                throw new Exception('Invalid input');
            }

            $refreshToken = $requestData['refreshToken'];

            if ($this->authUseCase->logout($refreshToken)) {
                header('Content-Type: application/json');
                $response = json_encode(['message' => 'Successfully logged out']);
                echo $response;
                return $response;
            } else {
                throw new Exception('Invalid or already revoked refresh token');
            }

        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
