<?php

namespace App\Mappers;

use App\DTO\UserDTO;
use App\Enums\UserRole;
use InvalidArgumentException;
use ValueError;

class UserMapper
{
    public static function toDTO(array $data): UserDTO
    {
        if (!isset($data['id']) || !is_int($data['id'])) {
            throw new InvalidArgumentException('Key "id" is missing or type is incorrect.');
        }
        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new InvalidArgumentException('Key "name" is missing or type is incorrect.');
        }
        if (!isset($data['email']) || !is_string($data['email'])) {
            throw new InvalidArgumentException('Key "email" is missing or type is incorrect.');
        }
        if (!isset($data['password']) || !is_string($data['password'])) {
            throw new InvalidArgumentException('Key "password" is missing or type is incorrect.');
        }
        if (!isset($data['role'])) {
            throw new InvalidArgumentException('Key "role" is missing.');
        }
        try {
            $data['role'] = UserRole::from($data['role']);
        } catch (ValueError $e) {
            throw new InvalidArgumentException('Invalid role provided.');
        }

        return new UserDTO($data);
    }
}
