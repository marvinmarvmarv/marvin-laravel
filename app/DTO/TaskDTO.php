<?php

namespace App\DTO;

use App\Enums\TaskStatus;
use Carbon\Carbon;

class TaskDTO
{
    public int $id;

    public string $title;

    public string $description;

    public TaskStatus $status;

    public Carbon $deadline;

    public int $user_id;

    public ?int $project_id;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->status = $data['status'] instanceof TaskStatus ? $data['status'] : TaskStatus::from($data['status']);
        $this->deadline = Carbon::parse($data['deadline']);
        $this->user_id = $data['user_id'];
        $this->project_id = $data['project_id'] ?? null;
    }
}
