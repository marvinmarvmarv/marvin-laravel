<?php

namespace App\Mappers;

use App\DTO\ProjectDTO;
use InvalidArgumentException;

class ProjectMapper
{
    public static function toDTO(array $data): ProjectDTO
    {
        if (!isset($data['id']) || !is_int($data['id'])) {
            throw new InvalidArgumentException('Key "id" is missing or type is incorrect');
        }
        if (!isset($data['name']) || !is_string($data['name'])) {
            throw new InvalidArgumentException('key "name" is missing or type is wrong.');
        }

        return new ProjectDTO($data);
    }
}
