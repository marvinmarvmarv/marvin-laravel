<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Events\TaskExpired;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'title', 'description', 'status', 'deadline', 'user_id', 'project_id'];

    protected $casts = [
        'id' => 'integer',
        'deadline' => 'date',
        'status' => TaskStatus::class,
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withDefault();
    }

    public function isExpired(): bool
    {
        return $this->deadline < Carbon::today();
    }

    protected static function booted()
    {
        static::updated(function (Task $task) {
            if ($task->deadline < now() && $task->status !== TaskStatus::DONE->value) {
                event(new TaskExpired($task));
            }
        });
    }
}
