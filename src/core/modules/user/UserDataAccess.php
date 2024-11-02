<?php
namespace App\core\modules\user;

use App\core\model\Auth;
use App\core\model\User;
use Exception;
use PDOException;
use PDO;

class UserDataAccess
{
    private PDO $db;

    public function __construct(PDO $connection)
    {
        $this->db = $connection;
        try {
            $this->db->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, nama TEXT, email TEXT)");
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function create(User $user)
    {
        $stmt = $this->db->prepare("INSERT INTO users (nama, email, password, created_at, updated_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        $stmt->execute([$user->getNama(), $user->getEmail(), $user->getPasswordHash()]);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data['id'], $data['nama'], $data['email'], $data['password']) : null;
    }

    public function findByEmail($email){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new User($data['id'], $data['nama'], $data['email'], $data['password']) : null;
    }

    public function findAll()
    {
        if(!$this->db){
            throw new Exception("Database connection not established.");
        }
        try {
            $stmt = $this->db->query("SELECT * FROM users");
            $users = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = new User($data['id'], $data['nama'], $data['email'], $data['password']);
            }

            return $users;
            
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            throw new Exception("An error occurred while retrieving users.");
        }
    }

    public function findRefreshTokenById(int $userId){
        try {
            $stmt = $this->db->prepare("SELECT refresh_token FROM users WHERE id=?");
            $stmt->execute([$userId]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? new Auth(null, $data['refresh_token'], null) : null;

        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function update(User $user)
    {
        $stmt = $this->db->prepare("UPDATE users SET nama = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$user->getNama(), $user->getEmail(), $user->getId()]);
        if($stmt->rowCount() > 0){
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
}
