<?php
namespace App\core\modules\user;
use App\core\modules\user\UserRepoImpl;
use App\core\model\User;

class UserUseCase
{
    private $userRepository;

    public function __construct(UserRepoImpl $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(User $user)
    {
        if($this->userRepository->findByEmail($user->getEmail())){
            return false;
        }

        $hashPassword = password_hash($user->getPasswordHash(), PASSWORD_DEFAULT);
        $user = new User(null, $user->getNama(), $user->getEmail(), $hashPassword);
        $this->userRepository->create($user);
        return $user;
    }

    public function getUserById($id)
    {
        $user = $this->userRepository->findById($id);
        return $user ? new User($user->getId(), $user->getNama(), $user->getEmail(), $user->getPasswordHash()) : null;
    }

    public function getUserByEmail($email){
        $user = $this->userRepository->findByEmail($email);
        return $user ? new User($user->getId(), $user->getNama(), $user->getEmail(), $user->getPasswordHash()) : null;
    }

    public function getAllUsers()
    {
        $users = $this->userRepository->findAll();
        return array_map(function ($user) {
            return new User($user->getId(), $user->getNama(), $user->getEmail(), $user->getPasswordHash());
        }, $users);
    }

    public function updateUser($id, $data)
    {
        $user = new User($id, $data['nama'], $data['email'], null);
        $isUpdated = $this->userRepository->update($user);
        if($isUpdated){
            return true;
        }
        return false;
    }

    public function deleteUser($id)
    {
        $this->userRepository->delete($id);
    }
}
