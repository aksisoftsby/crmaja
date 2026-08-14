<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'sort_order',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'status_id');
    }
}
