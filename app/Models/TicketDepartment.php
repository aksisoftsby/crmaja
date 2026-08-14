<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketDepartment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email_piping_address'];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'department_id');
    }
}
