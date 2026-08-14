<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Ticket::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'department_id' => ['nullable', 'integer', 'exists:ticket_departments,id'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['open', 'in_progress', 'answered', 'closed'])],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'message' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
