<?php
namespace App\core\modules\user;

use App\core\model\User;
use App\core\modules\user\UserUseCase;
use App\core\utils\Middleware;

class UserController
{
    private $userUseCase;
    private $middleware;

    public function __construct(UserUseCase $userUseCase,  Middleware $middleware)
    {
        $this->userUseCase = $userUseCase;
        $this->middleware = $middleware;
    }

    public function createUser()
    {
        header("Content-Type: application/json");

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nama']) || !isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid input, nama, email, and password are required.']);
            return;
        }

        $user = new User(null, $data['nama'], $data['email'], password: $data['password']);

        try {

            if ($this->userUseCase->createUser($user)) {
                http_response_code(201);
                echo json_encode(['message' => 'User created successfully']);
                return;
            }

            echo json_encode(['message' => 'Email sudah terdaftar']);

        } catch (\Exception $e) {

            http_response_code(500);
            echo json_encode(['message' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function getUser($id)
    {

        header("Content-Type: application/json");

        // Ambil access token dari cookie
        $this->middleware->middleware();

        $user = $this->userUseCase->getUserById($id);
        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
        }
    }

    public function getAllUsers()
    {
        header("Content-Type: application/json");

        // Ambil access token dari cookie
        $this->middleware->middleware();

        $users = $this->userUseCase->getAllUsers();
        if ($users === []) {
            echo json_encode(['error' => 'No users found.']);
            return;
        }

        $usersMap = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'nama' => $user->getNama(),
                'email' => $user->getEmail(),
            ];
        }, $users);

        if ($usersMap === false) {
            echo json_encode(['error' => json_last_error_msg()]);
        }

        echo json_encode($usersMap);

    }

    public function updateUser($id)
    {
        header("Content-Type: application/json");

        // Ambil access token dari cookie
        $this->middleware->middleware();

        $data = json_decode(file_get_contents("php://input"), true);


        if (!isset($data['nama']) || !isset($data['email'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Nama dan email diperlukan']);
            return;
        }

        $updateResult = $this->userUseCase->updateUser($id, $data);

        if ($updateResult) {
            http_response_code(200);
            echo json_encode(['message' => 'User updated successfully']);
            return;
        }

        http_response_code(404);
        echo json_encode(['message' => 'Gagal update user atau user tidak ditemukan!']);

    }

    public function deleteUser($id)
    {
        header("Content-Type: application/json");

        $this->middleware->middleware();

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['email'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Masukkan Email!']);
            return;
        }

        $user = $this->userUseCase->getUserById($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['message' => 'User tidak ditemukan!']);
            return;
        }

        $email = $user->getEmail();

        if ($data['email'] === $email) {
            $this->userUseCase->deleteUser($id);
            echo json_encode(['message' => 'User deleted successfully']);
            return;
        }

    }
}
