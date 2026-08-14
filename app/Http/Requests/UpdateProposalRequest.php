<?php

namespace App\Http\Requests;

use App\Models\Proposal;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        $proposal = $this->route('proposal');

        return $proposal instanceof Proposal && ($this->user()?->can('update', $proposal) ?? false);
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
            'status' => ['required', 'in:draft,sent,open,revised,declined,accepted'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['nullable', 'integer', 'exists:items,id'],
            'items.*.description' => ['required', 'string', 'max:5000'],
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
        ];
    }
}
