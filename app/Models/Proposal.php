<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['client_id', 'lead_id', 'number', 'subject', 'content', 'subtotal', 'discount', 'total', 'status', 'date', 'open_till'];

    protected function casts(): array
    {
        return ['date' => 'date', 'open_till' => 'date', 'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProposalItem::class);
    }
}
