<?php

namespace App\DTO;

use App\Enums\UserRole;

class UserDTO
{
    public int $id;

    public string $name;

    public string $email;

    public string $password;

    public UserRole $role;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->role = $data['role'] instanceof UserRole ? $data['role'] : UserRole::from($data['role']);
    }
}
