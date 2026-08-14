<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'client_id', 'description', 'start_date', 'deadline', 'status', 'billing_type', 'budget', 'progress', 'created_by'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'deadline' => 'date', 'budget' => 'decimal:2', 'progress' => 'integer'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')->withTimestamps();
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('sort_order');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'related_id')->where('related_type', self::class);
    }
}
