<?php
namespace App\core\modules\user;

use App\core\model\User;
use App\core\modules\user\UserRepoIntr;

class UserRepoImpl implements UserRepoIntr
{
    private $dataAccess;

    public function __construct(UserDataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    public function create(User $user)
    {
        $this->dataAccess->create($user);
    }

    public function findById($id)
    {
        return $this->dataAccess->findById($id);
    }

    public function findByEmail($email){
        return $this->dataAccess->findByEmail($email);
    }

    public function findAll()
    {
        return $this->dataAccess->findAll();
    }

    public function update(User $user)
    {
        return $this->dataAccess->update($user);
    }

    public function delete($id)
    {
        $this->dataAccess->delete($id);
    }
}
