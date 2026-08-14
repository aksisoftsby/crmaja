<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Contact extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'title',
        'is_primary',
        'is_active',
        'password',
    ];

    protected $hidden = ['password'];

    protected $appends = [
        'full_name',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the customer this contact represents.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the contact's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->first_name, $this->last_name])));
    }
}
