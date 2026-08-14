<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalItem extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'description', 'qty', 'rate', 'amount'];

    protected function casts(): array
    {
        return ['qty' => 'decimal:2', 'rate' => 'decimal:2', 'amount' => 'decimal:2'];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
