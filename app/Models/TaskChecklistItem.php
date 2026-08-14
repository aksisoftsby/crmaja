<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskChecklistItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['task_id', 'description', 'is_finished', 'sort_order'];

    protected function casts(): array
    {
        return ['is_finished' => 'boolean', 'sort_order' => 'integer'];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
