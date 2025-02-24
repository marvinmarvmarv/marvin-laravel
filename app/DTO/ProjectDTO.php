<?php

namespace App\DTO;

class ProjectDTO
{
    public int $id;

    public string $name;

    public ?string $description;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
    }
}
