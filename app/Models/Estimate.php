<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['client_id', 'number', 'date', 'expiry_date', 'status', 'subtotal', 'discount', 'total', 'notes'];

    protected function casts(): array
    {
        return ['date' => 'date', 'expiry_date' => 'date', 'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }
}
