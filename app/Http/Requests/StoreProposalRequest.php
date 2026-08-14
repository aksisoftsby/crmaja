<?php

namespace App\Http\Requests;

use App\Models\Proposal;
use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Proposal::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id'],
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:20000'],
            'date' => ['required', 'date'],
            'open_till' => ['nullable', 'date', 'after_or_equal:date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['nullable', 'integer', 'exists:items,id'],
            'items.*.description' => ['required', 'string', 'max:5000'],
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
        ];
    }
}
