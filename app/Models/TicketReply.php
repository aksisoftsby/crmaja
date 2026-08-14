<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['ticket_id', 'user_type', 'user_id', 'message', 'is_internal_note'];

    protected function casts(): array
    {
        return ['is_internal_note' => 'boolean'];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
