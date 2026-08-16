<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['code', 'name', 'symbol', 'decimal_places', 'exchange_rate', 'is_default', 'is_active'];

    protected function casts(): array
    {
        return ['decimal_places' => 'integer', 'exchange_rate' => 'decimal:8', 'is_default' => 'boolean', 'is_active' => 'boolean'];
    }
}
