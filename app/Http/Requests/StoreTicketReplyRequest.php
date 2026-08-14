<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $ticket instanceof Ticket && ($this->user()?->can('view', $ticket) ?? false);
    }

    public function rules(): array
    {
        return ['message' => ['required', 'string', 'max:10000'], 'is_internal_note' => ['nullable', 'boolean']];
    }
}
