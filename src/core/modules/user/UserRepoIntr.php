<?php
namespace App\core\modules\user;

use App\core\model\User;

interface UserRepoIntr
{
    public function create(User $user);

    public function findById($id);

    public function findByEmail($email);

    public function findAll();

    public function update(User $user);
    
    public function delete($id);
}
