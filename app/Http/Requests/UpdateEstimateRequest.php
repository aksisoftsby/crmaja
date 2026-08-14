<?php

namespace App\Http\Requests;

use App\Models\Estimate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $estimate = $this->route('estimate');

        return $estimate instanceof Estimate && ($this->user()?->can('update', $estimate) ?? false);
    }

    public function rules(): array
    {
        return ['client_id' => ['required', 'integer', 'exists:clients,id'], 'date' => ['required', 'date'], 'expiry_date' => ['nullable', 'date', 'after_or_equal:date'], 'status' => ['required', 'in:draft,sent,open,declined,accepted,expired'], 'discount' => ['nullable', 'numeric', 'min:0'], 'notes' => ['nullable', 'string', 'max:10000'], 'items' => ['required', 'array', 'min:1'], 'items.*.item_id' => ['nullable', 'integer', 'exists:items,id'], 'items.*.description' => ['required', 'string', 'max:5000'], 'items.*.qty' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0']];
    }
}
