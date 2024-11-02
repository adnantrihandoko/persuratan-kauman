<?php
namespace App\core\utils;

use App\core\modules\auth\AuthUseCase;
use App\core\utils\JWTService;

class Middleware{

    private $jwtService;
    private $authUseCase;

    function __construct(JWTService $jwtService,AuthUseCase $authUseCase){
        $this->jwtService = $jwtService;
        $this->authUseCase = $authUseCase;
    }
    public function middleware(){
        if (!isset($_COOKIE['a'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Access token is missing']);
            return;
        }

        $accessToken = $_COOKIE['a'];
        $refreshToken = $_COOKIE['r'];

        $decodedAccessToken = $this->jwtService->validateToken($accessToken);
        if (!$decodedAccessToken) {

            unset($_COOKIE['a']);
            setcookie('a', '', time() - 3600, '/');

            $newAccessToken = $this->authUseCase->refreshAccessToken($refreshToken);
            if (!$newAccessToken) {
                http_response_code(401);
                echo json_encode(['message' => 'Unauthorized, silahkan login ulang.', 'status' => false]);
                return;
            }

            if ($accessToken) {
                setcookie('a', $newAccessToken, [
                    'httponly' => true,
                    'secure' => true,
                    'samesite' => 'Strict',
                ]);
            }

        }
    }
}