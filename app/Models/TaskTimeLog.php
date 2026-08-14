<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskTimeLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['task_id', 'user_id', 'start_time', 'end_time', 'note'];

    protected function casts(): array
    {
        return ['start_time' => 'datetime', 'end_time' => 'datetime'];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
