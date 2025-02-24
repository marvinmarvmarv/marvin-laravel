<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskExpiredNotification extends Notification
{
    use Queueable;

    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'message' => "Task '{$this->task->title}' is expired.",
            'due_date' => $this->task->due_date,
        ];
    }
}
