<?php

namespace App\Mappers;

use App\DTO\TaskDTO;
use App\Enums\TaskStatus;
use Carbon\Carbon;
use InvalidArgumentException;

class TaskMapper
{
    public static function toDTO(array $data): TaskDTO
    {
        if (!isset($data['id']) || !is_int($data['id'])) {
            throw new InvalidArgumentException('Key "id" is missing or type is incorrect.');
        }
        if (!isset($data['title']) || !is_string($data['title'])) {
            throw new InvalidArgumentException('Key "title" is missing or type is incorrect.');
        }
        if (!isset($data['description']) || !is_string($data['description'])) {
            throw new InvalidArgumentException('Key "description" is missing or type is incorrect.');
        }
        if (!isset($data['status']) || !is_string($data['status'])) {
            throw new InvalidArgumentException('Key "status" is missing or type is incorrect.');
        }
        if (!isset($data['deadline'])) {
            throw new InvalidArgumentException('Key "deadline" is missing.');
        }
        if (!isset($data['user_id']) || !is_int($data['user_id'])) {
            throw new InvalidArgumentException('Key "user_id" is missing or type is incorrect.');
        }
        try {
            $data['status'] = TaskStatus::from($data['status']);
        } catch (\ValueError $e) {
            throw new InvalidArgumentException('Invalid status provided.');
        }
        try {
            $data['deadline'] = Carbon::parse($data['deadline']);
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Key "deadline" must be a valid date string or Carbon instance.');
        }

        $data['project_id'] = isset($data['project_id']) ? (int) $data['project_id'] : null;

        return new TaskDTO($data);
    }
}
