<?php
namespace App\core\model;

class User{
    private $id;
    private $nama;
    private $email;
    private $passwordHash;

    public function __construct($id, $nama, $email, $password)
    {
        $this->id = $id;
        $this->nama = $nama;
        $this->email = $email;
        $this->passwordHash = $password;
    }

    public function getId(){
        return $this->id;
    }

    public function getNama(){
        return $this->nama;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setNama($nama){
        $this->nama = $nama;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function getPasswordHash(){
        return $this->passwordHash;
    }
}